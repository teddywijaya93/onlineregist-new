<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class AuthController extends Controller
{
    public function loginNewRegistration(Request $request)
    {
        try {
            if (empty($request->username) || empty($request->password)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Username dan Password wajib diisi'
                ], 422);
            }

            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . config('services.profits.token'),
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                ])
                ->post('https://dev.profits.co.id:8283/registration/loginNewRegistration', [
                    'username' => trim($request->username),
                    'password' => trim($request->password),
                ]);

            // Jika API error (500 / 404 / dll)
            if (!$response->ok()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Server API sedang bermasalah'
                ], 500);
            }

            $result = $response->json();

            // Success
            if (!empty($result['status']) && $result['status'] === true) {

                session([
                    'accountId'        => $result['accountId'],
                    'registrationStep' => $result['registrationStep'],
                    // 'otpRequired'      => $result['otpRequired'],
                ]);

                // fallback mobilePhone dari createAccount
                if (!empty(session('register_phone'))) {
                    session(['mobilePhone' => session('register_phone')]);
                }

                return response()->json([
                    'status'           => true,
                    // 'otpRequired'      => $result['otpRequired'],
                    'registrationStep' => $result['registrationStep'],
                ]);
            }

            // Jika username/password salah
            return response()->json([
                'status'  => false,
                'message' => $result['message'] ?? 'Username / Password Salah!'
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
        try {   
            if (empty($request->otp)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Kode OTP wajib diisi'
                ], 422);
            }

            $accountId = session('accountId');

            if (!$accountId) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Session expired, silakan login kembali'
                ], 401);
            }

            $response = Http::timeout(15)
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

            if (!empty($result['status']) && $result['status'] === true) {
                // update registrationStep terbaru
                session([
                    'registrationStep' => $result['registrationStep']
                ]);

                return response()->json([
                    'status' => true,
                    'message' => $result['message'],
                    'registrationStep' => $result['registrationStep']
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
        try {

            $accountId = session('accountId');

            if (!$accountId) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Session expired, silakan login kembali'
                ], 401);
            }

            $response = Http::timeout(15)
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
    if (session()->has('accountId')) {

        switch (session('registrationStep')) {
            case 'otp':
                return redirect()->route('otp');

            case 'uploadKTP':
                return redirect()->route('verifikasi.ktp');

            default:
                return redirect('/dashboard');
        }
    }

    return view('login');
}
}