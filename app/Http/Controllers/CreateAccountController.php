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
            ])->post('https://dev.profits.co.id:8283/registration/createAccountNewRegistration',$payload);

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

            // simpan email utk halaman login
            session()->put('register_email', $request->email);
            
            // hapus data step
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

    public function savePersonal(Request $request) {
        $personalData = $request->validate([
            'nama'              => 'required',
            'nik'               => 'required',
            'tempat_lahir'      => 'required',
            'tanggal_lahir'     => 'required',
            'jenis_kelamin'     => 'required',
            'agama'             => 'required',
            'status_perkawinan' => 'required',
            'nama_ibu_kandung'  => 'required',
            'alamat'            => 'required',
            'rt'                => 'required',
            'rw'                => 'required',
            'kota'              => 'required',
            'kelurahan'         => 'required',
            'kecamatan'         => 'required',
        ]);
        // dd($personalData);

        session([
            // 'personal_data' => $request->except('_token')
            'personal_data' => $personalData,
        ]);

        return redirect()->route('data.pekerjaan');
    }

    public function saveEmployment(Request $request) {
        $employmentData = $request->validate([
            'employment'            => 'required',
            'company_name'          => 'required|string|max:255',
            'position'              => 'required',
            'businessline'          => 'required',
            'work_year'             => 'required|numeric|min:0',
            'work_month'            => 'required|numeric|min:0|max:11',
            'office_address'        => 'required|string',
            'office_postal_code'    => 'required|string|max:10',
            'office_phone'          => 'required|string|max:20',
        ]);
        $oldEmployment = session('employment_data.employment');
        if ($oldEmployment && $oldEmployment != $employmentData['employment']) {
            session()->forget([
                'referensi_perseorangan',
                'reference_form_type',
                'profil_resiko',
                'profil_risiko_total',
                'profil_risiko_hasil',
            ]);
        }

        session([
            'employment_data' => $employmentData
        ]);

        return redirect()->route('data.penghasilan');
    }

    public function saveFinancial(Request $request) {
        $finacialData = $request->validate([
            'incomeRange'          => 'required',
            'primaryFund'          => 'required',
            'investmentObjective'  => 'required',
        ]);
        // dd($finacialData);

        session([
            'financial_data' => $finacialData
        ]);

        return redirect()->route('data.referensi.perseorangan');
    }

    public function saveReferensiPerseorangan(Request $request) {
        $formType = session('reference_form_type');

        if (!$formType) {
            return redirect()->route('data.pekerjaan');
        }

        if ($formType === 'spouse') {
            $referensiPerseorangan = $request->validate([
                'referenceRelation'     => 'required',
                'nama_relasi_'          => 'required',
                'nomor_ponsel_relasi'   => 'required',
                'email_relasi'          => 'required',
            ]);
            // dd($referensiPerseorangan);
        } else {
            $referensiPerseorangan = $request->validate([
                'referenceRelation'         => 'required',
                'nama_relasi'               => 'required',
                'nik_relasi'                => 'required',
                'jenis_kelamin_relasi'      => 'required',
                'tempat_lahir_relasi'       => 'required',
                'tanggal_lahir_relasi'      => 'required',
                'status_perkawinan_relasi'  => 'required',
                'alamat_relasi'             => 'required',
                'kota_relasi'               => 'required',
                'kelurahan_relasi'          => 'required',
                'kecamatan_relasi'          => 'required',
            ]);
            // dd($referensiPerseorangan);
        }

        session([
            'referensi_perseorangan' => $referensiPerseorangan
        ]);

        return redirect()->route('data.profil.resiko');
    }

    public function saveProfilResiko(Request $request) {
        $profilResiko = $request->validate([
            'q1'    => 'required',
            'q2'    => 'required',
            'q3'    => 'required',
            'q4'    => 'required',
            'q5'    => 'required',
        ]);
        // dd($profilResiko);

        $total = $request->q1
               + $request->q2
               + $request->q3
               + $request->q4
               + $request->q5;

        if ($total >= 5 && $total <= 8) {
            $profil = 'Konservatif';
        } elseif ($total >= 9 && $total <= 14) {
            $profil = 'Moderat';
        } else {
            $profil = 'Agresif';
        }

        session([
            'profil_resiko'       => $profilResiko,
            'profil_risiko_total' => $total,
            'profil_risiko_hasil' => $profil,
        ]);

        return redirect()->route('profil.resiko.result');
    }

    public function resultProfilResiko() {
        if (!session()->has('profil_risiko_total')) {
            return redirect()->route('data.profil.resiko');
        }

        return redirect()->route('data.bank');
    }
}