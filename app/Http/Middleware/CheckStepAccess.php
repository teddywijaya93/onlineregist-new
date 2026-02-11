<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStepAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $route = $request->route()->getName();

        $steps = [
            'data.personal' => 1,
            'data.pekerjaan' => 2,
            'data.penghasilan' => 3,
            'data.referensi.perseorangan' => 4,
            'data.profil.resiko' => 5,
            'data.bank' => 6,
        ];

        $currentStep = $steps[$route] ?? 1;

        // Cek step sebelumnya
        if ($currentStep >= 2 && !session()->has('personal_data')) {
            return redirect()->route('data.personal');
        }

        if ($currentStep >= 3 && !session()->has('employment_data')) {
            return redirect()->route('data.pekerjaan');
        }

        if ($currentStep >= 4 && !session()->has('financial_data')) {
            return redirect()->route('data.penghasilan');
        }

        if ($currentStep >= 5 && !session()->has('referensi_perseorangan')) {
            return redirect()->route('data.referensi.perseorangan');
        }

        if ($currentStep >= 6 && !session()->has('profil_risiko_total')) {
            return redirect()->route('data.profil.resiko');
        }

        return $next($request);
    }
}
