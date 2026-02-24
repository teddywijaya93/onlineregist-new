<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Services\OcrNormalizer;

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
            session()->put('register_phone', $request->mobilePhone);
            
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

    public function showPersonal()
    {
        $ocr = session('ocr_result');
        if (!$ocr) {
            return redirect()
            ->route('verifikasi.ktp')
            ->with('api_message','Silakan upload KTP terlebih dahulu');
        }
        $raw = isset($ocr['result']) ? $ocr['result'] : $ocr;
        $data = \App\Services\OcrNormalizer::normalize($raw);

        return view('data-personal', compact('data'));
    }

    public function savePersonal(Request $request)
    {
        if (!session()->has('registrationId')) {
            return back()->with('error', 'Session registrasi tidak ada');
        }

        $personalData = $request->validate([
            'nama' => 'required',
            'nik' => 'required|digits:16',
            'tanggalLahir' => 'required|date|after:1900-01-01',
            'tempatLahir' => 'required',
            'agama' => 'required',
            'jenisKelamin' => 'required',
            'statusPerkawinan' => 'required',
            'alamat' => 'required',
            'rt' => 'required',
            'rw' => 'required',
            'kota' => 'required',
            'kelurahan' => 'required',
            'kecamatan' => 'required',
            'education' => 'required',
            'motherMaidenName' => 'required',
            'residenceAddress' => 'required',
            'residenceRT' => 'required',
            'residenceRW' => 'required',
            'residenceCity' => 'required',
            'residenceKelurahan' => 'required',
            'residenceKecamatan' => 'required',
        ]);
        dd($personalData);

        $rt = str_pad($personalData['rt'], 3, '0', STR_PAD_LEFT);
        $rw = str_pad($personalData['rw'], 3, '0', STR_PAD_LEFT);

        $datas = collect($personalData)->except(['rt','rw'])->toArray();
        $datas['rt_rw'] = "$rt/$rw";

        // simpan session untuk refill form
        session(['personal_data' => $personalData]);

        $payload = [
            "registrationId" => session('registrationId'),
            "step" => "personalInformation",
            "process" => "CREATE",
            "datas" => $datas
        ];

        try {
            // \Log::info('Personal Payload', $payload);
            $response = Http::timeout(15)
                ->connectTimeout(5)
                ->retry(1, 200)
                ->post('https://dev.profits.co.id:8283/registration/saveRegistration',$payload
            );
            $result = $response->json();

            // \Log::info('Personal API Response', $result);
            $message = $result['message'] ?? 'No message';
            $status  = $result['status'] ?? false;

            if ($status || str_contains($message, 'sudah dibuat')) {
                return redirect()
                ->route('data.pekerjaan')
                ->with('api_message', $result['message'] ?? 'Berhasil');
            }
            return back()
            ->withInput()
            ->with('api_message', $result['message'] ?? 'Gagal Menyimpan Data');

        } catch (\Throwable $e) {
            \Log::error('Personal API Exception', [
                'error' => $e->getMessage(),
            ]);

            return back()
            ->withInput()
            ->with('api_message', 'Server Tidak Dapat Dihubungi');
        }
    }

    public function saveEmployment(Request $request)
    {
        if (!session()->has('registrationId')) {
            return back()->with('error', 'Session registrasi tidak ada');
        }

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
        dd($employmentData);

        // simpan session untuk refill form
        session(['employment_data' => $employmentData]);

        $payload = [
            "registrationId" => session('registrationId'),
            "step" => "employmentInformation",
            "process" => "CREATE",
            "datas" => [
                "employer" => $employmentData['company_name'],
                "businessLine" => $employmentData['businessline'],
                "employmentType" => $employmentData['employment'],
                "employmentPosition" => $employmentData['position'],
                "employmentDurationYear" => (int) $employmentData['work_year'],
                "employmentDurationMonth" => (int) $employmentData['work_month'],
                "officeAddress" => $employmentData['office_address'],
                "officePostalCode" => $employmentData['office_postal_code'],
                "officeTelephone" => $employmentData['office_phone'],
            ]
        ];

        try {
            \Log::info('Employment Payload', $payload);

            $response = Http::timeout(15)
                ->connectTimeout(5)
                ->retry(1, 200)
                ->post('https://dev.profits.co.id:8283/registration/saveRegistration',$payload
            );
            $result = $response->json();

            \Log::info('Employment API Response', $result);

            $message = strtolower($result['message'] ?? '');
            $status  = $result['status'] ?? false;

            if ($status || str_contains($message, 'sudah dibuat')) {
                return redirect()
                ->route('data.penghasilan')
                ->with('api_message', $result['message'] ?? 'Berhasil');
            }
            return back()
            ->withInput()
            ->with('api_message', $result['message'] ?? 'Gagal Menyimpan');

        } catch (\Throwable $e) {
            \Log::error('Employment API Exception', [
                'error' => $e->getMessage()
            ]);

            return back()
            ->withInput()
            ->with('api_message', 'Server Tidak Dapat Dihubungi');
        }
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