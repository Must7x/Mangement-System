<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_custom_role_grants_only_assigned_permissions(): void
    {
        $role = Role::create([
            'name' => 'Reports Only',
            'slug' => 'reports-only',
            'is_system' => false,
        ]);

        $reportsPermission = Permission::where('slug', 'reports.view')->firstOrFail();
        $role->permissions()->sync([$reportsPermission->id]);

        $user = User::factory()->create(['role_id' => $role->id]);

        $this->actingAs($user)->get(route('reports.index'))->assertOk();
        $this->actingAs($user)->get(route('inventory.index'))
            ->assertRedirect($user->homeRoute())
            ->assertSessionHas('error', __('messages.errors.access_denied'));
    }

    public function test_user_without_assets_delete_cannot_delete_asset(): void
    {
        $user = User::factory()->warehouseKeeper()->create();
        $asset = Asset::factory()->warehouse()->create();

        $this->actingAs($user)->delete(route('assets.destroy', $asset))
            ->assertRedirect($user->homeRoute())
            ->assertSessionHas('error', __('messages.errors.access_denied'));
    }

    public function test_user_without_departments_view_cannot_access_departments(): void
    {
        $user = User::factory()->warehouseKeeper()->create();

        $this->actingAs($user)->get(route('departments.index'))
            ->assertRedirect($user->homeRoute())
            ->assertSessionHas('error', __('messages.errors.access_denied'));
    }

    public function test_user_without_users_view_cannot_access_users(): void
    {
        $user = User::factory()->inventorySupervisor()->create();

        $this->actingAs($user)->get(route('users.index'))
            ->assertRedirect($user->homeRoute())
            ->assertSessionHas('error', __('messages.errors.access_denied'));
    }
}
