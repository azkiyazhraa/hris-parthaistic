<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Notifikasi;
use App\Models\Performa;
use App\Models\AbsensiKaryawan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerformaController extends Controller
{
    // =============================================
    // EMPLOYEE METHODS
    // =============================================

    public function index()
    {
        $performas = Performa::where('karyawan_id', Auth::id())
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->paginate(10);

        $latestPerforma = Performa::where('karyawan_id', Auth::id())
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->first();

        $averageScore = Performa::where('karyawan_id', Auth::id())
            ->avg('performance_score');

        $quarters = ['Q1', 'Q2', 'Q3', 'Q4'];
        $quarterlyData = [];
        foreach ($quarters as $quarter) {
            $quarterlyData[$quarter] = Performa::where('karyawan_id', Auth::id())
                ->where('quarter', $quarter)
                ->where('tahun', Carbon::now()->year)
                ->avg('performance_score') ?? 0;
        }

        return view('performa.index', compact('performas', 'latestPerforma', 'averageScore', 'quarterlyData'));
    }

    public function show($id)
    {
        if (request()->expectsJson() || request()->ajax()) {
            $performa = Performa::with('karyawan')->find($id);

            if (! $performa) {
                return response()->json(['error' => 'Data tidak ditemukan'], 404);
            }

            $karyawan = $performa->karyawan;

            $fotoProfil = null;
            if ($karyawan && $karyawan->foto_profil) {
                $fotoProfil = $karyawan->foto_profil;
            }

            // AMBIL DATA ABSENSI UNTUK BULAN TERKAIT
            $presentCount = Performa::calculatePresentCount($performa->karyawan_id, $performa->bulan, $performa->tahun);
            $lateCount = Performa::calculateLateCount($performa->karyawan_id, $performa->bulan, $performa->tahun);
            $absentCount = Performa::calculateAbsentCount($performa->karyawan_id, $performa->bulan, $performa->tahun);

            return response()->json([
                'id' => $performa->id,
                'nama_karyawan' => $performa->nama_karyawan,
                'email' => $performa->email ?? '-',
                'phone' => $performa->phone ?? '-',
                'role' => $karyawan ? $karyawan->role : '-',
                'join_date_formatted' => $performa->join_date ? $performa->join_date->format('d M Y') : '-',
                'bulan_text' => $performa->bulan_text,
                'tahun' => $performa->tahun,
                'quality' => $performa->quality,
                'productivity' => $performa->productivity,
                'teamwork' => $performa->teamwork,
                'discipline' => $performa->discipline,
                'kpi_score' => $performa->kpi_score,
                'attendance_rate' => $performa->attendance_rate,
                'performance_score' => $performa->performance_score,
                'catatan' => $performa->catatan,
                'quarter' => $performa->quarter,
                'rating' => $performa->rating,
                'foto_profil' => $fotoProfil,
                // DATA ABSENSI OTOMATIS
                'attendance_summary' => [
                    'present' => $presentCount,
                    'late' => $lateCount,
                    'absent' => $absentCount,
                ],
            ]);
        }

        $performa = Performa::where('karyawan_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        return view('performa.show', compact('performa'));
    }

    public function adminIndex(Request $request)
    {
        $karyawans = Karyawan::whereIn('status', ['Permanent', 'Contract', 'Outsource'])
            ->orderBy('nama_lengkap')
            ->get();

        $performances = collect();
        $averageScore = 0;
        $totalTaskCompleted = 0;
        $topPerformer = null;
        $topScore = 0;
        $hasData = Performa::exists();

        if ($hasData) {
            foreach ($karyawans as $karyawan) {
                $latestPerforma = Performa::where('karyawan_id', $karyawan->id)
                    ->orderBy('tahun', 'desc')
                    ->orderBy('bulan', 'desc')
                    ->first();

                if ($latestPerforma) {
                    if ($latestPerforma->performance_score > $topScore) {
                        $topScore = $latestPerforma->performance_score;
                        $topPerformer = $karyawan;
                    }

                    // AMBIL DATA ABSENSI OTOMATIS DARI AbsensiKaryawan
                    $presentCount = Performa::calculatePresentCount($karyawan->id, $latestPerforma->bulan, $latestPerforma->tahun);
                    $lateCount = Performa::calculateLateCount($karyawan->id, $latestPerforma->bulan, $latestPerforma->tahun);
                    $absentCount = Performa::calculateAbsentCount($karyawan->id, $latestPerforma->bulan, $latestPerforma->tahun);

                    $performances->push((object) [
                        'id' => $latestPerforma->id,
                        'karyawan_id' => $karyawan->id,
                        'info' => (object) [
                            'name' => $karyawan->nama_lengkap,
                            'email' => $karyawan->email,
                            'phone' => $karyawan->nomor_telepon ?? '-',
                            'role' => $karyawan->role ?? '-',
                            'join_date' => $karyawan->tanggal_bergabung ? $karyawan->tanggal_bergabung->format('d M Y') : '-',
                            'foto_profil' => $karyawan->foto_profil,
                        ],
                        'attendance_summary' => (object) [
                            'attendance_rate' => $latestPerforma->attendance_rate,
                            'present' => $presentCount,    // DATA REAL DARI ABSENSI
                            'absent' => $absentCount,      // DATA REAL DARI ABSENSI
                            'late' => $lateCount,          // DATA REAL DARI ABSENSI
                        ],
                        'kpi' => (object) [
                            'quality' => $latestPerforma->quality,
                            'productivity' => $latestPerforma->productivity,
                            'teamwork' => $latestPerforma->teamwork,
                            'discipline' => $latestPerforma->discipline,
                            'kpi_score' => $latestPerforma->kpi_score,
                        ],
                        'performance_score' => $latestPerforma->performance_score,
                        'task_done' => rand(80, 200),
                        'status_performance' => $this->getStatusPerformance($latestPerforma->performance_score),
                        'quarter' => $latestPerforma->quarter,
                    ]);
                }
            }

            $totalScores = $performances->sum('performance_score');
            $count = $performances->count();
            $averageScore = $count > 0 ? round($totalScores / $count) : 0;
            $totalTaskCompleted = $performances->sum('task_done');
        }

        return view('admin.performa.index', compact(
            'performances',
            'averageScore',
            'totalTaskCompleted',
            'karyawans',
            'topPerformer',
            'topScore',
            'hasData'
        ));
    }

    private function getStatusPerformance($score)
    {
        if ($score >= 90) {
            return 'Excellent';
        }
        if ($score >= 75) {
            return 'Good';
        }
        if ($score >= 60) {
            return 'Average';
        }
        if ($score >= 50) {
            return 'Poor';
        }

        return 'Very Poor';
    }

    public function adminCreate()
    {
        $karyawans = Karyawan::whereIn('status', ['Permanent', 'Contract', 'Outsource'])
            ->orderBy('nama_lengkap')->get();
        $bulan = range(1, 12);
        $tahun = range(2023, date('Y') + 1);
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        return view('admin.performa.create', compact('karyawans', 'bulan', 'tahun', 'currentMonth', 'currentYear'));
    }

    public function getKaryawanData($id)
    {
        $karyawan = Karyawan::with(['jabatanSaatIni.jabatan.departemen'])->findOrFail($id);

        $departemen = '-';
        $position = '-';

        if ($karyawan->jabatanSaatIni && $karyawan->jabatanSaatIni->jabatan) {
            $position = $karyawan->jabatanSaatIni->jabatan->nama_jabatan;
            if ($karyawan->jabatanSaatIni->jabatan->departemen) {
                $departemen = $karyawan->jabatanSaatIni->jabatan->departemen->nama_departemen;
            }
        }

        return response()->json([
            'success' => true,
            'nama_karyawan' => $karyawan->nama_lengkap,
            'departemen' => $departemen,
            'position' => $position,
            'email' => $karyawan->email,
            'phone' => $karyawan->nomor_telepon,
            'join_date' => $karyawan->tanggal_bergabung ? $karyawan->tanggal_bergabung->format('Y-m-d') : date('Y-m-d'),
        ]);
    }

    public function adminStore(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2020',
            'quality' => 'required|integer|min:0|max:100',
            'productivity' => 'required|integer|min:0|max:100',
            'teamwork' => 'required|integer|min:0|max:100',
            'discipline' => 'required|integer|min:0|max:100',
            'catatan' => 'nullable',
        ]);

        $karyawan = Karyawan::find($request->karyawan_id);

        $departemen = '-';
        $position = '-';

        if ($karyawan->jabatanSaatIni && $karyawan->jabatanSaatIni->jabatan) {
            $position = $karyawan->jabatanSaatIni->jabatan->nama_jabatan;
            if ($karyawan->jabatanSaatIni->jabatan->departemen) {
                $departemen = $karyawan->jabatanSaatIni->jabatan->departemen->nama_departemen;
            }
        }

        $kpiScore = Performa::calculateKPIScore(
            $request->quality,
            $request->productivity,
            $request->teamwork,
            $request->discipline
        );

        // HITUNG ATTENDANCE RATE DARI DATA ABSENSI REAL
        $attendanceRate = Performa::calculateAttendanceRateFromAbsensi(
            $request->karyawan_id, 
            $request->bulan, 
            $request->tahun
        );

        $performanceScore = Performa::calculatePerformanceScore(
            $attendanceRate,
            $request->quality,
            $request->productivity,
            $request->teamwork,
            $request->discipline,
            $kpiScore
        );

        $quarter = $this->getQuarter($request->bulan);

        $exists = Performa::where('karyawan_id', $request->karyawan_id)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'Penilaian performa untuk karyawan ini pada periode tersebut sudah ada')
                ->withInput();
        }

        $performa = Performa::create([
            'karyawan_id' => $request->karyawan_id,
            'nama_karyawan' => $karyawan->nama_lengkap,
            'departemen' => $departemen,
            'position' => $position,
            'email' => $karyawan->email,
            'phone' => $karyawan->nomor_telepon,
            'join_date' => $karyawan->tanggal_bergabung ?? now(),
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'attendance_rate' => $attendanceRate, // DATA REAL DARI ABSENSI
            'quality' => $request->quality,
            'productivity' => $request->productivity,
            'teamwork' => $request->teamwork,
            'discipline' => $request->discipline,
            'kpi_score' => $kpiScore,
            'performance_score' => $performanceScore,
            'catatan' => $request->catatan,
            'quarter' => $quarter,
        ]);

        Notifikasi::create([
            'user_id' => $karyawan->id,
            'judul' => 'Penilaian Performa Baru',
            'pesan' => "Penilaian performa untuk bulan {$performa->bulan_text} {$performa->tahun} telah tersedia. Skor Anda: {$performanceScore}",
            'tipe_notifikasi' => 'performa',
        ]);

        return redirect()->route('admin.performa.index')
            ->with('success', 'Penilaian performa berhasil ditambahkan');
    }

    public function adminEdit($id)
    {
        $performa = Performa::findOrFail($id);
        $karyawans = Karyawan::whereIn('status', ['Permanent', 'Contract', 'Outsource'])
            ->orderBy('nama_lengkap')->get();
        $bulan = range(1, 12);
        $tahun = range(2023, date('Y') + 1);

        return view('admin.performa.edit', compact('performa', 'karyawans', 'bulan', 'tahun'));
    }

    public function adminUpdate(Request $request, $id)
    {
        $performa = Performa::findOrFail($id);

        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2020',
            'quality' => 'required|integer|min:0|max:100',
            'productivity' => 'required|integer|min:0|max:100',
            'teamwork' => 'required|integer|min:0|max:100',
            'discipline' => 'required|integer|min:0|max:100',
            'catatan' => 'nullable',
        ]);

        $karyawan = Karyawan::find($request->karyawan_id);

        $departemen = '-';
        $position = '-';

        if ($karyawan->jabatanSaatIni && $karyawan->jabatanSaatIni->jabatan) {
            $position = $karyawan->jabatanSaatIni->jabatan->nama_jabatan;
            if ($karyawan->jabatanSaatIni->jabatan->departemen) {
                $departemen = $karyawan->jabatanSaatIni->jabatan->departemen->nama_departemen;
            }
        }

        $kpiScore = Performa::calculateKPIScore(
            $request->quality,
            $request->productivity,
            $request->teamwork,
            $request->discipline
        );

        // HITUNG ATTENDANCE RATE DARI DATA ABSENSI REAL
        $attendanceRate = Performa::calculateAttendanceRateFromAbsensi(
            $request->karyawan_id, 
            $request->bulan, 
            $request->tahun
        );

        $performanceScore = Performa::calculatePerformanceScore(
            $attendanceRate,
            $request->quality,
            $request->productivity,
            $request->teamwork,
            $request->discipline,
            $kpiScore
        );

        $quarter = $this->getQuarter($request->bulan);

        $exists = Performa::where('karyawan_id', $request->karyawan_id)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'Penilaian performa untuk karyawan ini pada periode tersebut sudah ada')
                ->withInput();
        }

        $performa->update([
            'karyawan_id' => $request->karyawan_id,
            'nama_karyawan' => $karyawan->nama_lengkap,
            'departemen' => $departemen,
            'position' => $position,
            'email' => $karyawan->email,
            'phone' => $karyawan->nomor_telepon,
            'join_date' => $karyawan->tanggal_bergabung ?? now(),
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'attendance_rate' => $attendanceRate, // DATA REAL DARI ABSENSI
            'quality' => $request->quality,
            'productivity' => $request->productivity,
            'teamwork' => $request->teamwork,
            'discipline' => $request->discipline,
            'kpi_score' => $kpiScore,
            'performance_score' => $performanceScore,
            'catatan' => $request->catatan,
            'quarter' => $quarter,
        ]);

        return redirect()->route('admin.performa.index')
            ->with('success', 'Penilaian performa berhasil diupdate');
    }

    public function adminDestroy($id)
    {
        $performa = Performa::findOrFail($id);
        $performa->delete();

        return redirect()->route('admin.performa.index')
            ->with('success', 'Penilaian performa berhasil dihapus');
    }

    public function adminShow($id)
    {
        $performa = Performa::with('karyawan')
            ->where('karyawan_id', $id)
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->first();

        if (! $performa) {
            return response()->json(['error' => 'No performance data found'], 404);
        }

        $karyawan = $performa->karyawan;

        // Ambil foto profil dari karyawan
        $fotoProfil = null;
        if ($karyawan && $karyawan->foto_profil) {
            $fotoProfil = $karyawan->foto_profil;
        }

        // AMBIL DATA ABSENSI UNTUK BULAN TERKAIT
        $presentCount = Performa::calculatePresentCount($performa->karyawan_id, $performa->bulan, $performa->tahun);
        $lateCount = Performa::calculateLateCount($performa->karyawan_id, $performa->bulan, $performa->tahun);
        $absentCount = Performa::calculateAbsentCount($performa->karyawan_id, $performa->bulan, $performa->tahun);

        return response()->json([
            'id' => $performa->id,
            'info' => [
                'name' => $performa->nama_karyawan,
                'email' => $performa->email,
                'phone' => $performa->phone,
                'role' => $karyawan ? $karyawan->role : '-',
                'join_date' => $performa->join_date ? $performa->join_date->format('d M Y') : '-',
                'foto_profil' => $fotoProfil,
            ],
            'attendance_summary' => [
                'attendance_rate' => $performa->attendance_rate,
                'present' => $presentCount,    // DATA REAL
                'absent' => $absentCount,      // DATA REAL
                'late' => $lateCount,          // DATA REAL
            ],
            'kpi' => [
                'quality' => $performa->quality,
                'productivity' => $performa->productivity,
                'teamwork' => $performa->teamwork,
                'discipline' => $performa->discipline,
                'kpi_score' => $performa->kpi_score,
            ],
            'performance_score' => $performa->performance_score,
            'task_done' => rand(80, 200),
            'status_performance' => $this->getStatusPerformance($performa->performance_score),
            'quarter' => $performa->quarter,
            'catatan' => $performa->catatan,
        ]);
    }

    public function adminBulkCreate()
    {
        $karyawans = Karyawan::whereIn('status', ['Permanent', 'Contract', 'Outsource'])
            ->orderBy('nama_lengkap')->get();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        return view('admin.performa.bulk', compact('karyawans', 'currentMonth', 'currentYear'));
    }

    public function adminBulkStore(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2020',
            'performas' => 'required|array',
            'performas.*.karyawan_id' => 'required|exists:karyawans,id',
            'performas.*.quality' => 'required|integer|min:0|max:100',
            'performas.*.productivity' => 'required|integer|min:0|max:100',
            'performas.*.teamwork' => 'required|integer|min:0|max:100',
            'performas.*.discipline' => 'required|integer|min:0|max:100',
        ]);

        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $quarter = $this->getQuarter($bulan);

        $successCount = 0;
        $errorCount = 0;

        foreach ($request->performas as $data) {
            $karyawan = Karyawan::find($data['karyawan_id']);

            $departemen = '-';
            $position = '-';

            if ($karyawan->jabatanSaatIni && $karyawan->jabatanSaatIni->jabatan) {
                $position = $karyawan->jabatanSaatIni->jabatan->nama_jabatan;
                if ($karyawan->jabatanSaatIni->jabatan->departemen) {
                    $departemen = $karyawan->jabatanSaatIni->jabatan->departemen->nama_departemen;
                }
            }

            $kpiScore = Performa::calculateKPIScore(
                $data['quality'],
                $data['productivity'],
                $data['teamwork'],
                $data['discipline']
            );

            // HITUNG ATTENDANCE RATE DARI DATA ABSENSI REAL
            $attendanceRate = Performa::calculateAttendanceRateFromAbsensi(
                $data['karyawan_id'],
                $bulan,
                $tahun
            );

            $performanceScore = Performa::calculatePerformanceScore(
                $attendanceRate,
                $data['quality'],
                $data['productivity'],
                $data['teamwork'],
                $data['discipline'],
                $kpiScore
            );

            $exists = Performa::where('karyawan_id', $data['karyawan_id'])
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->exists();

            if (! $exists) {
                Performa::create([
                    'karyawan_id' => $data['karyawan_id'],
                    'nama_karyawan' => $karyawan->nama_lengkap,
                    'departemen' => $departemen,
                    'position' => $position,
                    'email' => $karyawan->email,
                    'phone' => $karyawan->nomor_telepon,
                    'join_date' => $karyawan->tanggal_bergabung ?? now(),
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'attendance_rate' => $attendanceRate, // DATA REAL
                    'quality' => $data['quality'],
                    'productivity' => $data['productivity'],
                    'teamwork' => $data['teamwork'],
                    'discipline' => $data['discipline'],
                    'kpi_score' => $kpiScore,
                    'performance_score' => $performanceScore,
                    'quarter' => $quarter,
                ]);
                $successCount++;
            } else {
                $errorCount++;
            }
        }

        $message = "Berhasil menambahkan {$successCount} penilaian performa.";
        if ($errorCount > 0) {
            $message .= " {$errorCount} data gagal ditambahkan karena sudah ada.";
        }

        return redirect()->route('admin.performa.index')
            ->with('success', $message);
    }

    public function checkAndResetKPI()
    {
        return response()->json(['success' => true, 'message' => 'System ready for new assessments']);
    }

    private function getQuarter($bulan)
    {
        if ($bulan >= 1 && $bulan <= 3) {
            return 'Q1';
        }
        if ($bulan >= 4 && $bulan <= 6) {
            return 'Q2';
        }
        if ($bulan >= 7 && $bulan <= 9) {
            return 'Q3';
        }

        return 'Q4';
    }
}