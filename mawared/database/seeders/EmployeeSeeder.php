<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            ['name' => 'محمد ولد أحمد', 'department' => 'مديرية البنية التحتية'],
            ['name' => 'فاطمة بنت سيدي', 'department' => 'مديرية الموارد البشرية'],
            ['name' => 'عبد الله ولد محمد', 'department' => 'مديرية الشؤون المالية'],
        ];

        foreach ($samples as $sample) {
            $department = Department::where('name', $sample['department'])->first();

            if (! $department) {
                continue;
            }

            Employee::updateOrCreate(
                ['name' => $sample['name'], 'department_id' => $department->id],
                ['name' => $sample['name'], 'department_id' => $department->id]
            );
        }
    }
}
