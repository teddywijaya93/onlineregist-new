@php
    $currentRoute = request()->route()->getName();

    $currentStepKey = array_search($currentRoute,\App\Services\StepRedirectService::STEP_ROUTE);
    $prev = \App\Services\StepRedirectService::prevStep($currentStepKey);
    $stepNumber = \App\Services\StepRedirectService::stepNumber($currentStepKey);
    $totalStep  = \App\Services\StepRedirectService::totalStep();

    $progressPercent = ($stepNumber / $totalStep) * 100;
@endphp

<div class="step-header mb-5">
    <div class="step-top d-flex align-items-center">
        @if(!$hideBack && $prev)
        <a href="{{ route(\App\Services\StepRedirectService::STEP_ROUTE[$prev]) }}" class="step-back">
            <i class="fa fa-arrow-left"></i>
        </a>
        @endif
        <div class="step-callcenter ms-auto">
            <a href="https://api.whatsapp.com/send/?phone=628119560188&text=Hi+Profits+Saya+ada+Kendala.+Apakah+bisa+dibantu&type=phone_number&app_absent=0">
                <i class="fa fa-headset text-white"></i>
            </a>
        </div>
    </div>
    <!-- <div class="step-progress">
        <div class="step-progress-fill" style="width: {{ $progressPercent }}%"></div>
    </div> -->
    <div class="step-progress d-flex gap-2">
        @for ($i = 1; $i <= $totalStep; $i++)
        <div class="step-segment {{ $i <= $stepNumber ? 'active' : '' }}"></div>
        @endfor
    </div>
</div>