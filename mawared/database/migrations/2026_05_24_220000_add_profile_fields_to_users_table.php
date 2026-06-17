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
            $table->string('first_name')->default('')->after('name');
            $table->string('last_name')->default('')->after('first_name');
            $table->string('phone')->nullable()->after('last_name');
            $table->string('job_title')->nullable()->after('phone');
            $table->string('employee_number')->nullable()->unique()->after('job_title');
        });

        foreach (DB::table('users')->get(['id', 'name']) as $user) {
            $parts = preg_split('/\s+/u', trim($user->name), 2) ?: [];
            DB::table('users')->where('id', $user->id)->update([
                'first_name' => $parts[0] ?? $user->name,
                'last_name' => $parts[1] ?? '',
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name', 'phone', 'job_title', 'employee_number']);
        });
    }
};
