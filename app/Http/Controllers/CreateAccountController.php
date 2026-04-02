<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Services\OcrNormalizer;
use App\Services\StepRedirectService;

class CreateAccountController extends Controller
{
    public function createPin(Request $request)
    {
        $accountId = session('accountId');
        if (!$accountId) {
            return response()->json([
                "status"=>false,
                "message"=>"AccountId Kosong"
            ]);
        }

        $response = Http::timeout(15)
            ->connectTimeout(5)
            ->retry(1, 200)
            ->post(
            'https://dev.profits.co.id:8283/registration/createPin',
            [
                "accountId" => $accountId,
                "pin" => $request->pin
            ]
        );
        return $response->json();
    }

    public function createAccountType(Request $request)
    {
        $registrationId = session('registrationId');
        if (!$registrationId) {
            return response()->json([
                "status" => false,
                "message" => "registrationId Kosong"
            ]);
        }

        $response = Http::timeout(15)
            ->connectTimeout(5)
            ->retry(1, 200)
            ->post(
            'https://dev.profits.co.id:8283/registration/createAccountType',
            [
                "registrationId" => $registrationId,
                "accountType" => $request->accountType
            ]
        );
        return $response->json();
    }

    public function showPersonal()
    {
        if ($r = StepRedirectService::guardStep()) {
            return redirect($r);
        }
        $step = session('registrationStep');
        $ocr = session('ocr_result');

        if (!$ocr && $step === 'personalInformation') {
            session(['registrationStep' => 'uploadKtp']);
            return redirect()->route('verifikasi.ktp')->with('error', 'Silakan upload ulang KTP');
        }
        $raw = isset($ocr['result']) ? $ocr['result'] : $ocr;
        $ocrData = $ocr
            ? \App\Services\OcrNormalizer::normalize($raw)
            : [];
        // dd($ocrData);

        $personalData = session('personalData', []);    
        $data = array_merge($ocrData, $personalData);

        // merge session override OCR
        $data = array_merge($ocrData, $personalData);

        return view('data-personal', [
            'data' => $data,
            'isUpdate' => !empty($personalData),
            'step' => StepRedirectService::stepNumber($step),
            'hideBack' => StepRedirectService::hideBack()
        ]);
    }

    public function savePersonal(Request $request)
    {
        if (!session()->has('registrationId')) {
            return redirect()->route('verifikasi.ktp');
        }

        if ($request->tanggalLahir) {
            try {
                $request->merge([
                    'tanggalLahir' => \Carbon\Carbon::parse($request->tanggalLahir)->format('Y-m-d')
                ]);
            } catch (\Exception $e) {}
        }

        $personalData = $request->validate([
            'identificationNumber'  => 'required|digits:16',
            'name'                  => 'required',
            'dateOfBirth'           => 'required|date',
            'birthLocation'         => 'required',
            'religion'              => 'required',
            'gender'                => 'required',
            'maritalStatus'         => 'required',
            'address'               => 'required',
            'city'                  => 'required',
            'kelurahan'             => 'required',
            'kecamatan'             => 'required',
            'postalCode'            => 'required',
            'residenceAddress'      => 'required',
            'residenceCity'         => 'required',
            'residenceKelurahan'    => 'required',
            'residenceKecamatan'    => 'required',
            'residencePostalCode'   => 'required',
            'motherMaidenName'      => 'required',
        ]);
        $processType = StepRedirectService::stepNumber(session('registrationStep')) >= StepRedirectService::stepNumber('personalInformation')
            ? 'UPDATE'
            : 'CREATE';

        $payload = [
            "registrationId" => session('registrationId'),
            "step" => "personalInformation",
            "process" => $processType,
            "datas" => $personalData
        ];
        // \Log::info('STEP 3 - PAYLOAD', $payload);

        try {
            $response = \Http::withHeaders([
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ])
            ->timeout(15)
            ->connectTimeout(5)
            ->retry(1, 200)
            ->post(
                'https://dev.profits.co.id:8283/registration/saveRegistration',
                $payload
            );
            $result = $response->json();

            \Log::info('STEP 4 - API RESPONSE', $result);
            if (!empty($result['status']) && $result['status'] === true) {

                \Log::info('STEP 5 - SUCCESS', [
                    'registrationId' => session('registrationId'),
                    'nextStep' => $result['registrationStep'] ?? null
                ]);

                session([
                    'personalData' => $personalData,
                    'registrationStep' => $result['registrationStep']
                ]);
                return redirect()->route('data.penghasilan')->with('success', $result['message'] ?? 'Berhasil');
            }

            return back()->with('error', $result['message'] ?? 'Gagal');

        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi Kesalahan Sistem');
        }
    }

    public function showEmployment()
    {
        if (!session()->has('registrationId')) {
            return redirect()->route('email');
        }

        $employmentData = session('employmentData', []);
        $isUpdate = !empty($employmentData);

        return view('data-pekerjaan', compact('employmentData', 'isUpdate'));
    }

    public function saveEmployment(Request $request)
    {
        if (!session()->has('registrationId')) {
            return redirect()->route('email');
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
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                ])
                ->timeout(15)
                ->connectTimeout(5)
                ->retry(1, 200)
                ->post(
                    'https://dev.profits.co.id:8283/registration/saveRegistration',$payload
                );

            if (!$response->ok()) {
                return back()->withInput()->with([
                    'api_message' => 'Failed Save Employment Information',
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
                'api_message' => 'Internal Server Error',
                'api_status'  => false
            ]);
        }
    }

    public function showFinancial()
    {
        if (!session()->has('registrationId')) {
            return redirect()->route('email');
        }

        $financialData = session('financialData', []);
        $isUpdate = !empty($financialData);

        return view('data-penghasilan', compact('financialData', 'isUpdate'));
    }

    public function saveFinancial(Request $request)
    {
        if (!session()->has('registrationId')) {
            return redirect()->route('email');
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
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                ])
                ->timeout(15)
                ->connectTimeout(5)
                ->retry(1, 200)
                ->post(
                    'https://dev.profits.co.id:8283/registration/saveRegistration',$payload
                );

            if (!$response->ok()) {
                return back()->withInput()->with([
                    'api_message' => 'Failed Save Financal Profile',
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
                'api_message' => 'Internal Server Error',
                'api_status'  => false
            ]);
        }
    }

    public function saveReferensiPerseorangan(Request $request)
    {
        if (!session()->has('registrationId')) {
            return redirect()->route('email');
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
                ->timeout(15)
                ->connectTimeout(5)
                ->retry(1, 200)
                ->post(
                    'https://dev.profits.co.id:8283/registration/saveRegistration',
                    $payload
                );

            if (!$response->ok()) {
                return back()->withInput()->with([
                    'api_message' => 'Failed Save Reference Information',
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
                'api_message' => 'Internal Server Error',
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