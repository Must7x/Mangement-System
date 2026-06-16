<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'department_id' => Department::factory(),
        ];
    }

    public function forDepartment(Department $department): static
    {
        return $this->state(fn () => ['department_id' => $department->id]);
    }
}
