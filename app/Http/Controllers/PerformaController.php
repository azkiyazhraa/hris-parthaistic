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
                'task_done' => $performa->task_done,
                'task_target' => $performa->task_target,
                'task_score' => $performa->task_score,
                'performance_score' => $performa->performance_score,
                'catatan' => $performa->catatan,
                'quarter' => $performa->quarter,
                'rating' => $performa->rating,
                'foto_profil' => $fotoProfil,
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
        $filterBulan = $request->filter_bulan ? (int) $request->filter_bulan : null;
        $filterTahun = $request->filter_tahun ? (int) $request->filter_tahun : null;
        $perPage = in_array((int) $request->per_page, [10, 15, 20, 50, 100]) ? (int) $request->per_page : 10;

        $karyawans = Karyawan::whereIn('status', ['Full-time', 'Contract', 'Internship'])
            ->whereNotIn('role', ['admin', 'hr'])
            ->orderBy('nama_lengkap')
            ->get();

        $performances = collect();
        $averageScore = 0;
        $averageAttendance = 0;
        $totalTaskCompleted = 0;
        $topPerformer = null;
        $topScore = 0;
        $topPerformerBulan = null;
        $topPerformerTahun = null;
        $hasData = Performa::exists();
        $paginator = null;

        if ($hasData) {
            $query = Performa::whereIn('karyawan_id', $karyawans->pluck('id'));

            if ($filterBulan) {
                $query->where('bulan', $filterBulan);
            }
            if ($filterTahun) {
                $query->where('tahun', $filterTahun);
            }

            // Summary stats dari semua record (bukan hanya halaman ini)
            $averageScore = round((clone $query)->avg('performance_score') ?? 0);
            $averageAttendance = round((clone $query)->avg('attendance_rate') ?? 0);
            $totalTaskCompleted = (clone $query)->sum('task_done');

            // Top performer: karyawan dgn skor tertinggi di bulan terbaru yg ada datanya
            $latestEntry = Performa::whereIn('karyawan_id', $karyawans->pluck('id'))
                ->orderBy('tahun', 'desc')
                ->orderBy('bulan', 'desc')
                ->first();

            if ($latestEntry) {
                $topRecord = Performa::whereIn('karyawan_id', $karyawans->pluck('id'))
                    ->where('bulan', $latestEntry->bulan)
                    ->where('tahun', $latestEntry->tahun)
                    ->orderBy('performance_score', 'desc')
                    ->first();

                if ($topRecord) {
                    $topScore = $topRecord->performance_score;
                    $topPerformer = $karyawans->find($topRecord->karyawan_id);
                    $topPerformerBulan = $latestEntry->bulan;
                    $topPerformerTahun = $latestEntry->tahun;
                }
            }

            // Paginated records untuk tabel
            $paginator = (clone $query)->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->paginate($perPage);

            foreach ($paginator as $record) {
                $karyawan = $karyawans->find($record->karyawan_id);
                if (! $karyawan) {
                    continue;
                }

                $presentCount = Performa::calculatePresentCount($karyawan->id, $record->bulan, $record->tahun);
                $lateCount = Performa::calculateLateCount($karyawan->id, $record->bulan, $record->tahun);
                $absentCount = Performa::calculateAbsentCount($karyawan->id, $record->bulan, $record->tahun);

                $performances->push((object) [
                    'id' => $record->id,
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
                        'attendance_rate' => $record->attendance_rate,
                        'present' => $presentCount,
                        'absent' => $absentCount,
                        'late' => $lateCount,
                    ],
                    'kpi' => (object) [
                        'quality' => $record->quality,
                        'productivity' => $record->productivity,
                        'teamwork' => $record->teamwork,
                        'discipline' => $record->discipline,
                        'kpi_score' => $record->kpi_score,
                    ],
                    'performance_score' => $record->performance_score,
                    'task_done' => $record->task_done,
                    'task_score' => $record->task_score,
                    'status_performance' => $this->getStatusPerformance($record->performance_score),
                    'quarter' => $record->quarter,
                    'bulan' => $record->bulan,
                    'tahun' => $record->tahun,
                ]);
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'rows' => view('admin.performa._rows', compact('performances'))->render(),
                'pagination' => ($paginator && $paginator->hasPages())
                    ? $paginator->appends($request->query())->links('vendor.pagination.simple-blue')->render()
                    : '',
            ]);
        }

        return view('admin.performa.index', compact(
            'performances',
            'averageScore',
            'averageAttendance',
            'totalTaskCompleted',
            'karyawans',
            'topPerformer',
            'topScore',
            'topPerformerBulan',
            'topPerformerTahun',
            'hasData',
            'filterBulan',
            'filterTahun',
            'paginator',
            'perPage',
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
        $karyawans = Karyawan::whereIn('status', ['Full-time', 'Contract', 'Internship'])
            ->whereNotIn('role', ['admin', 'hr'])
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

    public function getAttendanceRate(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'bulan'       => 'required|integer|between:1,12',
            'tahun'       => 'required|integer|min:2020',
        ]);

        $rate = Performa::calculateAttendanceRateFromAbsensi(
            $request->karyawan_id,
            $request->bulan,
            $request->tahun
        );

        return response()->json(['rate' => $rate]);
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

        $attendanceRate = Performa::calculateAttendanceRateFromAbsensi(
            $request->karyawan_id,
            $request->bulan,
            $request->tahun
        );

        $taskDone   = (int) ($request->task_done ?? 0);
        $taskTarget = max(1, (int) ($request->task_target ?? 1));
        $taskScore  = min(100, (int) round(($taskDone / $taskTarget) * 100));

        $performanceScore = Performa::calculatePerformanceScore($kpiScore, $taskScore);

        $quarter = $this->getQuarter($request->bulan);

        $exists = Performa::where('karyawan_id', $request->karyawan_id)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'A performance review for this employee in that period already exists')
                ->withInput();
        }

        $performa = Performa::create([
            'karyawan_id'    => $request->karyawan_id,
            'nama_karyawan'  => $karyawan->nama_lengkap,
            'departemen'     => $departemen,
            'position'       => $position,
            'email'          => $karyawan->email,
            'phone'          => $karyawan->nomor_telepon,
            'join_date'      => $karyawan->tanggal_bergabung ?? now(),
            'bulan'          => $request->bulan,
            'tahun'          => $request->tahun,
            'attendance_rate' => $attendanceRate,
            'task_done'      => $taskDone,
            'task_target'    => $taskTarget,
            'task_score'     => $taskScore,
            'quality'        => $request->quality,
            'productivity'   => $request->productivity,
            'teamwork'       => $request->teamwork,
            'discipline'     => $request->discipline,
            'kpi_score'      => $kpiScore,
            'performance_score' => $performanceScore,
            'catatan'        => $request->catatan,
            'quarter'        => $quarter,
        ]);

        Notifikasi::create([
            'user_id' => $karyawan->id,
            'judul' => 'Penilaian Performa Baru',
            'pesan' => "Penilaian performa untuk bulan {$performa->bulan_text} {$performa->tahun} telah tersedia. Skor Anda: {$performanceScore}",
            'tipe_notifikasi' => 'performa',
        ]);

        return redirect()->route('admin.performa.index')
            ->with('success', 'Performance review added successfully');
    }

    public function adminEdit($id)
    {
        $performa = Performa::findOrFail($id);
        $karyawans = Karyawan::whereIn('status', ['Full-time', 'Contract', 'Internship'])
            ->whereNotIn('role', ['admin', 'hr'])
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

        $attendanceRate = Performa::calculateAttendanceRateFromAbsensi(
            $request->karyawan_id,
            $request->bulan,
            $request->tahun
        );

        $taskDone   = (int) ($request->task_done ?? 0);
        $taskTarget = max(1, (int) ($request->task_target ?? 1));
        $taskScore  = min(100, (int) round(($taskDone / $taskTarget) * 100));

        $performanceScore = Performa::calculatePerformanceScore($kpiScore, $taskScore);

        $quarter = $this->getQuarter($request->bulan);

        $exists = Performa::where('karyawan_id', $request->karyawan_id)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'A performance review for this employee in that period already exists')
                ->withInput();
        }

        $performa->update([
            'karyawan_id'    => $request->karyawan_id,
            'nama_karyawan'  => $karyawan->nama_lengkap,
            'departemen'     => $departemen,
            'position'       => $position,
            'email'          => $karyawan->email,
            'phone'          => $karyawan->nomor_telepon,
            'join_date'      => $karyawan->tanggal_bergabung ?? now(),
            'bulan'          => $request->bulan,
            'tahun'          => $request->tahun,
            'attendance_rate' => $attendanceRate,
            'task_done'      => $taskDone,
            'task_target'    => $taskTarget,
            'task_score'     => $taskScore,
            'quality'        => $request->quality,
            'productivity'   => $request->productivity,
            'teamwork'       => $request->teamwork,
            'discipline'     => $request->discipline,
            'kpi_score'      => $kpiScore,
            'performance_score' => $performanceScore,
            'catatan'        => $request->catatan,
            'quarter'        => $quarter,
        ]);

        return redirect()->route('admin.performa.index')
            ->with('success', 'Performance review updated successfully');
    }

    public function adminDestroy($id)
    {
        $performa = Performa::findOrFail($id);
        $performa->delete();

        return redirect()->route('admin.performa.index')
            ->with('success', 'Performance review deleted successfully');
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
                'present' => $presentCount,
                'absent' => $absentCount,
                'late' => $lateCount,
            ],
            'kpi' => [
                'quality' => $performa->quality,
                'productivity' => $performa->productivity,
                'teamwork' => $performa->teamwork,
                'discipline' => $performa->discipline,
                'kpi_score' => $performa->kpi_score,
            ],
            'task_done' => $performa->task_done,
            'task_target' => $performa->task_target,
            'task_score' => $performa->task_score,
            'task_source' => $performa->trello_card_id ? 'trello' : 'manual',
            'performance_score' => $performa->performance_score,
            'status_performance' => $this->getStatusPerformance($performa->performance_score),
            'quarter' => $performa->quarter,
            'catatan' => $performa->catatan,
        ]);
    }

    public function adminBulkCreate()
    {
        $karyawans = Karyawan::whereIn('status', ['Full-time', 'Contract', 'Internship'])
            ->whereNotIn('role', ['admin', 'hr'])
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
            $quality    = (int) ($data['quality']     ?? 0);
            $productivity = (int) ($data['productivity'] ?? 0);
            $teamwork   = (int) ($data['teamwork']    ?? 0);
            $discipline = (int) ($data['discipline']  ?? 0);
            $taskDoneRaw = (int) ($data['task_done']  ?? 0);

            if ($quality === 0 && $productivity === 0 && $teamwork === 0 && $discipline === 0 && $taskDoneRaw === 0) {
                continue;
            }

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

            $attendanceRate = Performa::calculateAttendanceRateFromAbsensi(
                $data['karyawan_id'],
                $bulan,
                $tahun
            );

            $taskDone   = (int) ($data['task_done'] ?? 0);
            $taskTarget = max(1, (int) ($data['task_target'] ?? 1));
            $taskScore  = min(100, (int) round(($taskDone / $taskTarget) * 100));

            $performanceScore = Performa::calculatePerformanceScore($kpiScore, $taskScore);

            $exists = Performa::where('karyawan_id', $data['karyawan_id'])
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->exists();

            if (! $exists) {
                Performa::create([
                    'karyawan_id'    => $data['karyawan_id'],
                    'nama_karyawan'  => $karyawan->nama_lengkap,
                    'departemen'     => $departemen,
                    'position'       => $position,
                    'email'          => $karyawan->email,
                    'phone'          => $karyawan->nomor_telepon,
                    'join_date'      => $karyawan->tanggal_bergabung ?? now(),
                    'bulan'          => $bulan,
                    'tahun'          => $tahun,
                    'attendance_rate' => $attendanceRate,
                    'task_done'      => $taskDone,
                    'task_target'    => $taskTarget,
                    'task_score'     => $taskScore,
                    'quality'        => $data['quality'],
                    'productivity'   => $data['productivity'],
                    'teamwork'       => $data['teamwork'],
                    'discipline'     => $data['discipline'],
                    'kpi_score'      => $kpiScore,
                    'performance_score' => $performanceScore,
                    'quarter'        => $quarter,
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