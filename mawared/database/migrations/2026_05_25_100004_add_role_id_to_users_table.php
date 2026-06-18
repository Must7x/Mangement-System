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
            $table->foreignId('role_id')->nullable()->after('email')->constrained()->nullOnDelete();
        });

        if (Schema::hasColumn('users', 'role')) {
            $roles = DB::table('roles')->pluck('id', 'slug');

            DB::table('users')
                ->whereNotNull('role')
                ->orderBy('id')
                ->each(function (object $user) use ($roles): void {
                    $slug = $user->role;
                    if ($roles->has($slug)) {
                        DB::table('users')
                            ->where('id', $user->id)
                            ->update(['role_id' => $roles->get($slug)]);
                    }
                });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
        });
    }
};
