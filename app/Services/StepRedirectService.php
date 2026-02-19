<?php

namespace App\Services;

class StepRedirectService
{
    public static function routeByStep(?string $step): string
    {
        return match ($step) {

            'verificationWA' => route('otp'),

            'uploadKTP' => route('verifikasi.ktp'),

            // future step
            'dataPersonal' => route('data.personal'),

            default => url('/')
        };
    }
}