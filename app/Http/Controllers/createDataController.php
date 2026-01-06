<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Services\SessionDataValidator;

class CreateDataController extends Controller
{
    public function createData()
    {
        $client = new Client();
        $headers = ['Content-Type' => 'application/json'];
        Log::info('Session Data: ', Session::all());

        // Prepare the request body
        $body = [
            'deviceReferenceId' => Session::get('deviceReferenceId'),
            'devicePlatform' => Session::get('devicePlatform'),
            'accountType' => Session::get('accountType'),
            'currentStep' => Session::get('currentStep'),
            'name' => Session::get('name'),
            'identificationNumber' => Session::get('identificationNumber'),
            'birthLocation' => Session::get('birthLocation'),
            'dateOfBirth' => Session::get('dateOfBirth'),
            'email' => Session::get('email'),
            'mobilePhone' => Session::get('mobilePhone'),
            'gender' => Session::get('gender'),
            'maritalStatus' => Session::get('maritalStatus'),
            'separateAssetAgreement' => Session::get('separateAssetAgreement'),
            'numberOfdependents' => Session::get('numberOfdependents'),
            'religion' => Session::get('religion'),
            'education' => Session::get('education'),
            'motherMaidenName' => Session::get('motherMaidenName'),
            'address' => Session::get('address'),
            'postalCode' => Session::get('postalCode'),
            'kelurahan' => Session::get('kelurahan'),
            'residenceAddress' => Session::get('residenceAddress'),
            'residencePostalCode' => Session::get('residencePostalCode'),
            'residenceKelurahan' => Session::get('residenceKelurahan'),
            'totalIncome' => Session::get('totalIncome'),
            'primaryFundSources' => Session::get('primaryFundSources'),
            'investmentObjective' => Session::get('investmentObjective'),
            'bankAccountName' => Session::get('bankAccountName'),
            'bankName' => Session::get('bankName'),
            'bankAccountNumber' => Session::get('bankAccountNumber'),
            'employmentType' => Session::get('employmentType'),
            'employerName' => Session::get('employerName'),
            'employmentPosition' => Session::get('employmentPosition'),
            'employmentDurationYear' => Session::get('employmentDurationYear'),
            'employmentDurationMonth' => Session::get('employmentDurationMonth'),
            'businessLine' => Session::get('businessLine'),
            'officePhone' => Session::get('officePhone'),
            'officeAddress' => Session::get('officeAddress'),
            'officePostalCode' => Session::get('officePostalCode'),
            'beneficiaryOwnerRelationship' => Session::get('beneficiaryOwnerRelationship'),
            'beneficiaryOwnerName' => Session::get('beneficiaryOwnerName'),
            'beneficiaryOwnerIdentificationNumber' => Session::get('beneficiaryOwnerIdentificationNumber'),
            'beneficiaryOwnerEmployerName' => Session::get('beneficiaryOwnerEmployerName'),
            'beneficiaryOwnerAddress' => Session::get('beneficiaryOwnerAddress'),
            'beneficiaryOwnerPostalCode' => Session::get('beneficiaryOwnerPostalCode'),
            'beneficiaryOwnerKelurahan' => Session::get('beneficiaryOwnerKelurahan'),
            'beneficiaryOwnerBirthLocation' => Session::get('beneficiaryOwnerBirthLocation'),
            'beneficiaryOwnerDateOfBirth' => Session::get('beneficiaryOwnerDateOfBirth'),
            'beneficiaryOwnerEmail' => Session::get('beneficiaryOwnerEmail'),
            'beneficiaryOwnerMobilePhone' => Session::get('beneficiaryOwnerMobilePhone'),
            'beneficiaryOwnerGender' => Session::get('beneficiaryOwnerGender'),
            'beneficiaryOwnerMaritalStatus' => Session::get('beneficiaryOwnerMaritalStatus'),
            'beneficiaryOwnerEmploymentType' => Session::get('beneficiaryOwnerEmploymentType'),
            'beneficiaryOwnerBusinessLine' => Session::get('beneficiaryOwnerBusinessLine'),
            'beneficiaryOwnerEmploymentBussinessLineId' => Session::get('beneficiaryOwnerEmploymentBussinessLineId'),
            'beneficiaryOwnerOfficeAddress' => Session::get('beneficiaryOwnerOfficeAddress'),
            'beneficiaryOwnerTotalIncome' => Session::get('beneficiaryOwnerTotalIncome'),
            'beneficiaryOwnerPrimaryFundSources' => Session::get('beneficiaryOwnerPrimaryFundSources'),
            'deviceUniqueId' => Session::get('deviceUniqueId'),
            'accountOfficerCode' => Session::get('accountOfficerCode'),
            'linkCode' => Session::get('linkCode'),
            'personCode' => Session::get('personCode'),
            'referrerIDLinkCode' => Session::get('eventRefId'),
            'eventRefName' => Session::get('eventRefName'),
            'personRefId' => Session::get('personRefId'),
            'personRefName' => Session::get('personRefName'),
            'aoName' => Session::get('aoName'),
            
        ];
        // dd($body);

        // Setup validator dan label
        $validator = new SessionDataValidator();
        $labelMap  = SessionDataValidator::defaultLabelMap();

        $employmentType = (int) ($body['employmentType'] ?? 0);
        $accountType = strtoupper(trim($body['accountType'] ?? ''));

        $requiredKeys = [
            'devicePlatform','accountType','name',
            'identificationNumber','birthLocation','dateOfBirth','email',
            'mobilePhone','gender','address','postalCode','kelurahan',
            'bankAccountName','bankName','bankAccountNumber',
        ];

        $employmentFields = [
            'employmentDurationYear','employmentDurationMonth','employerName',
            'officeAddress','officePostalCode','officePhone',
        ];

        $beneficialOwnerFields = [
            'beneficiaryOwnerRelationship','beneficiaryOwnerName','beneficiaryOwnerIdentificationNumber',
            'beneficiaryOwnerEmployerName','beneficiaryOwnerAddress','beneficiaryOwnerPostalCode',
            'beneficiaryOwnerKelurahan','beneficiaryOwnerBirthLocation','beneficiaryOwnerDateOfBirth',
            'beneficiaryOwnerEmail','beneficiaryOwnerMobilePhone','beneficiaryOwnerGender',
            'beneficiaryOwnerMaritalStatus','beneficiaryOwnerEmploymentType','beneficiaryOwnerBusinessLine',
            'beneficiaryOwnerEmploymentBussinessLineId','beneficiaryOwnerOfficeAddress',
            'beneficiaryOwnerTotalIncome','beneficiaryOwnerPrimaryFundSources',
        ];

        if ($employmentType !== 4) {
            $requiredKeys = array_merge($requiredKeys, $employmentFields);
        }

        // Jika jenis pekerjaan memerlukan beneficial owner,
        if (in_array($employmentType, [4, 15, 27], true) && $accountType !== 'SSF') {
            $requiredKeys = array_merge($requiredKeys, $beneficialOwnerFields);
        }

        $nullFields = $validator->checkNullFields($body, $labelMap, $requiredKeys);

        if (!empty($nullFields)) {
            $fieldsText = implode(', ', $nullFields);
            $errorMessage = "Data '$fieldsText' belum terisi. Silakan lengkapi terlebih dahulu data tersebut.";
            Log::warning('[createData] Data kosong: ' . $errorMessage);
            return back()->with('error', $errorMessage);
        }

        // Validate endpoint
        $createNewEndpoint = Config::get('api.masterNewRegistration');
        if (!is_string($createNewEndpoint)) {
            Log::error('Invalid API endpoint for registration.');
            return back()->with('error', 'Invalid API endpoint.');
        }

        try {
            // STEP 1: Hit masterNewRegistration untuk simpan data
            $response = $client->request('POST', $createNewEndpoint, [
                'headers' => $headers,
                'json' => $body
            ]);

            $responseData = json_decode($response->getBody(), true);
            Log::info('Registration Response:', $responseData);
            if ($responseData['status'] == true) {
                $registrationId = $responseData['id'] ?? null;
                Session::put('registrationId', $registrationId);
                Log::info("Successfully registered with ID: {$registrationId}");

                // Jika accountType == SSF → langsung ke upload file tanpa create account
                if ($accountType === 'SSF') {
                    Log::info("Account type is SSF, skipping account creation step.");
                    return redirect()->route('uploadUserFiles');
                }

                // STEP 2: Non-SSF → create account
                $createAccountEndpoint = Config::get('api.masterAccountRegistration');
                if (!is_string($createAccountEndpoint)) {
                    Log::error('Invalid API endpoint for account registration.');
                    return back()->with('error', 'Invalid API endpoint.');
                }

                $accountResponse = $client->request('POST', $createAccountEndpoint, [
                    'headers' => $headers,
                    'json' => ['serverId' => $registrationId]
                ]);

                $accountIdResponse = json_decode($accountResponse->getBody(), true);
                Log::info('Account Creation Response:', $accountIdResponse);
                if (!empty($accountIdResponse['status']) && $accountIdResponse['status'] == true) {
                    Log::info('Account registration successful, redirecting to file upload.');
                    return redirect()->route('uploadUserFiles');
                } else {
                    Log::error('Account registration failed.');
                    return back()->with('error', 'Account registration failed.');
                }

            } else {
                Log::error('Registration failed: ' . json_encode($responseData));
                return back()->with('error', 'Registration failed.');
            }
        } catch (\Exception $e) {
            Log::error('Error occurred during registration: ' . $e->getMessage());
            return back()->with('error', 'Error occurred during registration.');
        }
    }

