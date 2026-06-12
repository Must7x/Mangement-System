<?php

namespace Tests\Feature;

use App\Enums\AssetStatus;
use App\Enums\UserRole;
use App\Models\Asset;
use App\Models\Assignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignmentStateTest extends TestCase
{
    use RefreshDatabase;

    public function test_assigning_asset_sets_status_to_active(): void
    {
        $user = User::factory()->create(['role' => UserRole::WarehouseKeeper]);
        $asset = Asset::create([
            'name' => 'Test Laptop',
            'type' => 'لابتوب',
            'serial_number' => 'TEST-SN-001',
            'status' => AssetStatus::Warehouse,
        ]);

        $this->actingAs($user)->post(route('assignments.store'), [
            'asset_id' => $asset->id,
            'employee_name' => 'موظف تجريبي',
            'department' => 'مديرية الاختبار',
            'assigned_date' => now()->format('Y-m-d'),
        ])->assertRedirect(route('assignments.index'));

        $asset->refresh();
        $this->assertSame(AssetStatus::Active, $asset->status);
        $this->assertDatabaseHas('assignments', [
            'asset_id' => $asset->id,
            'employee_name' => 'موظف تجريبي',
        ]);
    }

    public function test_withdrawing_assignment_returns_asset_to_warehouse(): void
    {
        $user = User::factory()->create(['role' => UserRole::WarehouseKeeper]);
        $asset = Asset::create([
            'name' => 'Test Printer',
            'type' => 'طابعة',
            'serial_number' => 'TEST-SN-002',
            'status' => AssetStatus::Warehouse,
        ]);

        $assignment = Assignment::create([
            'asset_id' => $asset->id,
            'employee_name' => 'موظف',
            'department' => 'قسم',
            'assigned_date' => now(),
        ]);
        $asset->update(['status' => AssetStatus::Active]);

        $this->actingAs($user)->delete(route('assignments.destroy', $assignment))
            ->assertRedirect(route('assignments.index'));

        $asset->refresh();
        $this->assertSame(AssetStatus::Warehouse, $asset->status);
        $this->assertDatabaseMissing('assignments', ['id' => $assignment->id]);
    }

    public function test_cannot_set_active_status_manually(): void
    {
        $user = User::factory()->create(['role' => UserRole::TechnicalAdmin]);
        $asset = Asset::create([
            'name' => 'Device',
            'type' => 'لابتوب',
            'serial_number' => 'TEST-SN-003',
            'status' => AssetStatus::Warehouse,
        ]);

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
