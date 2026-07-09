<?php

namespace App\Http\Controllers;

use App\Exports\PenggajianExport;
use App\Models\AbsensiKaryawan;
use App\Models\Karyawan;
use App\Models\Notifikasi;
use App\Models\Penggajian;
use App\Services\FontteService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;

class PenggajianController extends Controller
{
    // For Employees - View their own payslips
    public function index()
    {
        $karyawanId = auth()->id();

        $penggajian = Penggajian::where('karyawan_id', $karyawanId)
            ->where('status', Penggajian::STATUS_PAID)
            ->where('tahun', Carbon::now()->year)
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();

        $totalSalary = Penggajian::where('karyawan_id', $karyawanId)
            ->where('status', Penggajian::STATUS_PAID)
            ->where('tahun', Carbon::now()->year)
            ->sum('net_salary');

        $totalDeducations = Penggajian::where('karyawan_id', $karyawanId)
            ->where('status', Penggajian::STATUS_PAID)
            ->where('tahun', Carbon::now()->year)
            ->sum('total_deductions');

        $totalAllowances = Penggajian::where('karyawan_id', $karyawanId)
            ->where('status', Penggajian::STATUS_PAID)
            ->where('tahun', Carbon::now()->year)
            ->sum('transport_allowance', 'meal_allowance', 'internet_allowance', 'position_allowance', 'incentive');

        return view('penggajian.index', compact('penggajian', 'totalSalary', 'totalDeducations', 'totalAllowances'));
    }

    public function show($id)
    {
        $karyawanId = auth()->id();

        $penggajian = Penggajian::with('karyawan')
            ->where('karyawan_id', $karyawanId)
            ->where('id', $id)
            ->firstOrFail();

        return response()->json($penggajian);
    }

    // For Admin/HR
    public function adminIndex(Request $request)
    {
        $query = Penggajian::with('karyawan')->orderBy('tahun', 'desc')->orderBy('bulan', 'desc');

        if ($request->bulan && $request->tahun) {
            $query->where('bulan', $request->bulan)->where('tahun', $request->tahun);
        } elseif ($request->bulan) {
            $query->where('bulan', $request->bulan);
        } elseif ($request->tahun) {
            $query->where('tahun', $request->tahun);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->karyawan_id) {
            $query->where('karyawan_id', $request->karyawan_id);
        }

        $penggajian = $query->paginate(15);
        $karyawans = Karyawan::orderBy('nama_lengkap')->get();

        $statistics = [
            'total_payroll' => Penggajian::where('status', Penggajian::STATUS_PAID)->sum('net_salary'),
            'total_pending' => Penggajian::where('status', Penggajian::STATUS_PENDING)->count(),
            'total_approved' => Penggajian::where('status', Penggajian::STATUS_APPROVED)->count(),
            'total_paid' => Penggajian::where('status', Penggajian::STATUS_PAID)->count(),
        ];

        return view('admin.penggajian.index', compact('penggajian', 'karyawans', 'statistics'));
    }

    public function adminCreate()
    {
        $karyawans = Karyawan::where('role', 'karyawan')
            ->orderBy('nip', 'asc')
            ->get();

        $bulan = range(1, 12);

        return view('admin.penggajian.create', compact('karyawans', 'bulan'));
    }

