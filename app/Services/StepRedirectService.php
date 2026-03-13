<?php

namespace App\Services;

class StepRedirectService
{
    public const STEP_ROUTE = [
        'uploadKtp'             => 'verifikasi.ktp',
        'uploadSelfie'          => 'verifikasi.wajah',
        'personalInformation'   => 'data.personal',
        'employmentInformation' => 'data.pekerjaan',
        'financialProfile'      => 'data.penghasilan',
        'relation'              => 'data.referensi.perseorangan',
        'bankInformation'       => 'data.bank',
    ];

     public const STEP_NUMBER = [
        'uploadKtp'             => 1,
        'uploadSelfie'          => 1,
        'personalInformation'   => 2,
        'employmentInformation' => 3,
        'financialProfile'      => 4,
        'relation'              => 5,
        'bankInformation'       => 6,
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

        // if (!isset(self::STEP_ROUTE[$step])) {
        //     return route('login');
        // }

        return route(self::STEP_ROUTE[$step]);
    }

    public static function nextStep(string $currentStep): ?string
    {
        $keys = array_keys(self::STEP_ROUTE);
        $index = array_search($currentStep, $keys);

        return $keys[$index + 1] ?? null;
    }

    public static function stepNumber(?string $step): int
    {
        if (!$step) return 1;
        return self::STEP_NUMBER[$step] ?? 1;
    }

    public static function guardStep(): ?string
    {
        $step = session('registrationStep');
        if (!$step) return route('login');

        $allowed = self::STEP_ROUTE[$step] ?? null;
        if (!$allowed) return route('login');

        if (!request()->routeIs($allowed)) {
            return route($allowed);
        }

        return null;
    }

    public static function hideBack(): bool
    {
        $step = session('registrationStep');
        return in_array($step, [
            'uploadKtp',
            'uploadSelfie',
            'personalInformation'
        ]);
    }
}