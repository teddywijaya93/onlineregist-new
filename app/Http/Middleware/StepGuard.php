<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\StepRedirectService;

class StepGuard
{
    public function handle($request, Closure $next)
    {
        $sessionStep = session('registrationStep');

        if (!$sessionStep) {
            return redirect()->route('login');
        }

        $currentStep = array_search(
            $request->route()->getName(),
            StepRedirectService::STEP_ROUTE
        );

        $allowedSteps = array_keys(StepRedirectService::STEP_ROUTE);
        $sessionIndex = array_search($sessionStep, $allowedSteps);
        $currentIndex = array_search($currentStep, $allowedSteps);

        // Kalau user lompat ke step setelah sessionStep
        if ($currentIndex > $sessionIndex) {
            return redirect(
                StepRedirectService::routeByStep($sessionStep)
            );
        }

        // Kalau kembali ke step sebelumnya, izinkan
        return $next($request);
    }
}