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
        try
        {
            if (!session()->has('registrationId')) {
                return redirect()->route('login');
            }

            $request->validate([
                'ktp_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            $file = $request->file('ktp_image');
            $imageBase64 = base64_encode(
                file_get_contents($file->getRealPath())
            );

            // HIT Token Tilaka
            $auth = Http::asForm()->post(
                'https://sb-api.tilaka.id/auth',[
                    'client_id' => env('TILAKA_CLIENT_ID'),
                    'client_secret' => env('TILAKA_CLIENT_SECRET'),
                    'grant_type' => 'client_credentials'
                ]
            );

            if ($auth->failed()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Auth Tilaka Gagal!',
                    'response' => $auth->body()
                ]);
            }
            $accessToken = $auth->json('access_token');
            
            // HIT OCR Tilaka
            $ocr = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'])->post(
                'https://sb-api.tilaka.id/ocr-detection',[
                    'ktp' => $imageBase64,
                    'is_check_qualities' => true
                ]
            );

            if ($ocr->failed()) {
                return back()->with('api_message','OCR gagal');
            }
            $ocrResult = $ocr->json();

            $isKtp =
                $ocrResult['data']['qualities']['is_ktp']
                ?? $ocrResult['qualities']['is_ktp']
                ?? false;

            Log::info('IS KTP', [$isKtp]);
            if (!$isKtp) {
                return back()->with([
                    'api_message' => 'Foto bukan KTP, silakan upload ulang',
                    'api_status' => false
                ]);
            }

            session([
                'ocr_result' => $ocrResult
            ]);

            // Save to storage laravel
            $hash = md5($imageBase64);
            $namaFile = 'KTP_' . $hash . '.jpg';

            Storage::disk('public')->put(
                'ktp/' . $namaFile,
                base64_decode($imageBase64)
            );

            // Save to database  
            $response = Http::withHeaders([
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                ])
                ->withoutVerifying()
                ->timeout(15)
                ->connectTimeout(5)
                ->retry(1, 200)
                ->post(
                    'https://dev.profits.co.id:8283/registration/uploadAttachment',[
                    "registrationId" =>  session('registrationId'),
                    "datas" => [
                        "fileType" => "ktp",
                        "fileName" => $namaFile,
                        "fileImage" => $imageBase64
                    ]
                ]
            );
            $data = $response->json();

            Log::info('Upload Selfie', [$data]);
            if ($response->failed()) {
                return back()->with([
                    'api_message' => $data['message'] ?? 'Upload Selfie Gagal',
                    'api_status'  => false
                ]);
            }

            session([
                'registrationStep' => 'uploadSelfie'
            ]);

            return redirect()->route('verifikasi.wajah')
                ->with([
                    'api_message' => $data['message'],
                    'api_status'  => $data['status'] ?? true
                ]);

        } catch (\Throwable $e) {
            return back()->with('api_message','Internal Server Error');
        }
    }
}