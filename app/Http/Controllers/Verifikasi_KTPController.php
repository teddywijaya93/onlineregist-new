<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Verifikasi_KTPController extends Controller
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
                ->route('verifikasi.ktp')
                ->withErrors(['ocr' => 'OCR gagal diproses']);
        }

        // simpan hasil OCR ke session
        session([
            // 'verifikasi_ktp' => $response->json('result')
            'ocr_result' => $response->json('result')
        ]);

        // redirect ke halaman data personal (GET)
        return redirect()->route('data.personal');
    }

    public function dataPersonal() {
        if (!session()->has('verifikasi_ktp')) {
            return redirect()->route('verifikasi.ktp');
        }

        return view('data-personal', [
            'data' => session('verifikasi_ktp')
        ]);
    }

    // API ADVANCED AI
    public function processAdvanceOcrRaw(Request $request)
    {
        $request->validate([
            'ktp_image' => 'required|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        try {

            $response = Http::timeout(60)
                ->withHeaders([
                    'X-ADVAI-KEY'    => env('ADVAI_KEY'),
                    'X-ACCESS-TOKEN' => env('ADVAI_ACCESS_TOKEN'),
                ])
                ->attach(
                    'ocrImage',
                    file_get_contents($request->file('ktp_image')->getRealPath()),
                    $request->file('ktp_image')->getClientOriginalName()
                )
                ->post('https://api.advance.ai/openapi/face-recognition/v3/ocr-ktp-check');

            if ($response->failed()) {

                \Log::error('Advance OCR Failed', [
                    'status' => $response->status(),
                    'body'   => $response->body()
                ]);

                return back()->withErrors(['ocr' => 'OCR Advance.AI gagal']);
            }

            // simpan RAW JSON string
            session([
                'ocr_raw' => $response->body()
            ]);

            \Log::info('Advance OCR RAW', [
                'response' => $response->body()
            ]);

            return redirect()->route('verifikasi.ktp.advanced');

        } catch (\Exception $e) {

            \Log::error('Advance OCR Exception', [
                'error' => $e->getMessage()
            ]);

            return back()->withErrors(['ocr' => $e->getMessage()]);
        }
    }

    public function viewOcrRaw()
    {
        return view('verifikasi-ktp-advanced', [
            'raw' => session('ocr_raw')
        ]);
    }

    public function checkBankAccount(Request $request)
    {
        $request->validate([
            'bank' => 'required',
            'nomor_rekening' => 'required'
        ]);

        try {

            $response = Http::timeout(30)
                ->withHeaders([
                    'X-ADVAI-KEY' => env('ADVAI_KEY'),
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.advance.ai/openapi/verification/v1/bank-account-check', [
                    'bankCode' => $request->bank,
                    'bankAccount' => $request->nomor_rekening,
                ]);

            if ($response->failed()) {

                \Log::error('Bank Check Failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return back()->withErrors(['bank' => 'Bank verification gagal']);
            }

            $raw = $response->body();

            \Log::info('Bank Check RAW', [
                'response' => $raw
            ]);

            return back()->with('bank_raw', $raw);

        } catch (\Exception $e) {

            \Log::error('Bank Check Exception', [
                'error' => $e->getMessage()
            ]);

            return back()->withErrors(['bank' => $e->getMessage()]);
        }
    }
}