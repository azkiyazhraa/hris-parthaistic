<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensi_karyawans', function (Blueprint $table) {
            if (!Schema::hasColumn('absensi_karyawans', 'attachment')) {
                $table->string('attachment')->nullable()->after('keterangan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('absensi_karyawans', function (Blueprint $table) {
            if (Schema::hasColumn('absensi_karyawans', 'attachment')) {
                $table->dropColumn('attachment');
            }
        });
    }
};