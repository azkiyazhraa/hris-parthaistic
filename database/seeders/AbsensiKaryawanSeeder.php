<?php

namespace Database\Seeders;

use App\Models\AbsensiKaryawan;
use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AbsensiKaryawanSeeder extends Seeder
{
    /**
     * Working days June 1–13, 2026 (Mon–Sat).
     *
     * Pattern per employee:
     *   EMP0001 Azkiya       — 12/12 present (hadir terus, selalu on-time)
     *   EMP0002 Rizky        — 10/12 present, absent June 4 & 11, 1× late
     *   EMP0003 Ani          — 9/12 present, sick June 2, permit June 6, absent June 10, 1× late
     *   EMP0004 Dimas        —  9/12 present, absent June 3, 9, 13; 2× late
     *   EMP0005 Sari         —  8/12 present, absent June 5, 8, 12, 13; 1× late
     *   HR001  Dewi          — 11/12 present, absent June 13 only
     */
    public function run(): void
    {
        // [date => [status, jam_masuk|null, jam_pulang|null, keterangan|null]]
        $schedules = [
            'EMP0001' => [
                '2026-06-01' => ['present', '07:50', '17:05', null],
                '2026-06-02' => ['present', '07:45', '17:00', null],
                '2026-06-03' => ['present', '07:55', '17:10', null],
                '2026-06-04' => ['present', '07:48', '17:00', null],
                '2026-06-05' => ['present', '07:52', '17:15', null],
                '2026-06-06' => ['present', '07:50', '13:05', null],
                '2026-06-08' => ['present', '07:45', '17:00', null],
                '2026-06-09' => ['present', '07:50', '17:00', null],
                '2026-06-10' => ['present', '07:55', '17:05', null],
                '2026-06-11' => ['present', '07:48', '17:00', null],
                '2026-06-12' => ['present', '07:52', '17:10', null],
                '2026-06-13' => ['present', '07:50', '13:00', null],
            ],

            'EMP0002' => [
                '2026-06-01' => ['present', '08:05', '17:00', null],
                '2026-06-02' => ['present', '07:55', '17:00', null],
                '2026-06-03' => ['present', '08:10', '17:15', null],
                '2026-06-04' => ['absent',  null,    null,    'Tidak hadir tanpa keterangan'],
                '2026-06-05' => ['present', '07:58', '17:00', null],
                '2026-06-06' => ['present', '08:00', '13:00', null],
                '2026-06-08' => ['present', '08:25', '17:00', null], // late
                '2026-06-09' => ['present', '07:55', '17:05', null],
                '2026-06-10' => ['present', '08:00', '17:00', null],
                '2026-06-11' => ['absent',  null,    null,    'Tidak hadir tanpa keterangan'],
                '2026-06-12' => ['present', '07:58', '17:00', null],
                '2026-06-13' => ['present', '08:05', '13:00', null],
            ],

            'EMP0003' => [
                '2026-06-01' => ['present', '07:50', '17:00', null],
                '2026-06-02' => ['sick',    null,    null,    'Sakit demam'],
                '2026-06-03' => ['present', '08:18', '17:00', null], // late
                '2026-06-04' => ['present', '07:55', '17:00', null],
                '2026-06-05' => ['present', '08:00', '17:05', null],
                '2026-06-06' => ['permit',  null,    null,    'Keperluan keluarga'],
                '2026-06-08' => ['present', '07:58', '17:00', null],
                '2026-06-09' => ['present', '08:05', '17:10', null],
                '2026-06-10' => ['absent',  null,    null,    'Tidak hadir tanpa keterangan'],
                '2026-06-11' => ['present', '07:52', '17:00', null],
                '2026-06-12' => ['present', '08:00', '17:00', null],
                '2026-06-13' => ['present', '07:55', '13:00', null],
            ],

            'EMP0004' => [
                '2026-06-01' => ['present', '08:12', '17:00', null], // late
                '2026-06-02' => ['present', '07:58', '17:00', null],
                '2026-06-03' => ['absent',  null,    null,    'Tidak hadir tanpa keterangan'],
                '2026-06-04' => ['present', '08:00', '17:05', null],
                '2026-06-05' => ['present', '08:35', '17:00', null], // late
                '2026-06-06' => ['present', '07:55', '13:00', null],
                '2026-06-08' => ['present', '08:00', '17:00', null],
                '2026-06-09' => ['absent',  null,    null,    'Tidak hadir tanpa keterangan'],
                '2026-06-10' => ['present', '07:50', '17:00', null],
                '2026-06-11' => ['present', '08:05', '17:10', null],
                '2026-06-12' => ['present', '08:00', '17:00', null],
                '2026-06-13' => ['absent',  null,    null,    'Tidak hadir tanpa keterangan'],
            ],

            'EMP0005' => [
                '2026-06-01' => ['present', '07:55', '17:00', null],
                '2026-06-02' => ['present', '08:00', '17:00', null],
                '2026-06-03' => ['present', '08:50', '17:00', null], // late
                '2026-06-04' => ['present', '07:58', '17:05', null],
                '2026-06-05' => ['absent',  null,    null,    'Tidak hadir tanpa keterangan'],
                '2026-06-06' => ['present', '07:58', '13:00', null],
                '2026-06-08' => ['absent',  null,    null,    'Tidak hadir tanpa keterangan'],
                '2026-06-09' => ['present', '07:55', '17:00', null],
                '2026-06-10' => ['present', '08:10', '17:00', null],
                '2026-06-11' => ['present', '08:00', '17:10', null],
                '2026-06-12' => ['absent',  null,    null,    'Tidak hadir tanpa keterangan'],
                '2026-06-13' => ['absent',  null,    null,    'Tidak hadir tanpa keterangan'],
            ],

            'HR001' => [
                '2026-06-01' => ['present', '07:55', '17:00', null],
                '2026-06-02' => ['present', '07:50', '17:00', null],
                '2026-06-03' => ['present', '08:00', '17:05', null],
                '2026-06-04' => ['present', '07:52', '17:00', null],
                '2026-06-05' => ['present', '07:58', '17:10', null],
                '2026-06-06' => ['present', '07:55', '13:00', null],
                '2026-06-08' => ['present', '07:48', '17:00', null],
                '2026-06-09' => ['present', '08:00', '17:00', null],
                '2026-06-10' => ['present', '07:55', '17:05', null],
                '2026-06-11' => ['present', '07:50', '17:00', null],
                '2026-06-12' => ['present', '08:00', '17:00', null],
                '2026-06-13' => ['absent',  null,    null,    'Tidak hadir tanpa keterangan'],
            ],
        ];

        foreach ($schedules as $nip => $days) {
            $karyawan = Karyawan::where('nip', $nip)->first();
            if (! $karyawan) {
                $this->command->warn("Karyawan NIP {$nip} not found, skipping.");
                continue;
            }

            // Hapus data absensi Juni 2026 yang sudah ada agar tidak duplikat
            AbsensiKaryawan::where('karyawan_id', $karyawan->id)
                ->whereYear('tanggal', 2026)
                ->whereMonth('tanggal', 6)
                ->delete();

            foreach ($days as $date => [$status, $jamMasuk, $jamPulang, $keterangan]) {
                $totalJamKerja = null;

                if ($jamMasuk && $jamPulang) {
                    $masuk  = Carbon::parse("{$date} {$jamMasuk}");
                    $pulang = Carbon::parse("{$date} {$jamPulang}");
                    $totalJamKerja = round($masuk->diffInMinutes($pulang) / 60, 2);
                }

                AbsensiKaryawan::create([
                    'karyawan_id'      => $karyawan->id,
                    'nama_karyawan'    => $karyawan->nama_lengkap,
                    'tanggal'          => $date,
                    'jam_masuk'        => $jamMasuk ? $jamMasuk . ':00' : null,
                    'jam_pulang'       => $jamPulang ? $jamPulang . ':00' : null,
                    'total_jam_kerja'  => $totalJamKerja,
                    'status_kehadiran' => $status,
                    'keterangan'       => $keterangan,
                    'is_change_day'    => false,
                ]);
            }

            $presentCount = collect($days)->filter(fn($d) => $d[0] === 'present')->count();
            $absentCount  = collect($days)->filter(fn($d) => $d[0] === 'absent')->count();
            $sickCount    = collect($days)->filter(fn($d) => $d[0] === 'sick')->count();
            $permitCount  = collect($days)->filter(fn($d) => $d[0] === 'permit')->count();

            $this->command->info(
                "{$karyawan->nama_lengkap} ({$nip}): " .
                "{$presentCount}× present | {$absentCount}× absent | {$sickCount}× sick | {$permitCount}× permit"
            );
        }
    }
}
