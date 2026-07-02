<?php

namespace App\Console\Commands;

use App\Models\AbsensiKaryawan;
use App\Models\PengajuanCuti;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SyncLeaveAttendance extends Command
{
    protected $signature = 'leave:sync-attendance {--date= : Date to process (Y-m-d), defaults to today}';

    protected $description = 'Create attendance records (status: leave) for approved leave requests whose date has arrived today';

    public function handle(): int
    {
        $date = $this->option('date')
            ? Carbon::parse($this->option('date'))->toDateString()
            : Carbon::today()->toDateString();

        // Find all approved leaves that cover this date
        $leaves = PengajuanCuti::where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $date)
            ->whereDate('tanggal_selesai', '>=', $date)
            ->get();

        if ($leaves->isEmpty()) {
            $this->info("No approved leaves cover {$date}.");
            return self::SUCCESS;
        }

        $created = 0;
        $skipped = 0;

        foreach ($leaves as $cuti) {
            $exists = AbsensiKaryawan::where('karyawan_id', $cuti->karyawan_id)
                ->whereDate('tanggal', $date)
                ->where('is_change_day', false)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            AbsensiKaryawan::create([
                'karyawan_id'      => $cuti->karyawan_id,
                'nama_karyawan'    => $cuti->nama_karyawan,
                'tanggal'          => $date,
                'is_change_day'    => false,
                'status_kehadiran' => AbsensiKaryawan::STATUS_LEAVE,
                'keterangan'       => $cuti->jenis_cuti_label . ' (Leave approved)',
            ]);

            $created++;
        }

        $this->info("Processed {$date}: {$created} record(s) created, {$skipped} skipped (already exist).");

        return self::SUCCESS;
    }
}
