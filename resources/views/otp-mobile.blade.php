@extends('layouts.app')
@section('title','Profits Anywhere')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="auth-wrapper">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <a href="{{ url()->previous() }}" class="btn-back"><i class="fa-solid fa-arrow-left text-white step-back"></i></a>
            <a href="#" class="btn-headset"><i class="fa-solid fa-headset text-white step-headset"></i></a>
        </div>
        <div class="text-start mb-4"><img class="icon-regist" src="{{ asset('storage/mobile.svg') }}"></div>
        <h3 class="text-white congrats-text text-start mb-2">Verifikasi Nomor Ponsel</h3>
        <p class="silahkan-cek-text text-start mb-5">Masukkan 6 digit OTP yang dikirimkan melalui Whatsapp. {{ Str::mask(session('reg_phone'), '*',5, 5) }}</p>
        <input type="hidden" id="email" value="{{ session('reg_phone') }}">
        <div class="d-flex gap-2 mb-4">
            @for ($i = 0; $i < 6; $i++)
                <input type="text" maxlength="1" class="form-control text-center otp-box" style="width:50px;height:55px;font-size:20px;">
            @endfor
        </div>
        <p class="resend-otp">Tidak menerima OTP? &nbsp;
            <a href="#" id="resendLink" class="resend-otp resend-link" >Kirim ulang</a>
            <span id="timerText" class="ms-2"></span>
        </p>
        <button type="button" id="btnNext" class="btn btn-primary btn-regist w-100 mb-3">Lanjutkan</button>
    </div>
</section>

<script src="{{ asset('js/registerCreateAccount.js') }}"></script>

@endsection