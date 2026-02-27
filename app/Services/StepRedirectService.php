<?php

namespace App\Services;

class StepRedirectService
{
    public const STEP_ROUTE = [
        'verificationWA'        => 'otp',
        'uploadKTP'             => 'verifikasi.ktp',
        'personalInformation'   => 'data.personal',
        'employmentInformation' => 'data.pekerjaan',
        'financialProfile'      => 'data.penghasilan',
        'relation'              => 'data.referensi.perseorangan',
        'riskProfile'           => 'profil.resiko',
        'bankInformation'       => 'data.bank',
    ];

    public static function routeByStep(?string $step): string
    {
        if (!$step || !isset(self::STEP_ROUTE[$step])) {
        return route('login');
        }

        // kalau user sudah di route itu, jangan redirect lagi
        if (request()->routeIs(self::STEP_ROUTE[$step])) {
            return url()->current();
        }

        return route(self::STEP_ROUTE[$step]);
    }

    public static function nextStep(string $currentStep): ?string
    {
        $keys = array_keys(self::STEP_ROUTE);
        $index = array_search($currentStep, $keys);

        return $keys[$index + 1] ?? null;
    }
}