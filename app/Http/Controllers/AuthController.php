<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\StepRedirectService;

class AuthController extends Controller
{
    public function checkEmail(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email'
            ]);

            $response = Http::timeout(config('api.timeout'))
            ->connectTimeout(config('api.connect_timeout'))
            ->retry(
                config('api.retry'),
                config('api.retry_sleep')
            )
            ->post(config('api.checkEmail'),
            [
                "email" => $request->email
            ]);

            if (!$response->ok()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal Kirim Email'
                ]);
            }
            $data = $response->json();
            $data['redirect'] = null;

            if (($data['registrationStatus'] ?? null) === 'NEW') {
                $redirect = StepRedirectService::routeByStep($data['registrationStep']);
                if ($redirect) {
                    $data['redirect'] = $redirect;
                }
            }

            session([
                'accountId'          => $data['accountId'] ?? null,
                'registrationStatus' => $data['registrationStatus'] ?? null,
                'registrationStep'   => $data['registrationStep'] ?? null,
                'registrationId'     => $data['registrationId'] ?? null,
            ]);

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

            $response = Http::timeout(config('api.timeout'))
            ->connectTimeout(config('api.connect_timeout'))
            ->retry(
                config('api.retry'),
                config('api.retry_sleep')
            )
            ->post(config('api.sendOtpMail'),
            [
                "email" => $request->email
            ]);

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
            $response = Http::timeout(config('api.timeout'))
            ->connectTimeout(config('api.connect_timeout'))
            ->retry(
                config('api.retry'),
                config('api.retry_sleep')
            )
            ->post(config('api.verificationOtp'),
            [
                "type"  => "EMAIL",
                "value" => $request->email,
                "otp"   => (int)$request->otp
            ]);

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

    public function savePhone(Request $request)
    {
        $request->validate([
            'phone' => 'required'
        ]);

        session([
            'reg_phone' => $request->phone
        ]);

        return response()->json([
            "status" => true
        ]);
    }

    public function verifyOtpMobile(Request $request)
    {
        if (!session()->has('reg_phone')) {
            return response()->json([
                "status" => false,
                "message" => "Phone tidak ada"
            ]);
        }

        return response()->json([
            "status" => true
        ]);
    }

    public function createAccount(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required',
                'password' => 'required'
            ]);
            $email = session('reg_email');
            $phone = session('reg_phone');
            $referral = session('referral');

            if (!$email || !$phone) {
                return response()->json([
                    "status" => false,
                    "message" => "Session tidak lengkap"
                ]);
            }

            $payload = [
                "username" => $request->username,
                "password" => $request->password,
                "email"    => $email,
                "phone"    => $phone,
            ];

            if ($referral) {
                if (!empty($referral['aoCode'])) {
                    $payload['aoCode'] = $referral['aoCode'];
                }

                if (!empty($referral['rdnBank'])) {
                    $payload['rdnBankName'] = $referral['rdnBank'];
                }
            }
            Log::info('FINAL PAYLOAD', $payload);

            $response = Http::timeout(config('api.timeout'))
            ->connectTimeout(config('api.connect_timeout'))
            ->retry(
                config('api.retry'),
                config('api.retry_sleep')
            )
            ->post(config('api.createAccount'), $payload);

            Log::info('CREATE ACCOUNT RESPONSE', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            $data = $response->json();
            if ($data['status']) {
                session([
                    'accountId' => $data['accountId'],
                    'registrationId' => $data['registrationId'],
                    'registrationStep' => $data['registrationStep']
                ]);
                session()->forget('referral');
            }
            return response()->json($data);

        } catch (\Throwable $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }
}