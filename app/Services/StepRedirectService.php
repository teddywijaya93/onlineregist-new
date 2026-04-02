<?php

namespace App\Services;

class StepRedirectService
{
    public const STEP_ROUTE = [
        'createPin'             => 'create.pin',
        'accountType'           => 'account.type',
        'uploadKtp'             => 'verifikasi.ktp',
        'uploadSelfie'          => 'verifikasi.wajah',
        'personalInformation'   => 'data.personal',
        'financialProfile'      => 'data.penghasilan',
        'employmentInformation' => 'data.pekerjaan',
        'relation'              => 'data.referensi.perseorangan',
        'bankInformation'       => 'data.bank',
    ];

    public const STEP_NUMBER = [
        'createPin'             => 1,
        'accountType'           => 1,
        'uploadKtp'             => 2,
        'uploadSelfie'          => 2,
        'personalInformation'   => 3,
        'financialProfile'      => 4,
        'employmentInformation' => 5,
        'relation'              => 6,
        'bankInformation'       => 7,
    ];

    public static function guardStep(): ?string
    {
        $sessionStep = session('registrationStep');

        // kalau belum ada step → balik ke awal
        if (!$sessionStep) {
            return route('verifikasi.ktp');
        }
        $currentRouteName = request()->route()->getName();

        // cari step dari route sekarang
        $currentStep = array_search($currentRouteName, self::STEP_ROUTE);

        if (!$currentStep) {
            return null;
        }

        $sessionIndex = self::STEP_NUMBER[$sessionStep] ?? 0;
        $currentIndex = self::STEP_NUMBER[$currentStep] ?? 0;

        // kalau user di step LEBIH DEPAN → jangan tarik mundur
        if ($currentIndex <= $sessionIndex) {
            return null;
        }

        // kalau user lompat step → redirect ke step seharusnya
        return route(self::STEP_ROUTE[$sessionStep]);
    }

    public static function routeByStep(?string $step): string
    {
        if (!$step || !isset(self::STEP_ROUTE[$step])) {
            return route('verifikasi.ktp');
        }
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
        return self::STEP_NUMBER[$step] ?? 1;
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