<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (! Schema::hasColumn('employees', 'name')) {
                $table->string('name')->after('id');
            }

            if (! Schema::hasColumn('employees', 'department_id')) {
                $table->foreignId('department_id')
                    ->nullable()
                    ->after('name')
                    ->constrained('departments')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('employees', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'department_id')) {
                $table->dropConstrainedForeignId('department_id');
            }

            if (Schema::hasColumn('employees', 'deleted_at')) {
                $table->dropSoftDeletes();
            }

            if (Schema::hasColumn('employees', 'name')) {
                $table->dropColumn('name');
            }
        });
    }
};
