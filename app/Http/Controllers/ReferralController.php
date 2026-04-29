<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ReferralController extends Controller
{
    public function index(Request $request)
    {
        $idLinkCode   = $request->query('idLinkCode');
        $referralCode = $request->query('referralCode');

        if (!$idLinkCode && !$referralCode) {
            return view('referral-code', [
                'eventDisplayName' => '-',
                'aoName' => '-',
            ]);
        }
        $payload = [];

        if ($idLinkCode) {
            $payload['idLinkCode'] = $idLinkCode;
        }

        if ($referralCode) {
            $payload['referralCode'] = $referralCode;
        }

        try {
            $response = Http::withHeaders([
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ])
            ->post(
                'https://dev.profits.co.id:8283/registration/referralCheck',
                $payload
            );

            if ($response->successful()) {
                $data = $response->json();
                session([
                    'referral' => [
                        'idLinkCode'       => $idLinkCode,
                        'referralCode'     => $referralCode,
                        'eventDisplayName' => $data['eventDisplayName'] ?? null,
                        'aoCode'           => $data['aoCode'] ?? null,
                        'aoName'           => $data['aoName'] ?? null,
                    ]
                ]);

                return view('referral-code', [
                    'eventDisplayName' => $data['eventDisplayName'] ?? '-',
                    'aoName'           => $data['aoName'] ?? '-',
                ]);
            }
        } catch (\Exception $e) {
            // silent fail
        }

        return view('referral-code', [
            'eventDisplayName' => '-',
            'aoName' => '-',
        ]);
    }
}