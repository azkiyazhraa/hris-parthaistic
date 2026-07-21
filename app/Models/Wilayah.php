<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    protected $table = 'wilayah';

    protected $primaryKey = 'kode';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = ['kode', 'nama'];

    /**
     * Cari kode wilayah (provinsi/kota/kecamatan/kelurahan) berdasarkan nama yang tersimpan
     * sebagai teks bebas, untuk pre-select dropdown cascading di form edit/profile.
     * Pencarian di-scope berjenjang supaya tidak salah pilih saat ada nama kembar di daerah lain.
     */
    public static function resolveCodes(?string $provinsi, ?string $kota, ?string $kecamatan, ?string $kelurahan): array
    {
        $result = ['provinsi' => null, 'kota' => null, 'kecamatan' => null, 'kelurahan' => null];

        if (!$provinsi) {
            return $result;
        }

        $provinceCode = static::where('kode', 'not like', '%.%')
            ->whereRaw('LOWER(nama) = ?', [strtolower(trim($provinsi))])
            ->value('kode');

        if (!$provinceCode) {
            return $result;
        }
        $result['provinsi'] = $provinceCode;

        if (!$kota) {
            return $result;
        }

        $regencyCode = static::childrenOf($provinceCode)
            ->whereRaw('LOWER(nama) = ?', [strtolower(trim($kota))])
            ->value('kode');

        if (!$regencyCode) {
            return $result;
        }
        $result['kota'] = $regencyCode;

        if (!$kecamatan) {
            return $result;
        }

        $districtCode = static::childrenOf($regencyCode)
            ->whereRaw('LOWER(nama) = ?', [strtolower(trim($kecamatan))])
            ->value('kode');

        if (!$districtCode) {
            return $result;
        }
        $result['kecamatan'] = $districtCode;

        if (!$kelurahan) {
            return $result;
        }

        $result['kelurahan'] = static::childrenOf($districtCode)
            ->whereRaw('LOWER(nama) = ?', [strtolower(trim($kelurahan))])
            ->value('kode');

        return $result;
    }

    private static function childrenOf(string $parentCode)
    {
        return static::where('kode', 'like', $parentCode . '.%')
            ->where('kode', 'not like', $parentCode . '.%.%');
    }
}
