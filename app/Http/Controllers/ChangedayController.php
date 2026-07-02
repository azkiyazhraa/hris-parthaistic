<?php

namespace App\Http\Controllers;

use App\Models\AbsensiKaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChangedayController extends Controller
{
    public function index()
    {
        // Only admin/hr can access this
        $data = AbsensiKaryawan::with('karyawan')
            ->where('is_change_day', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Statistics
        $totalRequest = AbsensiKaryawan::where('is_change_day', true)
            ->whereMonth('created_at', now()->month)
            ->count();

        $approved = AbsensiKaryawan::where('is_change_day', true)
            ->where('change_day_status', AbsensiKaryawan::CHANGE_DAY_APPROVED)
            ->count();

        $requested = AbsensiKaryawan::where('is_change_day', true)
            ->where('change_day_status', AbsensiKaryawan::CHANGE_DAY_PENDING)
            ->count();

        $rejected = AbsensiKaryawan::where('is_change_day', true)
            ->where('change_day_status', AbsensiKaryawan::CHANGE_DAY_REJECTED)
            ->count();

        return view('admin.changeday.index', compact('data', 'totalRequest', 'approved', 'requested', 'rejected'));
    }

    // FOR EMPLPOYEES
    private function getEmployeeId()
    {
        return Auth::user()->id;
    }

    public function indexEmployee()
    {
        $query = AbsensiKaryawan::query()
            ->where('karyawan_id', $this->getEmployeeId())
            ->where('is_change_day', true)
            ->whereYear('created_at', now()->year);

        $data = (clone $query)
            ->latest()
            ->get();

        $monthlyQuery = (clone $query)
            ->whereMonth('created_at', now()->month);

        $totalRequest = (clone $monthlyQuery)->count();

        $approved = (clone $monthlyQuery)
            ->where('change_day_status', AbsensiKaryawan::CHANGE_DAY_APPROVED)
            ->count();

        $requested = (clone $monthlyQuery)
            ->where('change_day_status', AbsensiKaryawan::CHANGE_DAY_PENDING)
            ->count();

        $rejected = (clone $monthlyQuery)
            ->where('change_day_status', AbsensiKaryawan::CHANGE_DAY_REJECTED)
            ->count();

        return view('changeday.index', compact(
            'data',
            'approved',
            'requested',
            'rejected',
            'totalRequest'
        ));
    }

    public function requestChangeDay(Request $request)
    {
        $request->validate([
            'original_date'  => 'required|date',
            'requested_date' => 'required|date',
            'reason'         => 'required|string|max:500',
        ]);

        // SIMPAN DATA ATTACHMENT JIKA ADA
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments-changeday', 'public');
        }

        $data = [
            'karyawan_id'              => $this->getEmployeeId(),
            'nama_karyawan'            => Auth::user()->nama_lengkap,
            'tanggal'                  => now(),
            'is_change_day'            => true,
            'change_day_tanggal_awal'  => $request->input('original_date'),
            'change_day_tanggal_akhir' => $request->input('requested_date'),
            'change_day_alasan'        => $request->input('reason'),
            'attachment'               => $attachmentPath,
            'change_day_status'        => AbsensiKaryawan::CHANGE_DAY_PENDING,
        ];

        AbsensiKaryawan::create($data);

        return redirect()->route('changeday.index')
            ->with('success', 'Change day request submitted successfully');
    }

    public function show($id)
    {
        $data = AbsensiKaryawan::with(['karyawan', 'disetujuiOleh'])->findOrFail($id);

        return response()->json($data);
    }

    public function updateRequest(Request $request, $id)
    {
        $record = AbsensiKaryawan::where('karyawan_id', $this->getEmployeeId())
            ->where('id', $id)
            ->where('is_change_day', true)
            ->firstOrFail();

        if ($record->change_day_status !== AbsensiKaryawan::CHANGE_DAY_PENDING) {
            return redirect()->route('changeday.index')
                ->with('error', 'Only pending requests can be edited.');
        }

        $request->validate([
            'original_date'  => 'required|date',
            'requested_date' => 'required|date',
            'reason'         => 'required|string|max:500',
        ]);

        $record->update([
            'change_day_tanggal_awal'  => $request->original_date,
            'change_day_tanggal_akhir' => $request->requested_date,
            'change_day_alasan'        => $request->reason,
        ]);

        return redirect()->route('changeday.index')
            ->with('success', 'Change day request updated successfully.');
    }

    public function cancelRequest($id)
    {
        $record = AbsensiKaryawan::where('karyawan_id', $this->getEmployeeId())
            ->where('id', $id)
            ->where('is_change_day', true)
            ->firstOrFail();

        if ($record->change_day_status !== AbsensiKaryawan::CHANGE_DAY_PENDING) {
            return redirect()->route('changeday.index')
                ->with('error', 'Only pending requests can be cancelled.');
        }

        $record->delete();

        return redirect()->route('changeday.index')
            ->with('success', 'Change day request cancelled successfully.');
    }
}
