<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('break_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('absensi_id')->constrained('absensi_karyawans')->cascadeOnDelete();
            $table->foreignId('karyawan_id')->constrained('karyawans')->cascadeOnDelete();
            $table->dateTime('break_start')->nullable();
            $table->dateTime('break_end')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('break_times');
    }
};