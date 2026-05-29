<?php

$baseUrl = env('API_BASE_URL');
return [
    'timeout'                   => env('API_TIMEOUT'),
    'connect_timeout'           => env('API_CONNECT_TIMEOUT'),
    'retry'                     => env('API_RETRY'),
    'retry_sleep'               => env('API_RETRY_SLEEP'),
    'tilaka_client_id'          => env('TILAKA_CLIENT_ID'),
    'tilaka_client_secret'      => env('TILAKA_CLIENT_SECRET'),

    // HIT Endpoint Profits
    'referralCheck'             => $baseUrl . '/registration/referralCheck',
    'checkEmail'                => $baseUrl . '/registration/checkEmail',
    'sendOtpMail'               => $baseUrl . '/registration/sendOtpMail',
    'verificationOtp'           => $baseUrl . '/registration/verificationOtp',
    'createAccount'             => $baseUrl . '/registration/createAccount',
    'masterData'                => $baseUrl . '/registration/masterData',
    'getEmploymentPosition'     => $baseUrl . '/registration/getEmploymentPosition',
    'getEmploymentBusinessline' => $baseUrl . '/registration/getEmploymentBusinessline',
    'getKecamatan'              => $baseUrl . '/registration/getKecamatan',
    'getKelurahan'              => $baseUrl . '/registration/getKelurahan',
    'getAllKelurahan'           => $baseUrl . '/registration/getKelurahan',
    'createPin'                 => $baseUrl . '/registration/createPin',
    'createAccountType'         => $baseUrl . '/registration/createAccountType',
    'saveRegistration'          => $baseUrl . '/registration/saveRegistration',
    'getRegistration'           => $baseUrl . '/registration/getRegistration',
    'ocrResult'                 => $baseUrl . '/registration/ocrResult',
    'getOcrResult'              => $baseUrl . '/registration/getOcrResult',
    'uploadAttachment'          => $baseUrl . '/registration/uploadAttachment',

    // HIT Endpoint Tilaka
    'authTilaka'                => 'https://sb-api.tilaka.id/auth',
    'passiveLiveness'           => 'https://sb-api.tilaka.id/passive-liveness',
    'antiForgery'               => 'https://sb-api.tilaka.id/ocr/v2/ktp/antiforgery',
];