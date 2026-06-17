<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@mtnima.gov.mr'],
            [
                'first_name' => 'محمد',
                'last_name' => 'ولد الشيخ',
                'phone' => '+222 45 00 00 01',
                'job_title' => 'المسؤول التقني',
                'employee_number' => 'USR-001',
                'password' => 'password',
                'role' => UserRole::TechnicalAdmin,
            ]
        );

        User::updateOrCreate(
            ['email' => 'storekeeper@mtnima.gov.mr'],
            [
                'first_name' => 'أحمد',
                'last_name' => 'ولد سيدي',
                'phone' => '+222 45 00 00 02',
                'job_title' => 'أمين المخزن',
                'employee_number' => 'USR-002',
                'password' => 'password',
                'role' => UserRole::WarehouseKeeper,
            ]
        );

        User::updateOrCreate(
            ['email' => 'supervisor@mtnima.gov.mr'],
            [
                'first_name' => 'فاطمة',
                'last_name' => 'منت أحمد',
                'phone' => '+222 45 00 00 03',
                'job_title' => 'مشرف المخزون',
                'employee_number' => 'USR-003',
                'password' => 'password',
                'role' => UserRole::InventorySupervisor,
            ]
        );
    }
}
