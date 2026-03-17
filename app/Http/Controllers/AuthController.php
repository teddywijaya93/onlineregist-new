<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\StepRedirectService;

class AuthController extends Controller
{
    public function loginNewRegistration(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        try {
            $response = Http::timeout(15)
                ->retry(2, 300)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . config('services.profits.token'),
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                ])
                ->post('https://dev.profits.co.id:8283/registration/loginNewRegistration', [
                    'username' => trim($request->username),
                    'password' => trim($request->password),
                ]);

            if (!$response->ok()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Internal Server Error'
                ], 500);
            }

            $result = $response->json();
            $isSuccess = filter_var($result['status'] ?? false, FILTER_VALIDATE_BOOLEAN);
            if ($isSuccess) {
                $request->session()->regenerate();
                session([
                    'accountId'        => $result['accountId'],
                    'registrationId'   => $result['registrationId'],
                    'registrationStep' => $result['registrationStep'],
                ]);

                return response()->json([
                    'status'   => true,
                    'redirect' => StepRedirectService::routeByStep(
                        $result['registrationStep']
                    )
                ]);
            }

            return response()->json([
                'status'  => false,
                'message' => $result['message'] ?? 'Login gagal'
            ], 400);

        } catch (\Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }

    public function checkEmail(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email'
            ]);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->withoutVerifying()
            ->timeout(15)
            ->connectTimeout(5)
            ->retry(1, 200)
            ->post(
                'https://dev.profits.co.id:8283/registration/checkEmail',
                [
                    "email" => $request->email
                ]
            );

            if (!$response->ok()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal Kirim Email'
                ]);
            }
            $data = $response->json();
            
            return response()->json($data);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Server Error'
            ]);
        }
    }

    public function sendOtpMail(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email'
            ]);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->withoutVerifying()
            ->timeout(15)
            ->connectTimeout(5)
            ->retry(1, 200)
            ->post(
                'https://dev.profits.co.id:8283/registration/sendOtpMail',
                [
                    "email" => $request->email
                ]
            );

            $data = $response->json();
            if (!$data['status']) {
                return response()->json($data);
            }

            // simpan email ke session
            session(['reg_email' => $request->email]);

            return response()->json($data);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Server Error'
            ]);
        }
    }

    public function verifyOtp(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->withoutVerifying()
            ->timeout(15)
            ->connectTimeout(5)
            ->retry(1, 200)
            ->post(
                'https://dev.profits.co.id:8283/registration/verificationOtp',
                [
                    "type"  => "EMAIL",
                    "value" => $request->email,
                    "otp"   => $request->otp
                ]
            );

            return response()->json(
                $response->json()
            );

        } catch (\Throwable $e) {
            return response()->json([
                "status" => false,
                "message" => "Internal Server error"
            ]);
        }
    }
}