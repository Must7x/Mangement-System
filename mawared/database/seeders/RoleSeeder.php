<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        foreach (config('permissions.system_role_names') as $slug => $meta) {
            DB::table('roles')->updateOrInsert(
                ['slug' => $slug],
                [
                    'name' => $meta['name'],
                    'description' => $meta['description'],
                    'is_system' => true,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }
}
