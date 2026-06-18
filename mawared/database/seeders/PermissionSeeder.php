<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        foreach (config('permissions.definitions') as $slug => $group) {
            DB::table('permissions')->updateOrInsert(
                ['slug' => $slug],
                ['group' => $group, 'updated_at' => $now, 'created_at' => $now]
            );
        }
    }
}
