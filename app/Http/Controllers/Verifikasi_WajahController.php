<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class Verifikasi_WajahController extends Controller
{
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
            Log::info('Selfie saved', [$namaFile]);

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
            Log::info('Upload selfie', [$response->json()]);

            if ($response->failed()) {
                return back()->with('api_message','Upload selfie gagal');
            }

            session([
                'currentStep' => 'personalInformation'
            ]);
            return redirect()->route('data.personal');

        } catch (\Throwable $e) {
            Log::error('Proses Selfie Error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            return back()->with(
                'api_message',
                'Terjadi error selfie'
            );
        }
    }
}