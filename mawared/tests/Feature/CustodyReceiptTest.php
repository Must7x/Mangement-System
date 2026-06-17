<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustodyReceiptTest extends TestCase
{
    use RefreshDatabase;

    public function test_operational_user_can_view_custody_receipt(): void
    {
        $user = User::factory()->warehouseKeeper()->create();
        $assignment = Assignment::factory()->create();

        $this->actingAs($user)
            ->get(route('assignments.receipt', $assignment))
            ->assertOk()
            ->assertSee(sprintf('CR-%s-%06d', $assignment->assigned_date->format('Y'), $assignment->id), false)
            ->assertSee($assignment->asset->name)
            ->assertSee($assignment->asset->serial_number)
            ->assertSee($assignment->employee_name)
            ->assertSee($user->name)
            ->assertSee('إيصال استلام عهدة')
            ->assertSee('أمين المخزن')
            ->assertSee('مشرف المخزون');
    }

    public function test_technical_admin_cannot_view_custody_receipt(): void
    {
        $user = User::factory()->technicalAdmin()->create();
        $assignment = Assignment::factory()->create();

        $this->actingAs($user)
            ->get(route('assignments.receipt', $assignment))
            ->assertRedirect($user->homeRoute())
            ->assertSessionHas('error', __('messages.errors.access_denied'));
    }
}
