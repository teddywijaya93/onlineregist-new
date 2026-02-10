<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class Verifikasi_WajahController extends Controller
{
    public function process(Request $request)
    {
        $request->validate([
            'image' => 'required|string'
        ]);

        try {
            $client = new Client();
            $response = $client->post(
                'https://api.verihubs.com/v1/face/liveness',
                [
                    'headers' => [
                        'API-Key'      => env('VERIHUBS_API_KEY'),
                        'App-ID'       => env('VERIHUBS_APP_ID'),
                        'Accept'       => 'application/json',
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'image'              => $request->image,
                        'is_quality'         => true,
                        'is_attribute'       => true,
                        'validate_quality'   => false,
                        'validate_attribute' => false,
                        'validate_nface'     => false,
                    ],
                    'timeout' => 30
                ]
            );

            return response()->json(
                json_decode($response->getBody(), true)
            );

        } catch (\Throwable $e) {
            Log::error('Verihubs Liveness Error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Face Liveness gagal',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}