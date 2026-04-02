<?php

namespace App\Services;

class OcrNormalizer
{
    public static function normalize($raw)
    {
        $ktp        = $raw['data']['ktp'] ?? $raw['ktp'] ?? [];

        $name       = self::clean($ktp['nama']['value'] ?? '');
        $nik        = self::clean($ktp['nik']['value'] ?? '');
        $birthPlace = self::clean($ktp['tempatLahir']['value'] ?? '');
        $address    = self::clean($ktp['alamat']['value'] ?? '');
        $city       = self::cleanWilayah($ktp['kotaKabupaten']['value'] ?? '');
        $kecamatan  = self::cleanWilayah($ktp['kecamatan']['value'] ?? '');
        $kelurahan  = self::cleanWilayah($ktp['kelurahanDesa']['value'] ?? '');
        $religion   = self::mapAgama($ktp['agama']['value'] ?? '');
        $gender     = self::mapGender($ktp['jenisKelamin']['value'] ?? '');
        $marital    = self::mapMarital($ktp['statusPerkawinan']['value'] ?? '');
        $dob        = null;
        if (!empty($ktp['tanggalLahir']['value'])) {
            $dob = date('Y-m-d', strtotime($ktp['tanggalLahir']['value']));
        }

        return [
            'identificationNumber'  => $nik,
            'name'                  => $name,
            'dateOfBirth'           => $dob,
            'birthLocation'         => $birthPlace,
            'religion'              => $religion,
            'gender'                => $gender,
            'maritalStatus'         => $marital,
            'address'               => $address,
            'kelurahan'             => $kelurahan,
            'kecamatan'             => $kecamatan,
            'city'                  => $city,
            'postalCode'            => '',
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

        return match ($v) {
            'ISLAM' => '1',
            'KRISTEN' => '2',
            'BUDDHA' => '3',
            'KATOLIK' => '4',
            'KONGHUCU' => '5',
            'HINDU' => '6',
            default => null
        };
    }

    private static function mapGender($v)
    {
        $v = strtoupper($v);

        if (str_contains($v, 'LAKI')) return '1';
        if (str_contains($v, 'PEREMPUAN')) return '2';

        return null;
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