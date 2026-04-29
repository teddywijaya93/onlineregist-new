@extends('layouts.app')
@section('title','Profits Anywhere')
@section('content')

@php
    use App\Services\StepRedirectService;

    $currentIndex = array_search($currentStep, $flow);
    $currentIndex = $currentIndex === false ? 0 : $currentIndex;
@endphp

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="auth-wrapper">
    <div class="container">
        <div class="d-flex justify-content-end mb-5">
            <a href="https://api.whatsapp.com/send/?phone=628119560188&text=Hi+Profits+Saya+ada+Kendala.+Apakah+bisa+dibantu&type=phone_number&app_absent=0" class="btn-headset"><i class="fa-solid fa-headset text-white step-headset"></i></a>
        </div>
        <h3 class="head-lanjut text-white mb-5">Selesaikan Proses Registrasi Anda</h3>  
        @foreach($groups as $group)
            @php
                $isDone   = $currentIndex > $group['maxIndex'];
                $isActive = $currentIndex >= $group['minIndex'] && $currentIndex <= $group['maxIndex'];
                $number   = $loop->iteration;

                $route = StepRedirectService::routeByStep($group['firstStep']);

                $isEditable = !in_array($number, [1, 2]);
                $canClick = ($isActive || ($isDone && $isEditable)) && $route;
            @endphp

            <div class="step-item mb-3"
                 @if($canClick)
                    onclick="window.location='{{ $route }}?from=dashboard'"
                    style="cursor:pointer;"
                 @endif>

                <div class="d-flex align-items-center justify-content-between step-box">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            @if($isDone)
                            <div class="circle done">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            @else
                            <div class="circle {{ $isActive ? 'active' : 'inactive' }}">
                                {{ $number }}
                            </div>
                            @endif
                        </div>
                        <div class="text-white text-step-dashboard">
                            {{ $group['label'] }}
                        </div>
                    </div>
                    <div>
                        @if($isDone && $isEditable)
                            <i class="fa-solid fa-pen text-white"></i>
                        @elseif($isActive)
                            <i class="fa-solid fa-chevron-right text-white"></i>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>

@endsection