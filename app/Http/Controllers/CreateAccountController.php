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

        $data = OcrNormalizer::normalize($ocr['result'] ?? []);

        $showMaritalWarning = OcrNormalizer::isCerai(
            $ocr['result']['status_perkawinan'] ?? ''
        );

        return view('data-personal', compact('data','showMaritalWarning'));
    }

   public function savePersonal(Request $request)
{
    $validated = $request->validate([
        'nama' => 'required',
        'nik' => 'required',
        'tempatLahir' => 'required',
        'tanggalLahir' => 'required',
        'jenisKelamin' => 'required',
        'agama' => 'required',
        'education' => 'required',
        'statusPerkawinan' => 'required',
        'motherMaidenName' => 'required',
        'alamat' => 'required',
        'rt' => 'required',
        'rw' => 'required',
        'kota' => 'required',
        'kelurahan' => 'required',
        'kecamatan' => 'required',
    ]);

    $rt_rw = $validated['rt'].'/'.$validated['rw'];

    $payload = [
        "registrationId" => session('registrationId'),
        "step" => "personalInformation",
        "process" => "CREATE",
        "datas" => [
            "nik" => $validated['nik'],
            "nama" => $validated['nama'],
            "tanggalLahir" => $validated['tanggalLahir'],
            "tempatLahir" => $validated['tempatLahir'],
            "agama" => $validated['agama'],
            "jenisKelamin" => $validated['jenisKelamin'],
            "statusPerkawinan" => $validated['statusPerkawinan'],
            "alamat" => $validated['alamat'],
            "rt_rw" => $rt_rw,
            "kelurahan" => $validated['kelurahan'],
            "kecamatan" => $validated['kecamatan'],
            "kota" => $validated['kota'],
            "education" => $validated['education'],
            "motherMaidenName" => $validated['motherMaidenName'],
        ]
    ];

    try {

        $url = 'https://dev.profits.co.id:8283/registration/saveRegistration';

        Log::info('Calling API', [
            'url' => $url,
            'payload' => $payload
        ]);

        $response = Http::timeout(30)->post($url, $payload);

        if (!$response->successful()) {
            Log::error('HTTP error', ['body' => $response->body()]);
            return back()->withErrors('HTTP error dari server');
        }

        $result = $response->json();

        Log::info('API response', $result);

        if (!($result['status'] ?? false)) {
            return back()->withErrors($result['message'] ?? 'API gagal');
        }

        return redirect()->route('data.pekerjaan');

    } catch (\Throwable $e) {

        Log::error('API exception', [
            'message' => $e->getMessage()
        ]);

        return back()->withErrors('Server tidak dapat dihubungi');
    }
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
                // 'profil_resiko',
                // 'profil_risiko_total',
                // 'profil_risiko_hasil',
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