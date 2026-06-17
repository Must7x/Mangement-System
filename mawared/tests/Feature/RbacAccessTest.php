<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_technical_admin_can_access_users_and_settings(): void
    {
        $user = User::factory()->technicalAdmin()->create();

        $this->actingAs($user)->get(route('users.index'))->assertOk();
        $this->actingAs($user)->get(route('settings.index'))->assertOk();
    }

    public function test_technical_admin_cannot_access_operational_modules(): void
    {
        $user = User::factory()->technicalAdmin()->create();
        $asset = Asset::factory()->create();

        $this->assertAccessDenied($user, route('dashboard'));
        $this->assertAccessDenied($user, route('inventory.index'));
        $this->assertAccessDenied($user, route('assets.show', $asset));
        $this->assertAccessDenied($user, route('assignments.index'));
        $this->assertAccessDenied($user, route('assignment-history.index'));
        $this->assertAccessDenied($user, route('maintenances.index'));
        $this->assertAccessDenied($user, route('reports.index'));
        $this->assertAccessDenied($user, route('departments.index'));
        $this->assertAccessDenied($user, route('employees.index'));
    }

    public function test_inventory_supervisor_can_access_org_structure(): void
    {
        $user = User::factory()->inventorySupervisor()->create();

        $this->actingAs($user)->get(route('departments.index'))->assertOk();
        $this->actingAs($user)->get(route('employees.index'))->assertOk();
    }

    public function test_warehouse_keeper_cannot_access_org_structure_or_settings(): void
    {
        $user = User::factory()->warehouseKeeper()->create();
        $asset = Asset::factory()->warehouse()->create();

        $this->assertAccessDenied($user, route('departments.index'));
        $this->assertAccessDenied($user, route('employees.index'));
        $this->assertAccessDenied($user, route('settings.index'));
        $this->assertAccessDenied($user, route('users.index'));
        $this->assertAccessDenied($user, route('assets.destroy', $asset), 'delete');
    }

    public function test_warehouse_keeper_can_access_daily_operational_modules(): void
    {
        $user = User::factory()->warehouseKeeper()->create();

        $this->actingAs($user)->get(route('dashboard'))->assertOk();
        $this->actingAs($user)->get(route('inventory.index'))->assertOk();
        $this->actingAs($user)->get(route('assignments.index'))->assertOk();
        $this->actingAs($user)->get(route('assignment-history.index'))->assertOk();
        $this->actingAs($user)->get(route('reports.index'))->assertOk();
        $this->actingAs($user)->get(route('maintenances.index'))->assertOk();
    }

    public function test_technical_admin_home_redirects_to_users(): void
    {
        $user = User::factory()->technicalAdmin()->create();

        $this->actingAs($user)->get('/')->assertRedirect(route('users.index'));
    }

    public function test_technical_admin_login_ignores_inaccessible_intended_url(): void
    {
        User::factory()->technicalAdmin()->create([
            'email' => 'admin@example.com',
        ]);

        $this->get(route('dashboard'));

        $this->post(route('login'), [
            'email' => 'admin@example.com',
            'password' => 'password',
        ])->assertRedirect(route('users.index'));
    }

    private function assertAccessDenied(User $user, string $url, string $method = 'get'): void
    {
        $response = $this->actingAs($user)->{$method}($url);

        $response
            ->assertRedirect($user->homeRoute())
            ->assertSessionHas('error', __('messages.errors.access_denied'));
    }
}
