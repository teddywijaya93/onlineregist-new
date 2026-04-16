<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\StepRedirectService;

class Verifikasi_KTPController extends Controller
{
    public function index()
    {
        if (!session()->has('registrationId')) {
            return redirect()->route('email');
        }

        $step = session('registrationStep');
        
        return view('verifikasi-ktp', [
            'step' => StepRedirectService::stepNumber($step),
            'hideBack' => StepRedirectService::hideBack()
        ]);
    }

    public function process(Request $request)
    {
        try {
            // 1. VALIDASI SESSION
            if (!session()->has('registrationId')) {
                return redirect()->route('email');
            }

            $registrationId = session('registrationId');

            // 2. VALIDASI FILE
            $validator = \Validator::make($request->all(), [
                'ktp_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            if ($validator->fails()) {
                $message = $validator->errors()->first();

                Log::info('VALIDATION ERROR', ['message' => $message]);

                try {
                    $res = Http::asJson()
                    ->timeout(5)
                    ->connectTimeout(3)
                    ->post(
                        'https://dev.profits.co.id:8283/registration/otpResult',
                        [
                            "registrationId" => $registrationId,
                            "status" => false,
                            "message" => "[VALIDATION] " . $message,
                            "referenceId" => ""
                        ]
                    );

                    Log::info('VALIDATION OTP RESPONSE', [
                        'status_code' => $res->status(),
                        'body' => $res->body()
                    ]);

                } catch (\Throwable $e) {
                    Log::error('VALIDATION OTP ERROR', ['error' => $e->getMessage()]);
                }

                return back()->with('error', $message);
            }

            $file = $request->file('ktp_image');
            if (!$file->isValid()) {
                $msg = "File Upload Tidak Valid";

                Log::info('FILE ERROR', [$msg]);

                try {
                    Http::asJson()
                    ->post(
                        'https://dev.profits.co.id:8283/registration/otpResult',
                        [
                            "registrationId" => $registrationId,
                            "status" => false,
                            "message" => "[FILE ERROR] " . $msg,
                            "referenceId" => ""
                        ]
                    );
                } catch (\Throwable $e) {}

                return back()->with('error', $msg);
            }

            // 3. CONVERT KE BASE64 (CLEAN)
            $binary = file_get_contents($file->getRealPath());
            if ($binary === false) {
                return back()->with('error', 'Gagal membaca file');
            }
            $imageBase64 = base64_encode($binary);
            $imageBase64 = trim(preg_replace('/\s+/', '', $imageBase64));

            // 4. AUTH TILAKA
            $auth = Http::asForm()
            ->post(
                'https://sb-api.tilaka.id/auth',
                [
                    'client_id'     => env('TILAKA_CLIENT_ID'),
                    'client_secret' => env('TILAKA_CLIENT_SECRET'),
                    'grant_type'    => 'client_credentials'
                ]
            );

            if ($auth->failed()) {
                $msg = "[HTTP ".$auth->status()."] Auth Tilaka gagal";

                Log::info('AUTH ERROR', [$msg]);

                Http::asJson()
                ->post(
                    'https://dev.profits.co.id:8283/registration/otpResult',
                    [
                        "registrationId" => $registrationId,
                        "status" => false,
                        "message" => $msg,
                        "referenceId" => ""
                    ]
                );

                return back()->with('error', 'Auth Tilaka Gagal!');
            }
            $accessToken = $auth->json('access_token');

            // 5. HIT OCR
            $ocr = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type'  => 'application/json'
            ])
            ->post(
                'https://sb-api.tilaka.id/ocr/v2/ktp/antiforgery',
                [
                    'image' => $imageBase64,
                    'validate_quality' => true
                ]
            );

            $statusCode = $ocr->status();
            $rawBody    = $ocr->body();
            $parsed     = json_decode($rawBody, true) ?? [];

            Log::info('TILAKA RESPONSE FULL', [
                'status_code' => $statusCode,
                'raw_body' => $rawBody,
                'parsed' => $parsed
            ]);

            $message = "[HTTP $statusCode] " . ($parsed['message'] ?? $rawBody);
            $payload = [
                "registrationId" => $registrationId,
                "status" => $statusCode === 200,
                "message" => $message,
                "referenceId" => $parsed['data']['reference_id'] ?? ""
            ];

            Log::info('OTP RESULT PAYLOAD', $payload);

            try {
                $responseOtp = Http::asJson()
                    ->timeout(5)
                    ->connectTimeout(3)
                    ->post(
                        'https://dev.profits.co.id:8283/registration/otpResult',
                        $payload
                    );

                Log::info('OTP RESULT RESPONSE', [
                    'status_code' => $responseOtp->status(),
                    'body' => $responseOtp->body()
                ]);

            } catch (\Throwable $e) {
                Log::error('OTP RESULT ERROR', ['error' => $e->getMessage()]);
            }

            if ($ocr->failed()) {
                return back()->with('error', $parsed['message'] ?? 'Gagal OCR');
            }
            $ocrResult = $ocr->json();
            Log::info('OCR RESULT', [$ocrResult]);

            $data = $ocrResult['data'] ?? [];
            $isKtp =
                !empty($data['nik']) &&
                !empty($data['full_name']) &&
                !empty($data['date_of_birth']);

            Log::info('IS KTP CHECK', [$isKtp, $data]);

            if (!$isKtp) {
                return back()->with('error', 'Data KTP tidak terbaca dengan benar');
            }

            session([
                'ocr_result' => $ocrResult
            ]);

            $hash = md5($imageBase64);
            $namaFile = 'KTP_' . $hash . '.jpg';

            $response = Http::asJson()
            ->post(
                'https://dev.profits.co.id:8283/registration/uploadAttachment',
                [
                    "registrationId" => $registrationId,
                    "datas" => [
                        "fileType"  => "ktp",
                        "fileName"  => $namaFile,
                        "fileImage" => $imageBase64
                    ]
                ]
            );
            $resultUpload = $response->json();

            if ($response->failed()) {
                return back()->with('error', $resultUpload['message'] ?? 'Upload gagal');
            }

            session([
                'registrationStep' => 'uploadSelfie'
            ]);

            return redirect()->route('verifikasi.wajah')->with(
                ($resultUpload['status'] ?? true)
                    ? ['success' => $resultUpload['message'] ?? 'Berhasil']
                    : ['error'   => $resultUpload['message'] ?? 'Gagal']
            );

        } catch (\Throwable $e) {

            Log::error('GLOBAL ERROR', [$e->getMessage()]);

            try {
                Http::asJson()
                ->post(
                    'https://dev.profits.co.id:8283/registration/otpResult',
                    [
                        "registrationId" => session('registrationId'),
                        "status" => false,
                        "message" => "[EXCEPTION] " . $e->getMessage(),
                        "referenceId" => ""
                    ]
                );
            } catch (\Throwable $ex) {}

            return back()->with('error', $e->getMessage());
        }
    }
}