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
        // Total karyawan selain admin
        $totalKaryawan = Karyawan::where('role', '!=', 'admin')->count();

        // Hitung status karyawan sekaligus
        $statusCounts = Karyawan::selectRaw("
            SUM(status = 'permanent') as permanent,
            SUM(status = 'contract') as contract,
            SUM(status = 'outsource') as outsource
        ")
            ->where('role', '!=', 'admin')
            ->first();

        // Persentase status
        $permanent = $totalKaryawan > 0 ? ($statusCounts->permanent / $totalKaryawan) * 100 : 0;
        $contract = $totalKaryawan > 0 ? ($statusCounts->contract / $totalKaryawan) * 100 : 0;
        $outsource = $totalKaryawan > 0 ? ($statusCounts->outsource / $totalKaryawan) * 100 : 0;

        // Pengumuman terbaru
        $attachment = Pengumuman::latest()
            ->limit(4)
            ->get();

        // Absensi terbaru
        $absensi = AbsensiKaryawan::with('karyawan')
            ->where('is_change_day', false)
            ->whereDate('tanggal', today())
            ->latest('created_at')
            ->limit(5)
            ->get();

        // Statistik absensi
        $attendanceCounts = AbsensiKaryawan::selectRaw('
            COUNT(*) as total,
            SUM(status_kehadiran = ?) as pending,
            SUM(status_kehadiran = ?) as present,
            SUM(status_kehadiran = ?) as permit,
            SUM(status_kehadiran = ?) as sick,
            SUM(status_kehadiran = ?) as absent
        ', [
            AbsensiKaryawan::STATUS_PENDING,
            AbsensiKaryawan::STATUS_PRESENT,
            AbsensiKaryawan::STATUS_PERMIT,
            AbsensiKaryawan::STATUS_SICK,
            AbsensiKaryawan::STATUS_ABSENT,
        ])
            ->where('is_change_day', false)
            ->whereDate('tanggal', today())
            ->first();

        $statistics = [
            'total' => (int) $attendanceCounts->total,
            'pending' => (int) $attendanceCounts->pending,
            'present' => (int) $attendanceCounts->present,
            'permit' => (int) $attendanceCounts->permit,
            'sick' => (int) $attendanceCounts->sick,
            'absent' => (int) $attendanceCounts->absent,
        ];

        return view('admin.dashboard', compact(
            'totalKaryawan',
            'permanent',
            'contract',
            'outsource',
            'attachment',
            'absensi',
            'statistics'
        ));
    }

    public function karyawan()
    {
        $karyawans = Karyawan::where('role', '!=', 'admin')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.karyawan.index', compact('karyawans'));
    }

    public function storeKaryawan(Request $request)
    {
        try {

            $validated = $request->validate([
                'email' => 'required|email|unique:karyawans,email',
                'nama_lengkap' => 'required|string|max:255',
                'kata_sandi' => 'required|min:6',
                'role' => 'required|in:admin,hr,karyawan',
                'status' => 'required|in:Permanent,Contract,Outsource',

                'nik' => 'nullable|string|max:50',
                'nomor_telepon' => 'nullable|string|max:30',
                'alamat' => 'nullable|string',
                'npwp' => 'nullable|string|max:50',
                'tempat_lahir' => 'nullable|string|max:100',
                'tanggal_lahir' => 'nullable|date',
                'jenis_kelamin' => 'nullable|in:L,P',
                'agama' => 'nullable|string|max:50',
                'status_pernikahan' => 'nullable|string|max:50',
                'pendidikan_terakhir' => 'nullable|string|max:100',
                'universitas' => 'nullable|string|max:150',
                'jurusan' => 'nullable|string|max:150',
                'tahun_lulus' => 'nullable|digits:4',
                'nama_kontak_darurat' => 'nullable|string|max:100',
                'telepon_kontak_darurat' => 'nullable|string|max:30',
                'tanggal_bergabung' => 'nullable|date',
                'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            // Generate NIP
            $lastKaryawan = Karyawan::latest('id')->first();

            $newNip = 'EMP'.str_pad(
                ($lastKaryawan ? $lastKaryawan->id + 1 : 1),
                4,
                '0',
                STR_PAD_LEFT
            );

            // Upload foto
            $fotoPath = null;

            if ($request->hasFile('foto_profil')) {
                $fotoPath = $request->file('foto_profil')
                    ->store('karyawan', 'public');
            }

            Karyawan::create([
                'nip' => $newNip,
                'email' => $validated['email'],
                'kata_sandi' => Hash::make($validated['kata_sandi']),
                'nama_lengkap' => $validated['nama_lengkap'],
                'role' => $validated['role'],
                'status' => $validated['status'],

                'foto_profil' => $fotoPath,
                'nomor_telepon' => $validated['nomor_telepon'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
                'tanggal_bergabung' => $validated['tanggal_bergabung'] ?? now(),
                'nik' => $validated['nik'] ?? null,
                'npwp' => $validated['npwp'] ?? null,
                'tempat_lahir' => $validated['tempat_lahir'] ?? null,
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
                'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                'agama' => $validated['agama'] ?? null,
                'status_pernikahan' => $validated['status_pernikahan'] ?? null,
                'pendidikan_terakhir' => $validated['pendidikan_terakhir'] ?? null,
                'universitas' => $validated['universitas'] ?? null,
                'jurusan' => $validated['jurusan'] ?? null,
                'tahun_lulus' => $validated['tahun_lulus'] ?? null,
                'nama_kontak_darurat' => $validated['nama_kontak_darurat'] ?? null,
                'telepon_kontak_darurat' => $validated['telepon_kontak_darurat'] ?? null,
            ]);

            return redirect()
                ->route('admin.karyawan')
                ->with('success', 'Employee added successfully');

        } catch (\Throwable $th) {

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $th->getMessage());
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

        $validated = $request->validate([
            'email' => 'required|email|unique:karyawans,email,'.$id,
            'nama_lengkap' => 'required|string|max:255',
            'role' => 'required|in:admin,hr,karyawan',
            'status' => 'required|in:Permanent,Contract,Outsource',

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
            'pendidikan_terakhir' => 'nullable|string|max:100',
            'universitas' => 'nullable|string|max:150',
            'jurusan' => 'nullable|string|max:150',
            'tahun_lulus' => 'nullable|digits:4',
            'nama_kontak_darurat' => 'nullable|string|max:100',
            'telepon_kontak_darurat' => 'nullable|string|max:30',
            'tanggal_bergabung' => 'nullable|date',
            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $updateData = [
            'email' => $validated['email'],
            'nama_lengkap' => $validated['nama_lengkap'],
            'role' => $validated['role'],
            'status' => $validated['status'],

            'nomor_telepon' => $validated['nomor_telepon'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
            'tanggal_bergabung' => $validated['tanggal_bergabung'] ?? $karyawan->tanggal_bergabung,
            'nik' => $validated['nik'] ?? null,
            'npwp' => $validated['npwp'] ?? null,
            'tempat_lahir' => $validated['tempat_lahir'] ?? null,
            'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
            'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
            'agama' => $validated['agama'] ?? null,
            'status_pernikahan' => $validated['status_pernikahan'] ?? null,
            'pendidikan_terakhir' => $validated['pendidikan_terakhir'] ?? null,
            'universitas' => $validated['universitas'] ?? null,
            'jurusan' => $validated['jurusan'] ?? null,
            'tahun_lulus' => $validated['tahun_lulus'] ?? null,
            'nama_kontak_darurat' => $validated['nama_kontak_darurat'] ?? null,
            'telepon_kontak_darurat' => $validated['telepon_kontak_darurat'] ?? null,
        ];

        if ($request->filled('kata_sandi')) {
            $updateData['kata_sandi'] = Hash::make($validated['kata_sandi']);
        }

        if ($request->hasFile('foto_profil')) {
            $fotoPath = $request->file('foto_profil')->store('karyawan', 'public');
            $updateData['foto_profil'] = $fotoPath;
        }

        $karyawan->update($updateData);

        return redirect()
            ->route('admin.karyawan')
            ->with('success', 'Employee updated successfully');
    }

    public function destroyKaryawan($id)
    {
        $karyawan = Karyawan::findOrFail($id);

        // Cegah hapus akun sendiri
        if ($karyawan->id === auth()->id()) {
            return redirect()
                ->route('admin.karyawan')
                ->with('error', 'You cannot delete your own account');
        }

        // Hapus foto profil jika ada
        if (! empty($karyawan->foto_profil) && Storage::disk('public')->exists($karyawan->foto_profil)) {
            Storage::disk('public')->delete($karyawan->foto_profil);
        }

        $karyawan->delete();

        return redirect()
            ->route('admin.karyawan')
            ->with('success', 'Employee deleted successfully');
    }

    public function getEmployeeDetail($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $currentMonth = Carbon::now()->month;
        $currentYear  = Carbon::now()->year;

        // Attendance rate (all-time)
        $allAttendances = AbsensiKaryawan::where('karyawan_id', $id)
            ->whereIn('status_kehadiran', ['present', 'pending', 'permit', 'sick'])
            ->where('is_change_day', false)
            ->count();

        $totalWorkingDays = 0;
        if ($karyawan->tanggal_bergabung) {
            $totalWorkingDays = $this->countWeekdays(
                Carbon::parse($karyawan->tanggal_bergabung)->startOfDay(),
                Carbon::now()->startOfDay()
            );
        }
        $attendanceRate = $totalWorkingDays > 0
            ? round(($allAttendances / $totalWorkingDays) * 100, 1)
            : 0;

        // Present this month
        $presentCount = AbsensiKaryawan::where('karyawan_id', $id)
            ->whereMonth('tanggal', $currentMonth)
            ->whereYear('tanggal', $currentYear)
            ->where('status_kehadiran', 'present')
            ->count();

        // Late this month
        $lateCount = 0;
        $monthRecords = AbsensiKaryawan::where('karyawan_id', $id)
            ->whereMonth('tanggal', $currentMonth)
            ->whereYear('tanggal', $currentYear)
            ->where('status_kehadiran', 'present')
            ->whereNotNull('jam_masuk')
            ->get();
        foreach ($monthRecords as $r) {
            if (Carbon::parse($r->jam_masuk)->format('H:i:s') > '08:00:00') $lateCount++;
        }

        // Absent this month — only count records explicitly marked as 'absent'
        $absentCount = AbsensiKaryawan::where('karyawan_id', $id)
            ->whereMonth('tanggal', $currentMonth)
            ->whereYear('tanggal', $currentYear)
            ->where('is_change_day', false)
            ->where('status_kehadiran', AbsensiKaryawan::STATUS_ABSENT)
            ->count();

        // Recent attendances (last 5)
        $recentAttendances = AbsensiKaryawan::where('karyawan_id', $id)
            ->where('is_change_day', false)
            ->orderBy('tanggal', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($a) => [
                'tanggal'    => $a->tanggal->format('d M Y'),
                'jam_masuk'  => $a->jam_masuk  ? Carbon::parse($a->jam_masuk)->format('H:i')  : '-',
                'jam_pulang' => $a->jam_pulang ? Carbon::parse($a->jam_pulang)->format('H:i') : '-',
                'status'     => $a->status_kehadiran,
            ]);

        // Leave usage
        $annualUsed    = PengajuanCuti::where('karyawan_id', $id)->where('jenis_cuti', 'tahunan')->whereIn('status', ['disetujui', 'approved'])->sum('total_hari');
        $sickUsed      = PengajuanCuti::where('karyawan_id', $id)->where('jenis_cuti', 'sakit')->whereIn('status', ['disetujui', 'approved'])->sum('total_hari');
        $emergencyUsed = PengajuanCuti::where('karyawan_id', $id)->where('jenis_cuti', 'penting')->whereIn('status', ['disetujui', 'approved'])->sum('total_hari');
        $otherUsed     = PengajuanCuti::where('karyawan_id', $id)->where('jenis_cuti', 'lainnya')->whereIn('status', ['disetujui', 'approved'])->sum('total_hari');

        $leaveRequests = PengajuanCuti::where('karyawan_id', $id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($l) => [
                'tanggal_mulai'   => $l->tanggal_mulai->format('d/m/Y'),
                'tanggal_selesai' => $l->tanggal_selesai->format('d/m/Y'),
                'jenis_cuti'      => $l->jenis_cuti_label,
                'total_hari'      => $l->total_hari,
                'status'          => $l->status,
            ]);

        // Performance
        $latestPerf = Performa::where('karyawan_id', $id)
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->first();

        $prevPerf = null;
        if ($latestPerf) {
            $prevMonth = $latestPerf->bulan - 1;
            $prevYear  = $latestPerf->tahun;
            if ($prevMonth === 0) { $prevMonth = 12; $prevYear--; }
            $prevPerf = Performa::where('karyawan_id', $id)
                ->where('tahun', $prevYear)->where('bulan', $prevMonth)->first();
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
                'rate'    => $attendanceRate,
                'present' => $presentCount,
                'late'    => $lateCount,
                'absent'  => $absentCount,
                'recent'  => $recentAttendances,
            ],
            'leave' => [
                'annual_used'    => (int) $annualUsed,
                'annual_quota'   => 12,
                'sick_used'      => (int) $sickUsed,
                'sick_quota'     => 12,
                'emergency_used' => (int) $emergencyUsed,
                'emergency_quota'=> 12,
                'other_used'     => (int) $otherUsed,
                'other_quota'    => 12,
                'requests'       => $leaveRequests,
            ],
            'performance' => [
                'latest_score' => $latestPerf?->performance_score ?? 0,
                'rating_label' => $latestPerf?->rating['label'] ?? 'No Data',
                'rating_color' => $latestPerf?->rating['color'] ?? 'gray',
                'change'       => $perfChange,
                'quality'      => $latestPerf?->quality ?? 0,
                'productivity' => $latestPerf?->productivity ?? 0,
                'teamwork'     => $latestPerf?->teamwork ?? 0,
                'discipline'   => $latestPerf?->discipline ?? 0,
                'kpi_score'    => $latestPerf?->kpi_score ?? 0,
                'history'      => ['months' => $histMonths, 'scores' => $histScores],
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
        if ($start->gt($end)) return 0;

        $totalDays  = $start->diffInDays($end) + 1; // inclusive
        $fullWeeks  = intdiv($totalDays, 7);
        $weekdays   = $fullWeeks * 6;
        $extra      = $totalDays % 7;
        $dow        = $start->dayOfWeek; // 0=Sun … 6=Sat

        for ($i = 0; $i < $extra; $i++) {
            $d = ($dow + $i) % 7;
            if ($d >= 1 && $d <= 6) $weekdays++;
        }

        return $weekdays;
    }
}
