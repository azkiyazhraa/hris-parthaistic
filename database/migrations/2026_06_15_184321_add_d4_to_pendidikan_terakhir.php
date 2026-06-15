<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah kolom pendidikan_terakhir_new menjadi VARCHAR agar fleksibel
        DB::statement("ALTER TABLE `karyawans` MODIFY `pendidikan_terakhir_new` VARCHAR(20) DEFAULT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `karyawans` MODIFY `pendidikan_terakhir_new` ENUM('SMP', 'SMA/MA', 'SMK', 'D1', 'D2', 'D3', 'S1', 'S2') DEFAULT NULL");
    }
};
