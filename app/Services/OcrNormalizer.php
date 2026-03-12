<?php

namespace App\Services;

use Carbon\Carbon;

class OcrNormalizer
{
    public static function normalize($raw)
    {
        $ktp = $raw['data']['ktp'] ?? $raw['ktp'] ?? [];

        // $rtRw = $ktp['rtRw']['value'] ?? '';
        // $rt = '';
        // $rw = '';

        // if ($rtRw) {
        //     $split = explode('/', $rtRw);
        //     $rt = $split[0] ?? '';
        //     $rw = $split[1] ?? '';
        // }

        return [
            'nama' => $ktp['nama']['value'] ?? '',
            'nik' => $ktp['nik']['value'] ?? '',
            'tempatLahir' =>trim(str_replace(',', '', $ktp['tempatLahir']['value'] ?? '')),
            'tanggalLahir' =>
                isset($ktp['tanggalLahir']['value'])
                    ? date('Y-m-d', strtotime($ktp['tanggalLahir']['value']))
                    : '',

            'jenisKelamin' => $ktp['jenisKelamin']['value'] ?? '',
            'agama' => $ktp['agama']['value'] ?? '',
            'statusPerkawinan' => $ktp['statusPerkawinan']['value'] ?? '',
            'alamat' => $ktp['alamat']['value'] ?? '',
            'kota' => $ktp['kotaKabupaten']['value'] ?? '',
            'kecamatan' => $ktp['kecamatan']['value'] ?? '',
            'kelurahan' => $ktp['kelurahanDesa']['value'] ?? '',
        ];
    }

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