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
                    'message' => 'Server API bermasalah'
                ], 500);
            }

            $result = $response->json();
            $isSuccess = filter_var($result['status'] ?? false, FILTER_VALIDATE_BOOLEAN);
            if ($isSuccess) {
                $request->session()->regenerate();
                session([
                    'accountId'        => $result['accountId'],
                    'registrationId'   => $result['registrationId'],
                    // 'registrationStep' => $result['registrationStep'],
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
            Log::error('LOGIN ERROR', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required'
        ]);

        $accountId = session('accountId');
        if (!$accountId) {
            return response()->json([
                'status'  => false,
                'message' => 'Session expired, silakan login kembali'
            ], 401);
        }

        try {
            $response = Http::timeout(15)
                ->retry(2, 300)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json',
                ])
                ->post('https://dev.profits.co.id:8283/registration/verificationOTPRegistration', [
                    'accountId' => (string) $accountId,
                    'otp'       => $request->otp,
                ]);

            if (!$response->ok()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Server OTP bermasalah'
                ], 500);
            }

            $result = $response->json();
            $isSuccess = filter_var($result['status'] ?? false, FILTER_VALIDATE_BOOLEAN);
            if ($isSuccess) {
                // session([
                //     'registrationStep' => $result['registrationStep']
                // ]);

                return response()->json([
                    'status'   => true,
                    'redirect' => StepRedirectService::routeByStep(
                        $result['registrationStep']
                    )
                ]);
            }

            return response()->json([
                'status'  => false,
                'message' => $result['message'] ?? 'OTP tidak valid'
            ], 400);

        } catch (\Throwable $e) {
            Log::error('VERIFY OTP ERROR', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

    public function resendOtp(Request $request)
    {
        $accountId = session('accountId');
        if (!$accountId) {
            return response()->json([
                'status'  => false,
                'message' => 'Session expired, silakan login kembali'
            ], 401);
        }

        try {
            $response = Http::timeout(15)
                ->retry(2, 300)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json',
                ])
                ->post('https://dev.profits.co.id:8283/registration/resendOtp', [
                    'accountId' => (string) $accountId,
                ]);

            if (!$response->ok()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Server resend OTP bermasalah'
                ], 500);
            }
            return response()->json($response->json());

        } catch (\Throwable $e) {
            Log::error('RESEND OTP ERROR', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

    public function showLogin()
    {
        return view('login');
    }

    public function showOtp()
    {
        if (!session()->has('accountId')) {
            return redirect()->route('login');
        }

        return view('otp');
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}