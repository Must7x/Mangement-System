<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permissionId = DB::table('permissions')->where('slug', 'activity_log.view')->value('id');

        if (! $permissionId) {
            return;
        }

        $roleIds = DB::table('roles')->whereIn('slug', [
            'technical_admin',
            'inventory_supervisor',
            'warehouse_keeper',
        ])->pluck('id', 'slug');

        $grantRoleIds = collect(['technical_admin', 'inventory_supervisor'])
            ->map(fn (string $slug) => $roleIds->get($slug))
            ->filter();

        foreach ($grantRoleIds as $roleId) {
            DB::table('role_permission')->insertOrIgnore([
                'role_id' => $roleId,
                'permission_id' => $permissionId,
            ]);
        }

        $warehouseKeeperRoleId = $roleIds->get('warehouse_keeper');

        if ($warehouseKeeperRoleId) {
            DB::table('role_permission')
                ->where('role_id', $warehouseKeeperRoleId)
                ->where('permission_id', $permissionId)
                ->delete();
        }
    }

    public function down(): void
    {
        $permissionId = DB::table('permissions')->where('slug', 'activity_log.view')->value('id');

        if (! $permissionId) {
            return;
        }

        $technicalAdminRoleId = DB::table('roles')->where('slug', 'technical_admin')->value('id');

        if ($technicalAdminRoleId) {
            DB::table('role_permission')
                ->where('role_id', $technicalAdminRoleId)
                ->where('permission_id', $permissionId)
                ->delete();
        }

        $warehouseKeeperRoleId = DB::table('roles')->where('slug', 'warehouse_keeper')->value('id');

        if ($warehouseKeeperRoleId) {
            DB::table('role_permission')->insertOrIgnore([
                'role_id' => $warehouseKeeperRoleId,
                'permission_id' => $permissionId,
            ]);
        }
    }
};
