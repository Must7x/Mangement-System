<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_technical_admin_can_manage_roles(): void
    {
        $user = User::factory()->technicalAdmin()->create();

        $this->actingAs($user)->get(route('roles.index'))->assertOk();
        $this->actingAs($user)->get(route('roles.create'))->assertOk();
    }

    public function test_technical_admin_can_create_role_with_permissions(): void
    {
        $user = User::factory()->technicalAdmin()->create();
        $permissionIds = Permission::query()->limit(3)->pluck('id')->all();

        $this->actingAs($user)->post(route('roles.store'), [
            'name' => 'Custom Auditor',
            'description' => 'Read-only auditor',
            'permissions' => $permissionIds,
        ])->assertRedirect(route('roles.index'));

        $role = Role::where('slug', 'custom-auditor')->first();
        $this->assertNotNull($role);
        $this->assertCount(3, $role->permissions);
    }

    public function test_technical_admin_can_update_role_permissions(): void
    {
        $admin = User::factory()->technicalAdmin()->create();
        $role = Role::create([
            'name' => 'Temp Role',
            'slug' => 'temp-role',
            'is_system' => false,
        ]);

        $permissionId = Permission::query()->value('id');

        $this->actingAs($admin)->put(route('roles.update', $role), [
            'name' => 'Temp Role Updated',
            'permissions' => [$permissionId],
        ])->assertRedirect(route('roles.index'));

        $this->assertTrue($role->fresh()->permissions->contains('id', $permissionId));
    }

    public function test_technical_admin_cannot_delete_system_role(): void
    {
        $admin = User::factory()->technicalAdmin()->create();
        $role = Role::where('slug', 'technical_admin')->firstOrFail();

        $this->actingAs($admin)->delete(route('roles.destroy', $role))
            ->assertRedirect()
            ->assertSessionHasErrors('role');
    }

    public function test_technical_admin_cannot_delete_role_in_use(): void
    {
        $admin = User::factory()->technicalAdmin()->create();
        $role = Role::create([
            'name' => 'Assigned Role',
            'slug' => 'assigned-role',
            'is_system' => false,
        ]);

        User::factory()->create(['role_id' => $role->id]);

        $this->actingAs($admin)->delete(route('roles.destroy', $role))
            ->assertRedirect()
            ->assertSessionHasErrors('role');
    }

    public function test_technical_admin_can_delete_unused_custom_role(): void
    {
        $admin = User::factory()->technicalAdmin()->create();
        $role = Role::create([
            'name' => 'Disposable Role',
            'slug' => 'disposable-role',
            'is_system' => false,
        ]);

        $this->actingAs($admin)->delete(route('roles.destroy', $role))
            ->assertRedirect(route('roles.index'));

        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    public function test_inventory_supervisor_cannot_manage_roles(): void
    {
        $user = User::factory()->inventorySupervisor()->create();

        $this->actingAs($user)->get(route('roles.index'))
            ->assertRedirect($user->homeRoute())
            ->assertSessionHas('error', __('messages.errors.access_denied'));
    }

    public function test_warehouse_keeper_cannot_manage_roles(): void
    {
        $user = User::factory()->warehouseKeeper()->create();

        $this->actingAs($user)->get(route('roles.index'))
            ->assertRedirect($user->homeRoute())
            ->assertSessionHas('error', __('messages.errors.access_denied'));
    }
}
