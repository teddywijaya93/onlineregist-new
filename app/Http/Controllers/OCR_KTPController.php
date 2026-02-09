<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class OCR_KTPController extends Controller
{
    public function process(Request $request) {
        $request->validate([
            'ktp_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // convert image ke base64 (tanpa prefix)
        $imageBase64 = base64_encode(
            file_get_contents($request->file('ktp_image')->getRealPath())
        );

        // basic auth
        $username  = 'phintraco_sekuritas_9dj5Pcm';
        $password  = 'psnN8VUiku';
        $basicAuth = base64_encode("$username:$password");

        // hit API OCR Privy
        $response = Http::withHeaders([
            'Merchant-Key' => 'YB822NQFNXSA66AAL93WEV7Z7',
            'Content-Type' => 'application/json',
            'Authorization'=> 'Basic ' . $basicAuth,
        ])->post('https://b2b-api-av.privy.id/dev/api/v3/ocr', [
            'trId'  => (string) Str::uuid(),
            'image' => $imageBase64,
        ]);

        if ($response->failed()) {
            return redirect()
                ->route('ocr.ktp.upload')
                ->withErrors(['ocr' => 'OCR gagal diproses']);
        }

        // simpan hasil OCR ke session
        session([
            'ocr_ktp' => $response->json('result')
        ]);

        // redirect ke halaman data personal (GET)
        return redirect()->route('data.personal');
    }

    public function dataPersonal() {
        if (!session()->has('ocr_ktp')) {
            return redirect()->route('ocr.ktp.upload');
        }

        return view('data-personal', [
            'data' => session('ocr_ktp')
        ]);
    }
}