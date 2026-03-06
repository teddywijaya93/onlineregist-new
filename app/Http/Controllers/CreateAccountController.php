<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Services\OcrNormalizer;

class CreateAccountController extends Controller
{
    public function saveAccountType(Request $request) 
    {
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

    public function saveIdentity(Request $request) 
    {
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

    public function createAccount(Request $request) 
    {
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
        $ocrData = \App\Services\OcrNormalizer::normalize($raw);
        // dd($ocrData);
        
        $personalData = session('personalData', []);    
        $isUpdate = !empty($personalData);

        // merge session override OCR
        $data = array_merge($ocrData, $personalData);

        return view('data-personal', compact('data','isUpdate'));
    }

    public function savePersonal(Request $request)
    {
        if (!session()->has('registrationId')) {
            return back()->with('error', 'Session registrasi tidak ada');
        }

        if ($request->tanggalLahir) {
            try {
                $request->merge([
                    'tanggalLahir' => Carbon::parse($request->tanggalLahir)->format('Y-m-d')
                ]);
            } catch (\Exception $e) {}
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
            'process_type' => 'required|in:CREATE,UPDATE'
        ]);
        // dd($personalData);
        $processType = $personalData['process_type'];
        unset($personalData['process_type']);

        $rt = str_pad($personalData['rt'], 3, '0', STR_PAD_LEFT);
        $rw = str_pad($personalData['rw'], 3, '0', STR_PAD_LEFT);

        $datas = collect($personalData)->except(['rt','rw'])->toArray();
        $datas['rt_rw'] = "$rt/$rw";
        $payload = [
            "registrationId" => session('registrationId'),
            "step" => "personalInformation",
            "process" => $processType,
            "datas" => $datas
        ];

        try {
            Log::info('Personal Payload', $payload);
            $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . config('services.profits.token'),
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                ])
                ->withoutVerifying()
                ->timeout(15)
                ->connectTimeout(5)
                ->retry(1, 200)
                ->post(
                    'https://dev.profits.co.id:8283/registration/saveRegistration',
                    $payload
                );

            if (!$response->ok()) {
                return back()->withInput()->with([
                    'api_message' => 'Server API tidak merespon',
                    'api_status'  => false
                ]);
            }

            $result   = $response->json();
            $status   = $result['status'] ?? false;
            $message  = $result['message'] ?? '';
            $nextStep = $result['registrationStep'] ?? null;
            Log::info('Personal API Response', $result);

            if ($status) {
                session([
                    'personalData' => $personalData,
                    'registrationStep' => $nextStep,
                ]);

                if ($nextStep === 'employmentInformation') {
                    return redirect()->route('data.pekerjaan')
                        ->with([
                            'api_message' => $message,
                            'api_status'  => true
                        ]);
                }

                return redirect()->route('data.personal')
                    ->with([
                        'api_message' => $message,
                        'api_status'  => true
                    ]);
            }

            return back()->withInput()->with([
                'api_message' => $message,
                'api_status'  => false
            ]);

        } catch (\Throwable $e) {
            return back()->withInput()->with([
                'api_message' => 'Server tidak dapat dihubungi',
                'api_status'  => false
            ]);
        }
    }

    public function showEmployment()
    {
        if (!session()->has('registrationId')) {
            return redirect()->route('login');
        }

        $employmentData = session('employmentData', []);
        $isUpdate = !empty($employmentData);

        return view('data-pekerjaan', compact('employmentData', 'isUpdate'));
    }

    public function saveEmployment(Request $request)
    {
        if (!session()->has('registrationId')) {
            return redirect()->route('login');
        }

        $employmentData = $request->validate([
            'employmentType'          => 'required',
            'employer'                => 'nullable|string|max:255',
            'employmentPosition'      => 'required',
            'businessLine'            => 'required',
            'employmentDurationYear'  => 'nullable|string',
            'employmentDurationMonth' => 'nullable|string',
            'officeAddress'           => 'nullable|string',
            'officePostalCode'        => 'nullable|string|max:5',
            'officeTelephone'         => 'nullable|string|max:13',
            'process_type'            => 'required|in:CREATE,UPDATE',
        ]);
        // dd($employmentData);
        $processType = $employmentData['process_type'];
        unset($employmentData['process_type']);

        $payload = [
            "registrationId" => session('registrationId'),
            "step"           => "employmentInformation",
            "process"        => $processType,
            "datas"          => $employmentData
        ];

        try {
            Log::info('Employment Payload', $payload);
            $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . config('services.profits.token'),
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                ])
                ->withoutVerifying()
                ->timeout(15)
                ->connectTimeout(5)
                ->retry(1, 200)
                ->post(
                    'https://dev.profits.co.id:8283/registration/saveRegistration',
                    $payload
                );

            if (!$response->ok()) {
                return back()->withInput()->with([
                    'api_message' => 'Server API tidak merespon',
                    'api_status'  => false
                ]);
            }

            $result  = $response->json();
            $status  = $result['status'] ?? false;
            $message = $result['message'] ?? '';
            $nextStep = $result['registrationStep'] ?? null;
            Log::info('Employment API Response', $result);

            if ($status) {
                session([
                    'employmentData' => $employmentData,
                    'registrationStep' => $nextStep,
                ]);

                if ($nextStep === 'financialProfile') {
                    return redirect()->route('data.penghasilan')
                        ->with([
                            'api_message' => $message,
                            'api_status'  => true
                        ]);
                }

                return redirect()->route('data.pekerjaan')
                    ->with([
                        'api_message' => $message,
                        'api_status'  => true
                    ]);
            }

            return back()->withInput()->with([
                'api_message' => $message,
                'api_status'  => false
            ]);
        } catch (\Throwable $e) {
            return back()->withInput()->with([
                'api_message' => 'Server tidak dapat dihubungi',
                'api_status'  => false
            ]);
        }
    }

    public function showFinancial()
    {
        if (!session()->has('registrationId')) {
            return redirect()->route('login');
        }

        $financialData = session('financialData', []);
        $isUpdate = !empty($financialData);

        return view('data-penghasilan', compact('financialData', 'isUpdate'));
    }

    public function saveFinancial(Request $request)
    {
        if (!session()->has('registrationId')) {
            return redirect()->route('login');
        }

        $financialData = $request->validate([
            'mainIncomeRange'     => 'required',
            'primaryFundSources'  => 'required',
            'investmentObjective' => 'required',
            'process_type'        => 'required|in:CREATE,UPDATE',
        ]);
        // dd($financialData);
        $processType = $financialData['process_type'];
        unset($financialData['process_type']);

        $payload = [
            "registrationId" => session('registrationId'),
            "step"           => "financialProfile",
            "process"        => $processType,
            "datas"          => $financialData
        ];

        try {
            Log::info('Financial Payload', $payload);
            $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . config('services.profits.token'),
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                ])
                ->withoutVerifying()
                ->timeout(15)
                ->connectTimeout(5)
                ->retry(1, 200)
                ->post(
                    'https://dev.profits.co.id:8283/registration/saveRegistration',
                    $payload
                );

            if (!$response->ok()) {
                return back()->withInput()->with([
                    'api_message' => 'Server API tidak merespon',
                    'api_status'  => false
                ]);
            }

            $result  = $response->json();
            $status  = $result['status'] ?? false;
            $message = $result['message'] ?? '';
            $nextStep = $result['registrationStep'] ?? null;
            Log::info('Financial API Response', $result);

            if ($status) {
                session([
                    'financialData' => $financialData,
                    'registrationStep' => $nextStep,
                ]);

                if ($nextStep === 'relation') {
                    return redirect()->route('data.referensi.perseorangan')
                        ->with([
                            'api_message' => $message,
                            'api_status'  => true
                        ]);
                }

                return redirect()->route('data.penghasilan')
                    ->with([
                        'api_message' => $message,
                        'api_status'  => true
                    ]);
            }

            return back()->withInput()->with([
                'api_message' => $message,
                'api_status'  => false
            ]);
        } catch (\Throwable $e) {
            return back()->withInput()->with([
                'api_message' => 'Server tidak dapat dihubungi',
                'api_status'  => false
            ]);
        }
    }

    public function saveReferensiPerseorangan(Request $request)
    {
        if (!session()->has('registrationId')) {
            return redirect()->route('login');
        }

        $formType = session('reference_form_type');

        if (!$formType) {
            return redirect()->route('data.pekerjaan');
        }

        if ($formType === 'spouse') {
            $validated = $request->validate([
                'referenceRelation'   => 'required',
                'nama_relasi'         => 'required',
                'nomor_ponsel_relasi' => 'required',
                'email_relasi'        => 'required',
                'process_type'        => 'required|in:CREATE,UPDATE',
            ]);

            $datas = [
                "referenceRelation" => $validated['referenceRelation'],
                "name"              => $validated['nama_relasi'],
                "mobilePhone"       => $validated['nomor_ponsel_relasi'],
                "email"             => $validated['email_relasi'],
            ];
        } else {
            $validated = $request->validate([
                'referenceRelation'        => 'required',
                'nama_relasi'              => 'required',
                'nik_relasi'               => 'required',
                'jenis_kelamin_relasi'     => 'required',
                'tempat_lahir_relasi'      => 'required',
                'tanggal_lahir_relasi'     => 'required',
                'status_perkawinan_relasi' => 'required',
                'alamat_relasi'            => 'required',
                'kota_relasi'              => 'required',
                'kelurahan_relasi'         => 'required',
                'kecamatan_relasi'         => 'required',
                'process_type'             => 'required|in:CREATE,UPDATE',
            ]);

            $datas = [
                "referenceRelation" => $validated['referenceRelation'],
                "name"              => $validated['nama_relasi'],
                "nik"               => $validated['nik_relasi'],
                "gender"            => $validated['jenis_kelamin_relasi'],
                "birthPlace"        => $validated['tempat_lahir_relasi'],
                "birthDate"         => $validated['tanggal_lahir_relasi'],
                "maritalStatus"     => $validated['status_perkawinan_relasi'],
                "address"           => $validated['alamat_relasi'],
                "city"              => $validated['kota_relasi'],
                "subDistrict"       => $validated['kecamatan_relasi'],
                "village"           => $validated['kelurahan_relasi'],
            ];
        }

        $payload = [
            "registrationId" => session('registrationId'),
            "step"           => "relation",
            "process"        => $validated['process_type'],
            "datas"          => $datas
        ];

        try {
            $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . config('services.profits.token'),
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                ])
                ->withoutVerifying()
                ->timeout(15)
                ->connectTimeout(5)
                ->retry(1, 200)
                ->post(
                    'https://dev.profits.co.id:8283/registration/saveRegistration',
                    $payload
                );

            if (!$response->ok()) {
                return back()->withInput()->with([
                    'api_message' => 'Server API tidak merespon',
                    'api_status'  => false
                ]);
            }

            $result  = $response->json();
            $status  = $result['status'] ?? false;
            $message = $result['message'] ?? '';
            $nextStep = $result['registrationStep'] ?? null;

            if ($status) {
                session(['referensiData' => $datas]);

                if ($nextStep === 'riskProfile') {
                    return redirect()->route('data.profil.resiko')
                        ->with([
                            'api_message' => $message,
                            'api_status'  => true
                        ]);
                }

                return redirect()->route('data.referensi.perseorangan')
                    ->with([
                        'api_message' => $message,
                        'api_status'  => true
                    ]);
            }

            return back()->withInput()->with([
                'api_message' => $message,
                'api_status'  => false
            ]);
        } catch (\Throwable $e) {
            return back()->withInput()->with([
                'api_message' => 'Server tidak dapat dihubungi',
                'api_status'  => false
            ]);
        }
    }

    // public function saveProfilResiko(Request $request) {
    //     $profilResiko = $request->validate([
    //         'q1'    => 'required',
    //         'q2'    => 'required',
    //         'q3'    => 'required',
    //         'q4'    => 'required',
    //         'q5'    => 'required',
    //     ]);

    //     $total = $request->q1
    //            + $request->q2
    //            + $request->q3
    //            + $request->q4
    //            + $request->q5;

    //     if ($total >= 5 && $total <= 8) {
    //         $profil = 'Konservatif';
    //     } elseif ($total >= 9 && $total <= 14) {
    //         $profil = 'Moderat';
    //     } else {
    //         $profil = 'Agresif';
    //     }

    //     session([
    //         'profil_resiko'       => $profilResiko,
    //         'profil_risiko_total' => $total,
    //         'profil_risiko_hasil' => $profil,
    //     ]);

    //     return redirect()->route('profil.resiko.result');
    // }

    // public function resultProfilResiko() {
    //     if (!session()->has('profil_risiko_total')) {
    //         return redirect()->route('data.profil.resiko');
    //     }

    //     return redirect()->route('data.bank');
    // }
}