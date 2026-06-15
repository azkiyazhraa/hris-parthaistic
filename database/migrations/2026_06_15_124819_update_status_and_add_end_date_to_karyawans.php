<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            // Tambah kolom end_date
            if (!Schema::hasColumn('karyawans', 'end_date')) {
                $table->date('end_date')->nullable()->after('tanggal_bergabung');
            }

            // Tambah kolom total_hari_kerja
            if (!Schema::hasColumn('karyawans', 'total_hari_kerja')) {
                $table->integer('total_hari_kerja')->default(0)->after('end_date');
            }

            // Tambah kolom reason_resigned (alasan resign/terminate)
            if (!Schema::hasColumn('karyawans', 'reason_resigned')) {
                $table->text('reason_resigned')->nullable()->after('total_hari_kerja');
            }
        });
    }

    public function down(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            $table->dropColumn(['end_date', 'total_hari_kerja', 'reason_resigned']);
        });
    }
};
