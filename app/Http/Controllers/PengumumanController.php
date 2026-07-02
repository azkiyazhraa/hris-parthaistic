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
            'tanggal_berlaku_hingga' => ['nullable', 'date', 'after_or_equal:tanggal_terbit', 'after_or_equal:today'],
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ], [
            'tanggal_berlaku_hingga.after_or_equal' => 'Valid Until date cannot be in the past, otherwise the announcement will be hidden from employees immediately.',
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

        // Only notify targeted users once the announcement is actually published
        if ($pengumuman->status) {
            $this->sendNotifications($pengumuman);
        }

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
        $wasPublished = $pengumuman->status;

        $request->validate([
            'judul' => 'required|max:200',
            'konten' => 'required',
            'kategori' => 'nullable|max:50',
            'target_role' => 'nullable|in:all,admin,hr,karyawan',
            'tanggal_terbit' => 'nullable|date',
            'tanggal_berlaku_hingga' => [
                'nullable',
                'date',
                'after_or_equal:tanggal_terbit',
                function ($attribute, $value, $fail) use ($pengumuman) {
                    if (!$value) {
                        return;
                    }
                    $oldValue = optional($pengumuman->tanggal_berlaku_hingga)->format('Y-m-d');
                    // Only block newly-set past dates; leave already-archived announcements editable
                    if ($value !== $oldValue && Carbon::parse($value)->lt(Carbon::today())) {
                        $fail('Valid Until date cannot be in the past, otherwise the announcement will be hidden from employees immediately.');
                    }
                },
            ],
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

        // Only notify on the draft -> published transition, not on every subsequent edit
        if (!$wasPublished && $pengumuman->status) {
            $this->sendNotifications($pengumuman);
        }

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

    // Announcements currently visible to the given user (published, targeted, within date range)
    private function visibleToUser($user)
    {
        return Pengumuman::where('status', true)
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
            });
    }

    // For Employees to view announcements
    public function employeeIndex()
    {
        $pengumuman = $this->visibleToUser(Auth::user())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pengumuman.index', compact('pengumuman'));
    }

    public function employeeShow($id)
    {
        $pengumuman = $this->visibleToUser(Auth::user())
            ->with('pembuat')
            ->findOrFail($id);

        return response()->json($pengumuman);
    }
}
