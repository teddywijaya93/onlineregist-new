<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NIK_UsernameCheckController extends Controller
{
    public function nikCheck(Request $request) {
        $request->validate([
            'identity' => 'required|digits:16'
        ]);

        try {
            $response = Http::timeout(15)
                ->post('https://dev.profits.co.id:8283/registration/nikCheck', [
                    'identity' => $request->identity
                ]);

            if (!$response->successful()) {
                Log::error('NIK API Error', [
                    'status' => $response->status(),
                    'body'   => $response->body()
                ]);

                return response()->json([
                    'status'  => false,
                    'message' => 'Gagal Menghubungi Server'
                ], 500);
            }

            return response()->json($response->json());

        } catch (\Throwable $e) {
            Log::error('NIK Check Exception', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => false,
                'messageTitle' => 'Error',
                'messageBody' => 'Koneksi Server Gagal'
            ], 500);
        }
    }

    public function usernameCheck(Request $request) {
        $request->validate([
            'username' => 'required|min:5'
        ]);

        try {
            $response = Http::withoutVerifying()
                ->timeout(15)
                ->asJson()
                ->post('https://dev.profits.co.id:8283/registration/usernameCheck', [
                    'username' => $request->username
                ]);

            return response()->json($response->json(), $response->status());

        } catch (\Throwable $e) {
            Log::error('Username Check Exception', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => false,
                'messageTitle' => 'Error',
                'messageBody' => 'Koneksi Server Gagal'
            ], 500);
        }
    }   
}