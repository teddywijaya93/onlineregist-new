<?php

namespace App\Services;

class StepRedirectService
{
    public const STEP_ROUTE = [
        'createPIN'             => 'create.pin',
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

    // Flow Dinamis
    public static function getFlow(): array
    {
        $employmentData = session('financialData') ?? [];
        $employmentType = $employmentData['employmentType'] ?? null;

        $flow = [
            'createPIN',
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

        } elseif (str_contains(strtolower($employmentType), 'pensiunan') || str_contains(strtolower($employmentType), 'ibu rumah tangga')) {
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

    public static function nextStep(string $currentStep): ?string
    {
        $flow  = self::getFlow();
        $index = array_search($currentStep, $flow);

        return $flow[$index + 1] ?? null;
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

    public static function stepNumber(?string $step): int
    {
        $flow = self::getFlow();

        $filtered = array_values(array_filter($flow, function ($s) {
            return !in_array($s, ['createPIN', 'accountType', 'uploadSelfie']);
        }));

        if ($step === 'uploadSelfie') {
            $step = 'uploadKtp';
        }
        $index = array_search($step, $filtered);

        return $index !== false ? $index + 1 : 1;
    }

    public static function totalStep(): int
    {
        $flow = self::getFlow();

        $filtered = array_filter($flow, function ($s) {
            return !in_array($s, ['createPIN', 'accountType', 'uploadSelfie']);
        });

        return count($filtered);
    }

    // Helper    
    public static function routeByStep(?string $step): ?string
    {
        if (!$step) {
            return null;
        }

        return isset(self::STEP_ROUTE[$step])
            ? route(self::STEP_ROUTE[$step])
            : null;
    }

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

    // Dashboard
    public static function getGroupedFlow(): array
    {
        $flow = self::getFlow();
        $groupMap = [
            [
                'key'   => 'account',
                'steps' => ['createPIN', 'accountType'],
                'label' => 'Pembuatan Akun Profits',
            ],
            [
                'key'   => 'ktp',
                'steps' => ['uploadKtp', 'uploadSelfie'],
                'label' => 'Pengambilan Foto KTP dan Selfie',
            ],
            [
                'key'   => 'personalInformation',
                'steps' => ['personalInformation'],
                'label' => 'Melengkapi Identitas Diri',
            ],
            [
                'key'   => 'financialProfile',
                'steps' => ['financialProfile'],
                'label' => 'Melengkapi Profil Keuangan',
            ],
            [
                'key'   => 'employmentInformation',
                'steps' => ['employmentInformation'],
                'label' => 'Melengkapi Data Pekerjaan',
            ],
            [
                'key'   => 'universityInformation',
                'steps' => ['universityInformation'],
                'label' => 'Melengkapi Data Universitas',
            ],
            [
                'key'   => 'relation',
                'steps' => ['relation'],
                'label' => 'Melengkapi Data Relasi',
            ],
            [
                'key'   => 'financialInformation',
                'steps' => ['financialInformation'],
                'label' => 'Melengkapi Rekening Bank Pribadi',
            ],
            [
                'key'   => 'uploadSignature',
                'steps' => ['uploadSignature'],
                'label' => 'Persetujuan Syarat dan Ketentuan',
            ],
        ];

        $groups = [];
        foreach ($groupMap as $group) {
            $indexes = [];
            foreach ($group['steps'] as $step) {
                $i = array_search($step, $flow);
                if ($i !== false) {
                    $indexes[] = $i;
                }
            }

            if (empty($indexes)) {
                continue;
            }

            $groups[] = [
                'label'     => $group['label'],
                'minIndex'  => min($indexes),
                'maxIndex'  => max($indexes),
                'firstStep' => $flow[min($indexes)],
            ];
        }
        return $groups;
    }
}