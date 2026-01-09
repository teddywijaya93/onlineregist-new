<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    /* =======================
        * STEP 1
    * ======================= */
    public function index()
    {
        return view('register');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'kode_id'       => 'nullable|string|max:50',
            'kode_referral' => 'nullable|string|max:50',
        ]);

        // $refcoExist = $request->filled('kode_referral') ? 1 : 0;

        session([
            'aoCode'      => $request->kode_referral ?? '',
            'kode_id'     => $request->kode_id ?? '',
            'step_passed' => 'CODE',
        ]);

        return redirect('/Customer-Type');
    }

    /* =======================
        * STEP 2 - TIPE AKUN
    * ======================= */
    public function customerType()
    {
        if (!session()->has('aoCode')) {
            return redirect('/');
        }

        return view('customer_type');
    }

    public function selectCustomerType(Request $request)
    {
        // JANGAN cek step_passed CODE lagi
        if (!session()->has('aoCode')) {
            return redirect('/');
        }

        $request->validate([
            'accountType' => 'required|in:REGULAR,SYARIAH,DERIVATIF'
        ]);

        $accountType = $request->accountType;
        $aoCode      = session('aoCode', '');

        // simpan step
        session([
            'accountType' => $accountType,
            'step_passed' => 'EMAIL'
        ]);

        // LANGSUNG KE EMAIL
        return redirect()->to('/Email?' . http_build_query([
            '_token'      => csrf_token(),
            'aoCode'      => $aoCode,
            'accountType' => $accountType
        ]));
    }

    /* =====================
        * STEP 3 - EMAIL + TOKEN
    * ===================== */
    public function email(Request $request)
    {
        // cukup cek accountType ada
        if (!session()->has('accountType')) {
            return redirect('/');
        }

        return view('email', [
            'token'       => $request->query('_token'),
            'aoCode'      => $request->query('aoCode', ''),
            'accountType' => $request->query('accountType'),
        ]);
    }

    public function submitEmail(Request $request)
    {
        if (!session()->has('accountType')) {
            return redirect('/');
        }

        $request->validate([
            'email' => 'required|email'
        ]);

        // generate OTP dummy
        $otp = rand(100000, 999999);

        session([
            'email'        => $request->email,
            'otp_code'     => $otp,
            'otp_expired'  => now()->addMinutes(1)->timestamp,
            'step_passed'  => 'OTP'
        ]);

        // simulasi kirim email (log saja)
        logger("OTP for {$request->email}: {$otp}");

        return redirect('/OTP');
    }

    /**
        * STEP 4 - OTP PAGE
    */
    public function otp()
    {
        if (session('step_passed') !== 'OTP') {
            return redirect('/');
        }

        return view('otp', [
            'email' => session('email')
        ]);
    }

    /**
     * STEP 4 - VERIFY OTP
     */
    public function verifyOtp(Request $request)
    {
        if (session('step_passed') !== 'OTP') {
            return redirect('/');
        }

        $request->validate([
            'otp' => 'required|digits:6'
        ]);

        // if (
        //     $request->otp != session('otp_code') ||
        //     time() > session('otp_expired')
        // ) {
        //     return back()->withErrors(['otp' => 'Kode OTP tidak valid atau expired']);
        // }

        session([
            'step_passed' => 'OTP_VERIFIED'
        ]);

        return redirect('/verifikasi_ktp');
    }
}
