@extends('layouts.app')
@section('title','Verifikasi OTP')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="auth-wrapper">
    <div class="container">
        <div class="mb-4">
            <div class="otp-icon">
                <i class="fa-solid fa-mobile-screen"></i>
            </div>
        </div>
        <h3 class="text-white congrats-text fw-bold mb-2">Verifikasi Nomor Ponsel</h3>
        <p class="silahkan-cek-text mb-4">
            Masukan 6 digit kode yang telah kami kirimkan ke nomor kamu<br/>
            <span class="text-primary email-text"> {{ Str::mask(session('register_phone'), '*', 3, 6) }} </span>
        </p>
        <div class="d-flex gap-2 mb-4">
            @for ($i = 0; $i < 6; $i++)
                <input type="text" maxlength="1" class="form-control text-center otp-box" style="width:50px;height:55px;font-size:20px;">
            @endfor
        </div>
        <p class="resend-otp">Tidak menerima kode?<br>
            <a href="#" id="resendLink" class="resend-otp resend-link" >Kirim ulang</a>
            <span id="timerText" class="ms-2"></span>
        </p>
        <button id="btnContinue" class="btn btn-primary w-100">Lanjutkan</button>
    </div>
</section>

<script src="{{ asset('js/auth.js') }}"></script>

@endsection