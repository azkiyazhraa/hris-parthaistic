<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `karyawans` MODIFY `status` VARCHAR(50) DEFAULT 'Full-time'");

        DB::statement("UPDATE `karyawans` SET `status` = 'Full-time' WHERE `status` = 'Permanent' OR `status` = 'permanent'");
        DB::statement("UPDATE `karyawans` SET `status` = 'Contract' WHERE `status` = 'contract'");
        DB::statement("UPDATE `karyawans` SET `status` = 'Internship' WHERE `status` = 'Outsource' OR `status` = 'outsource'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `karyawans` MODIFY `status` VARCHAR(20) DEFAULT 'aktif'");
    }
};