    public function uploadUserFiles()
    {
        $client = new Client();
        $registrationId = Session::get('registrationId');

        if (!$registrationId) {
            return back()->with('error', 'Missing registration ID.');
        }

        $ktpFileName = session('ktp_image_name');
        $selfieFileName = session('selfie_image_name');
        $videoFileName = session('video_file_name');
        $signatureFileName = session('signature_image_name');
        $ktpbeneficiaryFileName = session('ktp_beneficiary_name');

        if (!$ktpFileName || !$selfieFileName || !$videoFileName || !$signatureFileName) {
            return back()->with('error', 'Some required files are missing in session.');
        }

        $files = [
            [
                'name' => 'file',
                'path' => storage_path('app/public/uploads/ktp_images/' . $ktpFileName),
                'type' => 'identity'
            ],
            [
                'name' => 'file',
                'path' => storage_path('app/public/uploads/selfie_images/' . $selfieFileName),
                'type' => 'selfie'
            ],
            [
                'name' => 'file',
                'path' => storage_path('app/public/uploads/videos/' . $videoFileName),
                'type' => 'selfieAgreement'
            ],
            [
                'name' => 'file',
                'path' => storage_path('app/public/uploads/signatures/' . $signatureFileName),
                'type' => 'signature'
            ]
        ];

        // Only add the beneficiary KTP file if it's not null or empty
        if ($ktpbeneficiaryFileName) {
            $files[] = [
                'name' => 'file',
                'path' => storage_path('app/public/uploads/ktp_images_beneficiary/' . $ktpbeneficiaryFileName),
                'type' => 'boIdentity'
            ];
        }

        foreach ($files as $file) {
            if (!file_exists($file['path'])) {
                Log::error('File not found: ' . $file['path']);
                return back()->with('error', 'File ' . $file['path'] . ' not found.');
            }
        }

        // Validate API endpoint
        $uploadFilesEndpoint = Config::get('api.uploadImage');

        if (!is_string($uploadFilesEndpoint) || empty($uploadFilesEndpoint)) {
            Log::error('Invalid or missing uploadFiles API endpoint.');
            return back()->with('error', 'Invalid API endpoint.');
        }

        try {
            foreach ($files as $file) {
                $multipart = [
                    [
                        'name' => 'id',
                        'contents' => $registrationId
                    ],
                    [
                        'name' => 'type',
                        'contents' => trim($file['type'])
                    ],
                    [
                        'name' => 'file',
                        'contents' => fopen($file['path'], 'r'),
                        'filename' => basename($file['path']),
                    ]
                ];

                Log::info('Uploading file: ', [
                    'id' => $registrationId,
                    'type' => $file['type'],
                    'filename' => basename($file['path']),
                    'fileSize' => filesize($file['path'])
                ]);

                $uploadResponse = $client->request('POST', $uploadFilesEndpoint, [
                    'headers' => ['Accept' => 'application/json'],
                    'multipart' => $multipart
                ]);

                $uploadResponseData = json_decode($uploadResponse->getBody(), true);

                if (!isset($uploadResponseData['id'])) {
                    Log::error('File upload failed for ' . $file['type'], $uploadResponseData);
                    return back()->with('error', 'File upload failed for ' . $file['type']);
                }
            }

            return view('Success_Page')->with('message', 'Data and files uploaded successfully.');

        } catch (\Exception $e) {
            Log::error('Error uploading files: ' . $e->getMessage());
            return back()->with('error', 'Error uploading files: ' . $e->getMessage());
        }
    }
}