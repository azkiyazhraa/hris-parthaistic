<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            // Tambah kolom nama_depan dan nama_belakang
            if (!Schema::hasColumn('karyawans', 'nama_depan')) {
                $table->string('nama_depan', 100)->nullable()->after('kata_sandi');
            }
            if (!Schema::hasColumn('karyawans', 'nama_belakang')) {
                $table->string('nama_belakang', 100)->nullable()->after('nama_depan');
            }

            // Ubah kolom pendidikan_terakhir menjadi enum
            if (!Schema::hasColumn('karyawans', 'pendidikan_terakhir_new')) {
                $table->enum('pendidikan_terakhir_new', [
                    'SMP', 'SMA/MA', 'SMK', 'D1', 'D2', 'D3', 'S1', 'S2'
                ])->nullable()->after('status_pernikahan');
            }

            // Tambah kolom untuk role/jabatan baru
            if (!Schema::hasColumn('karyawans', 'jabatan')) {
                $table->enum('jabatan', [
                    'Chief Executive Officer',
                    'Chief Operating Officer',
                    'Creative Writer',
                    'Finance',
                    'Business Development',
                    'Videographer',
                    'Video Editor',
                    'Social Media Manager',
                    'lainnya'
                ])->nullable()->after('role');
            }

            // Kolom untuk custom role jika memilih "lainnya"
            if (!Schema::hasColumn('karyawans', 'jabatan_lainnya')) {
                $table->string('jabatan_lainnya', 100)->nullable()->after('jabatan');
            }

            // Bank dan nomor rekening
            if (!Schema::hasColumn('karyawans', 'nama_bank')) {
                $table->string('nama_bank', 50)->default('BSI')->after('telepon_kontak_darurat');
            }
            if (!Schema::hasColumn('karyawans', 'nomor_rekening')) {
                $table->string('nomor_rekening', 30)->nullable()->after('nama_bank');
            }
        });

        // Migrasi data pendidikan_terakhir lama ke enum baru
        DB::statement("
            UPDATE karyawans SET pendidikan_terakhir_new =
            CASE
                WHEN pendidikan_terakhir = 'SMP' THEN 'SMP'
                WHEN pendidikan_terakhir = 'SMA/MA' THEN 'SMA/MA'
                WHEN pendidikan_terakhir = 'SMK' THEN 'SMK'
                WHEN pendidikan_terakhir = 'D1' THEN 'D1'
                WHEN pendidikan_terakhir = 'D2' THEN 'D2'
                WHEN pendidikan_terakhir = 'D3' THEN 'D3'
                WHEN pendidikan_terakhir = 'S1' THEN 'S1'
                WHEN pendidikan_terakhir = 'S2' THEN 'S2'
                ELSE NULL
            END
        ");
    }

    public function down(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            $table->dropColumn([
                'nama_depan',
                'nama_belakang',
                'pendidikan_terakhir_new',
                'jabatan',
                'jabatan_lainnya',
                'nama_bank',
                'nomor_rekening'
            ]);
        });
    }
};
