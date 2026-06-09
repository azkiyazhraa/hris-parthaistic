<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\AbsensiKaryawan;
use App\Models\PengajuanCuti;
use App\Models\Performa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $karyawan = Auth::user();

        // Data untuk tab Attendance & Leave
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Hitung attendance rate all time
        $allAttendances = AbsensiKaryawan::where('karyawan_id', $karyawan->id)
            ->whereIn('status_kehadiran', ['hadir', 'masuk', 'izin', 'sakit'])
            ->count();

        $totalWorkingDaysAllTime = $this->getTotalWorkingDaysAllTime($karyawan);
        $attendanceRate = $totalWorkingDaysAllTime > 0 ? round(($allAttendances / $totalWorkingDaysAllTime) * 100, 1) : 0;

        // Hitung present, late, absent untuk bulan ini
        $presentCount = AbsensiKaryawan::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $currentMonth)
            ->whereYear('tanggal', $currentYear)
            ->whereIn('status_kehadiran', ['hadir', 'masuk'])
            ->count();

        $lateCount = $this->calculateLateCount($karyawan->id, $currentMonth, $currentYear);

        // Hitung absent untuk bulan ini
        $totalWorkingDays = $this->getWorkingDaysInMonth($currentMonth, $currentYear);
        $recordedDays = AbsensiKaryawan::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $currentMonth)
            ->whereYear('tanggal', $currentYear)
            ->whereIn('status_kehadiran', ['hadir', 'masuk', 'izin', 'sakit'])
            ->count();
        $absentCount = max(0, $totalWorkingDays - $recordedDays);

        // Recent attendances (last 5)
        $recentAttendances = AbsensiKaryawan::where('karyawan_id', $karyawan->id)
            ->orderBy('tanggal', 'desc')
            ->limit(5)
            ->get();

        // Leave quotas and usage
        $annualLeaveUsed = PengajuanCuti::where('karyawan_id', $karyawan->id)
            ->where('jenis_cuti', 'tahunan')
            ->whereIn('status', ['disetujui', 'approved'])
            ->sum('total_hari');

        $sickLeaveUsed = PengajuanCuti::where('karyawan_id', $karyawan->id)
            ->where('jenis_cuti', 'sakit')
            ->whereIn('status', ['disetujui', 'approved'])
            ->sum('total_hari');

        $emergencyLeaveUsed = PengajuanCuti::where('karyawan_id', $karyawan->id)
            ->where('jenis_cuti', 'penting')
            ->whereIn('status', ['disetujui', 'approved'])
            ->sum('total_hari');

        $otherLeaveUsed = PengajuanCuti::where('karyawan_id', $karyawan->id)
            ->where('jenis_cuti', 'lainnya')
            ->whereIn('status', ['disetujui', 'approved'])
            ->sum('total_hari');

        // Leave requests (all status)
        $leaveRequests = PengajuanCuti::where('karyawan_id', $karyawan->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Data untuk tab Performance
        $latestPerformance = Performa::where('karyawan_id', $karyawan->id)
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->first();

        // Calculate performance change
        $previousPerformance = Performa::where('karyawan_id', $karyawan->id)
            ->where('tahun', $latestPerformance?->tahun ?? Carbon::now()->year)
            ->where('bulan', ($latestPerformance?->bulan ?? Carbon::now()->month) - 1)
            ->first();

        $performanceChange = 0;
        if ($latestPerformance && $previousPerformance) {
            $performanceChange = $latestPerformance->performance_score - $previousPerformance->performance_score;
        } elseif ($latestPerformance) {
            $performanceChange = $latestPerformance->performance_score;
        }

        // Task summary (example data - adjust based on your actual task system)
        $todoTasks = 5;
        $inProgressTasks = 3;
        $doneTasks = 8;
        $totalTasks = $todoTasks + $inProgressTasks + $doneTasks;
        $taskCompletionRate = $totalTasks > 0 ? round(($doneTasks / $totalTasks) * 100) : 0;

        return view('profile.edit', compact(
            'karyawan',
            'attendanceRate',
            'presentCount',
            'lateCount',
            'absentCount',
            'recentAttendances',
            'annualLeaveUsed',
            'sickLeaveUsed',
            'emergencyLeaveUsed',
            'otherLeaveUsed',
            'leaveRequests',
            'latestPerformance',
            'performanceChange',
            'todoTasks',
            'inProgressTasks',
            'doneTasks',
            'taskCompletionRate'
        ));
    }

    public function update(Request $request)
    {
        $karyawan = Auth::user();

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:karyawans,email,' . $karyawan->id,
            'alamat' => 'nullable|string',
            'tempat_lahir' => 'nullable|string|max:100',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'status_pernikahan' => 'nullable|in:Belum Menikah,Menikah,Cerai',
            'nomor_telepon' => 'nullable|string|max:20',
            'nik' => 'nullable|string|max:20',
            'npwp' => 'nullable|string|max:20',
            'tanggal_lahir' => 'nullable|date',
            'agama' => 'nullable|string|max:20',
            'pendidikan_terakhir' => 'nullable|string|max:100',
            'universitas' => 'nullable|string|max:200',
            'jurusan' => 'nullable|string|max:200',
            'tahun_lulus' => 'nullable|integer|min:1900|max:' . date('Y'),
            'nama_kontak_darurat' => 'nullable|string|max:255',
            'telepon_kontak_darurat' => 'nullable|string|max:20',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('foto_profil')) {
            if ($karyawan->foto_profil && Storage::disk('public')->exists($karyawan->foto_profil)) {
                Storage::disk('public')->delete($karyawan->foto_profil);
            }

            $path = $request->file('foto_profil')->store('profile-photos', 'public');
            $validated['foto_profil'] = $path;
        }

        $karyawan->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Profile updated successfully']);
        }

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $karyawan = Auth::user();

        if (!Hash::check($request->current_password, $karyawan->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $karyawan->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('profile.edit')->with('success', 'Password updated successfully.');
    }

    public function performanceChartData(Request $request)
    {
        $karyawan = Auth::user();
        $year = $request->get('year', Carbon::now()->year);

        $months = [];
        $scores = [];

        for ($month = 1; $month <= 12; $month++) {
            $performance = Performa::where('karyawan_id', $karyawan->id)
                ->where('tahun', $year)
                ->where('bulan', $month)
                ->first();

            $months[] = Carbon::create($year, $month, 1)->format('M');
            $scores[] = $performance?->performance_score ?? 0;
        }

        return response()->json([
            'months' => $months,
            'scores' => $scores,
        ]);
    }

    private function getWorkingDaysInMonth($month, $year)
    {
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $today = Carbon::now();
        if ($endDate->gt($today) && $startDate->lte($today)) {
            $endDate = $today->copy();
        }

        $workingDays = 0;
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            if ($currentDate->dayOfWeek >= Carbon::MONDAY && $currentDate->dayOfWeek <= Carbon::FRIDAY) {
                $workingDays++;
            }
            $currentDate->addDay();
        }

        return $workingDays;
    }

    private function getTotalWorkingDaysAllTime($karyawan)
    {
        if (!$karyawan->tanggal_bergabung) {
            return 0;
        }

        $startDate = Carbon::parse($karyawan->tanggal_bergabung);
        $endDate = Carbon::now();

        $workingDays = 0;
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            if ($currentDate->dayOfWeek >= Carbon::MONDAY && $currentDate->dayOfWeek <= Carbon::FRIDAY) {
                $workingDays++;
            }
            $currentDate->addDay();
        }

        return $workingDays;
    }

    private function calculateLateCount($karyawanId, $month, $year)
    {
        $jamMasukNormal = Carbon::parse('08:00:00');

        $absensi = AbsensiKaryawan::where('karyawan_id', $karyawanId)
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->whereIn('status_kehadiran', ['hadir', 'masuk'])
            ->whereNotNull('jam_masuk')
            ->get();

        $lateCount = 0;

        foreach ($absensi as $record) {
            if ($record->jam_masuk) {
                $jamMasuk = Carbon::parse($record->jam_masuk);
                if ($jamMasuk->format('H:i:s') > $jamMasukNormal->format('H:i:s')) {
                    $lateCount++;
                }
            }
        }

        return $lateCount;
    }
}
