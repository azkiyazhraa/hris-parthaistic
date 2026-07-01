<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('absensi_karyawans')
            ->where('status_kehadiran', 'sick')
            ->update(['status_kehadiran' => 'leave']);

        DB::table('absensi_karyawans')
            ->where('status_kehadiran', 'permit')
            ->update(['status_kehadiran' => 'change_day']);
    }

    public function down(): void
    {
        DB::table('absensi_karyawans')
            ->where('status_kehadiran', 'leave')
            ->update(['status_kehadiran' => 'sick']);

        DB::table('absensi_karyawans')
            ->where('status_kehadiran', 'change_day')
            ->update(['status_kehadiran' => 'permit']);
    }
};
