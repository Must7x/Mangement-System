<?php

namespace Database\Factories;

use App\Enums\MaintenancePriority;
use App\Enums\MaintenanceStatus;
use App\Models\Asset;
use App\Models\Maintenance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Maintenance>
 */
class MaintenanceFactory extends Factory
{
    protected $model = Maintenance::class;

    public function definition(): array
    {
        return [
            'asset_id' => Asset::factory()->warehouse(),
            'issue_description' => fake()->sentence(),
            'priority' => MaintenancePriority::Medium,
            'technician_name' => fake()->name(),
            'status' => MaintenanceStatus::Pending,
            'maintenance_start_date' => fake()->date(),
            'maintenance_end_date' => null,
            'notes' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => MaintenanceStatus::Pending,
            'maintenance_end_date' => null,
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn () => [
            'status' => MaintenanceStatus::InProgress,
            'maintenance_end_date' => null,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => MaintenanceStatus::Completed,
            'maintenance_end_date' => now()->toDateString(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => [
            'status' => MaintenanceStatus::Cancelled,
            'maintenance_end_date' => now()->toDateString(),
        ]);
    }
}
