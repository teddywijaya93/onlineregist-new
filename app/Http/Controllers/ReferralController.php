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
            $response = Http::timeout(config('api.timeout'))
            ->connectTimeout(config('api.connect_timeout'))
            ->retry(
                config('api.retry'),
                config('api.retry_sleep')
            )
            ->post(config('api.referralCheck'), $payload);

            if ($response->successful()) {
                $data = $response->json();
                session([
                    'referral' => [
                        'idLinkCode'       => $idLinkCode,
                        'referralCode'     => $referralCode,
                        'eventDisplayName' => $data['eventDisplayName'] ?? null,
                        'aoCode'           => $data['aoCode'] ?? null,
                        'aoName'           => $data['aoName'] ?? null,
                        'referrerId'       => $data['referrerId'] ?? null,
                        // 'rdnBank'          => $data['rdnBank'] ?? null,
                    ]
                ]);

                return view('referral-code', [
                    'eventDisplayName' => $data['eventDisplayName'] ?? '-',
                    'aoName'           => $data['aoName'] ?? '-',
                ]);
            }
        } catch (\Exception $e) {}

        return view('referral-code', [
            'eventDisplayName' => '-',
            'aoName' => '-',
        ]);
    }
}