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

    private function sendOcrResult($status, $message, $ocrResponse, $typeOcr = "KTP")
    {
        $data = isset($ocrResponse['data'])
            ? $ocrResponse['data']
            : $ocrResponse;

        $payload = [
            "registrationId" => session('registrationId'),
            "status"        => $status,
            "message"       => $message,
            "typeOcr"       => "KTP",
            "referenceId"   => $data['reference_id'] ?? null,
            "raw"           => $data,
        ];

        // HIT API
        Http::timeout(config('api.timeout'))
        ->connectTimeout(config('api.connect_timeout'))
        ->retry(
            config('api.retry'),
            config('api.retry_sleep')
        )
        ->post(config('api.ocrResult'), $payload);

        Log::info('OCR RESULT SENT', $payload);
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
                $this->sendOcrResult(false, "[VALIDATION] " . $errorMsg, []);

                return back()->with('error', $errorMsg);
            }

            $file = $request->file('ktp_image');
            if (!$file->isValid()) {
                $errorMsg = 'File upload tidak valid';
                $this->sendOcrResult(false, "[FILE INVALID] " . $errorMsg, []);

                return back()->with('error', $errorMsg);
            }

            // 3. CONVERT KE BASE64 (CLEAN)
            $binary = file_get_contents($file->getRealPath());
            if ($binary === false) {
                $errorMsg = 'Gagal membaca file upload';
                $this->sendOcrResult(false, "[READ FILE ERROR] " . $errorMsg, []);

                return back()->with('error', $errorMsg);
            }
            $imageBase64 = base64_encode($binary);
            $imageBase64 = trim($imageBase64);
            $imageBase64 = preg_replace('/\s+/', '', $imageBase64);

            // 4. AUTH TILAKA
            $auth = Http::asForm()
            ->timeout(config('api.timeout'))
            ->connectTimeout(config('api.connect_timeout'))
            ->retry(
                config('api.retry'),
                config('api.retry_sleep')
            )
            ->post(config('api.authTilaka'),
            [
                'client_id'     => config('api.tilaka_client_id'),
                'client_secret' => config('api.tilaka_client_secret'),
                'grant_type'    => 'client_credentials'
            ]);

            if ($auth->failed()) {
                $errorMsg = $auth->json('message') ?? 'Auth Tilaka gagal';
                $this->sendOcrResult(false, "[AUTH FAILED] " . $errorMsg, $auth->json() ?? []);

                return back()->with('error', $errorMsg);
            }
            $accessToken = $auth->json('access_token');

            // 5. HIT OCR
            $ocr = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])
            ->timeout(config('api.timeout'))
            ->connectTimeout(config('api.connect_timeout'))
            ->retry(
                config('api.retry'),
                config('api.retry_sleep'),
                throw: false
            )
            ->post(config('api.antiForgery'),
            [
                'image' => $imageBase64,
                'validate_quality' => true
            ]);

            // Get ALL Response
            $statusCode = $ocr->status();
            $ocrResult  = $ocr->json() ?? [];

            // Extract Data
            $tilakaSuccess = $ocrResult['success'] ?? false;
            $message       = $ocrResult['message'] ?? 'Unknown error';
            $referenceId   = $ocrResult['data']['reference_id'] ?? "";

            if ($ocr->failed()) {
                // HANDLE FORGERY
                if (($ocrResult['data']['forgeries'] ?? false) === true) {
                    $errorMsg = 'Foto terindikasi manipulasi / forgery';
                } else {
                    $errorMsg = $ocrResult['message']
                        ?? $ocrResult['error']
                        ?? 'OCR gagal diproses';
                }
                $this->sendOcrResult(false, "[OCR HTTP ERROR {$statusCode}] " . $errorMsg, $ocrResult);

                return back()->with('error', $errorMsg);
            }

            // SEND KE OTP RESULT
            $this->sendOcrResult(false, $message, $ocrResult);

            // HANDLE OCR FAIL
            if (!$tilakaSuccess) {
                $this->sendOcrResult(false, "[OCR FAILED] " . $errorMsg, $ocrResult );

                return back()->with('error', $message);
            }

            // VALIDASI IMAGE QUALITY
            $imageQuality = $ocrResult['data']['image_quality'] ?? [];
            $forgeries    = $ocrResult['data']['forgeries'] ?? false;
            $isInvalid = ($imageQuality['blur'] ?? false) || ($imageQuality['dark'] ?? false) || ($imageQuality['grayscale'] ?? false) || ($imageQuality['flashlight'] ?? false) || $forgeries;
            
            if ($isInvalid) {
                $reasons = [];

                if (!empty($imageQuality['blur'])) $reasons[] = 'Blur';
                if (!empty($imageQuality['dark'])) $reasons[] = 'Terlalu Gelap';
                if (!empty($imageQuality['grayscale'])) $reasons[] = 'Grayscale';
                if (!empty($imageQuality['flashlight'])) $reasons[] = 'Flash Terlalu Terang';
                if ($forgeries) $reasons[] = 'Terindikasi Manipulasi';

                $messageReject = 'Foto KTP tidak valid: ' . implode(', ', $reasons);

                // kirim ke BO
                $this->sendOcrResult(false, $messageReject, $referenceId, [
                    'image_quality' => $imageQuality,
                    'forgeries'     => $forgeries
                ]);

                return back()->with('error', $messageReject);
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
            $response = Http::asJson()
            ->timeout(config('api.timeout'))
            ->connectTimeout(config('api.connect_timeout'))
            ->retry(
                config('api.retry'),
                config('api.retry_sleep')
            )
            ->post(config('api.uploadAttachment'),
            [
                "registrationId" => session('registrationId'),
                "datas" => [
                    "fileType"  => "ktp",
                    "fileName"  => $namaFile,
                    "fileImage" => $imageBase64
                ]
            ]);
            $resultUpload = $response->json();

            // if ($response->failed()) {
            //     return back()->with('error', $resultUpload['message'] ?? 'Upload gagal');
            // }

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
            $errorMsg = $e->getMessage();
            $this->sendOcrResult(false,"[EXCEPTION] " . $errorMsg, []);

            return back()->with('error', $errorMsg);
        }
    }
}