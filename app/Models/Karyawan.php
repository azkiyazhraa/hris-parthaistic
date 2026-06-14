<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Karyawan extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'karyawans';

    protected $fillable = [
        'nip',
        'email',
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
        'tanggal_bergabung',
        'status',
        'nik',
        'npwp',
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
        'tanggal_lahir' => 'date',
        'email_verified_at' => 'datetime',
    ];

    // Accessor untuk pendidikan_terakhir (menggunakan kolom baru)
    public function getPendidikanTerakhirAttribute($value)
    {
        return $this->attributes['pendidikan_terakhir_new'] ?? $value;
    }

    // Accessor untuk nama_lengkap (gabungan nama_depan + nama_belakang)
    public function getNamaLengkapAttribute($value)
    {
        if (!empty($this->attributes['nama_depan']) || !empty($this->attributes['nama_belakang'])) {
            return trim(($this->attributes['nama_depan'] ?? '') . ' ' . ($this->attributes['nama_belakang'] ?? ''));
        }
        return $value;
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

    // Accessor untuk menampilkan jabatan (termasuk custom "lainnya")
    public function getJabatanDisplayAttribute()
    {
        if ($this->jabatan === 'lainnya' && !empty($this->jabatan_lainnya)) {
            return $this->jabatan_lainnya;
        }
        return $this->jabatan;
    }
}
