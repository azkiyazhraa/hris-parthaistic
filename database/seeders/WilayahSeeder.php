<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WilayahSeeder extends Seeder
{
    /**
     * Import data wilayah administrasi Indonesia (provinsi/kab-kota/kecamatan/kelurahan)
     * beserta kode pos, bersumber dari dataset cahyadsn (Kepmendagri No 300.2.2-2138 Tahun 2025).
     * https://github.com/cahyadsn/wilayah dan https://github.com/cahyadsn/wilayah_kodepos
     */
    public function run(): void
    {
        DB::unprepared($this->stripBom(file_get_contents(__DIR__ . '/data/wilayah.sql')));
        DB::unprepared($this->stripBom(file_get_contents(__DIR__ . '/data/wilayah_kodepos.sql')));

        // wilayah.sql tidak menyertakan collation eksplisit sehingga mengikuti default server,
        // yang bisa berbeda dari wilayah_kodepos (utf8mb4_unicode_ci) dan menyebabkan error
        // "Illegal mix of collations" saat kedua tabel di-JOIN.
        DB::statement('ALTER TABLE wilayah CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    }

    private function stripBom(string $sql): string
    {
        return preg_replace('/^\xEF\xBB\xBF/', '', $sql);
    }
}
