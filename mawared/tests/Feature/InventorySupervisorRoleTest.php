<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventorySupervisorRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_supervisor_can_access_dashboard(): void
    {
        $user = User::factory()->inventorySupervisor()->create();

        $this->actingAs($user)->get(route('dashboard'))
            ->assertOk();
    }

    public function test_supervisor_can_access_inventory(): void
    {
        $user = User::factory()->inventorySupervisor()->create();

        $this->actingAs($user)->get(route('inventory.index'))
            ->assertOk();
    }

    public function test_supervisor_can_access_maintenances(): void
    {
        $user = User::factory()->inventorySupervisor()->create();

        $this->actingAs($user)->get(route('maintenances.index'))
            ->assertOk();
    }

    public function test_supervisor_cannot_access_users(): void
    {
        $user = User::factory()->inventorySupervisor()->create();

        $this->actingAs($user)->get(route('users.index'))
            ->assertRedirect($user->homeRoute())
            ->assertSessionHas('error', __('messages.errors.access_denied'));
    }

    public function test_supervisor_cannot_access_settings(): void
    {
        $user = User::factory()->inventorySupervisor()->create();

        $this->actingAs($user)->get(route('settings.index'))
            ->assertRedirect($user->homeRoute())
            ->assertSessionHas('error', __('messages.errors.access_denied'));
    }
}
