<?php

namespace App\Services;

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

    public static function mapGender($value)
    {
        $v = strtoupper($value);

        if (str_contains($v, 'LAKI')) return 'Pria';
        if (str_contains($v, 'PEREMPUAN')) return 'Wanita';

        return null;
    }

    public static function mapMarital($value)
    {
        $v = strtoupper($value);

        if (str_contains($v, 'KAWIN') && !str_contains($v, 'BELUM'))
            return 'Menikah';

        if (str_contains($v, 'BELUM'))
            return 'Belum Menikah';

        // Cerai → user pilih manual
        return null;
    }

    public static function isCerai($value)
    {
        return str_contains(strtoupper($value), 'CERAI');
    }
}