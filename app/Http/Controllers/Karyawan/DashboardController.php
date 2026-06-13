<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\AbsensiKaryawan;
use App\Models\PengajuanCuti;
use App\Models\Pengumuman;
use App\Models\BreakTime;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private function months()
    {
        return [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];
    }

    public function index()
    {
        $months = $this->months();
        $karyawanId = auth()->id();
        $tahunSekarang = now()->year;

        /*
        |--------------------------------------------------------------------------
        | PENGUMUMAN
        |--------------------------------------------------------------------------
        */
        $attachment = Pengumuman::where('status', true)
            ->where(function ($query) {
                $query->where('target_role', 'all')
                    ->orWhere('target_role', 'karyawan');
            })
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | ABSENSI HARI INI
        |--------------------------------------------------------------------------
        */
        $absensi = AbsensiKaryawan::where('karyawan_id', $karyawanId)
            ->whereDate('tanggal', now()->toDateString())
            ->where('is_change_day', false)
            ->first();

        /*
        |--------------------------------------------------------------------------
        | BREAK TIME (TABEL TERPISAH)
        |--------------------------------------------------------------------------
        */
        $break = BreakTime::where('karyawan_id', $karyawanId)
            ->whereDate('created_at', now()->toDateString())
            ->latest()
            ->first();

        // ✅ kondisi sedang break
        $isOnBreak = $absensi && $break && $break->break_start && !$break->break_end;

        /*
        |--------------------------------------------------------------------------
        | CUTI TAHUNAN
        |--------------------------------------------------------------------------
        */
        $kuotaCutiTahunan = 12;

        $terpakaiTahunan = PengajuanCuti::where('karyawan_id', $karyawanId)
            ->where('tahun_cuti', $tahunSekarang)
            ->where('jenis_cuti', 'tahunan')
            ->where('status', 'disetujui')
            ->sum('total_hari');

        $sisaKuotaTahunan = max(0, $kuotaCutiTahunan - $terpakaiTahunan);

        /*
        |--------------------------------------------------------------------------
        | CUTI SAKIT
        |--------------------------------------------------------------------------
        */
        $kuotaCutiSakit = 10;

        $terpakaiSakit = PengajuanCuti::where('karyawan_id', $karyawanId)
            ->where('tahun_cuti', $tahunSekarang)
            ->where('jenis_cuti', 'sakit')
            ->where('status', 'disetujui')
            ->sum('total_hari');

        $sisaKuotaSakit = max(0, $kuotaCutiSakit - $terpakaiSakit);

        /*
        |--------------------------------------------------------------------------
        | CUTI KEPENTINGAN
        |--------------------------------------------------------------------------
        */
        $kuotaCutiKepentingan = 10;

        $terpakaiKepentingan = PengajuanCuti::where('karyawan_id', $karyawanId)
            ->where('tahun_cuti', $tahunSekarang)
            ->where('jenis_cuti', 'penting')
            ->where('status', 'disetujui')
            ->sum('total_hari');

        $sisaKuotaKepentingan = max(0, $kuotaCutiKepentingan - $terpakaiKepentingan);

        /*
        |--------------------------------------------------------------------------
        | CUTI MELAHIRKAN
        |--------------------------------------------------------------------------
        */
        $kuotaCutiMelahirkan = 90;

        $terpakaiMelahirkan = PengajuanCuti::where('karyawan_id', $karyawanId)
            ->where('tahun_cuti', $tahunSekarang)
            ->where('jenis_cuti', 'melahirkan')
            ->where('status', 'disetujui')
            ->sum('total_hari');

        $sisaKuotaMelahirkan = max(0, $kuotaCutiMelahirkan - $terpakaiMelahirkan);

        /*
        |--------------------------------------------------------------------------
        | TOTAL CUTI
        |--------------------------------------------------------------------------
        */
        $totalCuti = max(0, (
            $kuotaCutiTahunan +
            $kuotaCutiSakit +
            $kuotaCutiKepentingan +
            $kuotaCutiMelahirkan
        ));

        $cutiTerpakai = 
            $terpakaiTahunan +
            $terpakaiSakit +
            $terpakaiKepentingan +
            $terpakaiMelahirkan;

        return view('karyawan.dashboard', compact(
            'attachment',
            'absensi',
            'isOnBreak',

            'sisaKuotaTahunan',
            'sisaKuotaSakit',
            'sisaKuotaKepentingan',
            'sisaKuotaMelahirkan',
            'totalCuti',
            'cutiTerpakai',

            'kuotaCutiTahunan',
            'kuotaCutiSakit',
            'kuotaCutiKepentingan',
            'kuotaCutiMelahirkan',

            'terpakaiTahunan',
            'terpakaiSakit',
            'terpakaiKepentingan',
            'terpakaiMelahirkan',

            'months'
        ));
    }

    public function filterAttendance(Request $request)
    {
        $karyawanId = auth()->id();

        $bulan = $request->bulan ?? now()->month;
        $tahun = now()->year;

        $attendance = AbsensiKaryawan::where('karyawan_id', $karyawanId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('is_change_day', false)
            ->latest('tanggal')
            ->paginate(5);

        $allAttendance = AbsensiKaryawan::where('karyawan_id', $karyawanId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('is_change_day', false)
            ->get();

        return response()->json([
            'data' => $attendance->items(),

            'pagination' => [
                'current_page' => $attendance->currentPage(),
                'last_page' => $attendance->lastPage(),
                'next_page_url' => $attendance->nextPageUrl(),
                'prev_page_url' => $attendance->previousPageUrl(),
            ],

            'summary' => [
                'present' => $allAttendance->where('status_kehadiran', 'present')->count(),
                'permission' => $allAttendance->where('status_kehadiran', 'permit')->count(),
                'sick' => $allAttendance->where('status_kehadiran', 'sick')->count(),
                'pending' => $allAttendance->where('status_kehadiran', 'pending')->count(),
            ],
        ]);
    }
     public function getStatus()
    {
        $karyawanId = auth()->id();
        
        $absensi = AbsensiKaryawan::where('karyawan_id', $karyawanId)
            ->whereDate('tanggal', now()->toDateString())
            ->where('is_change_day', false)
            ->first();
            
        if (!$absensi || $absensi->jam_pulang) {
            return response()->json([
                'absensi' => $absensi,
                'isOnBreak' => false,
                'working_hours' => '00:00:00',
                'breaks' => []
            ]);
        }
        
        // Ambil semua break hari ini
        $breaks = BreakTime::where('karyawan_id', $karyawanId)
            ->whereDate('created_at', now()->toDateString())
            ->orderBy('break_start', 'asc')
            ->get()
            ->map(function($break) {
                return [
                    'id' => $break->id,
                    'start' => $break->break_start ? $break->break_start->format('Y-m-d H:i:s') : null,
                    'end' => $break->break_end ? $break->break_end->format('Y-m-d H:i:s') : null,
                    'is_active' => $break->break_start && !$break->break_end
                ];
            });
            
        $isOnBreak = $breaks->contains('is_active', true);
        
        return response()->json([
            'absensi' => [
                'id' => $absensi->id,
                'jam_masuk' => $absensi->jam_masuk,
                'tanggal' => $absensi->tanggal->format('Y-m-d'),
                'jam_pulang' => $absensi->jam_pulang
            ],
            'isOnBreak' => $isOnBreak,
            'breaks' => $breaks,
            'server_time' => now()->format('Y-m-d H:i:s')
        ]);
    }
}