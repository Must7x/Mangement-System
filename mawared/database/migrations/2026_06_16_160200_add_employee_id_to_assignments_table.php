<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            if (! Schema::hasColumn('assignments', 'employee_id')) {
                $table->foreignId('employee_id')
                    ->nullable()
                    ->after('asset_id')
                    ->constrained('employees')
                    ->nullOnDelete();
            }
        });

        $now = now();

        DB::table('assignments')
            ->whereNull('employee_id')
            ->orderBy('id')
            ->get()
            ->each(function (object $assignment) use ($now): void {
                $employeeName = trim((string) ($assignment->employee_name ?? ''));
                $departmentName = trim((string) ($assignment->department ?? ''));

                if ($employeeName === '') {
                    $employeeName = 'موظف غير محدد';
                }

                if ($departmentName === '') {
                    $departmentName = 'غير محدد';
                }

                $department = DB::table('departments')
                    ->where('name', $departmentName)
                    ->first();

                $departmentId = $department?->id ?? DB::table('departments')->insertGetId([
                    'name' => $departmentName,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $employee = DB::table('employees')
                    ->where('name', $employeeName)
                    ->where('department_id', $departmentId)
                    ->first();

                $employeeId = $employee?->id ?? DB::table('employees')->insertGetId([
                    'name' => $employeeName,
                    'department_id' => $departmentId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                DB::table('assignments')
                    ->where('id', $assignment->id)
                    ->update([
                        'employee_id' => $employeeId,
                        'employee_name' => $employeeName,
                        'department' => $departmentName,
                        'updated_at' => $now,
                    ]);
            });

        DB::table('assignments')
            ->orderBy('id')
            ->get()
            ->each(function (object $assignment) use ($now): void {
                $exists = DB::table('assignment_histories')
                    ->where('asset_id', $assignment->asset_id)
                    ->where('employee_id', $assignment->employee_id)
                    ->where('assigned_date', $assignment->assigned_date)
                    ->whereNull('returned_date')
                    ->exists();

                if (! $exists) {
                    DB::table('assignment_histories')->insert([
                        'asset_id' => $assignment->asset_id,
                        'employee_id' => $assignment->employee_id,
                        'employee_name' => $assignment->employee_name,
                        'department_name' => $assignment->department,
                        'assigned_date' => $assignment->assigned_date,
                        'returned_date' => null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            if (Schema::hasColumn('assignments', 'employee_id')) {
                $table->dropConstrainedForeignId('employee_id');
            }
        });
    }
};
