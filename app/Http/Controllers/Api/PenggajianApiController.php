<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Penggajian;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PenggajianApiController extends Controller
{
    /**
     * Display a listing of penggajian with filters.
     * GET /api/v1/penggajian
     */
    public function index(Request $request)
    {
        try {
            $query = Penggajian::with(['karyawan:id,nama_lengkap,nip,email', 'pembuat:id,nama_lengkap', 'getPayslipSentByUser:id,nama_lengkap'])
                ->orderBy('created_at', 'desc');
            
            // Filter by karyawan_id
            if ($request->has('karyawan_id') && $request->karyawan_id) {
                $query->where('karyawan_id', $request->karyawan_id);
            }
            
            // Filter by bulan
            if ($request->has('bulan') && $request->bulan) {
                $query->where('bulan', $request->bulan);
            }
            
            // Filter by tahun
            if ($request->has('tahun') && $request->tahun) {
                $query->where('tahun', $request->tahun);
            }
            
            // Filter by status
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }
            
            // Search by nama karyawan
            if ($request->has('search') && $request->search) {
                $query->where('nama_karyawan', 'like', '%' . $request->search . '%');
            }
            
            // Pagination
            $perPage = $request->get('per_page', 15);
            $penggajian = $query->paginate($perPage);
            
            // Transform data
            $penggajian->getCollection()->transform(function ($item) {
                return [
                    'id' => $item->id,
                    'karyawan_id' => $item->karyawan_id,
                    'nama_karyawan' => $item->nama_karyawan,
                    'bulan' => $item->bulan,
                    'tahun' => $item->tahun,
                    'bulan_text' => $item->bulan_text,
                    'gaji_pokok' => (float) $item->gaji_pokok,
                    'transport_allowance' => (float) $item->transport_allowance,
                    'meal_allowance' => (float) $item->meal_allowance,
                    'internet_allowance' => (float) $item->internet_allowance,
                    'position_allowance' => (float) $item->position_allowance,
                    'incentive' => (float) $item->incentive,
                    'total_earnings' => (float) $item->total_earnings,
                    'tax' => (float) $item->tax,
                    'bpjs_kesehatan' => (float) $item->bpjs_kesehatan,
                    'bpjs_ketenagakerjaan' => (float) $item->bpjs_ketenagakerjaan,
                    'late_absent_deduction' => (float) $item->late_absent_deduction,
                    'loan_deduction' => (float) $item->loan_deduction,
                    'total_deductions' => (float) $item->total_deductions,
                    'net_salary' => (float) $item->net_salary,
                    'tanggal_pembayaran' => $item->tanggal_pembayaran,
                    'metode_pembayaran' => $item->metode_pembayaran,
                    'nama_bank' => $item->nama_bank,
                    'nomor_rekening' => $item->nomor_rekening,
                    'status' => $item->status,
                    'status_badge' => strip_tags($item->status_badge),
                    'catatan' => $item->catatan,
                    'dibuat_oleh' => $item->dibuat_oleh,
                    'is_payslip_sent' => $item->isPayslipSent(),
                    'payslip_sent_at' => $item->payslip_sent_at,
                    'payslip_sent_by' => $item->payslip_sent_by,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                    'karyawan' => $item->karyawan
                ];
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Data penggajian berhasil diambil',
                'data' => $penggajian->items(),
                'pagination' => [
                    'current_page' => $penggajian->currentPage(),
                    'last_page' => $penggajian->lastPage(),
                    'per_page' => $penggajian->perPage(),
                    'total' => $penggajian->total(),
                    'from' => $penggajian->firstItem(),
                    'to' => $penggajian->lastItem()
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data penggajian: ' . $e->getMessage(),
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Display the specified penggajian.
     * GET /api/v1/penggajian/{id}
     */
    public function show($id)
    {
        try {
            $penggajian = Penggajian::with(['karyawan:id,nama_lengkap,nip,email,role,status', 'pembuat:id,nama_lengkap', 'getPayslipSentByUser:id,nama_lengkap'])
                ->find($id);
            
            if (!$penggajian) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data penggajian tidak ditemukan'
                ], 404);
            }
            
            // Add calculated fields
            $data = $penggajian->toArray();
            $data['bulan_text'] = $penggajian->bulan_text;
            $data['status_badge'] = strip_tags($penggajian->status_badge);
            $data['is_payslip_sent'] = $penggajian->isPayslipSent();
            $data['calculated_total_earnings'] = (float) $penggajian->calculateTotalEarnings();
            $data['calculated_total_deductions'] = (float) $penggajian->calculateTotalDeductions();
            $data['calculated_net_salary'] = (float) $penggajian->calculateNetSalary();
            
            return response()->json([
                'success' => true,
                'message' => 'Detail penggajian berhasil diambil',
                'data' => $data
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail penggajian: ' . $e->getMessage(),
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Store a newly created penggajian.
     * POST /api/v1/penggajian
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'karyawan_id' => 'required|exists:karyawans,id',
                'bulan' => 'required|integer|min:1|max:12',
                'tahun' => 'required|integer|min:2020|max:2100',
                'gaji_pokok' => 'required|numeric|min:0',
                'transport_allowance' => 'nullable|numeric|min:0',
                'meal_allowance' => 'nullable|numeric|min:0',
                'internet_allowance' => 'nullable|numeric|min:0',
                'position_allowance' => 'nullable|numeric|min:0',
                'incentive' => 'nullable|numeric|min:0',
                'tax' => 'nullable|numeric|min:0',
                'bpjs_kesehatan' => 'nullable|numeric|min:0',
                'bpjs_ketenagakerjaan' => 'nullable|numeric|min:0',
                'late_absent_deduction' => 'nullable|numeric|min:0',
                'loan_deduction' => 'nullable|numeric|min:0',
                'tanggal_pembayaran' => 'nullable|date',
                'metode_pembayaran' => 'nullable|string|in:transfer,tunai,cek',
                'nama_bank' => 'nullable|string|max:50',
                'nomor_rekening' => 'nullable|string|max:50',
                'status' => 'required|in:draft,pending,approved,paid,cancelled',
                'catatan' => 'nullable|string',
                'dibuat_oleh' => 'required|string|max:255'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Check duplicate
            $exists = Penggajian::where('karyawan_id', $request->karyawan_id)
                ->where('bulan', $request->bulan)
                ->where('tahun', $request->tahun)
                ->exists();
            
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Penggajian untuk karyawan ini pada periode ' . 
                               $request->bulan . '/' . $request->tahun . ' sudah ada'
                ], 409);
            }
            
            // Get karyawan data
            $karyawan = Karyawan::find($request->karyawan_id);
            
            if (!$karyawan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Karyawan tidak ditemukan'
                ], 404);
            }
            
            // Create penggajian
            $penggajian = new Penggajian();
            $penggajian->karyawan_id = $request->karyawan_id;
            $penggajian->nama_karyawan = $karyawan->nama_lengkap;
            $penggajian->bulan = $request->bulan;
            $penggajian->tahun = $request->tahun;
            $penggajian->gaji_pokok = $request->gaji_pokok ?? 0;
            $penggajian->transport_allowance = $request->transport_allowance ?? 0;
            $penggajian->meal_allowance = $request->meal_allowance ?? 0;
            $penggajian->internet_allowance = $request->internet_allowance ?? 0;
            $penggajian->position_allowance = $request->position_allowance ?? 0;
            $penggajian->incentive = $request->incentive ?? 0;
            $penggajian->tax = $request->tax ?? 0;
            $penggajian->bpjs_kesehatan = $request->bpjs_kesehatan ?? 0;
            $penggajian->bpjs_ketenagakerjaan = $request->bpjs_ketenagakerjaan ?? 0;
            $penggajian->late_absent_deduction = $request->late_absent_deduction ?? 0;
            $penggajian->loan_deduction = $request->loan_deduction ?? 0;
            $penggajian->tanggal_pembayaran = $request->tanggal_pembayaran;
            $penggajian->metode_pembayaran = $request->metode_pembayaran;
            $penggajian->nama_bank = $request->nama_bank;
            $penggajian->nomor_rekening = $request->nomor_rekening;
            $penggajian->status = $request->status;
            $penggajian->catatan = $request->catatan;
            $penggajian->dibuat_oleh = $request->dibuat_oleh;
            
            // Calculate totals
            $penggajian->updateCalculations();
            
            // Set detail_gaji
            $penggajian->detail_gaji = json_encode([
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
                'created_by' => $request->dibuat_oleh,
                'created_at' => now()->toDateTimeString()
            ]);
            
            $penggajian->save();
            
            // Load relations
            $penggajian->load(['karyawan:id,nama_lengkap,nip,email', 'pembuat:id,nama_lengkap']);
            
            return response()->json([
                'success' => true,
                'message' => 'Penggajian berhasil ditambahkan',
                'data' => $penggajian
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan penggajian: ' . $e->getMessage(),
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Update the specified penggajian.
     * PUT/PATCH /api/v1/penggajian/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $penggajian = Penggajian::find($id);
            
            if (!$penggajian) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data penggajian tidak ditemukan'
                ], 404);
            }
            
            // Prevent update if status is paid
            if ($penggajian->status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Penggajian yang sudah dibayar tidak dapat diubah'
                ], 422);
            }
            
            $validator = Validator::make($request->all(), [
                'karyawan_id' => 'sometimes|exists:karyawans,id',
                'bulan' => 'sometimes|integer|min:1|max:12',
                'tahun' => 'sometimes|integer|min:2020|max:2100',
                'gaji_pokok' => 'sometimes|numeric|min:0',
                'transport_allowance' => 'sometimes|numeric|min:0',
                'meal_allowance' => 'sometimes|numeric|min:0',
                'internet_allowance' => 'sometimes|numeric|min:0',
                'position_allowance' => 'sometimes|numeric|min:0',
                'incentive' => 'sometimes|numeric|min:0',
                'tax' => 'sometimes|numeric|min:0',
                'bpjs_kesehatan' => 'sometimes|numeric|min:0',
                'bpjs_ketenagakerjaan' => 'sometimes|numeric|min:0',
                'late_absent_deduction' => 'sometimes|numeric|min:0',
                'loan_deduction' => 'sometimes|numeric|min:0',
                'tanggal_pembayaran' => 'nullable|date',
                'metode_pembayaran' => 'nullable|string|in:transfer,tunai,cek',
                'nama_bank' => 'nullable|string|max:50',
                'nomor_rekening' => 'nullable|string|max:50',
                'status' => 'sometimes|in:draft,pending,approved,paid,cancelled',
                'catatan' => 'nullable|string',
                'dibuat_oleh' => 'sometimes|string|max:255'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Update fields
            $fillableFields = [
                'karyawan_id', 'bulan', 'tahun', 'gaji_pokok', 
                'transport_allowance', 'meal_allowance', 'internet_allowance',
                'position_allowance', 'incentive', 'tax', 'bpjs_kesehatan',
                'bpjs_ketenagakerjaan', 'late_absent_deduction', 'loan_deduction',
                'tanggal_pembayaran', 'metode_pembayaran', 'nama_bank',
                'nomor_rekening', 'status', 'catatan', 'dibuat_oleh'
            ];
            
            foreach ($fillableFields as $field) {
                if ($request->has($field)) {
                    $penggajian->$field = $request->$field;
                }
            }
            
            // Update nama_karyawan if karyawan_id changed
            if ($request->has('karyawan_id')) {
                $karyawan = Karyawan::find($request->karyawan_id);
                if ($karyawan) {
                    $penggajian->nama_karyawan = $karyawan->nama_lengkap;
                }
            }
            
            // Recalculate totals
            $penggajian->updateCalculations();
            
            // Update detail_gaji
            $detailGaji = json_decode($penggajian->detail_gaji ?? '{}', true);
            $detailGaji['updated_by'] = $request->dibuat_oleh ?? $penggajian->dibuat_oleh;
            $detailGaji['updated_at'] = now()->toDateTimeString();
            $penggajian->detail_gaji = json_encode($detailGaji);
            
            $penggajian->save();
            
            // Load relations
            $penggajian->load(['karyawan:id,nama_lengkap,nip,email', 'pembuat:id,nama_lengkap']);
            
            return response()->json([
                'success' => true,
                'message' => 'Penggajian berhasil diupdate',
                'data' => $penggajian
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate penggajian: ' . $e->getMessage(),
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Remove the specified penggajian.
     * DELETE /api/v1/penggajian/{id}
     */
    public function destroy($id)
    {
        try {
            $penggajian = Penggajian::find($id);
            
            if (!$penggajian) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data penggajian tidak ditemukan'
                ], 404);
            }
            
            // Prevent delete if status is paid or approved
            if (in_array($penggajian->status, ['paid', 'approved'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Penggajian dengan status ' . $penggajian->status . ' tidak dapat dihapus'
                ], 422);
            }
            
            $penggajian->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Penggajian berhasil dihapus'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus penggajian: ' . $e->getMessage(),
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Update status penggajian.
     * PATCH /api/v1/penggajian/{id}/status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $penggajian = Penggajian::find($id);
            
            if (!$penggajian) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data penggajian tidak ditemukan'
                ], 404);
            }
            
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:draft,pending,approved,paid,cancelled',
                'tanggal_pembayaran' => 'required_if:status,paid|nullable|date',
                'metode_pembayaran' => 'required_if:status,paid|nullable|string|in:transfer,tunai,cek',
                'nama_bank' => 'nullable|string|max:50',
                'nomor_rekening' => 'nullable|string|max:50',
                'catatan' => 'nullable|string',
                'updated_by' => 'required|string|max:255'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Validate status transition
            $validTransitions = [
                'draft' => ['pending', 'cancelled'],
                'pending' => ['approved', 'cancelled'],
                'approved' => ['paid', 'cancelled'],
                'paid' => [],
                'cancelled' => ['draft']
            ];
            
            if (!in_array($request->status, $validTransitions[$penggajian->status] ?? [])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transisi status dari ' . $penggajian->status . 
                               ' ke ' . $request->status . ' tidak valid'
                ], 422);
            }
            
            $penggajian->status = $request->status;
            
            // If status is paid, set payment details
            if ($request->status === 'paid') {
                $penggajian->tanggal_pembayaran = $request->tanggal_pembayaran ?? now()->format('Y-m-d');
                $penggajian->metode_pembayaran = $request->metode_pembayaran ?? 'transfer';
                $penggajian->nama_bank = $request->nama_bank;
                $penggajian->nomor_rekening = $request->nomor_rekening;
                $penggajian->payslip_sent_at = now();
                $penggajian->payslip_sent_by = $request->updated_by;
            }
            
            if ($request->has('catatan')) {
                $penggajian->catatan = $request->catatan;
            }
            
            $penggajian->save();
            
            $penggajian->load(['karyawan:id,nama_lengkap,nip,email', 'getPayslipSentByUser:id,nama_lengkap']);
            
            return response()->json([
                'success' => true,
                'message' => 'Status penggajian berhasil diupdate menjadi ' . $request->status,
                'data' => $penggajian
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate status penggajian: ' . $e->getMessage(),
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Get penggajian by karyawan.
     * GET /api/v1/penggajian/karyawan/{karyawan_id}
     */
    public function getByKaryawan($karyawan_id, Request $request)
    {
        try {
            $karyawan = Karyawan::find($karyawan_id);
            
            if (!$karyawan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Karyawan tidak ditemukan'
                ], 404);
            }
            
            $query = Penggajian::where('karyawan_id', $karyawan_id)
                ->orderBy('tahun', 'desc')
                ->orderBy('bulan', 'desc');
            
            if ($request->has('tahun') && $request->tahun) {
                $query->where('tahun', $request->tahun);
            }
            
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }
            
            // Pagination
            $perPage = $request->get('per_page', 15);
            $penggajian = $query->paginate($perPage);
            
            // Calculate statistics
            $allPenggajian = Penggajian::where('karyawan_id', $karyawan_id)->get();
            $statistics = [
                'total_gaji_diterima' => (float) $allPenggajian->where('status', 'paid')->sum('net_salary'),
                'total_penggajian' => $allPenggajian->count(),
                'total_paid' => $allPenggajian->where('status', 'paid')->count(),
                'total_pending' => $allPenggajian->where('status', 'pending')->count(),
                'total_approved' => $allPenggajian->where('status', 'approved')->count(),
                'total_draft' => $allPenggajian->where('status', 'draft')->count(),
                'total_cancelled' => $allPenggajian->where('status', 'cancelled')->count(),
                'rata_rata_gaji' => (float) ($allPenggajian->where('status', 'paid')->avg('net_salary') ?? 0),
                'gaji_tertinggi' => (float) ($allPenggajian->max('net_salary') ?? 0),
                'gaji_terendah' => (float) ($allPenggajian->min('net_salary') ?? 0),
                'total_earnings' => (float) $allPenggajian->sum('total_earnings'),
                'total_deductions' => (float) $allPenggajian->sum('total_deductions')
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Data penggajian karyawan berhasil diambil',
                'karyawan' => [
                    'id' => $karyawan->id,
                    'nama' => $karyawan->nama_lengkap,
                    'nip' => $karyawan->nip,
                    'email' => $karyawan->email,
                    'role' => $karyawan->role,
                    'status' => $karyawan->status
                ],
                'statistics' => $statistics,
                'data' => $penggajian->items(),
                'pagination' => [
                    'current_page' => $penggajian->currentPage(),
                    'last_page' => $penggajian->lastPage(),
                    'per_page' => $penggajian->perPage(),
                    'total' => $penggajian->total(),
                    'from' => $penggajian->firstItem(),
                    'to' => $penggajian->lastItem()
                ],
                'total' => $penggajian->total()
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data penggajian karyawan: ' . $e->getMessage(),
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Get summary/report penggajian.
     * GET /api/v1/penggajian/summary
     */
    public function getSummary(Request $request)
    {
        try {
            $query = Penggajian::query();
            
            $tahun = $request->get('tahun', date('Y'));
            $query->where('tahun', $tahun);
            
            if ($request->has('bulan') && $request->bulan) {
                $query->where('bulan', $request->bulan);
            }
            
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }
            
            $totalKaryawan = (clone $query)->distinct('karyawan_id')->count('karyawan_id');
            $totalTransaksi = (clone $query)->count();
            
            $summary = [
                'periode' => [
                    'tahun' => (int) $tahun,
                    'bulan' => $request->has('bulan') ? (int) $request->bulan : 'semua',
                ],
                'total_karyawan' => $totalKaryawan,
                'total_transaksi' => $totalTransaksi,
                'komponen_gaji' => [
                    'total_gaji_pokok' => (float) (clone $query)->sum('gaji_pokok'),
                    'total_transport_allowance' => (float) (clone $query)->sum('transport_allowance'),
                    'total_meal_allowance' => (float) (clone $query)->sum('meal_allowance'),
                    'total_internet_allowance' => (float) (clone $query)->sum('internet_allowance'),
                    'total_position_allowance' => (float) (clone $query)->sum('position_allowance'),
                    'total_incentive' => (float) (clone $query)->sum('incentive'),
                    'total_earnings' => (float) (clone $query)->sum('total_earnings')
                ],
                'komponen_potongan' => [
                    'total_tax' => (float) (clone $query)->sum('tax'),
                    'total_bpjs_kesehatan' => (float) (clone $query)->sum('bpjs_kesehatan'),
                    'total_bpjs_ketenagakerjaan' => (float) (clone $query)->sum('bpjs_ketenagakerjaan'),
                    'total_late_absent_deduction' => (float) (clone $query)->sum('late_absent_deduction'),
                    'total_loan_deduction' => (float) (clone $query)->sum('loan_deduction'),
                    'total_deductions' => (float) (clone $query)->sum('total_deductions')
                ],
                'total_net_salary' => (float) (clone $query)->sum('net_salary'),
                'rata_rata_per_karyawan' => $totalKaryawan > 0 ? 
                    (float) ((clone $query)->sum('net_salary') / $totalKaryawan) : 0,
                'by_status' => [
                    'draft' => (clone $query)->where('status', 'draft')->count(),
                    'pending' => (clone $query)->where('status', 'pending')->count(),
                    'approved' => (clone $query)->where('status', 'approved')->count(),
                    'paid' => (clone $query)->where('status', 'paid')->count(),
                    'cancelled' => (clone $query)->where('status', 'cancelled')->count()
                ]
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Ringkasan penggajian berhasil diambil',
                'data' => $summary
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil ringkasan penggajian: ' . $e->getMessage(),
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Send payslip to karyawan.
     * POST /api/v1/penggajian/{id}/send-payslip
     */
    public function sendPayslip($id, Request $request)
    {
        try {
            $penggajian = Penggajian::with('karyawan')->find($id);
            
            if (!$penggajian) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data penggajian tidak ditemukan'
                ], 404);
            }
            
            if ($penggajian->isPayslipSent()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payslip sudah pernah dikirim pada ' . 
                               $penggajian->payslip_sent_at->format('d F Y H:i:s')
                ], 422);
            }
            
            $validator = Validator::make($request->all(), [
                'sent_by' => 'required|string|max:255'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Update payslip sent info
            $penggajian->payslip_sent_at = now();
            $penggajian->payslip_sent_by = $request->sent_by;
            $penggajian->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Payslip berhasil dikirim',
                'data' => [
                    'penggajian_id' => $penggajian->id,
                    'karyawan' => $penggajian->nama_karyawan,
                    'email' => $penggajian->karyawan->email ?? 'N/A',
                    'sent_at' => $penggajian->payslip_sent_at->format('d F Y H:i:s'),
                    'sent_by' => $penggajian->payslip_sent_by
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim payslip: ' . $e->getMessage(),
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}