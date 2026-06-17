<?php

namespace Tests\Feature;

use App\Enums\AssetStatus;
use App\Enums\MaintenancePriority;
use App\Enums\MaintenanceStatus;
use App\Models\Asset;
use App\Models\Assignment;
use App\Models\Employee;
use App\Models\Maintenance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceStateTest extends TestCase
{
    use RefreshDatabase;

    public function test_opening_maintenance_sets_asset_status_to_maintenance(): void
    {
        $user = User::factory()->warehouseKeeper()->create();
        $asset = Asset::factory()->warehouse()->create();

        $this->actingAs($user)->post(route('maintenances.store'), [
            'asset_id' => $asset->id,
            'issue_description' => 'شاشة لا تعمل',
            'priority' => MaintenancePriority::High->value,
            'technician_name' => 'أحمد ولد محمد',
            'status' => MaintenanceStatus::Pending->value,
            'maintenance_start_date' => now()->format('Y-m-d'),
        ])->assertRedirect(route('maintenances.index'));

        $asset->refresh();

        $this->assertSame(AssetStatus::Maintenance, $asset->status);
        $this->assertDatabaseHas('maintenances', [
            'asset_id' => $asset->id,
            'issue_description' => 'شاشة لا تعمل',
            'technician_name' => 'أحمد ولد محمد',
            'status' => MaintenanceStatus::Pending->value,
            'maintenance_end_date' => null,
        ]);
    }

    public function test_maintenance_requires_issue_description_and_asset(): void
    {
        $user = User::factory()->warehouseKeeper()->create();

        $this->actingAs($user)->post(route('maintenances.store'), [
            'priority' => MaintenancePriority::Medium->value,
            'technician_name' => 'فني',
            'status' => MaintenanceStatus::Pending->value,
            'maintenance_start_date' => now()->format('Y-m-d'),
        ])->assertSessionHasErrors(['asset_id', 'issue_description']);
    }

    public function test_cannot_open_maintenance_for_active_assigned_asset(): void
    {
        $user = User::factory()->warehouseKeeper()->create();
        $employee = Employee::factory()->create();
        $asset = Asset::factory()->warehouse()->create(['status' => AssetStatus::Active]);

        Assignment::factory()->create([
            'asset_id' => $asset->id,
            'employee_id' => $employee->id,
        ]);

        $this->actingAs($user)->post(route('maintenances.store'), [
            'asset_id' => $asset->id,
            'issue_description' => 'عطل',
            'priority' => MaintenancePriority::Medium->value,
            'technician_name' => 'فني',
            'status' => MaintenanceStatus::Pending->value,
            'maintenance_start_date' => now()->format('Y-m-d'),
        ])->assertSessionHasErrors('asset_id');

        $asset->refresh();
        $this->assertSame(AssetStatus::Active, $asset->status);
        $this->assertDatabaseCount('maintenances', 0);
    }

    public function test_cannot_open_maintenance_for_asset_already_in_maintenance(): void
    {
        $user = User::factory()->warehouseKeeper()->create();
        $asset = Asset::factory()->warehouse()->create(['status' => AssetStatus::Maintenance]);

        Maintenance::factory()->pending()->create(['asset_id' => $asset->id]);

        $this->actingAs($user)->post(route('maintenances.store'), [
            'asset_id' => $asset->id,
            'issue_description' => 'عطل آخر',
            'priority' => MaintenancePriority::Low->value,
            'technician_name' => 'فني',
            'status' => MaintenanceStatus::Pending->value,
            'maintenance_start_date' => now()->format('Y-m-d'),
        ])->assertSessionHasErrors('asset_id');

        $this->assertDatabaseCount('maintenances', 1);
    }

    public function test_completing_maintenance_returns_asset_to_warehouse(): void
    {
        $user = User::factory()->warehouseKeeper()->create();
        $asset = Asset::factory()->warehouse()->create(['status' => AssetStatus::Maintenance]);

        $maintenance = Maintenance::factory()->inProgress()->create([
            'asset_id' => $asset->id,
        ]);

        $this->actingAs($user)->post(route('maintenances.complete', $maintenance))
            ->assertRedirect(route('maintenances.index'));

        $asset->refresh();
        $maintenance->refresh();

        $this->assertSame(AssetStatus::Warehouse, $asset->status);
        $this->assertSame(MaintenanceStatus::Completed, $maintenance->status);
        $this->assertNotNull($maintenance->maintenance_end_date);
        $this->assertDatabaseHas('maintenances', [
            'id' => $maintenance->id,
            'status' => MaintenanceStatus::Completed->value,
        ]);
    }

    public function test_cancelling_maintenance_returns_asset_to_warehouse(): void
    {
        $user = User::factory()->warehouseKeeper()->create();
        $asset = Asset::factory()->warehouse()->create(['status' => AssetStatus::Maintenance]);

        $maintenance = Maintenance::factory()->pending()->create([
            'asset_id' => $asset->id,
        ]);

        $this->actingAs($user)->post(route('maintenances.cancel', $maintenance))
            ->assertRedirect(route('maintenances.index'));

        $asset->refresh();
        $maintenance->refresh();

        $this->assertSame(AssetStatus::Warehouse, $asset->status);
        $this->assertSame(MaintenanceStatus::Cancelled, $maintenance->status);
        $this->assertNotNull($maintenance->maintenance_end_date);
    }

    public function test_cannot_assign_asset_in_maintenance(): void
    {
        $user = User::factory()->warehouseKeeper()->create();
        $employee = Employee::factory()->create();
        $asset = Asset::factory()->warehouse()->create(['status' => AssetStatus::Maintenance]);

        Maintenance::factory()->pending()->create(['asset_id' => $asset->id]);

        $this->actingAs($user)->post(route('assignments.store'), [
            'asset_id' => $asset->id,
            'employee_id' => $employee->id,
            'assigned_date' => now()->format('Y-m-d'),
        ])->assertSessionHasErrors('asset_id');

        $this->assertDatabaseCount('assignments', 0);
    }

    public function test_cannot_manually_set_maintenance_status_on_asset_form(): void
    {
        $user = User::factory()->warehouseKeeper()->create();
        $asset = Asset::factory()->warehouse()->create();

        $this->actingAs($user)->put(route('assets.update', $asset), [
            'name' => $asset->name,
            'type' => $asset->type,
            'serial_number' => $asset->serial_number,
            'status' => AssetStatus::Maintenance->value,
        ])->assertSessionHasErrors('status');

        $asset->refresh();
        $this->assertSame(AssetStatus::Warehouse, $asset->status);
    }
}
