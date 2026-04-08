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
        'universityInformation' => 'data.universitas',
        'relation'              => 'data.relation',
        'financialInformation'  => 'data.bank',
        'uploadSignature'       => 'data.signature',
    ];

    // FLOW DINAMIS
    public static function getFlow(): array
    {
        $employmentData = session('financialData') ?? [];
        $employmentType = $employmentData['employmentType'] ?? null;

        $flow = [
            'createPin',
            'accountType',
            'uploadKtp',
            'uploadSelfie',
            'personalInformation',
            'financialProfile',
        ];

        if (str_contains(strtolower($employmentType), 'mahasiswa')) {
            // Mahasiswa
            $flow[] = 'universityInformation';
            $flow[] = 'relation';

        } elseif (
            str_contains(strtolower($employmentType), 'pensiun') ||
            str_contains(strtolower($employmentType), 'irt')
        ) {
            // IRT & Pensiunan
            $flow[] = 'relation';

        } else {
            // Default
            $flow[] = 'employmentInformation';
        }

        $flow[] = 'financialInformation';
        $flow[] = 'uploadSignature';

        return $flow;
    }

    // NEXT STEP
    public static function nextStep(string $currentStep): ?string
    {
        $flow  = self::getFlow();
        $index = array_search($currentStep, $flow);

        return $flow[$index + 1] ?? null;
    }

    // STEP NUMBER (UI)
    public static function stepNumber(?string $step): int
    {
        $flow  = self::getFlow();
        $index = array_search($step, $flow);

        return $index !== false ? $index + 1 : 1;
    }

    public static function totalStep(): int
    {
        return count(self::getFlow());
    }

    // ROUTE HELPER
    public static function routeByStep(?string $step): string
    {
        if (!$step || !isset(self::STEP_ROUTE[$step])) {
            return route('verifikasi.ktp');
        }

        return route(self::STEP_ROUTE[$step]);
    }

    // PROCESS TYPE
    public static function getProcessType(string $targetStep): string
    {
        $sessionStep = session('registrationStep');
        $flow = self::getFlow();

        $sessionIndex = array_search($sessionStep, $flow);
        $targetIndex  = array_search($targetStep, $flow);

        return $sessionIndex >= $targetIndex ? 'UPDATE' : 'CREATE';
    }

    public static function hideBack(): bool
    {
        $step = session('registrationStep');

        return in_array($step, [
            'createPin',
            'accountType',
            'uploadKtp',
            'uploadSelfie',
        ]);
    }

    public static function prevStep(string $currentStep): ?string
    {
        $flow  = self::getFlow();
        $index = array_search($currentStep, $flow);

        if ($index === false || $index === 0) {
            return null;
        }
        return $flow[$index - 1];
    }
}