<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class CreateAccountController extends Controller
{
    public function saveAccountType(Request $request) {
        try {
            $request->validate([
                'accountType' => 'required|in:REGULAR,SYARIAH'
            ]);

            session()->put('register.account_type', $request->accountType);

            return response()->json([
                'status'  => true,
                'message' => 'Account type saved'
            ]);

        } catch (\Throwable $e) {
            Log::error('saveAccountType ERROR', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine()
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }

    public function saveIdentity(Request $request) {
        try {
            $request->validate([
                'name' => 'required|string|min:3',
                'nik'  => 'required|digits:16'
            ]);

            session()->put('register.name', $request->name);
            session()->put('register.nik', $request->nik);

            return response()->json([
                'status'  => true,
                'message' => 'Identity saved'
            ]);

        } catch (\Throwable $e) {
            Log::error('SaveIdentity ERROR', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine()
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }

    public function createAccount(Request $request) {
        try {
            $request->validate([
                'username'    => 'required|string|min:7|max:15',
                'email'       => 'required|email',
                'mobilePhone' => 'required|string|min:10',
            ]);

            // Pull session saved in steps 1 & 2
            $session = session()->get('register');

            if (!$session) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Session expired'
                ], 422);
            }

            $payload = [
                'nik'         => $session['nik'],
                'name'        => $session['name'],
                'username'    => $request->username,
                'email'       => $request->email,
                'mobilePhone' => $request->mobilePhone,
                'accountType' => $session['account_type']
            ];

            Log::info("CREATE ACCOUNT — PAYLOAD", $payload);

            $apiResponse = Http::withHeaders([
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ])->post(
                'https://dev.profits.co.id:8283/registration/createAccountNewRegistration',
                $payload
            );

            if (!$apiResponse->successful()) {
                Log::error('API ERROR', [
                    'status' => $apiResponse->status(),
                    'body'   => $apiResponse->body()
                ]);

                return response()->json([
                    'status'  => false,
                    'message' => 'API gagal memproses pendaftaran'
                ], 500);
            }
            $result = $apiResponse->json();

            session()->forget('register');
            return response()->json([
                'status'  => true,
                'message' => 'Account created successfully',
                'data'    => $result
            ]);

        } catch (\Throwable $e) {
            Log::error('CreateAccount ERROR', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine()
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }
}