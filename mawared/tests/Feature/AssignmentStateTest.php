<?php

namespace Tests\Feature;

use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\Assignment;
use App\Models\AssignmentHistory;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignmentStateTest extends TestCase
{
    use RefreshDatabase;

    public function test_assigning_asset_sets_status_to_active(): void
    {
        $user = User::factory()->warehouseKeeper()->create();
        $employee = Employee::factory()->create();
        $asset = Asset::factory()->warehouse()->create();

        $this->actingAs($user)->post(route('assignments.store'), [
            'asset_id' => $asset->id,
            'employee_id' => $employee->id,
            'assigned_date' => now()->format('Y-m-d'),
        ])->assertRedirect(route('assignments.index'));

        $asset->refresh();
        $employee->load('department');

        $this->assertSame(AssetStatus::Active, $asset->status);
        $this->assertDatabaseHas('assignments', [
            'asset_id' => $asset->id,
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'department' => $employee->department?->name ?? '',
        ]);
        $this->assertDatabaseHas('assignment_histories', [
            'asset_id' => $asset->id,
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'department_name' => $employee->department?->name,
            'returned_date' => null,
        ]);
    }

    public function test_assignment_requires_employee_id(): void
    {
        $user = User::factory()->warehouseKeeper()->create();
        $asset = Asset::factory()->warehouse()->create();

        $this->actingAs($user)->post(route('assignments.store'), [
            'asset_id' => $asset->id,
            'assigned_date' => now()->format('Y-m-d'),
        ])->assertSessionHasErrors('employee_id');

        $asset->refresh();
        $this->assertSame(AssetStatus::Warehouse, $asset->status);
        $this->assertDatabaseCount('assignments', 0);
    }

    public function test_withdrawing_assignment_returns_asset_to_warehouse(): void
    {
        $user = User::factory()->warehouseKeeper()->create();
        $employee = Employee::factory()->create();
        $employee->load('department');
        $assignedDate = now()->toDateString();
        $asset = Asset::factory()->warehouse()->create(['status' => AssetStatus::Active]);

        $assignment = Assignment::factory()->create([
            'asset_id' => $asset->id,
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'department' => $employee->department?->name ?? '',
            'assigned_date' => $assignedDate,
        ]);

        AssignmentHistory::create([
            'asset_id' => $asset->id,
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'department_name' => $employee->department?->name,
            'assigned_date' => $assignedDate,
        ]);

        $this->actingAs($user)->delete(route('assignments.destroy', $assignment))
            ->assertRedirect(route('assignments.index'));

        $asset->refresh();

        $this->assertSame(AssetStatus::Warehouse, $asset->status);
        $this->assertDatabaseMissing('assignments', ['id' => $assignment->id]);
        $this->assertDatabaseHas('assignment_histories', [
            'asset_id' => $asset->id,
            'employee_id' => $employee->id,
        ]);
        $this->assertNotNull(
            AssignmentHistory::where('asset_id', $asset->id)
                ->where('employee_id', $employee->id)
                ->value('returned_date')
        );
    }

    public function test_cannot_set_active_status_manually(): void
    {
        $user = User::factory()->technicalAdmin()->create();
        $asset = Asset::factory()->warehouse()->create();

        $this->actingAs($user)->put(route('assets.update', $asset), [
            'name' => $asset->name,
            'type' => $asset->type,
            'serial_number' => $asset->serial_number,
            'status' => AssetStatus::Active->value,
        ])->assertSessionHasErrors('status');

        $asset->refresh();
        $this->assertSame(AssetStatus::Warehouse, $asset->status);
    }
}
