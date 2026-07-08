<?php

namespace App\Http\Controllers;

use App\Models\AbsensiKaryawan;
use App\Models\Karyawan;
use App\Models\Notifikasi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class AbsensiController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $karyawanId = auth()->id();
        $currentMonth = Carbon::now()->month;

        // absensi bulan ini
        $absensi = AbsensiKaryawan::where('karyawan_id', $karyawanId)
            ->whereMonth('tanggal', $currentMonth)
            ->where('is_change_day', false)
            ->orderBy('created_at', 'desc')
            ->get();

        // Absensi hari ini
        $absensiToday = AbsensiKaryawan::where('karyawan_id', $karyawanId)
            ->whereDate('tanggal', $today)
            ->where('is_change_day', false)
            ->first();

        // ABSENSI BULAN INI
        $monthAbcense = AbsensiKaryawan::where('karyawan_id', $karyawanId)
            ->whereMonth('tanggal', $currentMonth)
            ->where('is_change_day', false)
            ->get();

        return view('absensi.index', compact('absensi', 'absensiToday', 'monthAbcense'));
    }

    public function show($id)
    {
        $absensi = AbsensiKaryawan::with(['karyawan', 'disetujuiOleh'])
            ->where('id', $id)
            ->firstOrFail();

        return response()->json($absensi);
    }

    public function create()
    {
        // Method ini tidak dipakai karena modal di-include langsung di index
        // Redirect ke index
        return redirect()->route('absensi.index');
    }

    public function store(Request $request)
    {
        // Validasi dasar dulu
        $request->validate([
            'jenis_absensi' => 'required|in:checkin,permit,sick,change_day',
        ]);

        // Validasi per jenis
        if ($request->jenis_absensi === 'checkin') {
            $request->validate([
                'lokasi_masuk' => 'required|string|max:255',
                'keterangan' => 'nullable',
                'attachment' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);
        } elseif ($request->jenis_absensi === 'change_day') {
            $request->validate([
                'change_day_tanggal_awal' => 'required|date',
                'change_day_tanggal_akhir' => 'required|date|after_or_equal:change_day_tanggal_awal',
                'change_day_jam_mulai' => 'required',
                'change_day_jam_selesai' => 'required',
                'change_day_alasan' => 'required',
                'keterangan' => 'nullable',
                'attachment' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);
        } else {
            // izin / sakit
            $request->validate([
                'keterangan' => 'nullable',
                'attachment' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);
        }

        $karyawan = Auth::user();
        $today = Carbon::today();

        DB::beginTransaction();
        try {
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('attachments', 'public');
            }

            // ── CHANGE DAY ──────────────────────────────────────────────────
            if ($request->jenis_absensi === 'change_day') {
                $tanggalAwal = Carbon::parse($request->change_day_tanggal_awal);

                $existingChangeDay = AbsensiKaryawan::where('karyawan_id', $karyawan->id)
                    ->where('is_change_day', true)
                    ->where(function ($query) use ($request) {
                        $query->whereBetween('change_day_tanggal_awal', [
                            $request->change_day_tanggal_awal,
                            $request->change_day_tanggal_akhir,
                        ])
                            ->orWhereBetween('change_day_tanggal_akhir', [
                                $request->change_day_tanggal_awal,
                                $request->change_day_tanggal_akhir,
                            ]);
                    })
                    ->whereIn('change_day_status', [
                        AbsensiKaryawan::CHANGE_DAY_PENDING,
                        AbsensiKaryawan::CHANGE_DAY_APPROVED,
                    ])
                    ->exists();

                if ($existingChangeDay) {
                    DB::rollBack();

                    return redirect()->route('absensi.index')
                        ->with('error', 'You already have a change day request for that period');
                }

                AbsensiKaryawan::create([
                    'karyawan_id' => $karyawan->id,
                    'nama_karyawan' => $karyawan->nama_lengkap,
                    'tanggal' => $tanggalAwal,
                    'status_kehadiran' => 'pending',
                    'keterangan' => $request->keterangan,
                    'attachment' => $attachmentPath,
                    'is_change_day' => true,
                    'change_day_tanggal_awal' => $request->change_day_tanggal_awal,
                    'change_day_tanggal_akhir' => $request->change_day_tanggal_akhir,
                    'change_day_jam_mulai' => $request->change_day_jam_mulai,
                    'change_day_jam_selesai' => $request->change_day_jam_selesai,
                    'change_day_alasan' => $request->change_day_alasan,
                    'change_day_status' => 'pending',
                ]);

                $admins = Karyawan::whereIn('role', ['admin', 'hr'])->get();
                foreach ($admins as $admin) {
                    Notifikasi::create([
                        'user_id' => $admin->id,
                        'judul' => 'Pengajuan Change Day Baru',
                        'pesan' => "{$karyawan->nama_lengkap} mengajukan change day pada tanggal {$tanggalAwal->format('d/m/Y')}",
                        'tipe_notifikasi' => 'absensi',
                    ]);
                }

                DB::commit();

                return redirect()->route('absensi.index')
                    ->with('success', 'Change day request submitted, pending HR/Admin approval');
            }

            // ── CHECK-IN ─────────────────────────────────────────────────────
            elseif ($request->jenis_absensi === 'checkin') {
                // Block check-in on Sunday / national holiday without approved Change Day
                if ($this->isSundayOrHoliday($today)) {
                    $hasApprovedChangeDay = AbsensiKaryawan::where('karyawan_id', $karyawan->id)
                        ->where('is_change_day', true)
                        ->where('change_day_status', AbsensiKaryawan::CHANGE_DAY_APPROVED)
                        ->whereDate('change_day_tanggal_akhir', $today)
                        ->exists();

                    if (!$hasApprovedChangeDay) {
                        DB::rollBack();
                        return redirect()->route('absensi.index')
                            ->with('error', 'Sunday and public holiday check-ins require an approved Change Day request.');
                    }
                }

                // Block check-in on approved Change Day off date (tanggal_awal = compensatory day off)
                $isChangeDayOff = AbsensiKaryawan::where('karyawan_id', $karyawan->id)
                    ->where('is_change_day', true)
                    ->where('change_day_status', AbsensiKaryawan::CHANGE_DAY_APPROVED)
                    ->whereDate('change_day_tanggal_awal', $today)
                    ->exists();

                if ($isChangeDayOff) {
                    DB::rollBack();
                    return redirect()->route('absensi.index')
                        ->with('error', 'Today is your approved Change Day off. You are not scheduled to work today.');
                }

                // Cek apakah sudah ada absensi hari ini (apapun jenisnya, bukan change day)
                $existingAbsensi = AbsensiKaryawan::where('karyawan_id', $karyawan->id)
                    ->whereDate('tanggal', $today)
                    ->where('is_change_day', false)
                    ->first();

                if ($existingAbsensi) {
                    DB::rollBack();
                    $pesan = $existingAbsensi->jam_masuk
                        ? 'You have already checked in today'
                        : 'You already have a permit/sick submission for today';

                    return redirect()->route('absensi.index')->with('error', $pesan);
                }

                AbsensiKaryawan::create([
                    'karyawan_id' => $karyawan->id,
                    'nama_karyawan' => $karyawan->nama_lengkap,
                    'tanggal' => $today,
                    'jam_masuk' => now()->format('H:i'),
                    'lokasi_masuk' => $request->lokasi_masuk,
                    'status_kehadiran' => 'pending',
                    'keterangan' => $request->keterangan,
                    'attachment' => $attachmentPath,
                    'is_change_day' => false,
                ]);

                DB::commit();

                return redirect()->route('absensi.index')
                    ->with('success', 'Check-in submitted successfully, pending HR/Admin approval');
            }

            // ── PERMIT ───────────────────────────────────────────────────────
            elseif ($request->jenis_absensi === 'permit') {
                $existingAbsensi = AbsensiKaryawan::where('karyawan_id', $karyawan->id)
                    ->whereDate('tanggal', $today)
                    ->where('is_change_day', false)
                    ->first();

                if ($existingAbsensi) {
                    DB::rollBack();

                    return redirect()->route('absensi.index')
                        ->with('error', 'You have already submitted attendance for today');
                }

                AbsensiKaryawan::create([
                    'karyawan_id' => $karyawan->id,
                    'nama_karyawan' => $karyawan->nama_lengkap,
                    'tanggal' => $today,
                    'status_kehadiran' => AbsensiKaryawan::STATUS_PENDING,
                    'keterangan' => $request->keterangan ?: 'Permit',
                    'attachment' => $attachmentPath,
                    'is_change_day' => false,
                ]);

                DB::commit();

                return redirect()->route('absensi.index')
                    ->with('success', 'Permit request submitted successfully');
            }

            // ── SICK ─────────────────────────────────────────────────────────
            elseif ($request->jenis_absensi === 'sick') {
                $existingAbsensi = AbsensiKaryawan::where('karyawan_id', $karyawan->id)
                    ->whereDate('tanggal', $today)
                    ->where('is_change_day', false)
                    ->first();

                if ($existingAbsensi) {
                    DB::rollBack();

                    return redirect()->route('absensi.index')
                        ->with('error', 'You have already submitted attendance for today');
                }

                AbsensiKaryawan::create([
                    'karyawan_id' => $karyawan->id,
                    'nama_karyawan' => $karyawan->nama_lengkap,
                    'tanggal' => $today,
                    'status_kehadiran' => AbsensiKaryawan::STATUS_PENDING,
                    'keterangan' => $request->keterangan ?: 'Sick',
                    'attachment' => $attachmentPath,
                    'is_change_day' => false,
                ]);

                DB::commit();

                return redirect()->route('absensi.index')
                    ->with('success', 'Sick leave request submitted successfully');
            }

            DB::commit();

            return redirect()->route('absensi.index')
                ->with('success', 'Attendance submitted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Absensi store error: ' . $e->getMessage());

            return redirect()->route('absensi.index')
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $absensi = AbsensiKaryawan::where('karyawan_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        if ($absensi->is_change_day) {
            if ($absensi->change_day_status !== AbsensiKaryawan::CHANGE_DAY_PENDING) {
                return redirect()->route('absensi.index')
                    ->with('error', 'A processed change day request cannot be edited');
            }

            return view('absensi.edit_change_day', compact('absensi'));
        }

        if ($absensi->status_kehadiran !== AbsensiKaryawan::STATUS_PENDING) {
            return redirect()->route('absensi.index')
                ->with('error', 'Approved attendance cannot be edited');
        }

        if ($absensi->jam_pulang) {
            return redirect()->route('absensi.index')
                ->with('error', 'Attendance with check-out recorded cannot be edited');
        }

        return view('absensi.edit', compact('absensi'));
    }

    public function update(Request $request, $id)
    {
        $absensi = AbsensiKaryawan::where('karyawan_id', Auth::id())
            ->findOrFail($id);

        if ($absensi->is_change_day) {
            if ($absensi->change_day_status !== AbsensiKaryawan::CHANGE_DAY_PENDING) {
                return redirect()
                    ->route('absensi.index')
                    ->with('error', 'A processed change day request cannot be edited');
            }

            $validated = $request->validate([
                'change_day_tanggal_awal' => 'required|date',
                'change_day_tanggal_akhir' => 'required|date|after_or_equal:change_day_tanggal_awal',
                'change_day_jam_mulai' => 'required',
                'change_day_jam_selesai' => 'required',
                'change_day_alasan' => 'required|string',
                'keterangan' => 'nullable|string',
                'attachment' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $updateData = [
                'change_day_tanggal_awal' => $validated['change_day_tanggal_awal'],
                'change_day_tanggal_akhir' => $validated['change_day_tanggal_akhir'],
                'change_day_jam_mulai' => $validated['change_day_jam_mulai'],
                'change_day_jam_selesai' => $validated['change_day_jam_selesai'],
                'change_day_alasan' => $validated['change_day_alasan'],
                'keterangan' => $validated['keterangan'] ?? null,
            ];

            if ($request->hasFile('attachment')) {
                if ($absensi->attachment && Storage::disk('public')->exists($absensi->attachment)) {
                    Storage::disk('public')->delete($absensi->attachment);
                }

                $updateData['attachment'] = $request->file('attachment')
                    ->store('attachments', 'public');
            }

            $absensi->update($updateData);

            return redirect()
                ->route('absensi.index')
                ->with('success', 'Change day request updated successfully');
        }

        // Regular absensi hanya bisa diedit sebelum check-out
        if ($absensi->jam_pulang) {
            return redirect()
                ->route('absensi.index')
                ->with('error', 'Attendance with check-out recorded cannot be edited');
        }

        $updateData = [
            'jam_masuk' => $absensi['jam_masuk'] ?? $request['change_day_jam_mulai'],
            'lokasi_masuk' => $absensi['lokasi_masuk'],
            'keterangan' => $request['keterangan'] ?? null,
        ];

        if ($request->hasFile('attachment')) {
            if ($absensi->attachment && Storage::disk('public')->exists($absensi->attachment)) {
                Storage::disk('public')->delete($absensi->attachment);
            }

            $updateData['attachment'] = $request->file('attachment')
                ->store('attachments', 'public');
        }

        $absensi->update($updateData);

        return redirect()
            ->route('absensi.index')
            ->with('success', 'Attendance updated successfully');
    }

    public function absensiPulang(Request $request, $id)
    {
        $absensi = AbsensiKaryawan::where('karyawan_id', Auth::id())
            ->where('id', $id)
            ->where('is_change_day', false)
            ->firstOrFail();

        if ($absensi->jam_pulang) {
            return redirect()->route('absensi.index')
                ->with('error', 'You have already checked out');
        }

        if (! $absensi->jam_masuk) {
            return redirect()->route('absensi.index')
                ->with('error', 'You have not checked in yet');
        }

        // Enforce 7-hour minimum working time before checkout
        $jamMasukRaw = $absensi->jam_masuk;
        if (strpos($jamMasukRaw, ' ') !== false) {
            $jamMasukRaw = substr($jamMasukRaw, 11, 5);
        }
        $checkInTime  = Carbon::parse($absensi->tanggal->format('Y-m-d') . ' ' . $jamMasukRaw);
        $minCheckout  = $checkInTime->copy()->addHours(7);

        if (Carbon::now()->lt($minCheckout)) {
            $remaining = Carbon::now()->diff($minCheckout);
            $hoursLeft = $remaining->h;
            $minsLeft  = $remaining->i;
            return redirect()->route('absensi.index')->with('error', "You can only check out after 7 working hours. Time remaining: {$hoursLeft}h {$minsLeft}m.");
        }

        $request->validate([
            'lokasi_pulang' => 'required|string|max:255',
            'keterangan'    => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $tanggalAbsensi = $absensi->tanggal->format('Y-m-d');
            $jamMasukValue = $absensi->jam_masuk;

            if (strpos($jamMasukValue, ' ') !== false) {
                $jamMasukValue = substr($jamMasukValue, 11, 5);
            }

            $jamMasukDateTime = Carbon::parse($tanggalAbsensi . ' ' . $jamMasukValue);
            $jamPulang = now()->format('H:i');
            $jamPulangDateTime = Carbon::parse($tanggalAbsensi . ' ' . $jamPulang);

            if ($jamPulangDateTime < $jamMasukDateTime) {
                $jamPulangDateTime->addDay();
            }

            $selisihMenit = $jamMasukDateTime->diffInMinutes($jamPulangDateTime);
            $totalJam = $selisihMenit / 60;

            $updateData = [
                'jam_pulang'      => $jamPulang,
                'lokasi_pulang'   => $request->lokasi_pulang,
                'total_jam_kerja' => round($totalJam, 2),
                'keterangan'       => $request->keterangan,
            ];

            if ($request->filled('keterangan')) {
                $updateData['keterangan'] = $request->keterangan;
            }

            $absensi->update($updateData);

            DB::commit();

            return redirect()->route('absensi.index')
                ->with('success', 'Check-out recorded. Total working hours: ' . number_format($totalJam, 2) . 'h');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('absensi.index')
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function cancelChangeDay($id)
    {
        $absensi = AbsensiKaryawan::where('karyawan_id', Auth::id())
            ->where('id', $id)
            ->where('is_change_day', true)
            ->where('change_day_status', AbsensiKaryawan::CHANGE_DAY_PENDING)
            ->firstOrFail();

        $absensi->delete();

        return redirect()->route('absensi.index')
            ->with('success', 'Change day request cancelled successfully');
    }

    // ==================== ADMIN/HR SECTION ====================
    public function adminIndex()
    {
        $absensi = AbsensiKaryawan::with('karyawan')
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->where('is_change_day', false)
            ->get();

        $statistics = [
            'total' => AbsensiKaryawan::where('is_change_day', false)->count(),
            'pending' => AbsensiKaryawan::where('status_kehadiran', AbsensiKaryawan::STATUS_PENDING)->where('is_change_day', false)->count(),
            'present' => AbsensiKaryawan::where('status_kehadiran', AbsensiKaryawan::STATUS_PRESENT)->where('is_change_day', false)->count(),
            'change_day' => AbsensiKaryawan::where('status_kehadiran', AbsensiKaryawan::STATUS_CHANGE_DAY)->where('is_change_day', false)->count(),
            'leave' => AbsensiKaryawan::where('status_kehadiran', AbsensiKaryawan::STATUS_LEAVE)->where('is_change_day', false)->count(),
        ];

        return view('admin.absensi.index', compact('absensi', 'statistics'));
    }

    public function adminUpdateStatusChangeDay(Request $request, $id)
    {
        $absensi = AbsensiKaryawan::findOrFail($id);

        DB::beginTransaction();
        try {
            $request->validate([
                'change_day_status' => 'required|in:pending,approved,rejected',
                'change_day_note' => 'nullable|string',
            ]);

            $updateData = [
                'change_day_status'        => $request->change_day_status,
                'change_day_catatan_admin' => $request->change_day_note ?? null,
            ];

            if ($request->change_day_status === AbsensiKaryawan::CHANGE_DAY_APPROVED) {
                $updateData['change_day_disetujui_pada'] = now();
                $updateData['change_day_disetujui_oleh'] = Auth::id();

                // Original Date (compensatory day off) — if already past or today, create immediately;
                // future dates are handled by the daily changeday:create-attendance scheduler.
                if ($absensi->change_day_tanggal_awal) {
                    $origDate = $absensi->change_day_tanggal_awal->toDateString();
                    if ($origDate <= Carbon::today()->toDateString()) {
                        AbsensiKaryawan::updateOrCreate(
                            [
                                'karyawan_id'   => $absensi->karyawan_id,
                                'tanggal'       => $origDate,
                                'is_change_day' => false,
                            ],
                            [
                                'nama_karyawan'    => $absensi->nama_karyawan,
                                'status_kehadiran' => AbsensiKaryawan::STATUS_CHANGE_DAY,
                                'keterangan'       => 'Change Day off — will work on ' .
                                    ($absensi->change_day_tanggal_akhir
                                        ? $absensi->change_day_tanggal_akhir->format('d/m/Y')
                                        : '-'),
                            ]
                        );
                    }
                }

                // Requested Date (Sunday/holiday) — if already past or today, create immediately;
                // future dates are handled by the daily changeday:create-attendance scheduler.
                if ($absensi->change_day_tanggal_akhir) {
                    $reqDate = $absensi->change_day_tanggal_akhir->toDateString();
                    if ($reqDate <= Carbon::today()->toDateString()) {
                        $alreadyExists = AbsensiKaryawan::where('karyawan_id', $absensi->karyawan_id)
                            ->whereDate('tanggal', $reqDate)
                            ->where('is_change_day', false)
                            ->exists();
                        if (!$alreadyExists) {
                            AbsensiKaryawan::create([
                                'karyawan_id'      => $absensi->karyawan_id,
                                'nama_karyawan'    => $absensi->nama_karyawan,
                                'tanggal'          => $reqDate,
                                'is_change_day'    => false,
                                'status_kehadiran' => AbsensiKaryawan::STATUS_PENDING,
                                'keterangan'       => 'Change Day — working on holiday/Sunday in exchange for ' .
                                    ($absensi->change_day_tanggal_awal
                                        ? $absensi->change_day_tanggal_awal->format('d/m/Y')
                                        : '-'),
                            ]);
                        }
                    }
                }

            } elseif ($request->change_day_status === AbsensiKaryawan::CHANGE_DAY_REJECTED) {
                // If reverting from approved: clean up the change_day attendance record
                if ($absensi->change_day_status === AbsensiKaryawan::CHANGE_DAY_APPROVED) {
                    if ($absensi->change_day_tanggal_awal) {
                        AbsensiKaryawan::where('karyawan_id', $absensi->karyawan_id)
                            ->where('tanggal', $absensi->change_day_tanggal_awal->toDateString())
                            ->where('is_change_day', false)
                            ->where('status_kehadiran', AbsensiKaryawan::STATUS_CHANGE_DAY)
                            ->delete();
                    }
                    // Remove requested date attendance regardless of current status,
                    // as long as it was created by the change day scheduler (matched by keterangan prefix)
                    if ($absensi->change_day_tanggal_akhir) {
                        AbsensiKaryawan::where('karyawan_id', $absensi->karyawan_id)
                            ->where('tanggal', $absensi->change_day_tanggal_akhir->toDateString())
                            ->where('is_change_day', false)
                            ->where('keterangan', 'like', 'Change Day — working on holiday/Sunday%')
                            ->delete();
                    }
                }
            }

            $absensi->update($updateData);

            $statusText = $request->change_day_status === 'approved' ? 'approved' : 'rejected';
            $message = 'Your change day request for ' .
                ($absensi->change_day_tanggal_awal ? $absensi->change_day_tanggal_awal->format('d/m/Y') : '-') .
                " has been {$statusText}";

            Notifikasi::create([
                'user_id' => $absensi->karyawan_id,
                'judul' => 'Change Day Status Updated',
                'pesan' => $message,
                'tipe_notifikasi' => 'change_day',
            ]);

            DB::commit();

            return redirect()->route('admin.changeday.index')
                ->with('success', 'Change day status updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('admin.absensi.index')
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function adminUpdateStatusAbsensi(Request $request, $id)
    {
        $absensi = AbsensiKaryawan::findOrFail($id);

        if ($absensi->tanggal->isToday()) {
            $message = "Today's attendance cannot be edited yet. Please review it tomorrow.";
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return redirect()->route('admin.absensi.index')->with('error', $message);
        }

        DB::beginTransaction();
        try {
            $request->validate([
                'status_kehadiran' => 'required|in:pending,present,change_day,leave,absent',
            ]);

            $updateData = [
                'status_kehadiran' => $request->status_kehadiran,
            ];

            if ($request->status_kehadiran === AbsensiKaryawan::STATUS_PRESENT) {
                $updateData['jam_masuk'] = $absensi->jam_masuk ?: now()->format('H:i');
                $updateData['jam_pulang'] = $absensi->jam_pulang ?: now()->format('H:i');

                if ($updateData['jam_masuk'] && $updateData['jam_pulang']) {
                    $jamMasukDateTime = Carbon::parse($absensi->tanggal->format('Y-m-d') . ' ' . $updateData['jam_masuk']);
                    $jamPulangDateTime = Carbon::parse($absensi->tanggal->format('Y-m-d') . ' ' . $updateData['jam_pulang']);

                    if ($jamPulangDateTime < $jamMasukDateTime) {
                        $jamPulangDateTime->addDay();
                    }

                    $selisihMenit = $jamMasukDateTime->diffInMinutes($jamPulangDateTime);
                    $totalJam = $selisihMenit / 60;
                    $updateData['total_jam_kerja'] = round($totalJam, 2);
                }
            }

            $absensi->update($updateData);

            Notifikasi::create([
                'user_id' => $absensi->karyawan_id,
                'judul' => 'Attendance Status Updated',
                'pesan' => "Your attendance on {$absensi->tanggal->format('d/m/Y')} has been updated to {$request->status_kehadiran}",
                'tipe_notifikasi' => 'absensi',
            ]);

            DB::commit();

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Attendance status updated successfully']);
            }

            return redirect()->route('admin.absensi.index')
                ->with('success', 'Attendance status updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
            }

            return redirect()->route('admin.absensi.index')
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function adminShow($id)
    {
        $absensi = AbsensiKaryawan::with(['karyawan', 'disetujuiOleh'])->findOrFail($id);

        return response()->json($absensi);
    }

    private function isSundayOrHoliday(Carbon $date): bool
    {
        if ($date->dayOfWeek === Carbon::SUNDAY) {
            return true;
        }

        $year = $date->year;
        $holidays = Cache::remember("national_holidays_{$year}", now()->addDay(), function () use ($year) {
            try {
                $response = Http::timeout(5)->get("https://libur.deno.dev/api?year={$year}");
                if ($response->successful()) {
                    return collect($response->json())
                        ->pluck('date')
                        ->map(fn($d) => substr($d, 0, 10))
                        ->toArray();
                }
            } catch (\Exception $e) {
                // API unavailable — fall back to Sunday-only check
            }
            return [];
        });

        return in_array($date->toDateString(), $holidays);
    }
}
