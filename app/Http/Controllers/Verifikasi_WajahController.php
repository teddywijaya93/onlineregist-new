<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Services\StepRedirectService;

class Verifikasi_WajahController extends Controller
{
    public function index()
    {
        if ($r = StepRedirectService::guardStep()) {
            return redirect($r);
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
            if (!session()->has('registrationId')) {
                return redirect()->route('login');
            }

            $request->validate([
                'image' => 'required|string'
            ]);
            $imageBase64 = $request->image;

            if (str_contains($imageBase64, ',')) {
                $imageBase64 = explode(',', $imageBase64)[1];
            }

            $hash = md5($imageBase64);
            $namaFile = 'Selfie_' . $hash . '.jpg';

            Storage::disk('public')->put(
                'selfie/' . $namaFile,
                base64_decode($imageBase64)
            );

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
                    "registrationId" => session('registrationId'),
                    "datas" => [
                        "fileType"  => "selfie",
                        "fileName"  => $namaFile,
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
                'registrationStep' => 'personalInformation'
            ]);

            return redirect()->route('data.personal')
                ->with([
                    'api_message' => $data['message'],
                    'api_status'  => $data['status'] ?? true
                ]);

        } catch (\Throwable $e) {
            return back()->with('api_message','Internal Server Error');
        }
    }
}