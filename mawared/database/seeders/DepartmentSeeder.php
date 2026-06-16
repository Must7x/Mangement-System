<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            'مديرية البنية التحتية',
            'مديرية الموارد البشرية',
            'مديرية الشؤون المالية',
            'مديرية الاتصال والإعلام',
        ];

        foreach ($departments as $name) {
            Department::updateOrCreate(['name' => $name], ['name' => $name]);
        }
    }
}
