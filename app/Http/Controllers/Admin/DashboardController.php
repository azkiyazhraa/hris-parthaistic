<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsensiKaryawan;
use App\Models\Karyawan;
use App\Models\PengajuanCuti;
use App\Models\Pengumuman;
use App\Models\Performa;
use App\Services\TrackerApiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    // Tanggal berdiri Parthaistic
    const COMPANY_FOUNDED = '2021-02-24';

    /**
     * Hitung tahun operasional ke-berapa berdasarkan tanggal join.
     * Tahun ke-1: 24 Feb 2021 s/d 23 Feb 2022
     * Tahun ke-2: 24 Feb 2022 s/d 23 Feb 2023
     * dst.
     */
    private function getOperationalYear(Carbon $joinDate): int
    {
        $founded = Carbon::parse(self::COMPANY_FOUNDED)->startOfDay();
        $join = $joinDate->copy()->startOfDay();

        // Jika join sebelum tanggal berdiri, anggap tahun ke-1
        if ($join->lt($founded)) {
            return 1;
        }

        // Anniversary tahun ini (berdasarkan tahun join)
        $anniversaryThisYear = Carbon::create($join->year, 2, 24)->startOfDay();

        // Jika join SEBELUM anniversary di tahun itu, pakai tahun sebelumnya sebagai acuan
        if ($join->lt($anniversaryThisYear)) {
            $yearDiff = $join->year - $founded->year;
        } else {
            $yearDiff = $join->year - $founded->year + 1;
        }

        // Tahun operasional minimum 1
        return max(1, $yearDiff);
    }

    /**
     * Generate NIP otomatis dengan format: [1][XX][G][NNN]
     * 1   — kode perusahaan Parthaistic
     * XX  — tahun operasional ke-berapa (2 digit, zero-padded)
     * G   — jenis kelamin (1=L, 2=P)
     * NNN — nomor urut karyawan global (3 digit)
     *
     * Contoh: 1051033 = Parthaistic, tahun ke-5, laki-laki, karyawan ke-33
     */
    private function generateNip(Carbon $joinDate, string $jenisKelamin): string
    {
        $XX = str_pad($this->getOperationalYear($joinDate), 2, '0', STR_PAD_LEFT);

        $G = match (strtoupper($jenisKelamin)) {
            'L' => '1',
            'P' => '2',
            default => '0',
        };

        $NNN = str_pad(Karyawan::where('role', 'karyawan')->count() + 1, 3, '0', STR_PAD_LEFT);

        return '1' . $XX . $G . $NNN;
    }

    public function index()
    {
        $totalKaryawan = Karyawan::where('role', '!=', 'admin')->where('role', '!=', 'hr')->count();

        $fulltime = Karyawan::where('role', '!=', 'admin')->where('role', '!=', 'hr')->where('status', 'Full-time')->count();
        $contract = Karyawan::where('role', '!=', 'admin')->where('role', '!=', 'hr')->where('status', 'Contract')->count();
        $internship = Karyawan::where('role', '!=', 'admin')->where('role', '!=', 'hr')->where('status', 'Internship')->count();
        $resigned = Karyawan::where('role', '!=', 'admin')->where('role', '!=', 'hr')->where('status', 'Resigned')->count();
        $contractEnded = Karyawan::where('role', '!=', 'admin')->where('role', '!=', 'hr')->where('status', 'Contract Ended')->count();
        $internshipCompleted = Karyawan::where('role', '!=', 'admin')->where('role', '!=', 'hr')->where('status', 'Internship Completed')->count();
        $terminated = Karyawan::where('role', '!=', 'admin')->where('role', '!=', 'hr')->where('status', 'Terminated')->count();

        $fulltimePercent = $totalKaryawan > 0 ? ($fulltime / $totalKaryawan) * 100 : 0;
        $contractPercent = $totalKaryawan > 0 ? ($contract / $totalKaryawan) * 100 : 0;
        $internshipPercent = $totalKaryawan > 0 ? ($internship / $totalKaryawan) * 100 : 0;

        $resignedEmployees = $resigned + $contractEnded + $internshipCompleted + $terminated;

        $attachment = Pengumuman::latest()->limit(4)->get();

        $absensi = AbsensiKaryawan::with('karyawan')->where('is_change_day', false)->whereDate('tanggal', today())->latest('created_at')->limit(5)->get();

        $attendanceCounts = AbsensiKaryawan::selectRaw(
            'COUNT(*) as total,
             SUM(status_kehadiran = ?) as pending,
             SUM(status_kehadiran = ?) as present,
             SUM(status_kehadiran = ?) as change_day,
             SUM(status_kehadiran = ?) as `leave`,
             SUM(status_kehadiran = ?) as absent',
            [AbsensiKaryawan::STATUS_PENDING, AbsensiKaryawan::STATUS_PRESENT, AbsensiKaryawan::STATUS_CHANGE_DAY, AbsensiKaryawan::STATUS_LEAVE, AbsensiKaryawan::STATUS_ABSENT],
        )
            ->where('is_change_day', false)
            ->whereDate('tanggal', today())
            ->first();

        $statistics = [
            'total' => (int) ($attendanceCounts->total ?? 0),
            'pending' => (int) ($attendanceCounts->pending ?? 0),
            'present'    => (int) ($attendanceCounts->present    ?? 0),
            'change_day' => (int) ($attendanceCounts->change_day ?? 0),
            'leave'      => (int) ($attendanceCounts->leave      ?? 0),
            'absent'     => (int) ($attendanceCounts->absent     ?? 0),
        ];

        // Task aggregate dari latest performa tiap karyawan aktif
        $activeKaryawanIds = Karyawan::whereIn('status', ['Full-time', 'Contract', 'Internship'])
            ->where('role', '!=', 'admin')
            ->where('role', '!=', 'hr')
            ->pluck('id');

        $latestPerformaIds = Performa::whereIn('karyawan_id', $activeKaryawanIds)
            ->selectRaw('MAX(id) as id')
            ->groupBy('karyawan_id')
            ->pluck('id');

        $taskAggregate = Performa::whereIn('id', $latestPerformaIds)
            ->selectRaw('COALESCE(SUM(task_done), 0) as total_done, COALESCE(SUM(task_target), 0) as total_target, COUNT(*) as employee_count')
            ->first();

        $adminTaskDone          = (int) ($taskAggregate->total_done     ?? 0);
        $adminTaskTarget        = (int) ($taskAggregate->total_target   ?? 0);
        $adminTaskRemaining     = max(0, $adminTaskTarget - $adminTaskDone);
        $adminTaskPercent       = $adminTaskTarget > 0 ? round(($adminTaskDone / $adminTaskTarget) * 100) : 0;
        $adminTaskEmployeeCount = (int) ($taskAggregate->employee_count ?? 0);

        // Tracker stats (all employees, all-time)
        $trackerStats       = (new TrackerApiService())->getAggregatedStats();
        $trackerConnected   = $trackerStats['connected'];
        $trackerTaskTotal   = $trackerStats['total_task'];
        $trackerTaskCompleted = $trackerStats['task_completed'];

        return view('admin.dashboard', compact(
            'totalKaryawan',
            'fulltime',
            'contract',
            'internship',
            'fulltimePercent',
            'contractPercent',
            'internshipPercent',
            'resignedEmployees',
            'attachment',
            'absensi',
            'statistics',
            'adminTaskDone',
            'adminTaskTarget',
            'adminTaskRemaining',
            'adminTaskPercent',
            'adminTaskEmployeeCount',
            'trackerConnected',
            'trackerTaskTotal',
            'trackerTaskCompleted',
        ));
    }

    public function karyawan()
    {
        $karyawans = Karyawan::where('role', '!=', 'admin')->where('role', '!=', 'hr')->orderBy('nip', 'asc')->get();

        return view('admin.karyawan.index', compact('karyawans'));
    }

    /**
     * Store a newly created employee
     */
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
                'pendidikan_terakhir' => 'nullable|in:SMP,SMA/MA,SMK,D1,D2,D3,D4,S1,S2',
                'nama_bank' => 'nullable|string|max:50',
                'nomor_rekening' => 'nullable|string|max:30',
                'nik' => 'nullable|string|max:50',
                'nomor_telepon' => 'nullable|string|max:30',
                'alamat' => 'nullable|string',
                'npwp' => 'nullable|string|max:50',
                'nomor_paspor' => 'nullable|string|max:50',
                'paspor_berlaku_hingga' => 'nullable|date',
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
                'tracker_email' => 'nullable|email|max:255',
            ]);

            // Tanggal bergabung (default hari ini jika tidak diisi)
            $tanggalBergabung = Carbon::parse($validated['tanggal_bergabung'] ?? now());

            $jenisKelamin = $validated['jenis_kelamin'] ?? '';

            // Admin/HR tidak perlu NIP
            $newNip = null;
            if ($validated['role'] === 'karyawan') {
                $newNip = $this->generateNip($tanggalBergabung, $jenisKelamin);

                // Jika tabrakan, naikkan NNN sampai unik
                $nipPrefix = substr($newNip, 0, 4); // "1" + XX + G
                $nnn = Karyawan::where('role', 'karyawan')->count() + 1;
                while (Karyawan::where('nip', $newNip)->exists()) {
                    $nnn++;
                    $newNip = $nipPrefix . str_pad($nnn, 3, '0', STR_PAD_LEFT);
                }
            }

            // Upload foto profil
            $fotoPath = null;
            if ($request->hasFile('foto_profil')) {
                $fotoPath = $request->file('foto_profil')->store('karyawan', 'public');
            }

            // Buat karyawan (model boot() akan auto-hitung total_hari_kerja)
            Karyawan::create([
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
                'tanggal_bergabung' => $tanggalBergabung,
                'end_date' => $validated['end_date'] ?? null,
                'reason_resigned' => $validated['reason_resigned'] ?? null,
                'pendidikan_terakhir' => $validated['pendidikan_terakhir'] ?? null,
                'pendidikan_terakhir_new' => $validated['pendidikan_terakhir'] ?? null,
                // BANK: Selalu BSI, abaikan input form
                'nama_bank' => 'BSI',
                'nomor_rekening' => $validated['nomor_rekening'] ?? null,
                'foto_profil' => $fotoPath,
                'nomor_telepon' => $validated['nomor_telepon'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
                'nik' => $validated['nik'] ?? null,
                'npwp' => $validated['npwp'] ?? null,
                'nomor_paspor' => $validated['nomor_paspor'] ?? null,
                'paspor_berlaku_hingga' => $validated['paspor_berlaku_hingga'] ?? null,
                'tempat_lahir' => $validated['tempat_lahir'] ?? null,
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
                'jenis_kelamin' => $jenisKelamin ?: null,
                'agama' => $validated['agama'] ?? null,
                'status_pernikahan' => $validated['status_pernikahan'] ?? null,
                'universitas' => $validated['universitas'] ?? null,
                'jurusan' => $validated['jurusan'] ?? null,
                'tahun_lulus' => $validated['tahun_lulus'] ?? null,
                'nama_kontak_darurat' => $validated['nama_kontak_darurat'] ?? null,
                'telepon_kontak_darurat' => $validated['telepon_kontak_darurat'] ?? null,
                'tracker_email' => $validated['tracker_email'] ?? null,
            ]);

            return redirect()
                ->route('admin.karyawan')
                ->with('success', 'Employee added successfully.' . ($newNip ? ' NIP: ' . $newNip . ' |' : '') . ' Bank: BSI');
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

    /**
     * Update employee data
     */
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
                'pendidikan_terakhir' => 'nullable|in:SMP,SMA/MA,SMK,D1,D2,D3,D4,S1,S2',
                'nama_bank' => 'nullable|string|max:50',
                'nomor_rekening' => 'nullable|string|max:30',
                'kata_sandi' => 'nullable|min:6',
                'nik' => 'nullable|string|max:50',
                'nomor_telepon' => 'nullable|string|max:30',
                'alamat' => 'nullable|string',
                'npwp' => 'nullable|string|max:50',
                'nomor_paspor' => 'nullable|string|max:50',
                'paspor_berlaku_hingga' => 'nullable|date',
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
                'tracker_email' => 'nullable|email|max:255',
            ]);

            // Tanggal bergabung
            $tanggalBergabung = Carbon::parse($validated['tanggal_bergabung'] ?? $karyawan->tanggal_bergabung);

            // Jenis kelamin
            $jenisKelamin = $validated['jenis_kelamin'] ?? ($karyawan->jenis_kelamin ?? '');

            // Admin/HR tidak punya NIP
            $newNip = null;
            if ($validated['role'] === 'karyawan') {
                $oldJoinDate = $karyawan->tanggal_bergabung ? $karyawan->tanggal_bergabung->format('Y-m-d') : null;
                $newJoinDate = $tanggalBergabung->format('Y-m-d');
                $oldGender   = $karyawan->jenis_kelamin ?? '';
                $nipChanged  = $oldJoinDate !== $newJoinDate || $oldGender !== $jenisKelamin;

                $newNip = $karyawan->nip; // default tetap NIP lama
                if ($nipChanged || $karyawan->nip === null) {
                    // NNN dipertahankan dari NIP lama; kalau tidak ada, hitung dari jumlah karyawan
                    $oldNnn = $karyawan->nip ? (int) substr($karyawan->nip, -3)
                        : Karyawan::where('role', 'karyawan')->count() + 1;

                    $XX = str_pad($this->getOperationalYear($tanggalBergabung), 2, '0', STR_PAD_LEFT);
                    $G  = match (strtoupper($jenisKelamin)) {
                        'L'     => '1',
                        'P'     => '2',
                        default => '0',
                    };
                    $generatedNip = '1' . $XX . $G . str_pad($oldNnn, 3, '0', STR_PAD_LEFT);

                    // Jika tabrakan dengan karyawan lain, naikkan NNN
                    $nnn = $oldNnn;
                    while (Karyawan::where('nip', $generatedNip)->where('id', '!=', $id)->exists()) {
                        $nnn++;
                        $generatedNip = '1' . $XX . $G . str_pad($nnn, 3, '0', STR_PAD_LEFT);
                    }
                    $newNip = $generatedNip;
                }
            }

            // Data yang akan diupdate
            $updateData = [
                'nip' => $newNip,
                'email' => $validated['email'],
                'nama_depan' => $validated['nama_depan'],
                'nama_belakang' => $validated['nama_belakang'],
                'nama_lengkap' => $validated['nama_depan'] . ' ' . $validated['nama_belakang'],
                'role' => $validated['role'],
                'jabatan' => $validated['jabatan'] ?? null,
                'jabatan_lainnya' => ($validated['jabatan'] ?? '') === 'lainnya' ? $validated['jabatan_lainnya'] ?? null : null,
                'status' => $validated['status'],
                'tanggal_bergabung' => $tanggalBergabung,
                'end_date' => $validated['end_date'] ?? null,
                'reason_resigned' => $validated['reason_resigned'] ?? null,
                'pendidikan_terakhir' => $validated['pendidikan_terakhir'] ?? null,
                'pendidikan_terakhir_new' => $validated['pendidikan_terakhir'] ?? null,
                // BANK: Selalu BSI, abaikan input form
                'nama_bank' => 'BSI',
                'nomor_rekening' => $validated['nomor_rekening'] ?? $karyawan->nomor_rekening,
                'nomor_telepon' => $validated['nomor_telepon'] ?? $karyawan->nomor_telepon,
                'alamat' => $validated['alamat'] ?? $karyawan->alamat,
                'nik' => $validated['nik'] ?? $karyawan->nik,
                'npwp' => $validated['npwp'] ?? $karyawan->npwp,
                'nomor_paspor' => $validated['nomor_paspor'] ?? $karyawan->nomor_paspor,
                'paspor_berlaku_hingga' => $validated['paspor_berlaku_hingga'] ?? $karyawan->paspor_berlaku_hingga,
                'tempat_lahir' => $validated['tempat_lahir'] ?? $karyawan->tempat_lahir,
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? $karyawan->tanggal_lahir,
                'jenis_kelamin' => $jenisKelamin ?: $karyawan->jenis_kelamin,
                'agama' => $validated['agama'] ?? $karyawan->agama,
                'status_pernikahan' => $validated['status_pernikahan'] ?? $karyawan->status_pernikahan,
                'universitas' => $validated['universitas'] ?? $karyawan->universitas,
                'jurusan' => $validated['jurusan'] ?? $karyawan->jurusan,
                'tahun_lulus' => $validated['tahun_lulus'] ?? $karyawan->tahun_lulus,
                'nama_kontak_darurat' => $validated['nama_kontak_darurat'] ?? $karyawan->nama_kontak_darurat,
                'telepon_kontak_darurat' => $validated['telepon_kontak_darurat'] ?? $karyawan->telepon_kontak_darurat,
                'tracker_email' => $validated['tracker_email'] ?? $karyawan->tracker_email,
            ];

            // Update password jika diisi
            if ($request->filled('kata_sandi')) {
                $updateData['kata_sandi'] = Hash::make($validated['kata_sandi']);
            }

            // Update foto profil jika ada
            if ($request->hasFile('foto_profil')) {
                if (!empty($karyawan->foto_profil) && Storage::disk('public')->exists($karyawan->foto_profil)) {
                    Storage::disk('public')->delete($karyawan->foto_profil);
                }
                $updateData['foto_profil'] = $request->file('foto_profil')->store('karyawan', 'public');
            }

            $karyawan->update($updateData);

            $nipMsg = $nipChanged ? ' NIP updated to: ' . $newNip : '';
            return redirect()
                ->route('admin.karyawan')
                ->with('success', 'Employee updated successfully. Bank: BSI (cannot be changed).' . $nipMsg);
        } catch (\Throwable $th) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error: ' . $th->getMessage());
        }
    }

    public function destroyKaryawan($id)
    {
        return redirect()->route('admin.karyawan')->with('error', 'Delete feature has been disabled');
    }

    public function getEmployeeDetail($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Attendance rate (all-time)
        $allAttendances = AbsensiKaryawan::where('karyawan_id', $id)
            ->whereIn('status_kehadiran', ['present', 'pending', 'change_day', 'leave'])
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

        // Absent this month
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
        $approvedStatuses = ['disetujui', 'approved'];
        $annualUsed = PengajuanCuti::where('karyawan_id', $id)
            ->where('jenis_cuti', 'tahunan')
            ->whereIn('status', $approvedStatuses)
            ->sum('total_hari');
        $melahirkanUsed = PengajuanCuti::where('karyawan_id', $id)
            ->where('jenis_cuti', 'melahirkan')
            ->whereIn('status', $approvedStatuses)
            ->sum('total_hari');
        $menikahUsed = PengajuanCuti::where('karyawan_id', $id)
            ->where('jenis_cuti', 'menikah')
            ->whereIn('status', $approvedStatuses)
            ->sum('total_hari');
        $dukaUsed = PengajuanCuti::where('karyawan_id', $id)
            ->where('jenis_cuti', 'duka')
            ->whereIn('status', $approvedStatuses)
            ->sum('total_hari');

        $kuotaMelahirkan = $karyawan->jenis_kelamin === 'P' ? 90 : 3;
        $labelMelahirkan = $karyawan->jenis_kelamin === 'P' ? 'Maternity Leave' : 'Paternity Leave';

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

        // Performance history
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
                'annual_used'     => (int) $annualUsed,
                'annual_quota'    => 12,
                'melahirkan_used'  => (int) $melahirkanUsed,
                'melahirkan_quota' => $kuotaMelahirkan,
                'melahirkan_label' => $labelMelahirkan,
                'menikah_used'    => (int) $menikahUsed,
                'menikah_quota'   => 3,
                'duka_used'       => (int) $dukaUsed,
                'duka_quota'      => 2,
                'requests'        => $leaveRequests,
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

    public function showPassword($id)
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

        $totalDays = $start->diffInDays($end) + 1;
        $fullWeeks = intdiv($totalDays, 7);
        $weekdays = $fullWeeks * 5;
        $extra = $totalDays % 7;
        $dow = $start->dayOfWeek;

        for ($i = 0; $i < $extra; $i++) {
            $d = ($dow + $i) % 7;
            if ($d >= 1 && $d <= 5) {
                $weekdays++;
            }
        }

        return $weekdays;
    }

    public function karyawanDetail(int $id)
    {
        $karyawan = Karyawan::findOrFail($id);

        return response()->json($karyawan);
    }
}
