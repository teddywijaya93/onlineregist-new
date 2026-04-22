<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
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

    private function sendOtpResult($status, $message, $referenceId = "", $extra = [])
    {
        $payload = [
            "registrationId" => session('registrationId'),
            "status"        => $status,
            "message"       => $message,
            "referenceId"   => $referenceId
        ];

        // HIT API
        Http::withHeaders([
            'Content-Type' => 'application/json'
        ])
        ->post(
            'https://dev.profits.co.id:8283/registration/otpResult',$payload
        );

        Log::info('OTP RESULT SENT', array_merge([
            'registrationId' => session('registrationId'),
            'status' => $status,
            'message' => $message,
            'referenceId' => $referenceId
        ], $extra));
    }

    public function process(Request $request)
    {
        try {
            // 1. VALIDASI SESSION
            if (!session()->has('registrationId')) {
                return redirect()->route('email');
            }

            // 2. VALIDASI FILE
            $validator = Validator::make($request->all(), [
                'ktp_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            if ($validator->fails()) {
                $errorMsg = $validator->errors()->first();

                $this->sendOtpResult(false, "[VALIDATION] ".$errorMsg);

                // Log::warning('KTP VALIDATION ERROR', [
                //     'registrationId' => session('registrationId'),
                //     'error' => $errorMsg
                // ]);

                return back()->with('error', $errorMsg);
            }

            $file = $request->file('ktp_image');
            if (!$file->isValid()) {
                $this->sendOtpResult(false, "[FILE INVALID] File upload tidak valid");

                // Log::warning('KTP FILE INVALID', [
                //     'registrationId' => session('registrationId')
                // ]);

                return back()->with('error', 'File upload tidak valid');
            }

            // 3. CONVERT KE BASE64 (CLEAN)
            $binary = file_get_contents($file->getRealPath());
            if ($binary === false) {
                $this->sendOtpResult(false, "[READ ERROR] Gagal membaca file");

                // Log::error('KTP READ ERROR', [
                //     'registrationId' => session('registrationId')
                // ]);

                return back()->with('error', 'Gagal membaca file');
            }
            $imageBase64 = base64_encode($binary);
            $imageBase64 = trim($imageBase64);
            $imageBase64 = preg_replace('/\s+/', '', $imageBase64);

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

            // Get ALL Response
            $statusCode = $ocr->status();
            $ocrResult  = $ocr->json() ?? [];

            // Extract Data
            $tilakaSuccess = $ocrResult['success'] ?? false;
            $message       = $ocrResult['message'] ?? 'Unknown error';
            $referenceId   = $ocrResult['data']['reference_id'] ?? "";

            Log::info('OCR ANTIFORGERY RESPONSE', [
                'registrationId' => session('registrationId'),
                'status_code'    => $statusCode,
                'success'        => $tilakaSuccess,
                'message'        => $message,
                'referenceId'    => $referenceId,
                'raw'            => $ocrResult
            ]);

            if ($ocr->failed()) {
                $this->sendOtpResult(false, "[OCR HTTP ERROR {$statusCode}] " . $message, $referenceId,[
                    'raw' => $ocrResult
                ]);

                // Log::error('OCR HTTP FAILED', [
                //     'registrationId' => session('registrationId'),
                //     'status_code'    => $statusCode,
                //     'response'       => $ocrResult
                // ]);

                return back()->with('error', 'OCR request gagal');
            }

            // SEND KE OTP RESULT
            $this->sendOtpResult($tilakaSuccess, "[{$statusCode}] " . $message, $referenceId,[
                'raw' => $ocrResult
            ]);

            // HANDLE OCR FAIL
            if (!$tilakaSuccess) {

                Log::warning('OCR FAILED', [
                    'registrationId' => session('registrationId'),
                    'status_code'    => $statusCode,
                    'message'        => $message
                ]);

                return back()->with('error', $message);
            }

            // 6. VALIDASI KTP (FIX TANPA is_ktp)
            $data = $ocrResult['data'] ?? [];
            $isKtp =
                !empty($data['nik']) &&
                !empty($data['full_name']) &&
                !empty($data['date_of_birth']);

            Log::info('IS KTP CHECK', [$isKtp, $data]);

            if (!$isKtp) {
                return back()->with('error', 'Data KTP tidak terbaca dengan benar');
            }

            // 7. SIMPAN KE SESSION
            session([
                'ocr_result' => $ocrResult
            ]);

            // 8. SIMPAN FILE LOKAL
            $hash = md5($imageBase64);
            $namaFile = 'KTP_' . $hash . '.jpg';

            // Storage::disk('public')->put(
            //     'ktp/' . $namaFile,
            //     base64_decode($imageBase64)
            // );

            // 9. UPLOAD KE API PROFITS
            $response = Http::withHeaders([
                'Accept' => 'application/json',
            ])
            ->asJson()
            ->timeout(15)
            ->connectTimeout(5)
            ->retry(1, 200)
            ->post(
                'https://dev.profits.co.id:8283/registration/uploadAttachment',
                [
                    "registrationId" => session('registrationId'),
                    "datas" => [
                        "fileType"  => "ktp",
                        "fileName"  => $namaFile,
                        "fileImage" => $imageBase64
                    ]
                ]
            );
            $resultUpload = $response->json();

            // Log::info('UPLOAD RESPONSE', [$resultUpload]);
            if ($response->failed()) {
                return back()->with('error', $resultUpload['message'] ?? 'Upload gagal');
            }

            // 10. NEXT STEP
            session([
                'registrationStep' => 'uploadSelfie'
            ]);

            return redirect()->route('verifikasi.wajah')->with(
                ($resultUpload['status'] ?? true)
                    ? ['success' => $resultUpload['message'] ?? 'Berhasil']
                    : ['error'   => $resultUpload['message'] ?? 'Gagal']
            );

        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}