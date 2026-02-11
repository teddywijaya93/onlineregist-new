<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MasterDataController extends Controller
{
    public function getGenderMaster() {
        $response = \Illuminate\Support\Facades\Http::withoutVerifying()
            ->withHeaders(['Content-Type' => 'text/plain'])
            ->send(
                'GET',
                'https://dev.profits.co.id:8283/onlineRegistrationV1/masterData',
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
        $response = \Illuminate\Support\Facades\Http::withoutVerifying()
            ->withHeaders(['Content-Type' => 'text/plain'])
            ->send(
                'GET',
                'https://dev.profits.co.id:8283/onlineRegistrationV1/masterData',
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
        $response = \Illuminate\Support\Facades\Http::withoutVerifying()
            ->withHeaders(['Content-Type' => 'text/plain'])
            ->send(
                'GET',
                'https://dev.profits.co.id:8283/onlineRegistrationV1/masterData',
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

    public function getEmploymentMaster() {
        $response = \Illuminate\Support\Facades\Http::withoutVerifying()
            ->withHeaders(['Content-Type' => 'text/plain'])
            ->send(
                'GET',
                'https://dev.profits.co.id:8283/onlineRegistrationV1/masterData',
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

        $response = Http::withoutVerifying()
            ->withHeaders(['Content-Type' => 'text/plain'])
            ->send(
                'GET',
                'https://dev.profits.co.id:8283/onlineRegistrationV1/getEmploymentPosition'
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

        $response = Http::withoutVerifying()
            ->withHeaders(['Content-Type' => 'text/plain'])
            ->send(
                'GET',
                'https://dev.profits.co.id:8283/onlineRegistrationV1/getEmploymentBusinessline'
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
        $response = Http::withoutVerifying()
            ->withHeaders(['Content-Type' => 'text/plain'])
            ->send(
                'GET',
                'https://dev.profits.co.id:8283/onlineRegistrationV1/masterData',
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
        $response = Http::withoutVerifying()
            ->withHeaders(['Content-Type' => 'text/plain'])
            ->send(
                'GET',
                'https://dev.profits.co.id:8283/onlineRegistrationV1/masterData',
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
        $response = Http::withoutVerifying()
            ->withHeaders(['Content-Type' => 'text/plain'])
            ->send(
                'GET',
                'https://dev.profits.co.id:8283/onlineRegistrationV1/masterData',
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
        $response = \Illuminate\Support\Facades\Http::withoutVerifying()
            ->withHeaders(['Content-Type' => 'text/plain'])
            ->send(
                'GET',
                'https://dev.profits.co.id:8283/onlineRegistrationV1/masterData',
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
}