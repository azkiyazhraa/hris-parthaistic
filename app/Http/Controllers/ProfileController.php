<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\AbsensiKaryawan;
use App\Models\PengajuanCuti;
use App\Models\Performa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        $karyawan = auth()->user();

        // Attendance data
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Attendance rate
        $allAttendances = AbsensiKaryawan::where('karyawan_id', $karyawan->id)
            ->whereIn('status_kehadiran', ['present', 'pending', 'change_day', 'leave'])
            ->where('is_change_day', false)
            ->count();

        $totalWorkingDays = 0;
        if ($karyawan->tanggal_bergabung) {
            $totalWorkingDays = $this->countWeekdays(Carbon::parse($karyawan->tanggal_bergabung)->startOfDay(), Carbon::now()->startOfDay());
        }
        $attendanceRate = $totalWorkingDays > 0 ? round(($allAttendances / $totalWorkingDays) * 100, 1) : 0;

        // Present this month
        $presentCount = AbsensiKaryawan::where('karyawan_id', $karyawan->id)->whereMonth('tanggal', $currentMonth)->whereYear('tanggal', $currentYear)->where('status_kehadiran', 'present')->count();

        // Late this month
        $lateCount = 0;
        $monthRecords = AbsensiKaryawan::where('karyawan_id', $karyawan->id)->whereMonth('tanggal', $currentMonth)->whereYear('tanggal', $currentYear)->where('status_kehadiran', 'present')->whereNotNull('jam_masuk')->get();
        foreach ($monthRecords as $r) {
            if (Carbon::parse($r->jam_masuk)->format('H:i:s') > '08:00:00') {
                $lateCount++;
            }
        }

        // Absent this month
        $absentCount = AbsensiKaryawan::where('karyawan_id', $karyawan->id)->whereMonth('tanggal', $currentMonth)->whereYear('tanggal', $currentYear)->where('is_change_day', false)->where('status_kehadiran', AbsensiKaryawan::STATUS_ABSENT)->count();

        // Recent attendances
        $recentAttendances = AbsensiKaryawan::where('karyawan_id', $karyawan->id)->where('is_change_day', false)->orderBy('tanggal', 'desc')->limit(5)->get();

        // Leave usage & quotas
        $leaveUsed = fn(string $jenis) => (int) PengajuanCuti::where('karyawan_id', $karyawan->id)
            ->where('jenis_cuti', $jenis)
            ->whereIn('status', ['disetujui', 'approved'])
            ->sum('total_hari');

        $annualLeaveUsed      = $leaveUsed('tahunan');
        $annualLeaveQuota     = 12;

        $maternityLeaveUsed   = $leaveUsed('melahirkan');
        $maternityLeaveQuota  = $karyawan->jenis_kelamin === 'P' ? 90 : 3;
        $maternityLeaveLabel  = $karyawan->jenis_kelamin === 'P' ? 'Maternity Leave' : 'Paternity Leave';

        $marriageLeaveUsed    = $leaveUsed('menikah');
        $marriageLeaveQuota   = 3;

        $bereavementLeaveUsed  = $leaveUsed('duka');
        $bereavementLeaveQuota = 2;

        $leaveRequests = PengajuanCuti::where('karyawan_id', $karyawan->id)->orderBy('created_at', 'desc')->limit(5)->get()->map(
            fn($l) => [
                'tanggal_mulai' => $l->tanggal_mulai->format('d/m/Y'),
                'tanggal_selesai' => $l->tanggal_selesai->format('d/m/Y'),
                'jenis_cuti' => $l->jenis_cuti_label,
                'total_hari' => $l->total_hari,
                'status' => $l->status,
            ],
        );

        // Performance
        $latestPerformance = Performa::where('karyawan_id', $karyawan->id)->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->first();

        $prevPerf = null;
        if ($latestPerformance) {
            $prevMonth = $latestPerformance->bulan - 1;
            $prevYear = $latestPerformance->tahun;
            if ($prevMonth === 0) {
                $prevMonth = 12;
                $prevYear--;
            }
            $prevPerf = Performa::where('karyawan_id', $karyawan->id)->where('tahun', $prevYear)->where('bulan', $prevMonth)->first();
        }

        $performanceChange = 0;
        if ($latestPerformance && $prevPerf) {
            $performanceChange = $latestPerformance->performance_score - $prevPerf->performance_score;
        } elseif ($latestPerformance) {
            $performanceChange = $latestPerformance->performance_score;
        }

        $taskCompletionRate = $latestPerformance?->kpi_score ?? 0;
        $todoTasks = 0;
        $inProgressTasks = 0;
        $doneTasks = 0;

        return view('profile.edit', compact(
            'karyawan', 'attendanceRate', 'presentCount', 'lateCount', 'absentCount', 'recentAttendances',
            'annualLeaveUsed', 'annualLeaveQuota',
            'maternityLeaveUsed', 'maternityLeaveQuota', 'maternityLeaveLabel',
            'marriageLeaveUsed', 'marriageLeaveQuota',
            'bereavementLeaveUsed', 'bereavementLeaveQuota',
            'leaveRequests', 'latestPerformance', 'performanceChange', 'taskCompletionRate', 'todoTasks', 'inProgressTasks', 'doneTasks'
        ));
    }

    /**
     * Update profile karyawan
     * BANK: Nama bank selalu BSI, tidak bisa diubah
     */
    public function update(Request $request)
    {
        $karyawan = auth()->user();

        try {
            // Jika hanya upload foto (dari AJAX)
            if ($request->hasFile('foto_profil') && !$request->has('nama_depan') && !$request->has('email')) {
                $request->validate([
                    'foto_profil' => 'image|mimes:jpg,jpeg,png|max:2048',
                ]);

                $fotoPath = $request->file('foto_profil')->store('karyawan', 'public');

                // Hapus foto lama
                if (!empty($karyawan->foto_profil) && Storage::disk('public')->exists($karyawan->foto_profil)) {
                    Storage::disk('public')->delete($karyawan->foto_profil);
                }

                $karyawan->update(['foto_profil' => $fotoPath]);

                if ($request->ajax()) {
                    return response()->json(['success' => true, 'message' => 'Photo updated successfully']);
                }
                return redirect()->route('profile.edit')->with('success', 'Photo updated successfully');
            }

            // Validasi untuk update profil lengkap
            $validated = $request->validate([
                'nama_depan' => 'required|string|max:100',
                'nama_belakang' => 'required|string|max:100',
                'email' => ['required', 'email', Rule::unique('karyawans')->ignore($karyawan->id)],
                'nomor_telepon' => 'nullable|string|max:30',
                'alamat' => 'nullable|string',
                'tempat_lahir' => 'nullable|string|max:100',
                'tanggal_lahir' => 'nullable|date',
                'jenis_kelamin' => 'nullable|in:L,P',
                'agama' => 'nullable|string|max:50',
                'status_pernikahan' => 'nullable|string|max:50',
                'nik' => 'nullable|string|max:50',
                'npwp' => 'nullable|string|max:50',
                'pendidikan_terakhir' => 'nullable|in:SMP,SMA/MA,SMK,D1,D2,D3,D4,S1,S2',
                'universitas' => 'nullable|string|max:150',
                'jurusan' => 'nullable|string|max:150',
                'tahun_lulus' => 'nullable|digits:4',
                'nama_kontak_darurat' => 'nullable|string|max:100',
                'telepon_kontak_darurat' => 'nullable|string|max:30',
                'nama_bank' => 'nullable|string|max:50',
                'nomor_rekening' => 'nullable|string|max:30',
                'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            $updateData = [
                'nama_depan' => $validated['nama_depan'],
                'nama_belakang' => $validated['nama_belakang'],
                'nama_lengkap' => $validated['nama_depan'] . ' ' . $validated['nama_belakang'],
                'email' => $validated['email'],
                'nomor_telepon' => $validated['nomor_telepon'] ?? $karyawan->nomor_telepon,
                'alamat' => $validated['alamat'] ?? $karyawan->alamat,
                'tempat_lahir' => $validated['tempat_lahir'] ?? $karyawan->tempat_lahir,
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? $karyawan->tanggal_lahir,
                'jenis_kelamin' => $validated['jenis_kelamin'] ?? $karyawan->jenis_kelamin,
                'agama' => $validated['agama'] ?? $karyawan->agama,
                'status_pernikahan' => $validated['status_pernikahan'] ?? $karyawan->status_pernikahan,
                'nik' => $validated['nik'] ?? $karyawan->nik,
                'npwp' => $validated['npwp'] ?? $karyawan->npwp,
                'pendidikan_terakhir' => $validated['pendidikan_terakhir'] ?? $karyawan->pendidikan_terakhir,
                'pendidikan_terakhir_new' => $validated['pendidikan_terakhir'] ?? $karyawan->pendidikan_terakhir_new,
                'universitas' => $validated['universitas'] ?? $karyawan->universitas,
                'jurusan' => $validated['jurusan'] ?? $karyawan->jurusan,
                'tahun_lulus' => $validated['tahun_lulus'] ?? $karyawan->tahun_lulus,
                'nama_kontak_darurat' => $validated['nama_kontak_darurat'] ?? $karyawan->nama_kontak_darurat,
                'telepon_kontak_darurat' => $validated['telepon_kontak_darurat'] ?? $karyawan->telepon_kontak_darurat,
                // BANK: Selalu BSI, tidak bisa diubah oleh employee
                'nama_bank' => 'BSI',
                'nomor_rekening' => $validated['nomor_rekening'] ?? $karyawan->nomor_rekening,
            ];

            // Handle foto profil
            if ($request->hasFile('foto_profil')) {
                if (!empty($karyawan->foto_profil) && Storage::disk('public')->exists($karyawan->foto_profil)) {
                    Storage::disk('public')->delete($karyawan->foto_profil);
                }
                $updateData['foto_profil'] = $request->file('foto_profil')->store('karyawan', 'public');
            }

            $karyawan->update($updateData);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Profile updated successfully. Bank: BSI (cannot be changed).'
                ]);
            }
            return redirect()->route('profile.edit')->with('success', 'Profile updated successfully. Bank: BSI (cannot be changed).');
        } catch (\Throwable $th) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $th->getMessage()], 422);
            }
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error: ' . $th->getMessage());
        }
    }

    public function updatePassword(Request $request)
    {
        $karyawan = auth()->user();

        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        // Verifikasi password lama
        if (!Hash::check($validated['current_password'], $karyawan->kata_sandi)) {
            return redirect()
                ->back()
                ->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $karyawan->update([
            'kata_sandi' => Hash::make($validated['new_password']),
        ]);

        return redirect()->route('profile.edit')->with('success', 'Password updated successfully');
    }

    public function performanceChartData()
    {
        $karyawan = auth()->user();
        $currentYear = Carbon::now()->year;

        $histMonths = [];
        $histScores = [];
        for ($m = 1; $m <= 12; $m++) {
            $p = Performa::where('karyawan_id', $karyawan->id)->where('tahun', $currentYear)->where('bulan', $m)->first();
            $histMonths[] = Carbon::create($currentYear, $m, 1)->format('M');
            $histScores[] = $p?->performance_score ?? 0;
        }

        return response()->json([
            'months' => $histMonths,
            'scores' => $histScores,
        ]);
    }

    private function countWeekdays(Carbon $start, Carbon $end): int
    {
        if ($start->gt($end)) {
            return 0;
        }

        $totalDays = $start->diffInDays($end) + 1;
        $fullWeeks = intdiv($totalDays, 7);
        $weekdays = $fullWeeks * 6;
        $extra = $totalDays % 7;
        $dow = $start->dayOfWeek;

        for ($i = 0; $i < $extra; $i++) {
            $d = ($dow + $i) % 7;
            if ($d >= 1 && $d <= 6) {
                $weekdays++;
            }
        }

        return $weekdays;
    }
}
