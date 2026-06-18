<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            RolePermissionSeeder::class,
            UserSeeder::class,
            DepartmentSeeder::class,
            EmployeeSeeder::class,
            AssetSeeder::class,
        ]);
    }
}
