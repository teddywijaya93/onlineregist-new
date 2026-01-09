<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use thiagoalessio\TesseractOCR\TesseractOCR;

class KtpVerificationController extends Controller
{
    public function index()
    {
        if (session('step_passed') !== 'OTP_VERIFIED') {
            return redirect('/');
        }

        return view('verifikasi_ktp', [
            'ktp_path' => session('ktp_path'),
            'ocr'      => session('ktp_ocr'),
            'nik'      => session('nik'),
            'nama'     => session('nama'),
        ]);
    }

    public function ajaxOcr(Request $request)
     {
        // ======================
        // VALIDASI FILE
        // ======================
        if (!$request->hasFile('ktp_image')) {
            return response()->json(['error' => 'File tidak ada']);
        }

        $path = $request->file('ktp_image')->store('ktp', 'public');
        $fullPath = storage_path('app/public/' . $path);

        // ======================
        // OCR
        // ======================
        try {
            $raw = (new TesseractOCR($fullPath))
                ->lang('ind')
                ->psm(6)
                ->oem(1)
                ->run();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }

        // ======================
        // NORMALISASI (AMAN)
        // ======================
        $clean = strtoupper($raw);
        $clean = preg_replace('/[^A-Z0-9\/\-\:\,\.\s]/', ' ', $clean);
        $clean = preg_replace('/\s+/', ' ', $clean);

        // ======================
        // HELPER REGEX AMAN
        // ======================
        $find = function ($pattern, $group = 1) use ($clean) {
            if (preg_match($pattern, $clean, $m)) {
                return trim($m[$group] ?? '');
            }
            return '';
        };

        // ======================
        // PARSING (TOLERAN OCR)
        // ======================

        $data = [
            'nik' => $find('/\b\d{15,17}\b/', 0),

            'nama' => $find('/N\s*A\s*M\s*A\s*[:\-]?\s*([A-Z\s]{3,})/'),

            'ttl' => $find(
                '/T\s*E\s*M\s*P\s*A\s*T\s*\/?\s*T\s*G\s*L\s*L\s*A\s*H\s*I\s*R\s*[:\-]?\s*([A-Z\s]+,\s*\d{2}[\-\/]\d{2}[\-\/]\d{4})/'
            ),

            'alamat' => $find('/A\s*L\s*A\s*M\s*A\s*T\s*[:\-]?\s*([A-Z0-9\s]{5,})/'),

            'rt' => $find('/R\s*T\s*\/?\s*R\s*W\s*[:\-]?\s*(\d{1,3})\/\d{1,3}/'),

            'rw' => $find('/R\s*T\s*\/?\s*R\s*W\s*[:\-]?\s*\d{1,3}\/(\d{1,3})/'),

            'kelurahan' => $find('/K\s*E\s*L\s*\/?\s*D\s*E\s*S\s*A\s*[:\-]?\s*([A-Z\s]{3,})/'),

            'kecamatan' => $find('/K\s*E\s*C\s*A\s*M\s*A\s*T\s*A\s*N\s*[:\-]?\s*([A-Z\s]{3,})/'),

            'agama' => $find('/A\s*G\s*A\s*M\s*A\s*[:\-]?\s*(ISLAM|KRISTEN|KATOLIK|HINDU|BUDDHA|KONGHUCU)/'),

            'status' => $find('/S\s*T\s*A\s*T\s*U\s*S\s*P\s*E\s*R\s*K\s*A\s*W\s*I\s*N\s*A\s*N\s*[:\-]?\s*([A-Z\s]+)/'),

            'pekerjaan' => $find('/P\s*E\s*K\s*E\s*R\s*J\s*A\s*A\s*N\s*[:\-]?\s*([A-Z\s]+)/'),
        ];

        // ======================
        // RESPONSE FINAL
        // ======================
        return response()->json([
            'image'   => asset('storage/' . $path),
            'raw_ocr' => $raw,
            'data'    => $data,
        ]);
    }
}
