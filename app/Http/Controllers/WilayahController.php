<?php

namespace App\Http\Controllers;

use App\Models\Wilayah;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class WilayahController extends Controller
{
    /**
     * Ambil daftar provinsi.
     */
    public function provinces(): JsonResponse
    {
        $provinces = Wilayah::where('kode', 'not like', '%.%')
            ->orderBy('nama')
            ->get(['kode', 'nama']);

        return response()->json($provinces);
    }

    /**
     * Ambil daftar kabupaten/kota berdasarkan kode provinsi.
     */
    public function regencies(string $provinceCode): JsonResponse
    {
        if (!$this->isValidCode($provinceCode)) {
            return response()->json(['message' => 'Invalid province code'], 422);
        }

        return response()->json($this->children($provinceCode));
    }

    /**
     * Ambil daftar kecamatan berdasarkan kode kabupaten/kota.
     */
    public function districts(string $regencyCode): JsonResponse
    {
        if (!$this->isValidCode($regencyCode)) {
            return response()->json(['message' => 'Invalid regency code'], 422);
        }

        return response()->json($this->children($regencyCode));
    }

    /**
     * Ambil daftar kelurahan/desa berdasarkan kode kecamatan, lengkap dengan kode pos.
     */
    public function villages(string $districtCode): JsonResponse
    {
        if (!$this->isValidCode($districtCode)) {
            return response()->json(['message' => 'Invalid district code'], 422);
        }

        $villages = Wilayah::where('wilayah.kode', 'like', $districtCode . '.%')
            ->where('wilayah.kode', 'not like', $districtCode . '.%.%')
            ->leftJoin('wilayah_kodepos', 'wilayah_kodepos.kode', '=', 'wilayah.kode')
            ->orderBy('wilayah.nama')
            ->get(['wilayah.kode', 'wilayah.nama', 'wilayah_kodepos.kodepos']);

        return response()->json($villages);
    }

    private function children(string $parentCode)
    {
        return Wilayah::where('kode', 'like', $parentCode . '.%')
            ->where('kode', 'not like', $parentCode . '.%.%')
            ->orderBy('nama')
            ->get(['kode', 'nama']);
    }

    private function isValidCode(string $code): bool
    {
        return (bool) preg_match('/^\d{2}(\.\d{2}){0,2}$/', $code);
    }
}
