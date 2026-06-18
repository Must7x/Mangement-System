<?php

namespace Tests\Feature;

use App\Enums\ActivityAction;
use App\Enums\AssetStatus;
use App\Models\ActivityLog;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\Maintenance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_activity_log(): void
    {
        $this->get(route('activity-log.index'))
            ->assertRedirect(route('login'));
    }

    public function test_technical_admin_can_view_activity_log(): void
    {
        $user = User::factory()->technicalAdmin()->create();

        $this->actingAs($user)->get(route('activity-log.index'))
            ->assertOk()
            ->assertViewIs('activity-log.index');
    }

    public function test_inventory_supervisor_can_view_activity_log(): void
    {
        $user = User::factory()->inventorySupervisor()->create();

        $this->actingAs($user)->get(route('activity-log.index'))
            ->assertOk()
            ->assertViewIs('activity-log.index');
    }

    public function test_warehouse_keeper_cannot_view_activity_log(): void
    {
        $user = User::factory()->warehouseKeeper()->create();

        $this->actingAs($user)->get(route('activity-log.index'))
            ->assertRedirect($user->homeRoute())
            ->assertSessionHas('error', __('messages.errors.access_denied'));
    }

    public function test_creating_asset_logs_activity_with_user_name(): void
    {
        $supervisor = User::factory()->inventorySupervisor()->create([
            'first_name' => 'أحمد',
            'last_name' => 'ولد محمد',
        ]);

        $this->actingAs($supervisor)->post(route('assets.store'), [
            'name' => 'HP Laptop',
            'type' => 'laptop',
            'serial_number' => 'SN-ACT-001',
            'status' => AssetStatus::Warehouse->value,
        ])->assertRedirect(route('inventory.index'));

        $log = ActivityLog::query()->where('action', ActivityAction::AssetCreated)->first();

        $this->assertNotNull($log);
        $this->assertSame($supervisor->id, $log->user_id);
        $this->assertSame('أحمد ولد محمد', $log->user_name);
        $this->assertStringContainsString('HP Laptop', $log->description());
    }

    public function test_different_users_are_recorded_separately(): void
    {
        $supervisor = User::factory()->inventorySupervisor()->create([
            'first_name' => 'مشرف',
            'last_name' => 'أول',
        ]);
        $keeper = User::factory()->warehouseKeeper()->create([
            'first_name' => 'أمين',
            'last_name' => 'ثاني',
        ]);

        $this->actingAs($supervisor)->post(route('assets.store'), [
            'name' => 'Device A',
            'type' => 'laptop',
            'serial_number' => 'SN-A',
            'status' => AssetStatus::Warehouse->value,
        ]);

        $this->actingAs($keeper)->post(route('assets.store'), [
            'name' => 'Device B',
            'type' => 'laptop',
            'serial_number' => 'SN-B',
            'status' => AssetStatus::Warehouse->value,
        ]);

        $this->assertSame(2, ActivityLog::count());
        $this->assertSame('مشرف أول', ActivityLog::query()->where('properties->serial_number', 'SN-A')->value('user_name'));
        $this->assertSame('أمين ثاني', ActivityLog::query()->where('properties->serial_number', 'SN-B')->value('user_name'));
    }

    public function test_assignment_and_return_are_logged(): void
    {
        $user = User::factory()->warehouseKeeper()->create();
        $asset = Asset::factory()->warehouse()->create(['name' => 'Printer X', 'serial_number' => 'SN-P']);
        $employee = Employee::factory()->create(['name' => 'Ali Employee']);

        $this->actingAs($user)->post(route('assignments.store'), [
            'asset_id' => $asset->id,
            'employee_id' => $employee->id,
            'assigned_date' => now()->toDateString(),
        ])->assertRedirect(route('assignments.index'));

        $this->assertDatabaseHas('activity_logs', [
            'action' => ActivityAction::AssignmentCreated->value,
            'asset_id' => $asset->id,
        ]);

        $assignment = $asset->fresh()->assignment;
        $this->assertNotNull($assignment);

        $this->actingAs($user)->delete(route('assignments.destroy', $assignment))
            ->assertRedirect(route('assignments.index'));

        $this->assertDatabaseHas('activity_logs', [
            'action' => ActivityAction::AssignmentReturned->value,
            'asset_id' => $asset->id,
        ]);
    }

    public function test_asset_360_shows_activity_log_section_for_supervisor(): void
    {
        $keeper = User::factory()->warehouseKeeper()->create();
        $supervisor = User::factory()->inventorySupervisor()->create();

        $this->actingAs($keeper)->post(route('assets.store'), [
            'name' => 'Logged Asset',
            'type' => 'laptop',
            'serial_number' => 'SN-360-LOG',
            'status' => AssetStatus::Warehouse->value,
        ]);

        $asset = Asset::query()->where('serial_number', 'SN-360-LOG')->firstOrFail();

        $this->actingAs($keeper)->get(route('assets.show', $asset))
            ->assertOk()
            ->assertViewHas('activityLogs', fn ($logs) => $logs->isEmpty());

        $this->actingAs($supervisor)->get(route('assets.show', $asset))
            ->assertOk()
            ->assertViewHas('activityLogs', fn ($logs) => $logs->count() >= 1);
    }

    public function test_maintenance_complete_is_logged(): void
    {
        $user = User::factory()->warehouseKeeper()->create();
        $maintenance = Maintenance::factory()->inProgress()->create();

        $this->actingAs($user)->post(route('maintenances.complete', $maintenance))
            ->assertRedirect(route('maintenances.index'));

        $this->assertDatabaseHas('activity_logs', [
            'action' => ActivityAction::MaintenanceCompleted->value,
            'asset_id' => $maintenance->asset_id,
        ]);
    }
}
