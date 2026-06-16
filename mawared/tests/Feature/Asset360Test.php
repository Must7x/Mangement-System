<?php

namespace Tests\Feature;

use App\Enums\AssetStatus;
use App\Enums\MaintenancePriority;
use App\Enums\MaintenanceStatus;
use App\Models\Asset;
use App\Models\Assignment;
use App\Models\AssignmentHistory;
use App\Models\Employee;
use App\Models\Maintenance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Asset360Test extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_asset_360(): void
    {
        $asset = Asset::factory()->warehouse()->create();

        $this->get(route('assets.show', $asset))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_asset_360(): void
    {
        $user = User::factory()->warehouseKeeper()->create();
        $asset = Asset::factory()->warehouse()->create();

        $this->actingAs($user)->get(route('assets.show', $asset))
            ->assertOk()
            ->assertViewIs('assets.show')
            ->assertViewHas('asset', $asset);
    }

    public function test_asset_360_displays_core_details(): void
    {
        $user = User::factory()->warehouseKeeper()->create();
        $asset = Asset::factory()->warehouse()->create([
            'name' => 'HP EliteBook',
            'type' => 'لابتوب',
            'serial_number' => 'SN-360-TEST',
        ]);

        $this->actingAs($user)->get(route('assets.show', $asset))
            ->assertOk()
            ->assertSee('HP EliteBook')
            ->assertSee('لابتوب')
            ->assertSee('SN-360-TEST')
            ->assertSee(AssetStatus::Warehouse->value);
    }

    public function test_asset_360_shows_current_assignment_when_active(): void
    {
        $user = User::factory()->warehouseKeeper()->create();
        $employee = Employee::factory()->create(['name' => 'محمد ولد أحمد']);
        $employee->load('department');
        $asset = Asset::factory()->warehouse()->create(['status' => AssetStatus::Active]);

        Assignment::factory()->create([
            'asset_id' => $asset->id,
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'department' => $employee->department?->name ?? '',
            'assigned_date' => now()->toDateString(),
        ]);

        $this->actingAs($user)->get(route('assets.show', $asset))
            ->assertOk()
            ->assertSee('محمد ولد أحمد')
            ->assertSee($employee->department?->name ?? '');
    }

    public function test_asset_360_shows_assignment_history(): void
    {
        $user = User::factory()->warehouseKeeper()->create();
        $asset = Asset::factory()->warehouse()->create();

        AssignmentHistory::create([
            'asset_id' => $asset->id,
            'employee_name' => 'فاطمة بنت سيدي',
            'department_name' => 'مديرية الموارد البشرية',
            'assigned_date' => now()->subDays(30)->toDateString(),
            'returned_date' => now()->subDays(10)->toDateString(),
        ]);

        $this->actingAs($user)->get(route('assets.show', $asset))
            ->assertOk()
            ->assertSee('فاطمة بنت سيدي')
            ->assertSee('مديرية الموارد البشرية');
    }

    public function test_asset_360_shows_maintenance_history(): void
    {
        $user = User::factory()->warehouseKeeper()->create();
        $asset = Asset::factory()->warehouse()->create();

        Maintenance::factory()->completed()->create([
            'asset_id' => $asset->id,
            'issue_description' => 'لوحة مفاتيح معطلة',
            'technician_name' => 'أحمد الفني',
            'priority' => MaintenancePriority::High,
            'status' => MaintenanceStatus::Completed,
        ]);

        $this->actingAs($user)->get(route('assets.show', $asset))
            ->assertOk()
            ->assertSee('لوحة مفاتيح معطلة')
            ->assertSee('أحمد الفني');
    }

    public function test_asset_360_shows_open_maintenance_when_in_maintenance(): void
    {
        $user = User::factory()->warehouseKeeper()->create();
        $asset = Asset::factory()->warehouse()->create(['status' => AssetStatus::Maintenance]);

        Maintenance::factory()->inProgress()->create([
            'asset_id' => $asset->id,
            'issue_description' => 'شاشة سوداء',
            'technician_name' => 'يوسف ولد علي',
        ]);

        $this->actingAs($user)->get(route('assets.show', $asset))
            ->assertOk()
            ->assertSee('شاشة سوداء')
            ->assertSee('يوسف ولد علي');
    }

    public function test_asset_360_returns_404_for_missing_asset(): void
    {
        $user = User::factory()->warehouseKeeper()->create();

        $this->actingAs($user)->get(route('assets.show', 99999))
            ->assertNotFound();
    }
}
