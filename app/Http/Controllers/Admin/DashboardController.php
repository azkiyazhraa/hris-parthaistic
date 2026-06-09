<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsensiKaryawan;
use App\Models\Karyawan;
use App\Models\Pengumuman;
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
            SUM(status_kehadiran = ?) as hadir,
            SUM(status_kehadiran = ?) as izin,
            SUM(status_kehadiran = ?) as sakit,
            SUM(status_kehadiran = ?) as alpha
        ', [
            AbsensiKaryawan::STATUS_PENDING,
            AbsensiKaryawan::STATUS_HADIR,
            AbsensiKaryawan::STATUS_IZIN,
            AbsensiKaryawan::STATUS_SAKIT,
            AbsensiKaryawan::STATUS_ALPHA,
        ])
            ->where('is_change_day', false)
            ->whereDate('tanggal', today())
            ->first();

        $statistics = [
            'total' => (int) $attendanceCounts->total,
            'pending' => (int) $attendanceCounts->pending,
            'hadir' => (int) $attendanceCounts->hadir,
            'izin' => (int) $attendanceCounts->izin,
            'sakit' => (int) $attendanceCounts->sakit,
            'alpha' => (int) $attendanceCounts->alpha,
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
                ->with('success', 'Karyawan berhasil ditambahkan');

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
            ->with('success', 'Karyawan berhasil diupdate');
    }

    public function destroyKaryawan($id)
    {
        $karyawan = Karyawan::findOrFail($id);

        // Cegah hapus akun sendiri
        if ($karyawan->id === auth()->id()) {
            return redirect()
                ->route('admin.karyawan')
                ->with('error', 'Anda tidak dapat menghapus akun sendiri');
        }

        // Hapus foto profil jika ada
        if (! empty($karyawan->foto_profil) && Storage::disk('public')->exists($karyawan->foto_profil)) {
            Storage::disk('public')->delete($karyawan->foto_profil);
        }

        $karyawan->delete();

        return redirect()
            ->route('admin.karyawan')
            ->with('success', 'Karyawan berhasil dihapus');
    }

    public function showKaryawanPassword($id)
    {
        $karyawan = Karyawan::findOrFail($id);

        return response()->json([
            'hashed_password' => $karyawan->kata_sandi,
            'message' => 'This is the hashed password. In production, password reset functionality should be used instead.',
        ]);
    }
}
