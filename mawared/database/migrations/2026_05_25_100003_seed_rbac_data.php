<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        foreach (config('permissions.definitions') as $slug => $group) {
            DB::table('permissions')->insertOrIgnore([
                'slug' => $slug,
                'group' => $group,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $permissionIds = DB::table('permissions')->pluck('id', 'slug');

        foreach (config('permissions.system_role_names') as $slug => $meta) {
            DB::table('roles')->insertOrIgnore([
                'name' => $meta['name'],
                'slug' => $slug,
                'description' => $meta['description'],
                'is_system' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $roleIds = DB::table('roles')->pluck('id', 'slug');

        foreach (config('permissions.system_roles') as $roleSlug => $permissionSlugs) {
            $roleId = $roleIds->get($roleSlug);
            if (! $roleId) {
                continue;
            }

            foreach ($permissionSlugs as $permissionSlug) {
                $permissionId = $permissionIds->get($permissionSlug);
                if (! $permissionId) {
                    continue;
                }

                DB::table('role_permission')->insertOrIgnore([
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('role_permission')->delete();
        DB::table('roles')->where('is_system', true)->delete();
        DB::table('permissions')->delete();
    }
};
