<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MasterDataController extends Controller
{
    public function getGenderMaster() {
       $response = Http::timeout(10)
            ->withHeaders(['Content-Type' => 'text/plain'])
            ->send(
                'GET',
                'https://dev.profits.co.id:8283/registration/masterData',
                [
                    'body' => json_encode([
                        'type' => 'gender'
                    ])
                ]
            );

        return response()->json([
            'status' => true,
            'data'   => $response->json()['datas'] ?? []
        ]);
    }

    public function getReligionMaster() {
        $response = Http::timeout(10)
            ->withHeaders(['Content-Type' => 'text/plain'])
            ->send(
                'GET',
                'https://dev.profits.co.id:8283/registration/masterData',
                [
                    'body' => json_encode([
                        'type' => 'religion'
                    ])
                ]
            );

        return response()->json([
            'status' => true,
            'data'   => $response->json()['datas'] ?? []
        ]);
    }

    public function getMaritalMaster() {
       $response = Http::timeout(10)
            ->withHeaders(['Content-Type' => 'text/plain'])
            ->send(
                'GET',
                'https://dev.profits.co.id:8283/registration/masterData',
                [
                    'body' => json_encode([
                        'type' => 'marital_status'
                    ])
                ]
            );

        return response()->json([
            'status' => true,
            'data'   => $response->json()['datas'] ?? []
        ]);
    }

    public function getEducationMaster() {
        $response = Http::timeout(10)
            ->withHeaders(['Content-Type' => 'text/plain'])
            ->send(
                'GET',
                'https://dev.profits.co.id:8283/registration/masterData',
                [
                    'body' => json_encode([
                        'type' => 'education'
                    ])
                ]
            );

        return response()->json([
            'status' => true,
            'data'   => $response->json()['datas'] ?? []
        ]);
    }

    public function getEmploymentMaster() {
        $response = Http::timeout(10)
            ->withHeaders(['Content-Type' => 'text/plain'])
            ->send(
                'GET',
                'https://dev.profits.co.id:8283/registration/masterData',
                [
                    'body' => json_encode([
                        'type' => 'employment'
                    ])
                ]
            );

        return response()->json([
            'status' => true,
            'data'   => $response->json()['datas'] ?? []
        ]);
    }

    public function getPositionByEmployment(Request $request) {
        if (!$request->employment_id) {
            return response()->json([
                'status' => true,
                'data' => []
            ]);
        }

        $response =Http::timeout(10)
            ->withHeaders(['Content-Type' => 'text/plain'])
            ->send(
                'GET',
                'https://dev.profits.co.id:8283/registration/getEmploymentPosition'
            );

        $datas = $response->json()['datas'] ?? [];
        $filtered = array_values(array_filter($datas, function ($item) use ($request) {
            return $item['employmentId'] == $request->employment_id;
        }));

        return response()->json([
            'status' => true,
            'data'   => $filtered
        ]);
    }

    public function getBusinesslineByEmployment(Request $request) {
        if (!$request->employment_id) {
            return response()->json([
                'status' => true,
                'data' => []
            ]);
        }

        $response = Http::timeout(10)
            ->withHeaders(['Content-Type' => 'text/plain'])
            ->send(
                'GET',
                'https://dev.profits.co.id:8283/registration/getEmploymentBusinessline'
            );

        $datas = $response->json()['datas'] ?? [];
        $filtered = array_values(array_filter($datas, function ($item) use ($request) {
            return $item['employmentId'] == $request->employment_id;
        }));

        return response()->json([
            'status' => true,
            'data'   => $filtered
        ]);
    }

    public function getIncomeRangeMaster() {
        $response = Http::timeout(10)
            ->withHeaders(['Content-Type' => 'text/plain'])
            ->send(
                'GET',
                'https://dev.profits.co.id:8283/registration/masterData',
                [
                    'body' => json_encode([
                        'type' => 'income_range'
                    ])
                ]
            );

        return response()->json([
            'status' => true,
            'data'   => $response->json()['datas'] ?? []
        ]);
    }

    public function getPrimaryFundMaster() {
        $response = Http::timeout(10)
            ->withHeaders(['Content-Type' => 'text/plain'])
            ->send(
                'GET',
                'https://dev.profits.co.id:8283/registration/masterData',
                [
                    'body' => json_encode([
                        'type' => 'primary_fund_sources'
                    ])
                ]
            );

        return response()->json([
            'status' => true,
            'data'   => $response->json()['datas'] ?? []
        ]);
    }

    public function getInvestmentObjective() {
        $response = Http::timeout(10)
            ->withHeaders(['Content-Type' => 'text/plain'])
            ->send(
                'GET',
                'https://dev.profits.co.id:8283/registration/masterData',
                [
                    'body' => json_encode([
                        'type' => 'investment_objective'
                    ])
                ]
            );

        return response()->json([
            'status' => true,
            'data'   => $response->json()['datas'] ?? []
        ]);
    }

    public function getBankMaster() {
        $response = Http::timeout(10)
            ->withHeaders(['Content-Type' => 'text/plain'])
            ->send(
                'GET',
                'https://dev.profits.co.id:8283/registration/masterData',
                [
                    'body' => json_encode([
                        'type' => 'bank'
                    ])
                ]
            );

        return response()->json([
            'status' => true,
            'data'   => $response->json()['datas'] ?? []
        ]);
    }

    public function getCityMaster() {
        $response = Http::timeout(10)
            ->withHeaders(['Content-Type' => 'text/plain'])
            ->send(
                'GET',
                'https://dev.profits.co.id:8283/registration/masterData',
                [
                    'body' => json_encode([
                        'type' => 'city'
                    ])
                ]
            );

        return response()->json([
            'status' => true,
            'data'   => $response->json()['data'] ?? []
        ]);
    }

    public function getKecamatanMaster(Request $request)
    {
        if (!$request->city_id) {
            return response()->json([
                'status' => true,
                'data' => []
            ]);
        }

        $response = Http::timeout(10)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->send(
                'GET',
                'https://dev.profits.co.id:8283/registration/getKecamatan',
                [
                    'body' => json_encode([
                        'cityId' => (string)$request->city_id
                    ]),
                ]
            );

        return response()->json([
            'status' => true,
            'data'   => $response->json()['data'] ?? []
        ]);
    }

    public function getKelurahanMaster(Request $request)
    {
        if (!$request->kecamatan_id) {
            return response()->json([
                'status' => true,
                'data' => []
            ]);
        }

        $response = Http::timeout(10)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->send(
                'GET',
                'https://dev.profits.co.id:8283/registration/getKelurahan',
                [
                    'body' => json_encode([
                        'kecamatanId' => (string)$request->kecamatan_id
                    ]),
                ]
            );

        return response()->json([
            'status' => true,
            'data'   => $response->json()['data'] ?? []
        ]);
    }

    public function getReferenceRelationMaster(Request $request) {
        $personal = session('personal_data');
        $employment = session('employment_data');
        
        if (!$personal || !$employment) {
            return response()->json(['datas' => []]);
        }

        $jk       = $personal['jenis_kelamin'];
        $status   = $personal['status_perkawinan'];
        $jobId    = $employment['employment'];

        // IRT, Pelajar, Pensiunan
        $familyEmployment = [4, 15, 27];    
        $formType = in_array($jobId, $familyEmployment)
            ? 'family'
            : 'spouse'; 

        $relations = [];
        $title     = '';

        if ($status == 1) {
            if ($jk == 1) {
                $title = 'Data Istri';
                $relations = [
                    ['id' => 1, 'description' => 'Istri']
                ];
            } else {
                $title = 'Data Suami';
                $relations = [
                    ['id' => 2, 'description' => 'Suami']
                ];
            }
        } elseif ($status == 2) {
            $title = 'Data Orang Tua / Saudara / Wali';
            $relations = [
                ['id' => 3, 'description' => 'Ayah'],
                ['id' => 4, 'description' => 'Ibu'],
                ['id' => 5, 'description' => 'Saudara'],
                ['id' => 6, 'description' => 'Wali'],
            ];
        } else { 
            $title = 'Data Orang Tua / Saudara / Anak / Wali';
            $relations = [
                ['id' => 3, 'description' => 'Ayah'],
                ['id' => 4, 'description' => 'Ibu'],
                ['id' => 5, 'description' => 'Saudara'],
                ['id' => 7, 'description' => 'Anak'],
                ['id' => 6, 'description' => 'Wali'],
            ];
        }

        session(['reference_form_type' => $formType]);

        return response()->json([
            'status'    => true,
            'title'     => $title,
            'form_type' => $formType,
            'datas'     => $relations
        ]);
    }
}