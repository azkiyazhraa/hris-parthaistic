<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

class Karyawan extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'karyawans';

    protected $fillable = [
        'nip',
        'email',
        'tracker_email',
        'kata_sandi',
        'nama_depan',
        'nama_belakang',
        'nama_lengkap',
        'role',
        'jabatan',
        'jabatan_lainnya',
        'foto_profil',
        'nomor_telepon',
        'alamat',
        'rt',
        'rw',
        'kelurahan',
        'kecamatan',
        'kota',
        'provinsi',
        'kode_pos',
        'tanggal_bergabung',
        'end_date',
        'total_hari_kerja',
        'reason_resigned',
        'status',
        'nik',
        'npwp',
        'nomor_paspor',
        'paspor_berlaku_hingga',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'status_pernikahan',
        'pendidikan_terakhir',
        'pendidikan_terakhir_new',
        'universitas',
        'jurusan',
        'tahun_lulus',
        'nama_kontak_darurat',
        'telepon_kontak_darurat',
        'nama_bank',
        'nomor_rekening',
    ];

    protected $hidden = [
        'kata_sandi',
        'remember_token',
    ];

    protected $casts = [
        'tanggal_bergabung' => 'date',
        'end_date' => 'date',
        'tanggal_lahir' => 'date',
        'paspor_berlaku_hingga' => 'date',
        'email_verified_at' => 'datetime',
    ];

    // Konstanta status
    const STATUS_FULL_TIME = 'Full-time';
    const STATUS_CONTRACT = 'Contract';
    const STATUS_INTERNSHIP = 'Internship';
    const STATUS_RESIGNED = 'Resigned';
    const STATUS_CONTRACT_ENDED = 'Contract Ended';
    const STATUS_INTERNSHIP_COMPLETED = 'Internship Completed';
    const STATUS_TERMINATED = 'Terminated';

    // Daftar status aktif (bisa login)
    const ACTIVE_STATUSES = [
        self::STATUS_FULL_TIME,
        self::STATUS_CONTRACT,
        self::STATUS_INTERNSHIP,
    ];

    // Daftar status berhenti (tidak bisa login)
    const INACTIVE_STATUSES = [
        self::STATUS_RESIGNED,
        self::STATUS_CONTRACT_ENDED,
        self::STATUS_INTERNSHIP_COMPLETED,
        self::STATUS_TERMINATED,
    ];

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Jika status termasuk inactive
            if (in_array($model->status, self::INACTIVE_STATUSES)) {
                // Set end_date ke today jika belum diisi
                if (empty($model->end_date)) {
                    $model->end_date = Carbon::now();
                }

                // Hitung total hari kerja
                if ($model->tanggal_bergabung && $model->end_date) {
                    $start = Carbon::parse($model->tanggal_bergabung)->startOfDay();
                    $end = Carbon::parse($model->end_date)->startOfDay();
                    $model->total_hari_kerja = (int) $start->diffInDays($end) + 1;
                }
            } else {
                // Jika status aktif, reset
                $model->end_date = null;
                $model->total_hari_kerja = 0;
                $model->reason_resigned = null;
            }
        });
    }

    // Accessor pendidikan_terakhir
    public function getPendidikanTerakhirAttribute($value)
    {
        return $this->attributes['pendidikan_terakhir_new'] ?? $value;
    }

    // Accessor nama_lengkap
    public function getNamaLengkapAttribute($value)
    {
        if (!empty($this->attributes['nama_depan']) || !empty($this->attributes['nama_belakang'])) {
            return trim(($this->attributes['nama_depan'] ?? '') . ' ' . ($this->attributes['nama_belakang'] ?? ''));
        }
        return $value;
    }

    // Status label
    public function getStatusLabelAttribute()
    {
        return $this->status;
    }

    // Status class untuk styling
    public function getStatusClassAttribute()
    {
        return match ($this->status) {
            self::STATUS_FULL_TIME => 'bg-green-100 text-green-800',
            self::STATUS_CONTRACT => 'bg-blue-100 text-blue-800',
            self::STATUS_INTERNSHIP => 'bg-purple-100 text-purple-800',
            self::STATUS_RESIGNED => 'bg-orange-100 text-orange-800',
            self::STATUS_CONTRACT_ENDED => 'bg-yellow-100 text-yellow-800',
            self::STATUS_INTERNSHIP_COMPLETED => 'bg-indigo-100 text-indigo-800',
            self::STATUS_TERMINATED => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    // Status badge besar untuk detail
    public function getStatusBadgeAttribute()
    {
        $icons = [
            self::STATUS_FULL_TIME => '💼',
            self::STATUS_CONTRACT => '📋',
            self::STATUS_INTERNSHIP => '🎓',
            self::STATUS_RESIGNED => '👋',
            self::STATUS_CONTRACT_ENDED => '📋',
            self::STATUS_INTERNSHIP_COMPLETED => '🎉',
            self::STATUS_TERMINATED => '🚫',
        ];

        $icon = $icons[$this->status] ?? '❓';
        return $icon . ' ' . $this->status;
    }

    // Cek apakah karyawan aktif (bisa login)
    public function isActive()
    {
        return in_array($this->status, self::ACTIVE_STATUSES);
    }

    // Cek apakah karyawan suspended
    public function isSuspended()
    {
        return in_array($this->status, self::INACTIVE_STATUSES);
    }

    // Alasan suspend dalam bahasa Inggris
    public function getSuspendReasonAttribute()
    {
        return match ($this->status) {
            self::STATUS_RESIGNED => 'Employee has resigned',
            self::STATUS_CONTRACT_ENDED => 'Employment contract has ended',
            self::STATUS_INTERNSHIP_COMPLETED => 'Internship program has been completed',
            self::STATUS_TERMINATED => 'Employee has been terminated',
            default => null,
        };
    }

    public function getAuthPassword()
    {
        return $this->kata_sandi;
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isHR()
    {
        return $this->role === 'hr';
    }

    // Accessor alamat lengkap (gabungan semua bagian alamat)
    public function getAlamatLengkapAttribute()
    {
        $rtRw = '';
        if (!empty($this->rt) || !empty($this->rw)) {
            $rtRw = 'RT ' . ($this->rt ?: '-') . '/RW ' . ($this->rw ?: '-');
        }

        $parts = array_filter([
            $this->alamat,
            $rtRw,
            $this->kelurahan ? 'Kel. ' . $this->kelurahan : null,
            $this->kecamatan ? 'Kec. ' . $this->kecamatan : null,
            $this->kota,
            $this->provinsi,
            $this->kode_pos,
        ], fn($part) => !empty($part));

        return implode(', ', $parts);
    }

    // Accessor jabatan display
    public function getJabatanDisplayAttribute()
    {
        if ($this->jabatan === 'lainnya' && !empty($this->jabatan_lainnya)) {
            return $this->jabatan_lainnya;
        }
        return $this->jabatan;
    }

    // Format total hari kerja
    public function getTotalHariKerjaFormattedAttribute()
    {
        if ($this->total_hari_kerja <= 0) return '-';

        $days = (int) $this->total_hari_kerja;
        $years = floor($days / 365);
        $months = floor(($days % 365) / 30);
        $remainingDays = $days % 30;

        $result = [];
        if ($years > 0) $result[] = $years . ' year' . ($years > 1 ? 's' : '');
        if ($months > 0) $result[] = $months . ' month' . ($months > 1 ? 's' : '');
        if ($remainingDays > 0 || empty($result)) $result[] = $remainingDays . ' day' . ($remainingDays > 1 ? 's' : '');

        return implode(', ', $result);
    }
}
