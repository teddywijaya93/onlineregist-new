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
            $response = Http::timeout(10)
                ->post('https://dev.profits.co.id:8283/registration/nikCheck', [
                    'identity' => $request->identity
                ]);

            if (!$response->successful()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Internal Server Error'
                ], 500);
            }

            return response()->json($response->json());

        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'messageTitle' => 'Error',
                'messageBody' => 'Internal Server Error'
            ], 500);
        }
    }

    public function usernameCheck(Request $request) {
        $request->validate([
            'username' => 'required|min:5'
        ]);

        try {
            $response = Http::timeout(10)
                ->timeout(15)
                ->asJson()
                ->post('https://dev.profits.co.id:8283/registration/usernameCheck', [
                    'username' => $request->username
                ]);

            return response()->json($response->json(), $response->status());

        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'messageTitle' => 'Error',
                'messageBody' => 'Internal Server Error'
            ], 500);
        }
    }   
}