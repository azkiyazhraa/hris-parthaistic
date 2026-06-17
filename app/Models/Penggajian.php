<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Penggajian extends Model
{
    use HasFactory;

    protected $table = 'penggajians';

    protected $fillable = [
        'karyawan_id',
        'nama_karyawan',
        'bulan',
        'tahun',
        'gaji_pokok',
        'transport_allowance',
        'meal_allowance',
        'internet_allowance',
        'position_allowance',
        'incentive',
        'total_earnings',
        'tax',
        'bpjs_kesehatan',
        'bpjs_ketenagakerjaan',
        'late_absent_deduction',
        'loan_deduction',
        'total_deductions',
        'net_salary',
        'tanggal_pembayaran',
        'metode_pembayaran',
        'nama_bank',
        'nomor_rekening',
        'status',
        'catatan',
        'dibuat_oleh',
        'detail_gaji',
        'payslip_sent_at',
        'payslip_sent_by',
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'date',
        'payslip_sent_at' => 'datetime',
        'gaji_pokok' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'meal_allowance' => 'decimal:2',
        'internet_allowance' => 'decimal:2',
        'position_allowance' => 'decimal:2',
        'incentive' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'tax' => 'decimal:2',
        'bpjs_kesehatan' => 'decimal:2',
        'bpjs_ketenagakerjaan' => 'decimal:2',
        'late_absent_deduction' => 'decimal:2',
        'loan_deduction' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            self::STATUS_DRAFT => '<span class="px-3 py-1 text-xs text-gray-800 bg-gray-200 rounded-full">Draft</span>',
            self::STATUS_PENDING => '<span class="px-3 py-1 text-xs text-yellow-800 bg-yellow-200 rounded-full">Pending</span>',
            self::STATUS_APPROVED => '<span class="px-3 py-1 text-xs text-blue-800 bg-blue-200 rounded-full">Approved</span>',
            self::STATUS_PAID => '<span class="px-3 py-1 text-xs text-green-800 bg-green-200 rounded-full">Paid</span>',
            self::STATUS_CANCELLED => '<span class="px-3 py-1 text-xs text-red-800 bg-red-200 rounded-full">Cancelled</span>',
            default => '<span class="px-3 py-1 text-xs text-gray-800 bg-gray-200 rounded-full">' . ucfirst($this->status) . '</span>',
        };
    }

    public function getBulanTextAttribute()
    {
        $bulan = [
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

        return $bulan[$this->bulan] ?? '-';
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    public function pembuat()
    {
        return $this->belongsTo(Karyawan::class, 'dibuat_oleh', 'nama_lengkap');
    }

    public function getPayslipSentByUser()
    {
        return $this->belongsTo(Karyawan::class, 'payslip_sent_by', 'nama_lengkap');
    }

    public function calculateTotalEarnings()
    {
        return $this->gaji_pokok + $this->transport_allowance + $this->meal_allowance +
            $this->internet_allowance + $this->position_allowance + $this->incentive;
    }

    public function calculateTotalDeductions()
    {
        return $this->tax + $this->bpjs_kesehatan + $this->bpjs_ketenagakerjaan +
            $this->late_absent_deduction + $this->loan_deduction;
    }

    public function calculateNetSalary()
    {
        return $this->calculateTotalEarnings() - $this->calculateTotalDeductions();
    }

    public function updateCalculations()
    {
        $this->total_earnings = $this->calculateTotalEarnings();
        $this->total_deductions = $this->calculateTotalDeductions();
        $this->net_salary = $this->calculateNetSalary();
    }

    public function isPayslipSent()
    {
        return !is_null($this->payslip_sent_at);
    }
}
