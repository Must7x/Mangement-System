<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\Assignment;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Assignment>
 */
class AssignmentFactory extends Factory
{
    protected $model = Assignment::class;

    public function definition(): array
    {
        return [
            'asset_id' => Asset::factory()->warehouse(),
            'employee_id' => Employee::factory(),
            'employee_name' => '',
            'department' => '',
            'assigned_date' => fake()->date(),
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Assignment $assignment): void {
            $this->syncEmployeeSnapshot($assignment);
        });
    }

    private function syncEmployeeSnapshot(Assignment $assignment): void
    {
        if (! $assignment->employee_id) {
            return;
        }

        $employee = Employee::with('department')->find($assignment->employee_id);

        if (! $employee) {
            return;
        }

        if ($assignment->employee_name === '') {
            $assignment->employee_name = $employee->name;
        }

        if ($assignment->department === '') {
            $assignment->department = $employee->department?->name ?? '';
        }
    }
}
