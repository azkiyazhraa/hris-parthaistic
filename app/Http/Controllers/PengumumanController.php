<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Notifikasi;
use App\Models\Pengumuman;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PengumumanController extends Controller
{
    // For Admin/HR
    public function index()
    {
        $pengumuman = Pengumuman::with('pembuat')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.pengumuman.index', compact('pengumuman'));
    }

    public function create()
    {
        return view('admin.pengumuman.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|max:200',
            'konten' => 'required',
            'kategori' => 'nullable|max:50',
            'target_role' => 'nullable|in:all,admin,hr,karyawan',
            'tanggal_terbit' => 'nullable|date',
            'tanggal_berlaku_hingga' => 'nullable|date|after_or_equal:tanggal_terbit',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        $data = $request->except(['lampiran']);
        $data['dibuat_oleh'] = Auth::id();
        $data['status'] = $request->has('status');

        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('pengumuman', $filename, 'public');
            $data['lampiran'] = $path;
        }

        if (empty($data['tanggal_terbit'])) {
            $data['tanggal_terbit'] = Carbon::now();
        }

        $pengumuman = Pengumuman::create($data);

        // Create notifications for targeted users
        $this->sendNotifications($pengumuman);

        return redirect()->route('admin.pengumuman.index')->with('success', 'Announcement created and notifications sent successfully');
    }

    public function show($id)
    {
        $pengumuman = Pengumuman::with('pembuat')->findOrFail($id);

        return response()->json($pengumuman);
    }

    public function edit($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        $karyawans = Karyawan::all();

        return view('admin.pengumuman.edit', compact('pengumuman', 'karyawans'));
    }

    public function update(Request $request, $id)
    {
        $pengumuman = Pengumuman::findOrFail($id);

        $request->validate([
            'judul' => 'required|max:200',
            'konten' => 'required',
            'kategori' => 'nullable|max:50',
            'target_role' => 'nullable|in:all,admin,hr,karyawan',
            'tanggal_terbit' => 'nullable|date',
            'tanggal_berlaku_hingga' => 'nullable|date|after_or_equal:tanggal_terbit',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        $data = $request->except(['lampiran']);
        $data['status'] = $request->has('status');

        if ($request->hasFile('lampiran')) {
            if ($pengumuman->lampiran) {
                Storage::disk('public')->delete($pengumuman->lampiran);
            }
            $file = $request->file('lampiran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('pengumuman', $filename, 'public');
            $data['lampiran'] = $path;
        }

        if (empty($data['tanggal_terbit'])) {
            $data['tanggal_terbit'] = Carbon::now();
        }

        $pengumuman->update($data);

        $this->sendNotifications($pengumuman);

        return redirect()->route('admin.pengumuman.index')
            ->with('success', 'Announcement updated successfully');
    }

    public function destroy($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);

        if ($pengumuman->lampiran) {
            Storage::disk('public')->delete($pengumuman->lampiran);
        }

        $pengumuman->delete();

        return redirect()->route('admin.pengumuman.index')
            ->with('success', 'Announcement deleted successfully');
    }

    private function sendNotifications($pengumuman)
    {
        $query = Karyawan::query();

        if ($pengumuman->target_role && $pengumuman->target_role !== 'all') {
            $query->where('role', $pengumuman->target_role);
        }

        $users = $query->get();

        foreach ($users as $user) {
            Notifikasi::create([
                'user_id' => $user->id,
                'judul' => $pengumuman->judul,
                'pesan' => substr($pengumuman->konten, 0, 200) . (strlen($pengumuman->konten) > 200 ? '...' : ''),
                'tipe_notifikasi' => 'pengumuman',
                'status' => false,
            ]);
        }
    }

    // For Employees to view announcements
    public function employeeIndex()
    {
        $user = Auth::user();

        $pengumuman = Pengumuman::where('status', true)
            ->where(function ($query) use ($user) {
                $query->where('target_role', 'all')
                    ->orWhere('target_role', $user->role)
                    ->orWhereNull('target_role');
            })
            ->where(function ($query) {
                $query->whereNull('tanggal_berlaku_hingga')
                    ->orWhere('tanggal_berlaku_hingga', '>=', Carbon::today());
            })
            ->where(function ($query) {
                $query->whereNull('tanggal_terbit')
                    ->orWhere('tanggal_terbit', '<=', Carbon::now());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pengumuman.index', compact('pengumuman'));
    }

    public function employeeShow($id)
    {
        $pengumuman = Pengumuman::with('pembuat')->findOrFail($id);
        return response()->json($pengumuman);
    }
}
