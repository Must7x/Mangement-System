<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->string('employee_name');
            $table->string('department');
            $table->date('assigned_date');
            $table->timestamps();

            $table->unique('asset_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
