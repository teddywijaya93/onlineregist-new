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
            return redirect()->route('email');
        }
        $currentRoute = $request->route()->getName();
        $flow = StepRedirectService::getFlow();
        $currentStep = array_search(
            $currentRoute,
            StepRedirectService::STEP_ROUTE
        );

        if ($currentStep === false) {
            return $next($request);
        }
        $sessionIndex = array_search($sessionStep, $flow);
        $currentIndex = array_search($currentStep, $flow);

        // Block hanya kalau lompat ke depan
        if ($currentIndex > $sessionIndex) {
            return redirect()->route(StepRedirectService::STEP_ROUTE[$sessionStep]);
        }
        return $next($request);
    }
}