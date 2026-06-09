<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanCuti extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_cutis';

    protected $fillable = [
        'karyawan_id',
        'nama_karyawan',
        'jenis_cuti',
        'tanggal_mulai',
        'tanggal_selesai',
        'total_hari',
        'alasan',
        'status',
        'tanggal_disetujui',
        'lampiran',
        'catatan',
        'tahun_cuti',
        'kuota_total',
        'kuota_terpakai',
        'sisa_kuota',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tanggal_disetujui' => 'datetime',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    // Calculate total days
    public static function hitungTotalHari($start, $end)
    {
        $startDate = Carbon::parse($start);
        $endDate = Carbon::parse($end);

        return $startDate->diffInDays($endDate) + 1;
    }

    // Get status color class
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'pending' => 'yellow',
            'disetujui' => 'green',
            'approved' => 'green',
            'ditolak' => 'red',
            'rejected' => 'red',
            default => 'gray',
        };
    }

    // Get status text
    public function getStatusTextAttribute()
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'disetujui' => 'Disetujui',
            'approved' => 'Approved',
            'ditolak' => 'Ditolak',
            'rejected' => 'Rejected',
            default => ucfirst($this->status),
        };
    }

    // Get status badge HTML
    public function getStatusBadgeAttribute()
    {
        $text = $this->status_text;

        if ($this->status == 'pending' || $this->status == 'Requested') {
            return '<span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs">Requested</span>';
        } elseif ($this->status == 'disetujui' || $this->status == 'approved' || $this->status == 'Approved') {
            return '<span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">Approved</span>';
        } elseif ($this->status == 'ditolak' || $this->status == 'rejected' || $this->status == 'Rejected') {
            return '<span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs">Rejected</span>';
        }

        return '<span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs">'.$text.'</span>';
    }

    // Get jenis cuti label
    public function getJenisCutiLabelAttribute()
    {
        $jenis = [
            'tahunan' => 'Cuti Tahunan',
            'sakit' => 'Sick Leave',
            'melahirkan' => 'Maternity Leave',
            'penting' => 'Emergency Leave',
            'ibadah' => 'Personal Leave',
            'lainnya' => 'Other',
        ];

        return $jenis[$this->jenis_cuti] ?? ucfirst($this->jenis_cuti);
    }
}
