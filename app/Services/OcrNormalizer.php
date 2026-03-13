<?php

namespace App\Services;

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

        $nama = self::clean($ktp['nama']['value'] ?? '');
        $nik  = self::clean($ktp['nik']['value'] ?? '');
        $tempat = self::clean($ktp['tempatLahir']['value'] ?? '');
        $alamat = self::clean($ktp['alamat']['value'] ?? '');
        $kota = self::cleanWilayah($ktp['kotaKabupaten']['value'] ?? '');
        $kecamatan = self::cleanWilayah($ktp['kecamatan']['value'] ?? '');
        $kelurahan = self::cleanWilayah($ktp['kelurahanDesa']['value'] ?? '');
        $agama = self::mapAgama($ktp['agama']['value'] ?? '');
        $gender = self::mapGender($ktp['jenisKelamin']['value'] ?? '');
        $marital = self::mapMarital($ktp['statusPerkawinan']['value'] ?? '');
        $tanggal = '';
        if (!empty($ktp['tanggalLahir']['value'])) {
            $tanggal =date('Y-m-d',strtotime($ktp['tanggalLahir']['value']));
        }

        return [
            'nama' => $nama,
            'nik' => $nik,
            'tempatLahir' => $tempat,
            'tanggalLahir' => $tanggal,
            'jenisKelamin' => $gender,
            'agama' => $agama,
            'statusPerkawinan' => $marital,
            'alamat' => $alamat,
            'kota' => $kota,
            'kecamatan' => $kecamatan,
            'kelurahan' => $kelurahan,
            // 'rt' => $rt,
            // 'rw' => $rw,
        ];
    }

    private static function clean($v)
    {
        $v = strtoupper($v);
        $v = str_replace(',', '', $v);
        $v = trim($v);

        return $v;
    }

    private static function mapAgama($v)
    {
        $v = strtoupper($v);

        if ($v == 'ISLAM') return 'Islam';
        if ($v == 'KRISTEN') return 'Kristen';
        if ($v == 'KATOLIK') return 'Katolik';
        if ($v == 'HINDU') return 'Hindu';
        if ($v == 'BUDDHA') return 'Buddha';
        if ($v == 'KONGHUCU') return 'Konghucu';
        return $v;
    }

    private static function mapGender($v)
    {
        $v = strtoupper($v);
        if (str_contains($v,'LAKI')) return 'Pria';
        if (str_contains($v,'PEREMPUAN')) return 'Wanita';
        return '';
    }

    private static function mapMarital($v)
    {
        $v = strtoupper($v);
        if (str_contains($v, 'BELUM')) {
            return 'Belum Menikah';
        }

        if (str_contains($v, 'KAWIN')) {
            return 'Menikah';
        }

        if (str_contains($v, 'CERAI')) {
            return 'Janda';
        }
        return '';
    }

    private static function cleanWilayah($v)
    {
        $v = strtoupper($v);

        $v = str_replace('KOTA ', '', $v);
        $v = str_replace('KABUPATEN ', '', $v);
        $v = trim($v);
        // title case
        $v = ucwords(strtolower($v));

        return $v;
    }
}