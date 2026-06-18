<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissionIds = DB::table('permissions')->pluck('id', 'slug');
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
}
