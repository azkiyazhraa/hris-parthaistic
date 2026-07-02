<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\AbsensiKaryawan;
use App\Models\Performa;
use App\Models\PengajuanCuti;
use App\Models\Pengumuman;
use App\Models\BreakTime;
use App\Services\TrackerApiService;
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

        $karyawan = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | KUOTA CUTI
        |--------------------------------------------------------------------------
        */
        $used = fn(string $jenis) => (int) PengajuanCuti::where('karyawan_id', $karyawanId)
            ->where('tahun_cuti', $tahunSekarang)
            ->where('jenis_cuti', $jenis)
            ->where('status', 'disetujui')
            ->sum('total_hari');

        $kuotaCutiTahunan    = 12;
        $kuotaCutiMelahirkan = $karyawan->jenis_kelamin === 'P' ? 90 : 3;
        $kuotaCutiMenikah    = 3;
        $kuotaCutiDuka       = 2;

        $terpakaiTahunan    = $used('tahunan');
        $terpakaiMelahirkan = $used('melahirkan');
        $terpakaiMenikah    = $used('menikah');
        $terpakaiDuka       = $used('duka');

        $sisaKuotaTahunan    = max(0, $kuotaCutiTahunan    - $terpakaiTahunan);
        $sisaKuotaMelahirkan = max(0, $kuotaCutiMelahirkan - $terpakaiMelahirkan);
        $sisaKuotaMenikah    = max(0, $kuotaCutiMenikah    - $terpakaiMenikah);
        $sisaKuotaDuka       = max(0, $kuotaCutiDuka       - $terpakaiDuka);

        $maternityLabel = $karyawan->jenis_kelamin === 'P' ? 'Maternity Leave' : 'Paternity Leave';

        $totalCutiKuota = $kuotaCutiTahunan + $kuotaCutiMelahirkan + $kuotaCutiMenikah + $kuotaCutiDuka;
        $cutiTerpakai   = $terpakaiTahunan + $terpakaiMelahirkan + $terpakaiMenikah + $terpakaiDuka;

        /*
        |--------------------------------------------------------------------------
        | TASK RECORD (PHASE 1 — dari tabel performas)
        |--------------------------------------------------------------------------
        */
        $latestPerforma = Performa::where('karyawan_id', $karyawanId)
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->first();

        $taskDone      = (int) ($latestPerforma?->task_done   ?? 0);
        $taskTarget    = (int) ($latestPerforma?->task_target ?? 0);
        $taskRemaining = max(0, $taskTarget - $taskDone);
        $taskPercent   = $taskTarget > 0 ? round(($taskDone / $taskTarget) * 100) : 0;
        $taskPeriod    = $latestPerforma
            ? ($months[$latestPerforma->bulan] . ' ' . $latestPerforma->tahun)
            : null;

        // Tracker stats (this employee, all-time)
        $trackerEmail = $karyawan->tracker_email ?: $karyawan->email;
        $trackerStat  = $trackerEmail
            ? (new TrackerApiService())->getSingleStatByEmail($trackerEmail)
            : ['connected' => false, 'total_task' => 0, 'task_completed' => 0];

        $trackerConnected     = $trackerStat['connected'];
        $trackerTaskTotal     = $trackerStat['total_task'];
        $trackerTaskCompleted = $trackerStat['task_completed'];

        return view('karyawan.dashboard', compact(
            'attachment',
            'absensi',
            'isOnBreak',

            'kuotaCutiTahunan',
            'kuotaCutiMelahirkan',
            'kuotaCutiMenikah',
            'kuotaCutiDuka',

            'terpakaiTahunan',
            'terpakaiMelahirkan',
            'terpakaiMenikah',
            'terpakaiDuka',

            'sisaKuotaTahunan',
            'sisaKuotaMelahirkan',
            'sisaKuotaMenikah',
            'sisaKuotaDuka',

            'maternityLabel',
            'totalCutiKuota',
            'cutiTerpakai',

            'months',

            'taskDone',
            'taskTarget',
            'taskRemaining',
            'taskPercent',
            'taskPeriod',

            'trackerConnected',
            'trackerTaskTotal',
            'trackerTaskCompleted',
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
                'present'    => $allAttendance->where('status_kehadiran', 'present')->count(),
                'change_day' => $allAttendance->where('status_kehadiran', 'change_day')->count(),
                'leave'      => $allAttendance->where('status_kehadiran', 'leave')->count(),
                'pending'    => $allAttendance->where('status_kehadiran', 'pending')->count(),
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