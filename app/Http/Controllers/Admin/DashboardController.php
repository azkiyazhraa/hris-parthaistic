<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsensiKaryawan;
use App\Models\Karyawan;
use App\Models\PengajuanCuti;
use App\Models\Pengumuman;
use App\Models\Performa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index()
    {
        // Total karyawan selain admin & hr
        $totalKaryawan = Karyawan::where('role', '!=', 'admin')->where('role', '!=', 'hr')->count();

        // Hitung status karyawan - cara aman untuk MySQL
        $fulltime = Karyawan::where('role', '!=', 'admin')->where('role', '!=', 'hr')->where('status', 'Full-time')->count();

        $contract = Karyawan::where('role', '!=', 'admin')->where('role', '!=', 'hr')->where('status', 'Contract')->count();

        $internship = Karyawan::where('role', '!=', 'admin')->where('role', '!=', 'hr')->where('status', 'Internship')->count();

        $resigned = Karyawan::where('role', '!=', 'admin')->where('role', '!=', 'hr')->where('status', 'Resigned')->count();

        $contractEnded = Karyawan::where('role', '!=', 'admin')->where('role', '!=', 'hr')->where('status', 'Contract Ended')->count();

        $internshipCompleted = Karyawan::where('role', '!=', 'admin')->where('role', '!=', 'hr')->where('status', 'Internship Completed')->count();

        $terminated = Karyawan::where('role', '!=', 'admin')->where('role', '!=', 'hr')->where('status', 'Terminated')->count();

        // Persentase status
        $fulltimePercent = $totalKaryawan > 0 ? ($fulltime / $totalKaryawan) * 100 : 0;
        $contractPercent = $totalKaryawan > 0 ? ($contract / $totalKaryawan) * 100 : 0;
        $internshipPercent = $totalKaryawan > 0 ? ($internship / $totalKaryawan) * 100 : 0;

        // Total resigned/terminated employees
        $resignedEmployees = $resigned + $contractEnded + $internshipCompleted + $terminated;

        // Pengumuman terbaru
        $attachment = Pengumuman::latest()->limit(4)->get();

        // Absensi terbaru
        $absensi = AbsensiKaryawan::with('karyawan')->where('is_change_day', false)->whereDate('tanggal', today())->latest('created_at')->limit(5)->get();

        // Statistik absensi
        $attendanceCounts = AbsensiKaryawan::selectRaw(
            '
        COUNT(*) as total,
        SUM(status_kehadiran = ?) as pending,
        SUM(status_kehadiran = ?) as present,
        SUM(status_kehadiran = ?) as permit,
        SUM(status_kehadiran = ?) as sick,
        SUM(status_kehadiran = ?) as absent
    ',
            [AbsensiKaryawan::STATUS_PENDING, AbsensiKaryawan::STATUS_PRESENT, AbsensiKaryawan::STATUS_PERMIT, AbsensiKaryawan::STATUS_SICK, AbsensiKaryawan::STATUS_ABSENT],
        )
            ->where('is_change_day', false)
            ->whereDate('tanggal', today())
            ->first();

        $statistics = [
            'total' => (int) ($attendanceCounts->total ?? 0),
            'pending' => (int) ($attendanceCounts->pending ?? 0),
            'present' => (int) ($attendanceCounts->present ?? 0),
            'permit' => (int) ($attendanceCounts->permit ?? 0),
            'sick' => (int) ($attendanceCounts->sick ?? 0),
            'absent' => (int) ($attendanceCounts->absent ?? 0),
        ];

        return view('admin.dashboard', compact('totalKaryawan', 'fulltime', 'contract', 'internship', 'fulltimePercent', 'contractPercent', 'internshipPercent', 'resignedEmployees', 'attachment', 'absensi', 'statistics'));
    }

    public function karyawan()
    {
        $karyawans = Karyawan::where('role', '!=', 'admin')->where('role', '!=', 'hr')->orderBy('created_at', 'desc')->get();

        return view('admin.karyawan.index', compact('karyawans'));
    }

    public function storeKaryawan(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|unique:karyawans,email',
                'nama_depan' => 'required|string|max:100',
                'nama_belakang' => 'required|string|max:100',
                'kata_sandi' => 'required|min:6',
                'role' => 'required|in:admin,hr,karyawan',
                'jabatan' => 'nullable|in:Chief Executive Officer,Chief Operating Officer,Creative Writer,Finance,Business Development,Videographer,Video Editor,Social Media Manager,lainnya',
                'jabatan_lainnya' => 'nullable|required_if:jabatan,lainnya|string|max:100',
                'status' => 'required|in:Full-time,Contract,Internship,Resigned,Contract Ended,Internship Completed,Terminated',
                'pendidikan_terakhir' => 'nullable|in:SMP,SMA/MA,SMK,D1,D2,D3,S1,S2',
                'nama_bank' => 'nullable|string|max:50',
                'nomor_rekening' => 'nullable|string|max:30',

                'nik' => 'nullable|string|max:50',
                'nomor_telepon' => 'nullable|string|max:30',
                'alamat' => 'nullable|string',
                'npwp' => 'nullable|string|max:50',
                'tempat_lahir' => 'nullable|string|max:100',
                'tanggal_lahir' => 'nullable|date',
                'jenis_kelamin' => 'nullable|in:L,P',
                'agama' => 'nullable|string|max:50',
                'status_pernikahan' => 'nullable|string|max:50',
                'universitas' => 'nullable|string|max:150',
                'jurusan' => 'nullable|string|max:150',
                'tahun_lulus' => 'nullable|digits:4',
                'nama_kontak_darurat' => 'nullable|string|max:100',
                'telepon_kontak_darurat' => 'nullable|string|max:30',
                'tanggal_bergabung' => 'nullable|date',
                'end_date' => 'nullable|date',
                'reason_resigned' => 'nullable|string|max:255',
                'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            // Generate NIP
            $lastKaryawan = Karyawan::orderBy('id', 'desc')->first();
            $lastNumber = 0;
            if ($lastKaryawan && preg_match('/EMP(\d+)/', $lastKaryawan->nip, $matches)) {
                $lastNumber = (int) $matches[1];
            }
            $newNip = 'EMP' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

            // Upload foto
            $fotoPath = null;
            if ($request->hasFile('foto_profil')) {
                $fotoPath = $request->file('foto_profil')->store('karyawan', 'public');
            }

            // Buat karyawan baru (boot model akan otomatis hitung total_hari_kerja)
            $karyawan = Karyawan::create([
                'nip' => $newNip,
                'email' => $validated['email'],
                'kata_sandi' => Hash::make($validated['kata_sandi']),
                'nama_depan' => $validated['nama_depan'],
                'nama_belakang' => $validated['nama_belakang'],
                'nama_lengkap' => $validated['nama_depan'] . ' ' . $validated['nama_belakang'],
                'role' => $validated['role'],
                'jabatan' => $validated['jabatan'] ?? null,
                'jabatan_lainnya' => ($validated['jabatan'] ?? '') === 'lainnya' ? $validated['jabatan_lainnya'] ?? null : null,
                'status' => $validated['status'],
                'tanggal_bergabung' => $validated['tanggal_bergabung'] ?? now(),
                'end_date' => $validated['end_date'] ?? null,
                'reason_resigned' => $validated['reason_resigned'] ?? null,
                'pendidikan_terakhir' => $validated['pendidikan_terakhir'] ?? null,
                'pendidikan_terakhir_new' => $validated['pendidikan_terakhir'] ?? null,
                'nama_bank' => $validated['nama_bank'] ?? 'BSI',
                'nomor_rekening' => $validated['nomor_rekening'] ?? null,
                'foto_profil' => $fotoPath,
                'nomor_telepon' => $validated['nomor_telepon'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
                'nik' => $validated['nik'] ?? null,
                'npwp' => $validated['npwp'] ?? null,
                'tempat_lahir' => $validated['tempat_lahir'] ?? null,
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
                'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                'agama' => $validated['agama'] ?? null,
                'status_pernikahan' => $validated['status_pernikahan'] ?? null,
                'universitas' => $validated['universitas'] ?? null,
                'jurusan' => $validated['jurusan'] ?? null,
                'tahun_lulus' => $validated['tahun_lulus'] ?? null,
                'nama_kontak_darurat' => $validated['nama_kontak_darurat'] ?? null,
                'telepon_kontak_darurat' => $validated['telepon_kontak_darurat'] ?? null,
            ]);

            return redirect()->route('admin.karyawan')->with('success', 'Employee added successfully. Working days calculated automatically.');
        } catch (\Throwable $th) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error: ' . $th->getMessage());
        }
    }
    public function editKaryawan($id)
    {
        $karyawan = Karyawan::findOrFail($id);

        return response()->json($karyawan);
    }

    public function updateKaryawan(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);

        try {
            $validated = $request->validate([
                'email' => 'required|email|unique:karyawans,email,' . $id,
                'nama_depan' => 'required|string|max:100',
                'nama_belakang' => 'required|string|max:100',
                'role' => 'required|in:admin,hr,karyawan',
                'jabatan' => 'nullable|in:Chief Executive Officer,Chief Operating Officer,Creative Writer,Finance,Business Development,Videographer,Video Editor,Social Media Manager,lainnya',
                'jabatan_lainnya' => 'nullable|required_if:jabatan,lainnya|string|max:100',
                'status' => 'required|in:Full-time,Contract,Internship,Resigned,Contract Ended,Internship Completed,Terminated',
                'pendidikan_terakhir' => 'nullable|in:SMP,SMA/MA,SMK,D1,D2,D3,S1,S2',
                'nama_bank' => 'nullable|string|max:50',
                'nomor_rekening' => 'nullable|string|max:30',

                'kata_sandi' => 'nullable|min:6',
                'nik' => 'nullable|string|max:50',
                'nomor_telepon' => 'nullable|string|max:30',
                'alamat' => 'nullable|string',
                'npwp' => 'nullable|string|max:50',
                'tempat_lahir' => 'nullable|string|max:100',
                'tanggal_lahir' => 'nullable|date',
                'jenis_kelamin' => 'nullable|in:L,P',
                'agama' => 'nullable|string|max:50',
                'status_pernikahan' => 'nullable|string|max:50',
                'universitas' => 'nullable|string|max:150',
                'jurusan' => 'nullable|string|max:150',
                'tahun_lulus' => 'nullable|digits:4',
                'nama_kontak_darurat' => 'nullable|string|max:100',
                'telepon_kontak_darurat' => 'nullable|string|max:30',
                'tanggal_bergabung' => 'nullable|date',
                'end_date' => 'nullable|date',
                'reason_resigned' => 'nullable|string|max:255',
                'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            $updateData = [
                'email' => $validated['email'],
                'nama_depan' => $validated['nama_depan'],
                'nama_belakang' => $validated['nama_belakang'],
                'nama_lengkap' => $validated['nama_depan'] . ' ' . $validated['nama_belakang'],
                'role' => $validated['role'],
                'jabatan' => $validated['jabatan'] ?? null,
                'jabatan_lainnya' => ($validated['jabatan'] ?? '') === 'lainnya' ? $validated['jabatan_lainnya'] ?? null : null,
                'status' => $validated['status'],
                'tanggal_bergabung' => $validated['tanggal_bergabung'] ?? $karyawan->tanggal_bergabung,
                'end_date' => $validated['end_date'] ?? null,
                'reason_resigned' => $validated['reason_resigned'] ?? null,
                'pendidikan_terakhir' => $validated['pendidikan_terakhir'] ?? null,
                'pendidikan_terakhir_new' => $validated['pendidikan_terakhir'] ?? null,
                'nama_bank' => $validated['nama_bank'] ?? $karyawan->nama_bank,
                'nomor_rekening' => $validated['nomor_rekening'] ?? $karyawan->nomor_rekening,
                'nomor_telepon' => $validated['nomor_telepon'] ?? $karyawan->nomor_telepon,
                'alamat' => $validated['alamat'] ?? $karyawan->alamat,
                'nik' => $validated['nik'] ?? $karyawan->nik,
                'npwp' => $validated['npwp'] ?? $karyawan->npwp,
                'tempat_lahir' => $validated['tempat_lahir'] ?? $karyawan->tempat_lahir,
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? $karyawan->tanggal_lahir,
                'jenis_kelamin' => $validated['jenis_kelamin'] ?? $karyawan->jenis_kelamin,
                'agama' => $validated['agama'] ?? $karyawan->agama,
                'status_pernikahan' => $validated['status_pernikahan'] ?? $karyawan->status_pernikahan,
                'universitas' => $validated['universitas'] ?? $karyawan->universitas,
                'jurusan' => $validated['jurusan'] ?? $karyawan->jurusan,
                'tahun_lulus' => $validated['tahun_lulus'] ?? $karyawan->tahun_lulus,
                'nama_kontak_darurat' => $validated['nama_kontak_darurat'] ?? $karyawan->nama_kontak_darurat,
                'telepon_kontak_darurat' => $validated['telepon_kontak_darurat'] ?? $karyawan->telepon_kontak_darurat,
            ];

            if ($request->filled('kata_sandi')) {
                $updateData['kata_sandi'] = Hash::make($validated['kata_sandi']);
            }

            if ($request->hasFile('foto_profil')) {
                if (!empty($karyawan->foto_profil) && Storage::disk('public')->exists($karyawan->foto_profil)) {
                    Storage::disk('public')->delete($karyawan->foto_profil);
                }
                $updateData['foto_profil'] = $request->file('foto_profil')->store('karyawan', 'public');
            }

            // Update data (boot model akan otomatis hitung total_hari_kerja)
            $karyawan->update($updateData);

            return redirect()->route('admin.karyawan')->with('success', 'Employee updated successfully. Working days calculated automatically.');
        } catch (\Throwable $th) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error: ' . $th->getMessage());
        }
    }
    public function destroyKaryawan($id)
    {
        // Method ini tidak digunakan lagi (fitur delete dihapus)
        return redirect()->route('admin.karyawan')->with('error', 'Delete feature has been disabled');
    }

    public function getEmployeeDetail($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Attendance rate (all-time)
        $allAttendances = AbsensiKaryawan::where('karyawan_id', $id)
            ->whereIn('status_kehadiran', ['present', 'pending', 'permit', 'sick'])
            ->where('is_change_day', false)
            ->count();

        $totalWorkingDays = 0;
        if ($karyawan->tanggal_bergabung) {
            $totalWorkingDays = $this->countWeekdays(Carbon::parse($karyawan->tanggal_bergabung)->startOfDay(), Carbon::now()->startOfDay());
        }
        $attendanceRate = $totalWorkingDays > 0 ? round(($allAttendances / $totalWorkingDays) * 100, 1) : 0;

        // Present this month
        $presentCount = AbsensiKaryawan::where('karyawan_id', $id)->whereMonth('tanggal', $currentMonth)->whereYear('tanggal', $currentYear)->where('status_kehadiran', 'present')->count();

        // Late this month
        $lateCount = 0;
        $monthRecords = AbsensiKaryawan::where('karyawan_id', $id)->whereMonth('tanggal', $currentMonth)->whereYear('tanggal', $currentYear)->where('status_kehadiran', 'present')->whereNotNull('jam_masuk')->get();
        foreach ($monthRecords as $r) {
            if (Carbon::parse($r->jam_masuk)->format('H:i:s') > '08:00:00') {
                $lateCount++;
            }
        }

        // Absent this month — only count records explicitly marked as 'absent'
        $absentCount = AbsensiKaryawan::where('karyawan_id', $id)->whereMonth('tanggal', $currentMonth)->whereYear('tanggal', $currentYear)->where('is_change_day', false)->where('status_kehadiran', AbsensiKaryawan::STATUS_ABSENT)->count();

        // Recent attendances (last 5)
        $recentAttendances = AbsensiKaryawan::where('karyawan_id', $id)->where('is_change_day', false)->orderBy('tanggal', 'desc')->limit(5)->get()->map(
            fn($a) => [
                'tanggal' => $a->tanggal->format('d M Y'),
                'jam_masuk' => $a->jam_masuk ? Carbon::parse($a->jam_masuk)->format('H:i') : '-',
                'jam_pulang' => $a->jam_pulang ? Carbon::parse($a->jam_pulang)->format('H:i') : '-',
                'status' => $a->status_kehadiran,
            ],
        );

        // Leave usage
        $annualUsed = PengajuanCuti::where('karyawan_id', $id)
            ->where('jenis_cuti', 'tahunan')
            ->whereIn('status', ['disetujui', 'approved'])
            ->sum('total_hari');
        $sickUsed = PengajuanCuti::where('karyawan_id', $id)
            ->where('jenis_cuti', 'sakit')
            ->whereIn('status', ['disetujui', 'approved'])
            ->sum('total_hari');
        $emergencyUsed = PengajuanCuti::where('karyawan_id', $id)
            ->where('jenis_cuti', 'penting')
            ->whereIn('status', ['disetujui', 'approved'])
            ->sum('total_hari');
        $otherUsed = PengajuanCuti::where('karyawan_id', $id)
            ->where('jenis_cuti', 'lainnya')
            ->whereIn('status', ['disetujui', 'approved'])
            ->sum('total_hari');

        $leaveRequests = PengajuanCuti::where('karyawan_id', $id)->orderBy('created_at', 'desc')->limit(5)->get()->map(
            fn($l) => [
                'tanggal_mulai' => $l->tanggal_mulai->format('d/m/Y'),
                'tanggal_selesai' => $l->tanggal_selesai->format('d/m/Y'),
                'jenis_cuti' => $l->jenis_cuti_label,
                'total_hari' => $l->total_hari,
                'status' => $l->status,
            ],
        );

        // Performance
        $latestPerf = Performa::where('karyawan_id', $id)->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->first();

        $prevPerf = null;
        if ($latestPerf) {
            $prevMonth = $latestPerf->bulan - 1;
            $prevYear = $latestPerf->tahun;
            if ($prevMonth === 0) {
                $prevMonth = 12;
                $prevYear--;
            }
            $prevPerf = Performa::where('karyawan_id', $id)->where('tahun', $prevYear)->where('bulan', $prevMonth)->first();
        }

        $perfChange = 0;
        if ($latestPerf && $prevPerf) {
            $perfChange = $latestPerf->performance_score - $prevPerf->performance_score;
        } elseif ($latestPerf) {
            $perfChange = $latestPerf->performance_score;
        }

        // Performance history (current year, all 12 months)
        $histMonths = [];
        $histScores = [];
        for ($m = 1; $m <= 12; $m++) {
            $p = Performa::where('karyawan_id', $id)->where('tahun', $currentYear)->where('bulan', $m)->first();
            $histMonths[] = Carbon::create($currentYear, $m, 1)->format('M');
            $histScores[] = $p?->performance_score ?? 0;
        }

        return response()->json([
            'attendance' => [
                'rate' => $attendanceRate,
                'present' => $presentCount,
                'late' => $lateCount,
                'absent' => $absentCount,
                'recent' => $recentAttendances,
            ],
            'leave' => [
                'annual_used' => (int) $annualUsed,
                'annual_quota' => 12,
                'sick_used' => (int) $sickUsed,
                'sick_quota' => 12,
                'emergency_used' => (int) $emergencyUsed,
                'emergency_quota' => 12,
                'other_used' => (int) $otherUsed,
                'other_quota' => 12,
                'requests' => $leaveRequests,
            ],
            'performance' => [
                'latest_score' => $latestPerf?->performance_score ?? 0,
                'rating_label' => $latestPerf?->rating['label'] ?? 'No Data',
                'rating_color' => $latestPerf?->rating['color'] ?? 'gray',
                'change' => $perfChange,
                'quality' => $latestPerf?->quality ?? 0,
                'productivity' => $latestPerf?->productivity ?? 0,
                'teamwork' => $latestPerf?->teamwork ?? 0,
                'discipline' => $latestPerf?->discipline ?? 0,
                'kpi_score' => $latestPerf?->kpi_score ?? 0,
                'history' => ['months' => $histMonths, 'scores' => $histScores],
            ],
        ]);
    }

    public function showKaryawanPassword($id)
    {
        $karyawan = Karyawan::findOrFail($id);

        return response()->json([
            'hashed_password' => $karyawan->kata_sandi,
            'message' => 'This is the hashed password. In production, password reset functionality should be used instead.',
        ]);
    }

    private function countWeekdays(Carbon $start, Carbon $end): int
    {
        if ($start->gt($end)) {
            return 0;
        }

        $totalDays = $start->diffInDays($end) + 1; // inclusive
        $fullWeeks = intdiv($totalDays, 7);
        $weekdays = $fullWeeks * 6;
        $extra = $totalDays % 7;
        $dow = $start->dayOfWeek; // 0=Sun … 6=Sat

        for ($i = 0; $i < $extra; $i++) {
            $d = ($dow + $i) % 7;
            if ($d >= 1 && $d <= 6) {
                $weekdays++;
            }
        }

        return $weekdays;
    }
}
