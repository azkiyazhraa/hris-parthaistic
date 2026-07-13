<?php

namespace App\Http\Controllers;

use App\Models\AbsensiKaryawan;
use App\Models\Karyawan;
use App\Models\Notifikasi;
use App\Models\PengajuanCuti;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LeaveController extends Controller
{
    const QUOTA_TAHUNAN      = 12;
    const QUOTA_MELAHIRKAN_P = 90;
    const QUOTA_MELAHIRKAN_L = 3;
    const QUOTA_MENIKAH      = 3;
    const QUOTA_DUKA         = 2;

    private function getLeaveQuotas(int $karyawanId): array
    {
        $karyawan = Karyawan::find($karyawanId);
        $tahun    = date('Y');

        $kuotaMelahirkan = $karyawan->jenis_kelamin === 'P'
            ? self::QUOTA_MELAHIRKAN_P
            : self::QUOTA_MELAHIRKAN_L;

        $used = fn(string $jenis) => (int) PengajuanCuti::where('karyawan_id', $karyawanId)
            ->where('tahun_cuti', $tahun)
            ->where('jenis_cuti', $jenis)
            ->where('status', 'disetujui')
            ->sum('total_hari');

        return [
            'karyawan'        => $karyawan,
            'tahunSekarang'   => $tahun,
            'kuotaTahunan'    => self::QUOTA_TAHUNAN,
            'kuotaMelahirkan' => $kuotaMelahirkan,
            'kuotaMenikah'    => self::QUOTA_MENIKAH,
            'kuotaDuka'       => self::QUOTA_DUKA,
            'sisaTahunan'     => max(0, self::QUOTA_TAHUNAN - $used('tahunan')),
            'sisaMelahirkan'  => max(0, $kuotaMelahirkan - $used('melahirkan')),
            'sisaMenikah'     => max(0, self::QUOTA_MENIKAH - $used('menikah')),
            'sisaDuka'        => max(0, self::QUOTA_DUKA - $used('duka')),
        ];
    }

    // Employee: Index - List all leave requests
    public function index()
    {
        $karyawanId = Auth::id();

        $cuti = PengajuanCuti::where('karyawan_id', $karyawanId)
            ->whereYear('created_at', date('Y'))
            ->orderBy('created_at', 'desc')
            ->get();

        $approvedCount = $cuti->where('status', 'disetujui')->count();
        $pendingCount  = $cuti->where('status', 'pending')->count();
        $rejectedCount = $cuti->where('status', 'ditolak')->count();

        $quotas = $this->getLeaveQuotas($karyawanId);

        return view('cuti.index', array_merge(
            compact('cuti', 'approvedCount', 'pendingCount', 'rejectedCount'),
            $quotas
        ));
    }

    // Employee: Show create form
    public function create()
    {
        $quotas = $this->getLeaveQuotas(Auth::id());
        return view('cuti.create', $quotas);
    }

    // Employee: Store leave request
    public function store(Request $request)
    {
        $request->validate([
            'jenis_cuti'      => 'required|in:tahunan,melahirkan,menikah,duka',
            'tanggal_mulai'   => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan'          => 'required|string|min:10',
            'lampiran'        => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ], [
            'jenis_cuti.required'  => 'Leave type is required.',
            'tanggal_mulai.required' => 'Start date is required.',
            'tanggal_selesai.required' => 'End date is required.',
            'alasan.required'      => 'Reason for leave is required.',
            'alasan.min'           => 'Reason for leave must be at least 10 characters.',
            'lampiran.mimes'       => 'File type must be PDF, DOC, DOCX, JPG, JPEG, or PNG.',
            'lampiran.max'         => 'File size must be less than 5MB.',
        ]);

        $karyawanId = Auth::id();
        $karyawan   = Karyawan::find($karyawanId);
        $totalHari  = PengajuanCuti::hitungTotalHari($request->tanggal_mulai, $request->tanggal_selesai);

        $quotas = $this->getLeaveQuotas($karyawanId);
        $quotaMap = [
            'tahunan'   => ['sisa' => $quotas['sisaTahunan'],   'kuota' => $quotas['kuotaTahunan'],   'label' => 'Annual'],
            'melahirkan'=> ['sisa' => $quotas['sisaMelahirkan'],'kuota' => $quotas['kuotaMelahirkan'],'label' => $karyawan->jenis_kelamin === 'P' ? 'Maternity' : 'Paternity'],
            'menikah'   => ['sisa' => $quotas['sisaMenikah'],   'kuota' => $quotas['kuotaMenikah'],   'label' => 'Marriage'],
            'duka'      => ['sisa' => $quotas['sisaDuka'],      'kuota' => $quotas['kuotaDuka'],      'label' => 'Bereavement'],
        ];

        $q = $quotaMap[$request->jenis_cuti];
        if ($totalHari > $q['sisa']) {
            return redirect()->back()
                ->with('error', $q['label'] . ' leave quota exceeded. Remaining: ' . $q['sisa'] . ' days.')
                ->withInput();
        }

        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $lampiranPath = $request->file('lampiran')->store('lampiran-cuti', 'public');
        }

        $cuti = PengajuanCuti::create([
            'karyawan_id'    => $karyawanId,
            'nama_karyawan'  => $karyawan->nama_lengkap,
            'jenis_cuti'     => $request->jenis_cuti,
            'tanggal_mulai'  => $request->tanggal_mulai,
            'tanggal_selesai'=> $request->tanggal_selesai,
            'total_hari'     => $totalHari,
            'alasan'         => $request->alasan,
            'status'         => 'pending',
            'lampiran'       => $lampiranPath,
            'tahun_cuti'     => date('Y'),
            'kuota_total'    => $q['kuota'],
            'kuota_terpakai' => $q['kuota'] - $q['sisa'],
            'sisa_kuota'     => $q['sisa'] - $totalHari,
        ]);

        $leaveLabel = $q['label'];
        $period     = Carbon::parse($request->tanggal_mulai)->format('d/m/Y')
                    . ' – ' . Carbon::parse($request->tanggal_selesai)->format('d/m/Y');

        foreach (Karyawan::whereIn('role', ['admin', 'hr'])->get() as $admin) {
            Notifikasi::create([
                'user_id'         => $admin->id,
                'judul'           => 'New Leave Request',
                'pesan'           => "{$karyawan->nama_lengkap} submitted a {$leaveLabel} leave request for {$period} ({$totalHari} day(s)).",
                'tipe_notifikasi' => 'cuti',
            ]);
        }

        return redirect()->route('cuti.index')->with('success', 'Leave request submitted successfully');
    }

    // Employee: Show leave details
    public function show($id)
    {
        $karyawanId = Auth::id();
        $cuti = PengajuanCuti::with('karyawan')->where('karyawan_id', $karyawanId)->findOrFail($id);

        return response()->json($cuti);
    }

    // Employee: Show edit form
    public function edit($id)
    {
        $cuti = PengajuanCuti::where('karyawan_id', Auth::id())->findOrFail($id);

        if ($cuti->status != 'pending') {
            return redirect()->route('cuti.index')->with('error', 'A processed leave request cannot be changed');
        }

        $quotas = $this->getLeaveQuotas(Auth::id());

        return view('cuti.edit', array_merge(compact('cuti'), $quotas));
    }

    // Employee: Update leave request
    public function update(Request $request, $id)
    {
        $cuti = PengajuanCuti::where('karyawan_id', Auth::id())->findOrFail($id);

        if ($cuti->status != 'pending') {
            return redirect()->route('cuti.index')->with('error', 'A processed leave request cannot be changed');
        }

        $request->validate([
            'jenis_cuti'      => 'required|in:tahunan,melahirkan,menikah,duka',
            'tanggal_mulai'   => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan'          => 'required|string|min:10',
            'lampiran'        => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        $karyawanId = Auth::id();
        $karyawan   = Karyawan::find($karyawanId);
        $totalHari  = PengajuanCuti::hitungTotalHari($request->tanggal_mulai, $request->tanggal_selesai);

        $quotas = $this->getLeaveQuotas($karyawanId);
        $quotaMap = [
            'tahunan'   => ['sisa' => $quotas['sisaTahunan'],   'label' => 'Annual'],
            'melahirkan'=> ['sisa' => $quotas['sisaMelahirkan'],'label' => $karyawan->jenis_kelamin === 'P' ? 'Maternity' : 'Paternity'],
            'menikah'   => ['sisa' => $quotas['sisaMenikah'],   'label' => 'Marriage'],
            'duka'      => ['sisa' => $quotas['sisaDuka'],      'label' => 'Bereavement'],
        ];

        $q = $quotaMap[$request->jenis_cuti];
        // Add back current record's days before checking
        $sisaEfektif = $q['sisa'] + $cuti->total_hari;
        if ($totalHari > $sisaEfektif) {
            return redirect()->back()
                ->with('error', $q['label'] . ' leave quota exceeded. Remaining: ' . $q['sisa'] . ' days.')
                ->withInput();
        }

        $updateData = [
            'jenis_cuti' => $request->jenis_cuti,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'total_hari' => $totalHari,
            'alasan' => $request->alasan,
        ];

        if ($request->hasFile('lampiran')) {
            if ($cuti->lampiran) {
                Storage::disk('public')->delete($cuti->lampiran);
            }
            $updateData['lampiran'] = $request->file('lampiran')->store('lampiran-cuti', 'public');
        }

        $cuti->update($updateData);

        return redirect()->route('cuti.index')->with('success', 'Leave request updated successfully');
    }

    // Employee: Delete/Cancel leave request
    public function destroy($id)
    {
        $cuti = PengajuanCuti::where('karyawan_id', Auth::id())->findOrFail($id);

        if ($cuti->status != 'pending') {
            return redirect()->route('cuti.index')->with('error', 'A processed leave request cannot be cancelled');
        }

        if ($cuti->lampiran) {
            Storage::disk('public')->delete($cuti->lampiran);
        }

        $cuti->delete();

        return redirect()->route('cuti.index')->with('success', 'Leave request cancelled successfully');
    }

    // Admin/HR: Index all leave requests
    public function adminIndex(Request $request)
    {
        $query = PengajuanCuti::with('karyawan')->orderBy('created_at', 'desc');

        // Filter sudah benar
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->month) {
            $query->whereMonth('tanggal_mulai', $request->month);
        }

        $dataLeave = $query->paginate(10);

        // Statistik
        $totalRequest = PengajuanCuti::whereMonth('created_at', date('m'))->count();
        $approved = PengajuanCuti::where('status', 'disetujui')->count();
        $requested = PengajuanCuti::where('status', 'pending')->count();
        $rejected = PengajuanCuti::where('status', 'ditolak')->count();

        return view('admin.leave.index', compact('dataLeave', 'totalRequest', 'approved', 'requested', 'rejected'));
    }

    // Admin/HR: Show detail leave request
    public function adminShow($id)
    {
        $cuti = PengajuanCuti::with('karyawan')->findOrFail($id);

        return response()->json($cuti);
    }

    // Admin/HR: Update status (Approve/Reject)
    public function adminUpdateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak,pending',
            'catatan' => 'nullable|string',
        ]);

        $cuti = PengajuanCuti::findOrFail($id);
        $oldStatus = $cuti->status;
        $cuti->status = $request->status;

        if ($request->status == 'disetujui') {
            $cuti->tanggal_disetujui = now();

            $tahunCuti = date('Y', strtotime($cuti->tanggal_mulai));
            $kuotaTerpakaiBaru = PengajuanCuti::where('karyawan_id', $cuti->karyawan_id)
                ->where('tahun_cuti', $tahunCuti)
                ->where('jenis_cuti', $cuti->jenis_cuti)
                ->where('status', 'disetujui')
                ->sum('total_hari') + $cuti->total_hari;

            $cuti->kuota_terpakai = $kuotaTerpakaiBaru;
            $cuti->sisa_kuota     = max(0, $cuti->kuota_total - $kuotaTerpakaiBaru);
        }

        if ($request->filled('catatan')) {
            $cuti->catatan = $request->catatan;
        }

        $cuti->save();

        // Sync attendance records for the leave period
        $this->syncLeaveAttendance($cuti, $request->status, $oldStatus);

        $statusText = $request->status == 'disetujui' ? 'approved' : 'rejected';
        $message = 'Your leave request for ' . Carbon::parse($cuti->tanggal_mulai)->format('d/m/Y') . ' - ' . Carbon::parse($cuti->tanggal_selesai)->format('d/m/Y') . " has been $statusText";

        if ($request->filled('catatan')) {
            $message .= ' with note: ' . $request->catatan;
        }

        Notifikasi::create([
            'user_id' => $cuti->karyawan_id,
            'judul' => 'Leave Request Status Updated',
            'pesan' => $message,
            'tipe_notifikasi' => 'cuti',
        ]);

        return redirect()->route('admin.leave.index')->with('success', 'Leave status updated successfully');
    }

    /**
     * Create or remove AbsensiKaryawan records when leave status changes.
     * Creates records for the full leave range immediately on approval
     * (does not wait for the daily scheduler to reach future dates).
     * Removes records when an approved leave is rejected.
     */
    private function syncLeaveAttendance(PengajuanCuti $cuti, string $newStatus, string $oldStatus): void
    {
        if ($newStatus === 'disetujui') {
            $current = Carbon::parse($cuti->tanggal_mulai)->startOfDay();
            $end     = Carbon::parse($cuti->tanggal_selesai)->startOfDay();

            while ($current->lte($end)) {
                $dateStr = $current->toDateString();

                $exists = AbsensiKaryawan::where('karyawan_id', $cuti->karyawan_id)
                    ->whereDate('tanggal', $dateStr)
                    ->where('is_change_day', false)
                    ->exists();

                if (! $exists) {
                    AbsensiKaryawan::create([
                        'karyawan_id'      => $cuti->karyawan_id,
                        'nama_karyawan'    => $cuti->nama_karyawan,
                        'tanggal'          => $dateStr,
                        'is_change_day'    => false,
                        'status_kehadiran' => AbsensiKaryawan::STATUS_LEAVE,
                        'keterangan'       => $cuti->jenis_cuti_label . ' (Leave approved)',
                    ]);
                }

                $current->addDay();
            }
        } elseif ($newStatus === 'ditolak' && $oldStatus === 'disetujui') {
            // Remove attendance records that were created for this leave
            AbsensiKaryawan::where('karyawan_id', $cuti->karyawan_id)
                ->where('status_kehadiran', AbsensiKaryawan::STATUS_LEAVE)
                ->where('is_change_day', false)
                ->whereBetween('tanggal', [
                    $cuti->tanggal_mulai->toDateString(),
                    $cuti->tanggal_selesai->toDateString(),
                ])
                ->delete();
        }
    }
}
