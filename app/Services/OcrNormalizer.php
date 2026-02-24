<?php

namespace App\Services;

use Carbon\Carbon;

class OcrNormalizer
{
    public static function normalize(array $ocr)
    {
        return [
            'nik' => $ocr['nik'] ?? null,
            'nama' => $ocr['nama'] ?? null,
            'tanggalLahir' => $ocr['tanggal_lahir'] ?? null,
            'tempatLahir' => $ocr['tempat_lahir'] ?? null,
            'agama' => $ocr['agama'] ?? null,
            'jenisKelamin' => self::mapGender($ocr['jenis_kelamin'] ?? ''),
            'statusPerkawinan' => self::mapMarital($ocr['status_perkawinan'] ?? ''),
            'alamat' => $ocr['alamat'] ?? null,
            'rt_rw' => $ocr['rt_rw'] ?? null,
            'kota' => $ocr['kota'] ?? null,
            'kelurahan' => $ocr['kelurahan'] ?? null,
            'kecamatan' => $ocr['kecamatan'] ?? null,
        ];
    }

    // private static function digits($v)
    // {
    //     return $v ? preg_replace('/\D/', '', $v) : null;
    // }

    // private static function cleanText($v)
    // {
    //     if (!$v) return null;
    //     $v = strtoupper($v);
    //     return trim(preg_replace('/\s+/', ' ', $v));
    // }

    // private static function cleanAlpha($v)
    // {
    //     if (!$v) return null;
    //     return trim(preg_replace('/[^A-Za-z\s]/', '', strtoupper($v)));
    // }

    // private static function date($v)
    // {
    //     if (!$v) return null;

    //     try {
    //         return Carbon::parse($v)->format('Y-m-d');
    //     } catch (\Exception $e) {
    //         return null;
    //     }
    // }

    private static function mapGender($v)
    {
        $v = strtoupper($v);
        if (str_contains($v,'LAKI')) return 'Pria';
        if (str_contains($v,'PEREMPUAN')) return 'Wanita';
        return null;
    }

    private static function mapMarital($v)
    {
        $v = strtoupper($v);
        if (str_contains($v,'KAWIN')) return 'Menikah';
        if (str_contains($v,'BELUM')) return 'Belum Menikah';
        return null;
    }
}