    public function adminStore(Request $request)
    {
        $karyawan = Karyawan::find($request->karyawan_id);

        $tahunSekarang = Carbon::now()->year;

        $exists = Penggajian::where('karyawan_id', $request->karyawan_id)
            ->where('bulan', $request->bulan)
            ->where('tahun', $tahunSekarang)
            ->exists();

        if ($exists) {
            $monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            $monthName  = $monthNames[(int)$request->bulan - 1] ?? $request->bulan;
            return redirect()->back()
                ->with('error', "Payroll for {$karyawan->nama_lengkap} in {$monthName} {$tahunSekarang} already exists.")
                ->withInput();
        }

        $penggajian = new Penggajian;
        $penggajian->karyawan_id = $request->karyawan_id;
        $penggajian->nama_karyawan = $karyawan->nama_lengkap;
        $penggajian->bulan = $request->bulan;
        $penggajian->tahun = $tahunSekarang;
        $penggajian->gaji_pokok = (float) str_replace('.', '', $request->gaji_pokok);
        $penggajian->transport_allowance = (float) str_replace('.', '', $request->transport_allowance);
        $penggajian->meal_allowance = (float) str_replace('.', '', $request->meal_allowance);
        $penggajian->internet_allowance = (float) str_replace('.', '', $request->internet_allowance);
        $penggajian->position_allowance = (float) str_replace('.', '', $request->position_allowance);
        $penggajian->incentive = (float) str_replace('.', '', $request->incentive);
        $penggajian->tax = (float) str_replace('.', '', $request->tax);
        $penggajian->bpjs_kesehatan = (float) str_replace('.', '', $request->bpjs_kesehatan);
        $penggajian->bpjs_ketenagakerjaan = (float) str_replace('.', '', $request->bpjs_ketenagakerjaan);
        $penggajian->late_absent_deduction = (float) str_replace('.', '', $request->late_absent_deduction);
        $penggajian->loan_deduction = (float) str_replace('.', '', $request->loan_deduction);

        $penggajian->total_earnings = $penggajian->calculateTotalEarnings();
        $penggajian->total_deductions = $penggajian->calculateTotalDeductions();
        $penggajian->net_salary = $penggajian->calculateNetSalary();

        $penggajian->tanggal_pembayaran = $request->tanggal_pembayaran;
        $penggajian->metode_pembayaran = $request->metode_pembayaran;
        $penggajian->nama_bank = $request->nama_bank;
        $penggajian->nomor_rekening = $request->nomor_rekening;
        $penggajian->status = $request->status;
        $penggajian->catatan = $request->catatan;
        $penggajian->dibuat_oleh = Auth::user()->nama_lengkap;
        $penggajian->detail_gaji = json_encode([
            'created_by' => Auth::user()->nama_lengkap,
            'role' => Auth::user()->role,
            'created_at' => now(),
        ]);

        try {
            $penggajian->save();
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            return redirect()->back()
                ->with('error', "Payroll for {$karyawan->nama_lengkap} in that period already exists.")
                ->withInput();
        }

        if (in_array($request->status, ['approved', 'paid'])) {
            Notifikasi::create([
                'user_id' => $karyawan->id,
                'judul' => 'Slip Gaji Tersedia',
                'pesan' => 'Slip gaji untuk periode ' . $this->getBulanText($request->bulan) . Carbon::now()->year . " telah tersedia.",
                'tipe_notifikasi' => 'penggajian',
            ]);
        }

        return redirect()->route('admin.penggajian.index')
            ->with('success', 'Payroll data added successfully');
    }

    public function adminEdit($id)
    {
        $penggajian = Penggajian::findOrFail($id);
        $karyawans = Karyawan::whereIn('status', ['Permanent', 'Contract', 'Outsource'])
            ->orderBy('nama_lengkap')
            ->get();
        $bulan = range(1, 12);
        $tahun = range(date('Y') - 2, date('Y') + 1);

        return view('admin.penggajian.edit', compact('penggajian', 'karyawans', 'bulan', 'tahun'));
    }

