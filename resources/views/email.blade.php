@extends('layouts.app')
@section('title','Profits Anywhere')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="auth-wrapper">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <a href="{{ request()->routeIs('email') ? url('/') : url()->previous() }}" class="btn-back"><i class="fa-solid fa-arrow-left text-white step-back"></i></a>
            <a href="https://api.whatsapp.com/send/?phone=628119560188&text=Hi+Profits+Saya+ada+Kendala.+Apakah+bisa+dibantu&type=phone_number&app_absent=0" class="btn-headset"><i class="fa-solid fa-headset text-white step-headset"></i></a>
        </div>
        <div class="text-start mb-4"><img class="icon-regist" src="{{ asset('storage/email.svg') }}"></div>
        <h3 class="text-white congrats-text text-start mb-2">Alamat Email</h3>
        <p class="silahkan-cek-text text-start mb-5">Masukkan alamat email Anda yang aktif.</p>
        <div class="form-group text-start mb-5">
            <input type="text" id="email" name="email" class="form-control form-global" placeholder="Masukan Alamat Email" autocomplete="off" required>
        </div>
       <button type="button" id="btnNext" class="btn btn-primary btn-regist w-100 mb-3">Lanjutkan</button>
    </div>
</section>

<script>
window.routes = {
    checkEmail : "{{ route('check.email') }}",
    sendOtp    : "{{ route('send.otp') }}",
    otpPage    : "{{ route('otp') }}"
};
</script>
<script src="{{ asset('js/registerCreateAccount.js') }}"></script>

@endsection