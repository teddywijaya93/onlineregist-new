@php
    $currentRoute = request()->route()->getName();

    $currentStepKey = array_search($currentRoute,\App\Services\StepRedirectService::STEP_ROUTE);
    $prev = \App\Services\StepRedirectService::prevStep($currentStepKey);
    $stepNumber = \App\Services\StepRedirectService::stepNumber($currentStepKey);
    $totalStep  = \App\Services\StepRedirectService::totalStep();

    $progressPercent = ($stepNumber / $totalStep) * 100;
@endphp

<div class="step-header mb-5">
    <div class="step-top">
        @if(!$hideBack && $prev)
        <a href="{{ route(\App\Services\StepRedirectService::STEP_ROUTE[$prev]) }}" class="step-back">
            <i class="fa fa-angle-left"></i>
        </a>
        @endif
        <div class="step-text">Step {{ $stepNumber }} / {{ $totalStep }}</div>
    </div>
    <div class="step-progress">
        <div class="step-progress-fill" style="width: {{ $progressPercent }}%"></div>
    </div>
</div>