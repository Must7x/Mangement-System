<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@mtnima.gov.mr'],
            [
                'name' => 'المسؤول التقني',
                'password' => Hash::make('password'),
                'role' => UserRole::TechnicalAdmin,
            ]
        );

        User::updateOrCreate(
            ['email' => 'storekeeper@mtnima.gov.mr'],
            [
                'name' => 'أمين المخزن',
                'password' => Hash::make('password'),
                'role' => UserRole::WarehouseKeeper,
            ]
        );

        User::updateOrCreate(
            ['email' => 'supervisor@mtnima.gov.mr'],
            [
                'name' => 'مشرف المخزون',
                'password' => Hash::make('password'),
                'role' => UserRole::InventorySupervisor,
            ]
        );
    }
}
