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

    private function getRegistration()
    {
        $registrationId = session('registrationId');

        if (!$registrationId) return;

        try {
            $response = \Http::withHeaders([
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ])
            ->timeout(15)
            ->connectTimeout(5)
            ->retry(1, 200)
            ->post('https://dev.profits.co.id:8283/registration/getRegistration', 
            [
                "registrationId" => $registrationId
            ]);
            $result = $response->json();

            if (!($result['status'] ?? false)) return;

            session([
                'registrationStep' => $result['registration']['registrationStep'],

                'personalData'     => $result['personalInformation'] ?? [],
                'financialData'    => $result['financialProfile'] ?? [],
                'employmentData'   => $result['employmentInformation'] ?? [],
                'bankData'         => $result['financialInformation'] ?? [],
            ]);

        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function showPersonal()
    {
        if (!session()->has('registrationId')) {
            return redirect()->route('email');
        }

        $this->getRegistration();

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

        // Change maritalStatus from OCR to API
        if (!empty($personalData['maritalStatus'])) {
            $map = [
                'KAWIN' => 'Menikah',
                'BELUM KAWIN' => 'Belum Menikah',
                'CERAI' => 'Janda',
            ];

            $key = strtoupper($personalData['maritalStatus']);
            $personalData['maritalStatus'] = $map[$key] ?? $personalData['maritalStatus'];
        }

        // Change gender from OCR to API
        if (!empty($personalData['gender'])) {
            $genderMap = [
                'Pria' => '1',
                'Wanita' => '2',
            ];

            $personalData['gender'] = $genderMap[$personalData['gender']] ?? $personalData['gender'];
        }

        // Change religion from OCR to API
        if (!empty($personalData['religion'])) {
            $religionMap = [
                'Islam' => '1',
                'Kristen' => '2',
                'Buddha' => '3',
                'Katolik' => '4',
                'Kong Hu Cu' => '5',
                'Hindu' => '6',
            ];
            $personalData['religion'] = $religionMap[$personalData['religion']] ?? $personalData['religion'];
        }

        // Format birthDate
        if (!empty($personalData['dateOfBirth'])) {
            try {
                $personalData['dateOfBirth'] = \Carbon\Carbon::parse($personalData['dateOfBirth'])->format('Y-m-d');
            } catch (\Exception $e) {}
        }

        // merge session override OCR
        $data = array_merge($ocrData, $personalData);

        return view('data-personal', [
            'data' => $data,
            'isUpdate' => !empty($personalData),
            'step' => StepRedirectService::stepNumber($step),
            'totalStep' => StepRedirectService::totalStep(),
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
            'kelurahan'             => 'required',
            'postalCode'            => 'required',
            'residenceAddress'      => 'required',
            'residenceKelurahan'    => 'required',
            'residencePostalCode'   => 'required',
            'motherMaidenName'      => 'required',
        ]);
        // dd($personalData);
        $processType = StepRedirectService::stepNumber(session('registrationStep')) >= StepRedirectService::stepNumber('personalInformation')
            ? 'UPDATE'
            : 'CREATE';

        $payload = [
            "registrationId" => session('registrationId'),
            "step" => "personalInformation",
            "process" => $processType,
            "datas" => $personalData
        ];
        \Log::info('Step Personal Information - Payload', $payload);

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

            \Log::info('Step Personal Information - API Response', $result);
            if (!empty($result['status']) && $result['status'] === true) {

                \Log::info('Step Personal Information - Success', [
                    'registrationId' => session('registrationId'),
                    'nextStep' => $result['registrationStep'] ?? null
                ]);

                session([
                    'personalData' => $personalData,
                    'registrationStep' => $result['registrationStep']
                ]);
                $nextStep = StepRedirectService::nextStep('personalInformation');

                return redirect()->route(StepRedirectService::STEP_ROUTE[$nextStep])->with('success', $result['message'] ?? 'Berhasil');
            }

            return back()->with('error', $result['message'] ?? 'Gagal');

        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function showFinancial()
    {
        if (!session()->has('registrationId')) {
            return redirect()->route('email');
        }

        $this->getRegistration();

        $step = session('registrationStep');
        $financialData = session('financialData', []);

        return view('data-penghasilan', [
            'financialData' => $financialData,
            'isUpdate' => !empty($financialData),
            'step' => StepRedirectService::stepNumber($step),
            'hideBack' => StepRedirectService::hideBack()
        ]);
    }

    public function saveFinancial(Request $request)
    {
        if (!session()->has('registrationId')) {
            return redirect()->route('email');
        }

        $financialData = $request->validate([
            'employmentType'      => 'required',
            'education'           => 'required',
            'mainIncomeRange'     => 'required',
            'primaryFundSources'  => 'required',
            'investmentObjective' => 'required',
        ]);
        // dd($financialData);
        $processType = StepRedirectService::stepNumber(session('registrationStep')) >= StepRedirectService::stepNumber('financialProfile')
            ? 'UPDATE'
            : 'CREATE';

        $payload = [
            "registrationId" => session('registrationId'),
            "step"           => "financialProfile",
            "process"        => $processType,
            "datas"          => $financialData
        ];
        \Log::info('Step Financial Profile - Payload', $payload);

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

            \Log::info('Step Financial Profile - API Response', $result);
            if (!empty($result['status']) && $result['status'] === true) {

                \Log::info('Step Financial Profile - Success', [
                    'registrationId' => session('registrationId'),
                    'nextStep' => $result['registrationStep'] ?? null
                ]);

                session([
                    'financialData' => $financialData,
                    'registrationStep' => $result['registrationStep']
                ]);
                $nextStep = StepRedirectService::nextStep('financialProfile');

                return redirect()->route(StepRedirectService::STEP_ROUTE[$nextStep])->with('success', $result['message'] ?? 'Berhasil');
            }

            return back()->with('error', $result['message'] ?? 'Gagal');

        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function showRelation()
    {
        if (!session()->has('registrationId')) {
            return redirect()->route('email');
        }

        $this->getRegistration();

        $step = session('registrationStep');
        $relationData = session('relationData', []);

        return view('data-relation', [
            'isUpdate' => !empty($relationData),
            'step' => StepRedirectService::stepNumber($step),
            'totalStep' => StepRedirectService::totalStep(),
            'hideBack' => StepRedirectService::hideBack()
        ]);
    }

    public function showEmployment()
    {
        if (!session()->has('registrationId')) {
            return redirect()->route('email');
        }

        $this->getRegistration();

        $step = session('registrationStep');
        $employmentData = session('employmentData', []);

        return view('data-pekerjaan', [
            'employmentData' => $employmentData,
            'isUpdate' => !empty($employmentData),
            'step' => StepRedirectService::stepNumber($step),
            'totalStep' => StepRedirectService::totalStep(),
            'hideBack' => StepRedirectService::hideBack()
        ]);
    }

    public function saveEmployment(Request $request)
    {
        if (!session()->has('registrationId')) {
            return redirect()->route('email');
        }

        $employmentData = $request->validate([
            'employer'                => 'nullable|string|max:255',
            'employmentPosition'      => 'required',
            'businessLine'            => 'required',
            'employmentDurationYear'  => 'nullable|string',
            'employmentDurationMonth' => 'nullable|string',
            'officeAddress'           => 'nullable|string',
            'officeTelephone'         => 'nullable|string|max:13',
        ]);
        // dd($employmentData);
        $processType = StepRedirectService::stepNumber(session('registrationStep')) >= StepRedirectService::stepNumber('employmentInformation')
            ? 'UPDATE'
            : 'CREATE';

        $payload = [
            "registrationId" => session('registrationId'),
            "step"           => "employmentInformation",
            "process"        => $processType,
            "datas"          => $employmentData
        ];
        \Log::info('Step Employment Information - Payload', $payload);

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

            \Log::info('Step Employment Information - API Response', $result);
            if (!empty($result['status']) && $result['status'] === true) {

                \Log::info('Step Employment Information - Success', [
                    'registrationId' => session('registrationId'),
                    'nextStep' => $result['registrationStep'] ?? null
                ]);

                session([
                    'employmentData' => $employmentData,
                    'registrationStep' => $result['registrationStep']
                ]);
                $nextStep = StepRedirectService::nextStep('employmentInformation');

                return redirect()->route(StepRedirectService::STEP_ROUTE[$nextStep])->with('success', $result['message'] ?? 'Berhasil');
            }

            return back()->with('error', $result['message'] ?? 'Gagal');

        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function showUniversitas()
    {
        if (!session()->has('registrationId')) {
            return redirect()->route('email');
        }

        $this->getRegistration();

        $step = session('registrationStep');
        $universitasData = session('universitasData', []);

        return view('data-universitas', [
            'universitasData' => $universitasData,
            'isUpdate' => !empty($universitasData),
            'step' => StepRedirectService::stepNumber($step),
            'totalStep' => StepRedirectService::totalStep(),
            'hideBack' => StepRedirectService::hideBack()
        ]);
    }

    public function saveUniversitas(Request $request)
    {
        if (!session()->has('registrationId')) {
            return redirect()->route('email');
        }

        $universitasData = $request->validate([
            'employer'                => 'nullable|string|max:255',
            'employmentDurationYear'  => 'required|int',
            'employmentDurationMonth' => 'required|int',
            'officeAddress'           => 'nullable|string',
        ]);
        // dd($universitasData);
        $processType = StepRedirectService::stepNumber(session('registrationStep')) >= StepRedirectService::stepNumber('universityInformation')
            ? 'UPDATE'
            : 'CREATE';

        $payload = [
            "registrationId" => session('registrationId'),
            "step"           => "universityInformation",
            "process"        => $processType,
            "datas"          => $universitasData
        ];
        \Log::info('Step University Information - Payload', $payload);

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

            \Log::info('Step University Information - API Response', $result);
            if (!empty($result['status']) && $result['status'] === true) {

                \Log::info('Step University Information - Success', [
                    'registrationId' => session('registrationId'),
                    'nextStep' => $result['registrationStep'] ?? null
                ]);

                session([
                    'universitasData' => $universitasData,
                    'registrationStep' => $result['registrationStep']
                ]);
                $nextStep = StepRedirectService::nextStep('universityInformation');

                return redirect()->route(StepRedirectService::STEP_ROUTE[$nextStep])->with('success', $result['message'] ?? 'Berhasil');
            }

            return back()->with('error', $result['message'] ?? 'Gagal');

        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function showbank()
    {
        if (!session()->has('registrationId')) {
            return redirect()->route('email');
        }

        $this->getRegistration();

        $step = session('registrationStep');
        $bankData = session('bankData', []);

        return view('data-bank', [
            'bankData' => $bankData,
            'isUpdate' => !empty($bankData),
            'step' => StepRedirectService::stepNumber($step),
            'totalStep' => StepRedirectService::totalStep(),
            'hideBack' => StepRedirectService::hideBack()
        ]);
    }

    public function saveBank(Request $request)
    {
        if (!session()->has('registrationId')) {
            return redirect()->route('email');
        }

        $bankData = $request->validate([
            'bankName'          => 'required',
            'bankAccountOwner'  => 'required',
            'bankAccountNumber' => 'required',
        ]);
        // dd($bankData);
        $processType = StepRedirectService::stepNumber(session('registrationStep')) >= StepRedirectService::stepNumber('financialInformation')
            ? 'UPDATE'
            : 'CREATE';

        $payload = [
            "registrationId" => session('registrationId'),
            "step"           => "financialInformation",
            "process"        => $processType,
            "datas"          => $bankData
        ];
        \Log::info('Step Financial Information - Payload', $payload);

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

            \Log::info('Step Financial Information - API Response', $result);
            if (!empty($result['status']) && $result['status'] === true) {

                \Log::info('Step Financial Information - Success', [
                    'registrationId' => session('registrationId'),
                    'nextStep' => $result['registrationStep'] ?? null
                ]);

                session([
                    'bankData' => $bankData,
                    'registrationStep' => $result['registrationStep']
                ]);
                $nextStep = StepRedirectService::nextStep('financialInformation');

                return redirect()->route(StepRedirectService::STEP_ROUTE[$nextStep])->with('success', $result['message'] ?? 'Berhasil');
            }

            return back()->with('error', $result['message'] ?? 'Gagal');

        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function showSignature()
    {
        if (!session()->has('registrationId')) {
            return redirect()->route('email');
        }

        $step = session('registrationStep');

        return view('data-signature', [
            'step' => StepRedirectService::stepNumber($step),
            'totalStep' => StepRedirectService::totalStep(),
            'hideBack' => StepRedirectService::hideBack()
        ]);
    }

    public function saveSignature(Request $request)
    {
        try {
            if (!session()->has('registrationId')) {
                return redirect()->route('email');
            }

            $request->validate([
                'image' => 'required|string'
            ]);
            $imageBase64 = $request->image;

            $hash = md5($imageBase64);
            $namaFile = 'Signature_' . $hash . '.png';

            $response = \Http::withHeaders([
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ])
            ->post(
                'https://dev.profits.co.id:8283/registration/uploadAttachment',
                [
                    "registrationId" => session('registrationId'),
                    "datas" => [
                        "fileType"  => "signature",
                        "fileName"  => $namaFile,
                        "fileImage" => $imageBase64
                    ]
                ]
            );
            $result = $response->json();

            \Log::info('Upload Signature', $result);
            if (!empty($result['status']) && $result['status'] === true) {

                session([
                    'registrationStep' => $result['registrationStep']
                ]);
                return redirect()->route('data.signature')->with('success', $result['message'] ?? 'Berhasil');
            }

            return back()->with('error', $result['message'] ?? 'Gagal');

        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}