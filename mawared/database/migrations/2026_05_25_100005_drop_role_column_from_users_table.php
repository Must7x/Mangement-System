<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('warehouse_keeper')->after('email');
        });

        $roles = DB::table('roles')->pluck('slug', 'id');

        DB::table('users')
            ->whereNotNull('role_id')
            ->orderBy('id')
            ->each(function (object $user) use ($roles): void {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['role' => $roles->get($user->role_id, 'warehouse_keeper')]);
            });
    }
};