    public function adminUpdate(Request $request, $id)
    {
        $penggajian = Penggajian::findOrFail($id);
        $karyawan = Karyawan::find($request->karyawan_id);

        $exists = Penggajian::where('karyawan_id', $request->karyawan_id)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'A payroll entry for this employee in that period already exists')
                ->withInput();
        }

        $penggajian->karyawan_id = $request->karyawan_id;
        $penggajian->nama_karyawan = $karyawan->nama_lengkap;
        $penggajian->bulan = $request->bulan;
        $penggajian->tahun = $request->tahun;
        $penggajian->gaji_pokok = (float) str_replace('.', '', $request->gaji_pokok);
        $penggajian->transport_allowance = (float) str_replace('.', '', $request->transport_allowance);
        $penggajian->meal_allowance = (float) str_replace('.', '', $request->meal_allowance);
        $penggajian->internet_allowance = (float) str_replace('.', '', $request->internet_allowance);
        $penggajian->position_allowance = (float) str_replace('.', '', $request->position_allowance);
        $penggajian->incentive = (float) str_replace('.', '', $request->incentive);
        $penggajian->tax = (float) str_replace('.', '', $request->tax);
        $penggajian->bpjs_kesehatan = (float) str_replace('.', '', $request->bpjs_kesehatan);
        $penggajian->bpjs_ketenagakerjaan = (float) str_replace('.', '', $request->bpjs_ketenagakerjaan);
        $penggajian->late_absent_deduction = (float) str_replace('.', '', $request->late_absent_deduction);
        $penggajian->loan_deduction = (float) str_replace('.', '', $request->loan_deduction);

        $penggajian->total_earnings = $penggajian->calculateTotalEarnings();
        $penggajian->total_deductions = $penggajian->calculateTotalDeductions();
        $penggajian->net_salary = $penggajian->calculateNetSalary();

        $penggajian->tanggal_pembayaran = $request->tanggal_pembayaran;
        $penggajian->metode_pembayaran = $request->metode_pembayaran;
        $penggajian->nama_bank = $request->nama_bank;
        $penggajian->nomor_rekening = $request->nomor_rekening;
        $penggajian->status = $request->status;
        $penggajian->catatan = $request->catatan;

        $detailGaji = json_decode($penggajian->detail_gaji, true) ?? [];
        $detailGaji['updated_by'] = Auth::user()->nama_lengkap;
        $detailGaji['updated_at'] = now();
        $penggajian->detail_gaji = json_encode($detailGaji);

        $penggajian->save();

        if ($penggajian->wasChanged('status')) {
            Notifikasi::create([
                'user_id' => $karyawan->id,
                'judul' => 'Payroll Status Updated',
                'pesan' => 'Your payroll status for ' . $this->getBulanText($request->bulan) . " {$request->tahun} has been changed to " . strtoupper($request->status),
                'tipe_notifikasi' => 'penggajian',
            ]);
        }


        // update status paid dan hit ke api parthafin
        if ($penggajian->status == Penggajian::STATUS_PAID && env('PARTHAFIN_API_URL')) {
            try {
                Http::timeout(5)->post(env('PARTHAFIN_API_URL') . '/expense-salary', [
                    "category_id" => 6,
                    "date" => $penggajian->tanggal_pembayaran,
                    "amount" => $penggajian->net_salary,
                    "description" => "gaji a.n " . $penggajian->karyawan->nama_lengkap,
                ]);
            } catch (\Exception $e) {
                // Parthafin API unavailable — proceed without blocking payroll update
            }
        }


        return redirect()->route('admin.penggajian.index')
            ->with('success', 'Payroll data updated successfully');
    }

    public function adminDestroy($id)
    {
        $penggajian = Penggajian::findOrFail($id);

        if ($penggajian->status == Penggajian::STATUS_PAID) {
            return redirect()->route('admin.penggajian.index')
                ->with('error', 'Paid payroll cannot be deleted');
        }

        $penggajian->delete();

        return redirect()->route('admin.penggajian.index')
            ->with('success', 'Payroll data deleted successfully');
    }

    public function adminUpdateStatus(Request $request, $id)
    {
        $penggajian = Penggajian::findOrFail($id);

        $request->validate([
            'status' => 'required|in:draft,pending,approved,paid,cancelled',
            'tanggal_pembayaran' => 'nullable|date',
            'metode_pembayaran' => 'nullable|in:transfer,tunai,cek',
            'nama_bank' => 'nullable|string|max:50',
            'nomor_rekening' => 'nullable|string|max:50',
            'catatan' => 'nullable',
        ]);

        $updateData = [
            'status' => $request->status,
            'catatan' => $request->catatan,
        ];

        if ($request->status == Penggajian::STATUS_PAID) {
            $updateData['tanggal_pembayaran'] = $request->tanggal_pembayaran ?: now();
            $updateData['metode_pembayaran'] = $request->metode_pembayaran;
            $updateData['nama_bank'] = $request->nama_bank;
            $updateData['nomor_rekening'] = $request->nomor_rekening;
        }

        $penggajian->update($updateData);

        $detailGaji = json_decode($penggajian->detail_gaji, true) ?? [];
        $detailGaji['status_updated_by'] = Auth::user()->nama_lengkap;
        $detailGaji['status_updated_at'] = now();
        $detailGaji['status_updated_to'] = $request->status;
        $penggajian->detail_gaji = json_encode($detailGaji);
        $penggajian->save();

        Notifikasi::create([
            'user_id' => $penggajian->karyawan_id,
            'judul' => 'Payroll Status Updated',
            'pesan' => 'Your payroll status for ' . $this->getBulanText($penggajian->bulan) . " {$penggajian->tahun} has been changed to " . strtoupper($request->status),
            'tipe_notifikasi' => 'penggajian',
        ]);

        return redirect()->route('admin.penggajian.index')
            ->with('success', 'Payroll status updated successfully');
    }

    public function adminShow($id)
    {
        $penggajian = Penggajian::with('karyawan')->findOrFail($id);

        return response()->json($penggajian);
    }

    public function getJson($id)
    {
        $penggajian = Penggajian::with('karyawan')->where('karyawan_id', Auth::id())->findOrFail($id);

        // Generate status badge HTML
        $statusBadge = '';
        switch ($penggajian->status) {
            case 'draft':
                $statusBadge = '<span class="px-2 py-1 text-xs text-gray-800 bg-gray-100 rounded-full">Draft</span>';
                break;
            case 'pending':
                $statusBadge = '<span class="px-2 py-1 text-xs text-yellow-800 bg-yellow-100 rounded-full">Pending</span>';
                break;
            case 'approved':
                $statusBadge = '<span class="px-2 py-1 text-xs text-blue-800 bg-blue-100 rounded-full">Approved</span>';
                break;
            case 'paid':
                $statusBadge = '<span class="px-2 py-1 text-xs text-green-800 bg-green-100 rounded-full">Paid</span>';
                break;
            default:
                $statusBadge = '<span class="px-2 py-1 text-xs bg-gray-100 rounded-full">' . ucfirst($penggajian->status) . '</span>';
        }

        return response()->json([
            'id' => $penggajian->id,
            'nama_karyawan' => $penggajian->nama_karyawan,
            'bulan' => $penggajian->bulan,
            'tahun' => $penggajian->tahun,
            'bulan_text' => $penggajian->bulan_text,
            'gaji_pokok' => $penggajian->gaji_pokok,
            'transport_allowance' => $penggajian->transport_allowance,
            'meal_allowance' => $penggajian->meal_allowance,
            'internet_allowance' => $penggajian->internet_allowance,
            'position_allowance' => $penggajian->position_allowance,
            'incentive' => $penggajian->incentive,
            'total_earnings' => $penggajian->total_earnings,
            'tax' => $penggajian->tax,
            'bpjs_kesehatan' => $penggajian->bpjs_kesehatan,
            'bpjs_ketenagakerjaan' => $penggajian->bpjs_ketenagakerjaan,
            'late_absent_deduction' => $penggajian->late_absent_deduction,
            'loan_deduction' => $penggajian->loan_deduction,
            'total_deductions' => $penggajian->total_deductions,
            'net_salary' => $penggajian->net_salary,
            'status' => $penggajian->status,
            'status_badge' => $statusBadge,
            'catatan' => $penggajian->catatan,
            'tanggal_pembayaran' => $penggajian->tanggal_pembayaran,
            'metode_pembayaran' => $penggajian->metode_pembayaran,
            'nama_bank' => $penggajian->nama_bank,
            'nomor_rekening' => $penggajian->nomor_rekening,
        ]);
    }

    public function sendPayslip($id)
    {
        $penggajian = Penggajian::with('karyawan')->findOrFail($id);
        $karyawan = $penggajian->karyawan;

        if (! $karyawan || ! $karyawan->nomor_telepon) {
            return redirect()->back()->with('error', 'Employee WhatsApp number not found in database');
        }

        try {
            $message = $this->buildPayslipMessage($penggajian);
            $fonnte = new FontteService();
            $result = $fonnte->send($karyawan->nomor_telepon, $message);

            if ($result['status'] ?? false) {
                $penggajian->payslip_sent_at = now();
                $penggajian->payslip_sent_by = Auth::user()->nama_lengkap;
                $penggajian->save();

                return redirect()->back()->with('success', 'Payslip successfully sent to ' . $karyawan->nama_lengkap . '\'s WhatsApp');
            }

            return redirect()->back()->with('error', 'Failed to send: ' . ($result['reason'] ?? 'Unknown error'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to send: ' . $e->getMessage());
        }
    }

    public function bulkSendWhatsapp(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['success' => 0, 'failed' => 0, 'results' => []]);
        }

        $fonnte = new FontteService();
        $results = [];
        $successCount = 0;
        $failedCount = 0;

        foreach ($ids as $id) {
            $penggajian = Penggajian::with('karyawan')->find($id);

            if (! $penggajian) {
                $results[] = ['name' => 'ID: ' . $id, 'status' => 'failed', 'reason' => 'Data not found'];
                $failedCount++;
                continue;
            }

            $karyawan = $penggajian->karyawan;

            if (! $karyawan || ! $karyawan->nomor_telepon) {
                $results[] = ['name' => $penggajian->nama_karyawan, 'status' => 'failed', 'reason' => 'WhatsApp number not found'];
                $failedCount++;
                continue;
            }

            $message = $this->buildPayslipMessage($penggajian);
            $result = $fonnte->send($karyawan->nomor_telepon, $message);

            if ($result['status'] ?? false) {
                $penggajian->payslip_sent_at = now();
                $penggajian->payslip_sent_by = Auth::user()->nama_lengkap;
                $penggajian->save();

                $results[] = ['name' => $karyawan->nama_lengkap, 'status' => 'sent'];
                $successCount++;
            } else {
                $results[] = ['name' => $karyawan->nama_lengkap, 'status' => 'failed', 'reason' => $result['reason'] ?? 'Failed to send'];
                $failedCount++;
            }
        }

        return response()->json(['success' => $successCount, 'failed' => $failedCount, 'results' => $results]);
    }

    private function buildPayslipMessage(Penggajian $penggajian): string
    {
        $nama = $penggajian->karyawan->nama_lengkap ?? $penggajian->nama_karyawan;
        $periode = $this->getBulanText($penggajian->bulan) . ' ' . $penggajian->tahun;
        $gajiPokok = 'Rp ' . number_format($penggajian->gaji_pokok, 0, ',', '.');
        $tunjangan = 'Rp ' . number_format($penggajian->total_earnings - $penggajian->gaji_pokok, 0, ',', '.');
        $potongan = 'Rp ' . number_format($penggajian->total_deductions, 0, ',', '.');
        $netSalary = 'Rp ' . number_format($penggajian->net_salary, 0, ',', '.');
        $downloadUrl = url('/penggajian/' . $penggajian->id . '/download');

        return "Hello *{$nama}* 👋\n\n"
            . "Your payslip for *{$periode}* is now available.\n\n"
            . "📋 *Salary Summary:*\n"
            . "▪ Base Salary: {$gajiPokok}\n"
            . "▪ Total Additional Earnings: {$tunjangan}\n"
            . "▪ Total Deductions: {$potongan}\n"
            . "▪ *Net Salary: {$netSalary}*\n\n"
            . "📥 *Download Payslip:*\n"
            . "{$downloadUrl}\n\n"
            . "_Sent automatically by Parthaistic HR System_";
    }

    public function downloadPayslip($id)
    {
        ini_set('memory_limit', '512M');

        $penggajian = Penggajian::with('karyawan')->findOrFail($id);

        if (! (auth()->user()->isAdmin() || auth()->user()->isHR() || auth()->id() == $penggajian->karyawan_id)) {
            abort(403, 'Unauthorized');
        }

        $bulan = $penggajian->bulan;
        $tahun = $penggajian->tahun;

        // --- Fetch national holidays from API ---
        // Response: [{"date":"YYYY-MM-DD","name":"...","is_national_holiday":true/false}, ...]
        $nationalHolidays = [];
        try {
            $response = Http::timeout(5)->get('https://libur.deno.dev/api', ['year' => $tahun]);
            if ($response->successful()) {
                foreach ($response->json() as $item) {
                    if (isset($item['date'])) {
                        $nationalHolidays[] = $item['date'];
                    }
                }
            }
        } catch (\Exception $e) {
            // Proceed without holidays if API unavailable
        }

        // --- Count working days in the month (Mon–Sat, excluding national holidays) ---
        $totalHariKerja = 0;
        $current = Carbon::create($tahun, $bulan, 1)->startOfDay();
        $endOfMonth = $current->copy()->endOfMonth()->startOfDay();
        while ($current <= $endOfMonth) {
            if ($current->dayOfWeek !== Carbon::SUNDAY && ! in_array($current->format('Y-m-d'), $nationalHolidays)) {
                $totalHariKerja++;
            }
            $current->addDay();
        }

        // --- Count employee present days from attendance records ---
        $totalMasuk = AbsensiKaryawan::where('karyawan_id', $penggajian->karyawan_id)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->where('status_kehadiran', 'present')
            ->count();

        $pdf = Pdf::loadView('admin.penggajian.payslip', [
            'penggajian'     => $penggajian,
            'karyawan'       => $penggajian->karyawan,
            'totalHariKerja' => $totalHariKerja,
            'totalMasuk'     => $totalMasuk,
        ])->setPaper('a4', 'portrait');

        $filename = 'Payslip_' .
            str_replace(' ', '_', $penggajian->karyawan->nama_lengkap) . '_' .
            $this->getBulanText($penggajian->bulan) . '_' .
            $penggajian->tahun . '.pdf';

        return $pdf->download($filename);
    }

    // public function exportReport(Request $request)
    // {
    //     $query = Penggajian::with('karyawan');

    //     if ($request->bulan && $request->tahun) {
    //         $query->where('bulan', $request->bulan)->where('tahun', $request->tahun);
    //     }

    //     if ($request->status) {
    //         $query->where('status', $request->status);
    //     }

    //     $penggajian = $query->get();

    //     $fileName = 'laporan_gaji_' . date('Y-m-d') . '.csv';
    //     $headers = [
    //         'Content-Type' => 'text/csv',
    //         'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
    //     ];

    //     $callback = function () use ($penggajian) {
    //         $file = fopen('php://output', 'w');
    //         fputcsv($file, ['ID', 'Nama Karyawan', 'NIP', 'Email', 'Role', 'Bulan', 'Tahun', 'Status', 'Gaji Pokok', 'Transport', 'Meal', 'Internet', 'Position', 'Incentive', 'Total Earnings', 'Tax', 'BPJS Kesehatan', 'BPJS Ketenagakerjaan', 'Late/Absent', 'Loan', 'Total Deductions', 'Net Salary']);

    //         foreach ($penggajian as $item) {
    //             fputcsv($file, [
    //                 $item->id,
    //                 $item->nama_karyawan,
    //                 $item->karyawan->nip ?? '-',
    //                 $item->karyawan->email ?? '-',
    //                 $item->karyawan->role ?? '-',
    //                 $this->getBulanText($item->bulan),
    //                 $item->tahun,
    //                 $item->status,
    //                 number_format($item->gaji_pokok, 0, ',', '.'),
    //                 number_format($item->transport_allowance, 0, ',', '.'),
    //                 number_format($item->meal_allowance, 0, ',', '.'),
    //                 number_format($item->internet_allowance, 0, ',', '.'),
    //                 number_format($item->position_allowance, 0, ',', '.'),
    //                 number_format($item->incentive, 0, ',', '.'),
    //                 number_format($item->total_earnings, 0, ',', '.'),
    //                 number_format($item->tax, 0, ',', '.'),
    //                 number_format($item->bpjs_kesehatan, 0, ',', '.'),
    //                 number_format($item->bpjs_ketenagakerjaan, 0, ',', '.'),
    //                 number_format($item->late_absent_deduction, 0, ',', '.'),
    //                 number_format($item->loan_deduction, 0, ',', '.'),
    //                 number_format($item->total_deductions, 0, ',', '.'),
    //                 number_format($item->net_salary, 0, ',', '.'),
    //             ]);
    //         }

    //         fclose($file);
    //     };

    //     return response()->stream($callback, 200, $headers);
    // }

    public function exportReport(Request $request)
    {
        $fileName = 'laporan_gaji_' . date('Y-m-d') . '.xlsx';

        return Excel::download(
            new PenggajianExport($request->bulan, $request->tahun, $request->status),
            $fileName
        );
    }

    private function getBulanText($bulan)
    {
        $bulanNama = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];

        return $bulanNama[$bulan] ?? '-';
    }
}
