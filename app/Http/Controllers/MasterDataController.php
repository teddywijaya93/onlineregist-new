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

    public function getallKelurahanMaster(Request $request)
    {
        $search = strtolower($request->input('q', ''));

        $response = Http::timeout(10)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post(
                'https://dev.profits.co.id:8283/registration/getKelurahan',
                [
                    'type' => 'kelurahan'
                ]
            );

        $raw = $response->json()['data'] ?? [];
        $data = collect($raw)->map(function ($item) {
            $alamat = $item['alamat_lengkap'] ?? '';
            $parts  = array_map('trim', explode(',', $alamat));

            return [
                'postalCode' => $parts[0] ?? null,
                'kelurahan'=> $parts[1] ?? null,
                'kecamatan'=> $parts[2] ?? null,
                'city'     => $parts[3] ?? null,
                'label'    => $alamat,
                'value'    => $parts[1] ?? null
            ];
        });

        // FILTER (server side)
        if ($search) {
            $data = $data->filter(function ($item) use ($search) {
                return str_contains(strtolower($item['label']), $search);
            });
        }

        return response()->json([
            'status' => true,
            'data'   => $data->take(50)->values()
        ]);
    }
}