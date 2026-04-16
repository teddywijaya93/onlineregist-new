<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use App\Services\StepRedirectService;

class Verifikasi_WajahController extends Controller
{
    public function index()
    {
        if (!session()->has('registrationId')) {
            return redirect()->route('email');
        }

        $step = session('registrationStep');

        return view('verifikasi-liveness-wajah', [
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

            // 2. VALIDASI INPUT
            $request->validate([
                'image' => 'required|string'
            ]);
            $imageBase64 = $request->image;

            // 3. CLEAN BASE64
            if (str_contains($imageBase64, ',')) {
                $imageBase64 = explode(',', $imageBase64)[1];
            }

            $imageBase64 = trim($imageBase64);
            $imageBase64 = preg_replace('/\s+/', '', $imageBase64);

            // 4. AUTH TILAKA
            $auth = Http::asForm()->post(
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

            // 5. HIT LIVENESS
            $liveness = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type'  => 'application/json'
            ])
            ->post(
                'https://sb-api.tilaka.id/passive-liveness',
                [
                    'image'                 => $imageBase64,
                    'is_quality'            => true,
                    'is_attribute'          => true,
                    'validate_quality'      => false,
                    'validate_attribute'    => true,
                    'validate_nface'        => true
                ]
            );

            $livenessResult = $liveness->json();
            Log::info('LIVENESS RESULT', [$livenessResult]);

            if (!$liveness->successful()) {
                Log::error('LIVENESS ERROR', [
                    'status' => $liveness->status(),
                    'body' => $liveness->body()
                ]);
                return back()->with('error', 'Gagal memproses wajah. Pastikan wajah terlihat jelas.');
            }

            // 6. VALIDASI LIVENESS
            $data = $livenessResult['data'] ?? [];

            $nface = $data['nface'] ?? 0;
            $deepfake = $data['deepfake'] ?? false;
            $livenessStatus = $data['liveness']['status'] ?? false;
            $probability = (float) ($data['liveness']['probability'] ?? 0);

            $attr = $data['attributes'] ?? [];
            $mask = $attr['mask_on'] ?? false;
            $sunglasses = $attr['sunglasses_on'] ?? false;
            $eyeglasses = $attr['eyeglasses_on'] ?? false;
            $hat = $attr['hat_on'] ?? false;
            $faceBlocker = $attr['face_blocker_on'] ?? false;
    
            // VALIDASI lebih dari 1 wajah
            if ($nface > 1) {
                return back()->with('error', 'Terdeteksi lebih dari 1 wajah. Harap ambil foto sendiri.');
            }
            // VALIDASI DEEPFAKE
            if ($deepfake === true) {
                return back()->with('error', 'Terdeteksi penggunaan deepfake / manipulasi wajah.');
            }

            // VALIDASI Liveness False
            if (!$livenessStatus) {
                return back()->with('error', 'Wajah tidak terdeteksi sebagai live (spoofing terindikasi).');
            }

            // VALIDASI Probability < 60%
            if ($probability < 60) {
                return back()->with('error', 'Kualitas liveness rendah. Silakan ulangi foto wajah.');
            }

            // VALIDASI Masker
            if ($mask) {
                return back()->with('error', 'Harap lepas masker saat verifikasi.');
            }

            // VALIDASI Kacamata Hitam & Kacamata Biasa
            if ($sunglasses || $eyeglasses) {
                return back()->with('error', 'Harap lepas kacamata saat verifikasi.');
            }

            // VALIDASI Topi
            if ($hat) {
                return back()->with('error', 'Harap lepas topi saat verifikasi.');
            }

            // VALIDASI Penutup Wajah
            if ($faceBlocker) {
                return back()->with('error', 'Wajah tidak terlihat jelas.');
            }

            // 7. SIMPAN FILE LOKAL
            $hash = md5($imageBase64);
            $namaFile = 'Selfie_' . $hash . '.jpg';

            // 8. UPLOAD KE PROFITS
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
                        "fileType"  => "selfie",
                        "fileName"  => $namaFile,
                        "fileImage" => $imageBase64
                    ]
                ]
            );
            $resultUpload = $response->json();

            if ($response->failed()) {
                return back()->with('error', $resultUpload['message'] ?? 'Upload Gagal');
            }

            // 9. NEXT STEP
            session([
                'registrationStep' => 'personalInformation'
            ]);

            if ($resultUpload['status'] ?? true) {
                return redirect()->route('data.personal')->with('success', $resultUpload['message'] ?? 'Berhasil');
            }
            return redirect()->route('data.personal')->with('error', $resultUpload['message'] ?? 'Gagal');

        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}