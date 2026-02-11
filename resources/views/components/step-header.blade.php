@php
    $total = 6;
    $percent = ($step / $total) * 100;
@endphp

<div class="step-header mb-5">
    <div class="step-top">
        <a href="{{ $back ?? url()->previous() }}" class="step-back">
            ←
        </a>
        <div class="step-text">
            Langkah {{ $step }} dari {{ $total }}
        </div>
    </div>
    <div class="step-progress">
        <div class="step-progress-fill"
             style="width: {{ $percent }}%;">
        </div>
    </div>
</div>