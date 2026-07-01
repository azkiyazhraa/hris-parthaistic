<?php

namespace Database\Seeders;

use App\Models\AbsensiKaryawan;
use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * Seed attendance records for a given date (defaults to today).
 *
 * Purpose: test the "admin/HR can only edit attendance starting tomorrow" rule.
 * Today's rows appear locked; past rows are editable via AJAX.
 *
 * Run for today:      php artisan db:seed --class=TodayAbsensiSeeder
 * Run for a date:     $env:SEED_DATE="2026-06-29"; php artisan db:seed --class=TodayAbsensiSeeder
 */
class TodayAbsensiSeeder extends Seeder
{
    public function run(): void
    {
        $dateArg = env('SEED_DATE');
        $today   = $dateArg ? Carbon::parse($dateArg)->toDateString() : Carbon::today()->toDateString();

        // All records start as pending — HR sets the final status the next day
        $timeCycle = [
            ['07:50', '17:05'],
            ['08:05', '17:00'],
            ['07:45', '17:10'],
            ['08:00', '17:05'],
            ['07:55', '17:00'],
            ['08:10', '17:15'],
            ['07:48', '17:00'],
            ['08:02', '17:00'],
            ['07:58', '17:05'],
            ['08:00', '17:00'],
        ];

        // All active employees (skip Resigned)
        $employees = Karyawan::where('status', '!=', 'Resigned')
            ->orderBy('id')
            ->get();

        if ($employees->isEmpty()) {
            $this->command->warn('No active employees found. Run DatabaseSeeder first.');
            return;
        }

        $inserted = 0;
        $skipped  = 0;

        foreach ($employees as $index => $karyawan) {
            $exists = AbsensiKaryawan::where('karyawan_id', $karyawan->id)
                ->where('tanggal', $today)
                ->exists();

            if ($exists) {
                $this->command->warn("  Skip {$karyawan->nama_lengkap} — record already exists for {$today}.");
                $skipped++;
                continue;
            }

            $cycle = $index % count($timeCycle);
            [$jamMasuk, $jamPulang] = $timeCycle[$cycle];

            $masuk  = Carbon::parse("{$today} {$jamMasuk}");
            $pulang = Carbon::parse("{$today} {$jamPulang}");
            $totalJamKerja = round($masuk->diffInMinutes($pulang) / 60, 2);

            AbsensiKaryawan::create([
                'karyawan_id'      => $karyawan->id,
                'nama_karyawan'    => $karyawan->nama_lengkap,
                'tanggal'          => $today,
                'jam_masuk'        => $jamMasuk . ':00',
                'jam_pulang'       => $jamPulang . ':00',
                'total_jam_kerja'  => $totalJamKerja,
                'status_kehadiran' => 'pending',
                'keterangan'       => null,
                'is_change_day'    => false,
            ]);

            $this->command->info(
                sprintf("  ✓ %-25s (%s) → pending", $karyawan->nama_lengkap, $karyawan->role)
            );

            $inserted++;
        }

        $this->command->newLine();
        $this->command->info("Done. {$inserted} records inserted, {$skipped} skipped for {$today}.");
        $isToday = $today === Carbon::today()->toDateString();
        $this->command->line($isToday
            ? "Filter by today in admin/absensi → locked read-only badges."
            : "Filter by {$today} in admin/absensi → editable dropdowns (AJAX, no reload)."
        );
    }
}
