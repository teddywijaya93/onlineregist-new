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

    private function sendlivenessResult($status, $message, $ocrResponse, $typeOcr = "LIVENESS")
    {
        $data = isset($ocrResponse['data'])
            ? $ocrResponse['data']
            : $ocrResponse;

        $payload = [
            "registrationId" => session('registrationId'),
            "status"        => $status,
            "message"       => $message,
            "typeOcr"       => "LIVENESS",
            "referenceId"   => $data['reference_id'] ?? null,
            "raw"           => $data,
        ];

        // HIT API
        Http::timeout(60)
        ->connectTimeout(10)
        ->retry(3, 1000)
        ->post(
            'https://dev.profits.co.id:8283/registration/otpResult',$payload
        );

        Log::info('Liveness RESULT SENT', $payload);
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
                $this->sendlivenessResult(false, "[HTTP ".$auth->status()."] Auth Tilaka gagal", []);

                return back()->with('error', 'Auth Tilaka Gagal!');
            }
            $accessToken = $auth->json('access_token');

            // 5. HIT LIVENESS
            $liveness = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])
            ->timeout(60)
            ->connectTimeout(10)
            ->retry(3, 1000)
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

            $statusCode = $liveness->status();
            $livenessResult = $liveness->json() ?? [];

            Log::info('LIVENESS RESULT', [
                'status_code' => $statusCode,
                'body' => $livenessResult
            ]);

            $message = $livenessResult['message'] ?? 'Unknown Error';

            $this->sendlivenessResult($statusCode === 200, "[HTTP {$statusCode}] ".$message, $livenessResult);

            // HANDLE HTTP ERROR
            if (!$liveness->successful()) {
                Log::error('LIVENESS ERROR', [
                    'status' => $liveness->status(),
                    'body' => $liveness->body()
                ]);

                return back()->with('error', $message ?? 'Gagal memproses wajah.');
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
                $msg = 'Terdeteksi lebih dari 1 wajah.';
                $this->sendlivenessResult(false, $msg, $livenessResult);

                return back()->with('error', $msg);
            }
            // VALIDASI DEEPFAKE
            if ($deepfake === true) {
                $msg = 'Terdeteksi penggunaan deepfake / manipulasi wajah.';
                $this->sendlivenessResult(false, $msg, $livenessResult);

                return back()->with('error', $msg);
            }

            // VALIDASI Liveness False
            if (!$livenessStatus) {
                $msg = 'Wajah tidak terdeteksi sebagai live.';
                $this->sendlivenessResult(false, $msg, $livenessResult);

                return back()->with('error', $msg);
            }

            // VALIDASI Probability < 60%
            if ($probability < 60) {
                $msg = 'Kualitas liveness rendah.';
                $this->sendlivenessResult(false, $msg, $livenessResult);

                return back()->with('error', $msg);
            }

            // VALIDASI Masker
            if ($mask) {
                $msg = 'Harap lepas masker saat verifikasi.';
                $this->sendlivenessResult(false, $msg, $livenessResult);

                return back()->with('error', $msg);
            }

            // VALIDASI Kacamata Hitam & Kacamata Biasa
            if ($sunglasses || $eyeglasses) {
                $msg = 'Harap lepas kacamata saat verifikasi.';
                $this->sendlivenessResult(false, $msg, $livenessResult);

                return back()->with('error', $msg);
            }

            // VALIDASI Topi
            if ($hat) {
                $msg = 'Harap lepas topi saat verifikasi.';
                $this->sendlivenessResult(false, $msg, $livenessResult);

                return back()->with('error', $msg);
            }

            // VALIDASI Penutup Wajah
            if ($faceBlocker) {
                $msg = 'Wajah tidak terlihat jelas.';
                $this->sendlivenessResult(false, $msg, $livenessResult);

                return back()->with('error', $msg);
            }

            // SUCCESS JUGA KIRIM
            $this->sendlivenessResult(true, 'Liveness Success', $livenessResult);

            // 7. SIMPAN FILE
            $hash = md5($imageBase64);
            $namaFile = 'Selfie_' . $hash . '.jpg';

            // 8. UPLOAD KE PROFITS
            $response = Http::asJson()
            ->timeout(60)
            ->connectTimeout(10)
            ->retry(3, 1000)
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

            return redirect()->route('data.personal')->with(
                ($resultUpload['status'] ?? true)
                    ? ['success' => $resultUpload['message'] ?? 'Berhasil']
                    : ['error'   => $resultUpload['message'] ?? 'Gagal']
            );

        } catch (\Throwable $e) {
            $this->sendlivenessResult(false, '[EXCEPTION] '.$e->getMessage(), []);

            return back()->with('error', $e->getMessage());
        }
    }
}