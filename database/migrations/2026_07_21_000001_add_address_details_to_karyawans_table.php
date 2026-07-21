<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            if (!Schema::hasColumn('karyawans', 'rt')) {
                $table->string('rt', 5)->nullable()->after('alamat');
            }
            if (!Schema::hasColumn('karyawans', 'rw')) {
                $table->string('rw', 5)->nullable()->after('rt');
            }
            if (!Schema::hasColumn('karyawans', 'kelurahan')) {
                $table->string('kelurahan', 100)->nullable()->after('rw');
            }
            if (!Schema::hasColumn('karyawans', 'kecamatan')) {
                $table->string('kecamatan', 100)->nullable()->after('kelurahan');
            }
            if (!Schema::hasColumn('karyawans', 'kota')) {
                $table->string('kota', 100)->nullable()->after('kecamatan');
            }
            if (!Schema::hasColumn('karyawans', 'provinsi')) {
                $table->string('provinsi', 100)->nullable()->after('kota');
            }
            if (!Schema::hasColumn('karyawans', 'kode_pos')) {
                $table->string('kode_pos', 10)->nullable()->after('provinsi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            $table->dropColumn(['rt', 'rw', 'kelurahan', 'kecamatan', 'kota', 'provinsi', 'kode_pos']);
        });
    }
};
