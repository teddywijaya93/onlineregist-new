<?php

namespace App\Services;
use Carbon\Carbon;

class OcrNormalizer
{
    public static function normalize($raw)
    {
        $data       = $raw['data'] ?? [];

        $nik            = self::clean($data['nik'] ?? '');
        $name           = self::clean($data['full_name'] ?? '');
        $birthPlace     = self::clean($data['place_of_birth'] ?? '');
        $address        = self::clean($data['address'] ?? '');
        $state          = self::cleanWilayah($data['state'] ?? '');
        $city           = self::cleanWilayah($data['city'] ?? '');
        $kecamatan      = self::cleanWilayah($data['district'] ?? '');
        $kelurahan      = self::cleanWilayah($data['administrative_village'] ?? '');
        $religion       = self::mapAgama($data['religion'] ?? '');
        $gender         = self::mapGender($data['gender'] ?? '');
        $marital        = self::mapMarital($data['marital_status'] ?? '');
        $nationality    = self::cleanWilayah($data['nationality'] ?? '');
        $occupation     = self::cleanWilayah($data['occupation'] ?? '');
        $blood_type     = self::cleanWilayah($data['blood_type'] ?? '');
        $dob            = null;
        if (!empty($data['date_of_birth'])) {
            try {
                $dob = Carbon::createFromFormat('d-m-Y', $data['date_of_birth'])->format('Y-m-d');
            } catch (\Exception $e) {
                $dob = null;
            }
        }

        return [
            'identificationNumber' => $nik,
            'name'                 => $name,
            'dateOfBirth'          => $dob,
            'birthLocation'        => $birthPlace,
            'religion'             => $religion,
            'gender'               => $gender,
            'maritalStatus'        => $marital,
            'address'              => $address,
            'kelurahan'            => $kelurahan,
            'kecamatan'            => $kecamatan,
            'city'                 => $city,
            'state'                => $state,
            'nationality'          => $nationality,
            'occupation'           => $occupation,
            'blood_type'           => $blood_type,
            'postalCode'           => '',
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
            return '2';
        }

        if (str_contains($v, 'KAWIN')) {
            return '1';
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