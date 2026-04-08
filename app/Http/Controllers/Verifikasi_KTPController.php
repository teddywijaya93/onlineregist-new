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
        if ($r = StepRedirectService::guardStep()) {
            return redirect($r);
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

            // 2. VALIDASI FILE
            $request->validate([
                'ktp_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            $file = $request->file('ktp_image');
            if (!$file->isValid()) {
                return back()->with('api_message', 'File upload tidak valid');
            }

            // 3. CONVERT KE BASE64 (CLEAN)
            $binary = file_get_contents($file->getRealPath());
            if ($binary === false) {
                return back()->with('api_message', 'Gagal membaca file');
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
                return back()->with('api_message', 'Auth Tilaka Gagal!');
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

            if ($ocr->failed()) {
                return back()->with('api_message', 'Foto Bukan KTP, Silahkan Upload Ulang');
            }
            $ocrResult = $ocr->json();
            Log::info('OCR RESULT', [$ocrResult]);

            // 6. VALIDASI KTP (FIX TANPA is_ktp)
            $data = $ocrResult['data'] ?? [];
            $isKtp =
                !empty($data['nik']) &&
                !empty($data['full_name']) &&
                !empty($data['date_of_birth']);

            Log::info('IS KTP CHECK', [$isKtp, $data]);

            if (!$isKtp) {
                return back()->with([
                    'api_message' => 'Data KTP tidak terbaca dengan benar',
                    'api_status'  => false
                ]);
            }

            // 7. SIMPAN KE SESSION
            session([
                'ocr_result' => $ocrResult
            ]);

            // 8. SIMPAN FILE LOKAL
            $hash = md5($imageBase64);
            $namaFile = 'KTP_' . $hash . '.jpg';
            Storage::disk('public')->put(
                'ktp/' . $namaFile,
                base64_decode($imageBase64)
            );

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
                return back()->with([
                    'api_message' => $resultUpload['message'] ?? 'Upload gagal',
                    'api_status'  => false
                ]);
            }

            // 10. NEXT STEP
            session([
                'registrationStep' => 'uploadSelfie'
            ]);

            return redirect()->route('verifikasi.wajah')->with([
                'api_message' => $resultUpload['message'] ?? 'Berhasil',
                'api_status'  => $resultUpload['status'] ?? true
            ]);

        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}