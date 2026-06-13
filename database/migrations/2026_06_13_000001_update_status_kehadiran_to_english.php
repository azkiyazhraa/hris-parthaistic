<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // hadir & masuk → present
        DB::table('absensi_karyawans')
            ->whereIn('status_kehadiran', ['hadir', 'masuk'])
            ->update(['status_kehadiran' => 'present']);

        // izin → permit
        DB::table('absensi_karyawans')
            ->where('status_kehadiran', 'izin')
            ->update(['status_kehadiran' => 'permit']);

        // sakit → sick
        DB::table('absensi_karyawans')
            ->where('status_kehadiran', 'sakit')
            ->update(['status_kehadiran' => 'sick']);

        // alpha → absent
        DB::table('absensi_karyawans')
            ->where('status_kehadiran', 'alpha')
            ->update(['status_kehadiran' => 'absent']);
    }

    public function down(): void
    {
        DB::table('absensi_karyawans')
            ->where('status_kehadiran', 'present')
            ->update(['status_kehadiran' => 'hadir']);

        DB::table('absensi_karyawans')
            ->where('status_kehadiran', 'permit')
            ->update(['status_kehadiran' => 'izin']);

        DB::table('absensi_karyawans')
            ->where('status_kehadiran', 'sick')
            ->update(['status_kehadiran' => 'sakit']);

        DB::table('absensi_karyawans')
            ->where('status_kehadiran', 'absent')
            ->update(['status_kehadiran' => 'alpha']);
    }
};
