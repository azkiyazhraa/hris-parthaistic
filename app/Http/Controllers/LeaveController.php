<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Notifikasi;
use App\Models\PengajuanCuti;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LeaveController extends Controller
{
    // Employee: Index - List all leave requests
    public function index()
    {
        $karyawanId = Auth::id();

        $cuti = PengajuanCuti::where('karyawan_id', $karyawanId)
            ->whereYear('created_at', date('Y'))
            ->orderBy('created_at', 'desc')
            ->get();

        $approvedCount = $cuti->where('status', 'disetujui')->count();
        $pendingCount = $cuti->where('status', 'pending')->count();
        $rejectedCount = $cuti->where('status', 'ditolak')->count();

        $sisaTahunan = 12 - PengajuanCuti::where('karyawan_id', $karyawanId)
            ->where('tahun_cuti', date('Y'))
            ->where('jenis_cuti', 'tahunan')
            ->where('status', 'disetujui')
            ->sum('total_hari');

        return view('cuti.index', compact('cuti', 'approvedCount', 'pendingCount', 'rejectedCount', 'sisaTahunan'));
    }

    // Employee: Show create form
    public function create()
    {
        $tahunSekarang = date('Y');
        $kuotaTotal = 12;
        $kuotaTerpakai = PengajuanCuti::where('karyawan_id', Auth::id())
            ->where('tahun_cuti', $tahunSekarang)
            ->where('jenis_cuti', 'tahunan')
            ->where('status', 'disetujui')
            ->sum('total_hari');
        $sisaKuota = max(0, $kuotaTotal - $kuotaTerpakai);

        return view('cuti.create', compact('sisaKuota'));
    }

    // Employee: Store leave request
    public function store(Request $request)
    {
        $request->validate([
            'jenis_cuti' => 'required|in:tahunan,sakit,melahirkan,penting,ibadah,lainnya',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string|min:10',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ], [
            'jenis_cuti.required' => 'Leave type is required.',
            'tanggal_mulai.required' => 'Start date is required.',
            'tanggal_selesai.required' => 'End date is required.',
            'alasan.required' => 'Reason for leave is required.',
            'alasan.min' => 'Reason for leave must be at least 10 characters.',
            'lampiran.mimes' => 'File type must be PDF, DOC, DOCX, JPG, JPEG, or PNG.',
            'lampiran.max' => 'File size must be less than 5MB.',
        ]);

        $karyawan = Karyawan::find(Auth::id());
        $totalHari = PengajuanCuti::hitungTotalHari($request->tanggal_mulai, $request->tanggal_selesai);

        // Check quota for annual leave
        if ($request->jenis_cuti == 'tahunan') {
            $tahunSekarang = date('Y');
            $kuotaTerpakai = PengajuanCuti::where('karyawan_id', Auth::id())
                ->where('tahun_cuti', $tahunSekarang)
                ->where('jenis_cuti', 'tahunan')
                ->where('status', 'disetujui')
                ->sum('total_hari');
            $sisaKuota = 12 - $kuotaTerpakai;

            if ($totalHari > $sisaKuota) {
                return redirect()->back()->with('error', 'Insufficient annual leave quota. Remaining quota: ' . $sisaKuota . ' days')->withInput();
            }
        }

        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $lampiranPath = $request->file('lampiran')->store('lampiran-cuti', 'public');
        }

        PengajuanCuti::create([
            'karyawan_id' => Auth::id(),
            'nama_karyawan' => $karyawan->nama_lengkap,
            'jenis_cuti' => $request->jenis_cuti,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'total_hari' => $totalHari,
            'alasan' => $request->alasan,
            'status' => 'pending',
            'lampiran' => $lampiranPath,
            'tahun_cuti' => date('Y'),
            'kuota_total' => 12,
            'kuota_terpakai' => 0,
            'sisa_kuota' => 12,
        ]);

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

        $tahunSekarang = date('Y');
        $kuotaTotal = 12;
        $kuotaTerpakai = PengajuanCuti::where('karyawan_id', Auth::id())
            ->where('tahun_cuti', $tahunSekarang)
            ->where('jenis_cuti', 'tahunan')
            ->where('status', 'disetujui')
            ->where('id', '!=', $id)
            ->sum('total_hari');
        $sisaKuota = max(0, $kuotaTotal - $kuotaTerpakai);

        return view('cuti.edit', compact('cuti', 'sisaKuota'));
    }

    // Employee: Update leave request
    public function update(Request $request, $id)
    {
        $cuti = PengajuanCuti::where('karyawan_id', Auth::id())->findOrFail($id);

        if ($cuti->status != 'pending') {
            return redirect()->route('cuti.index')->with('error', 'A processed leave request cannot be changed');
        }

        $request->validate([
            'jenis_cuti' => 'required|in:tahunan,sakit,melahirkan,penting,ibadah,lainnya',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string|min:10',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        $totalHari = PengajuanCuti::hitungTotalHari($request->tanggal_mulai, $request->tanggal_selesai);

        // Check quota for annual leave
        if ($request->jenis_cuti == 'tahunan') {
            $tahunSekarang = date('Y');
            $kuotaTerpakai = PengajuanCuti::where('karyawan_id', Auth::id())
                ->where('tahun_cuti', $tahunSekarang)
                ->where('jenis_cuti', 'tahunan')
                ->where('status', 'disetujui')
                ->where('id', '!=', $id)
                ->sum('total_hari');
            $sisaKuota = 12 - $kuotaTerpakai;

            if ($totalHari > $sisaKuota) {
                return redirect()->back()->with('error', 'Insufficient annual leave quota. Remaining quota: ' . $sisaKuota . ' days')->withInput();
            }
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

            if ($cuti->jenis_cuti == 'tahunan') {
                $tahunCuti = date('Y', strtotime($cuti->tanggal_mulai));
                $kuotaTerpakaiBaru = PengajuanCuti::where('karyawan_id', $cuti->karyawan_id)
                    ->where('tahun_cuti', $tahunCuti)
                    ->where('jenis_cuti', 'tahunan')
                    ->where('status', 'disetujui')
                    ->sum('total_hari') + $cuti->total_hari;

                $cuti->kuota_terpakai = $kuotaTerpakaiBaru;
                $cuti->sisa_kuota = max(0, $cuti->kuota_total - $kuotaTerpakaiBaru);
            }
        }

        if ($request->filled('catatan')) {
            $cuti->catatan = $request->catatan;
        }

        $cuti->save();

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
}
