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

        $approved = AbsensiKaryawan::query()
            ->where('is_change_day', true)
            ->where('change_day_status', AbsensiKaryawan::CHANGE_DAY_APPROVED)
            ->where(function ($q) use ($date) {
                $q->whereDate('change_day_tanggal_akhir', $date)
                  ->orWhereDate('change_day_tanggal_awal', $date);
            })
            ->get();

        if ($approved->isEmpty()) {
            $this->info("No approved change day requests for {$date}.");
            return self::SUCCESS;
        }

        $created = 0;
        $skipped = 0;

        foreach ($approved as $record) {
            $exists = AbsensiKaryawan::where('karyawan_id', $record->karyawan_id)
                ->whereDate('tanggal', $date)
                ->where('is_change_day', false)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            // Requested Date (change_day_tanggal_akhir) = day they worked on Sunday/holiday
            if ($record->change_day_tanggal_akhir && $record->change_day_tanggal_akhir->toDateString() === $date) {
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
            // Original Date (change_day_tanggal_awal) = compensatory day off
            elseif ($record->change_day_tanggal_awal && $record->change_day_tanggal_awal->toDateString() === $date) {
                AbsensiKaryawan::create([
                    'karyawan_id'      => $record->karyawan_id,
                    'nama_karyawan'    => $record->nama_karyawan,
                    'tanggal'          => $date,
                    'is_change_day'    => false,
                    'status_kehadiran' => AbsensiKaryawan::STATUS_CHANGE_DAY,
                    'keterangan'       => 'Change Day off — will work on ' .
                        ($record->change_day_tanggal_akhir
                            ? $record->change_day_tanggal_akhir->format('d/m/Y')
                            : '-'),
                ]);
                $created++;
            }
        }

        $this->info("Processed {$date}: {$created} attendance record(s) created, {$skipped} skipped (already exist).");

        return self::SUCCESS;
    }
}
