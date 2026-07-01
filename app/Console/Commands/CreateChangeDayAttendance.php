<?php

namespace App\Console\Commands;

use App\Models\AbsensiKaryawan;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateChangeDayAttendance extends Command
{
    protected $signature = 'changeday:create-attendance {--date= : Date to process (Y-m-d), defaults to today}';

    protected $description = 'Create pending attendance records for approved change day requests whose requested date has arrived';

    public function handle(): int
    {
        $date = $this->option('date')
            ? Carbon::parse($this->option('date'))->toDateString()
            : Carbon::today()->toDateString();

        $records = AbsensiKaryawan::query()
            ->where('is_change_day', true)
            ->where('change_day_status', AbsensiKaryawan::CHANGE_DAY_APPROVED)
            ->whereDate('change_day_tanggal_akhir', $date)
            ->get();

        if ($records->isEmpty()) {
            $this->info("No approved change day requests with requested_date = {$date}.");
            return self::SUCCESS;
        }

        $created = 0;
        $skipped = 0;

        foreach ($records as $record) {
            $exists = AbsensiKaryawan::where('karyawan_id', $record->karyawan_id)
                ->where('tanggal', $date)
                ->where('is_change_day', false)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            AbsensiKaryawan::create([
                'karyawan_id'      => $record->karyawan_id,
                'nama_karyawan'    => $record->nama_karyawan,
                'tanggal'          => $date,
                'is_change_day'    => false,
                'status_kehadiran' => AbsensiKaryawan::STATUS_PENDING,
                'keterangan'       => 'Change Day — working on holiday/Sunday in exchange for ' .
                    ($record->change_day_tanggal_awal
                        ? $record->change_day_tanggal_awal->format('d/m/Y')
                        : '-'),
            ]);

            $created++;
        }

        $this->info("Processed {$date}: {$created} attendance record(s) created, {$skipped} skipped (already exist).");

        return self::SUCCESS;
    }
}
