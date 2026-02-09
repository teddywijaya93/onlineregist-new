@extends('layouts.app')
@section('title','Login')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="auth-wrapper">
    <div class="container text-start">
        <div class="mb-4">
            <div class="success-icon">
                <i class="fa-solid fa-envelope"></i>
            </div>
        </div>

        <h3 class="text-white congrats-text fw-bold mb-2">Selamat Bergabung! Pendaftaran Email Kamu Sudah Berhasil.</h3>
        <p class="silahkan-cek-text mb-4">
            Silakan cek email dan masukan data yang kami kirimkan ke
            <span class="text-primary email-text"> {{ Str::mask(session('register_email'), '*', 3, 6) }} </span>
        </p>

        <div class="form-group mb-4">
            <label class="form-label text-white text-form-global mb-2">Username</label>
            <input type="text" id="username" class="form-control form-global" placeholder="Masukan username kamu">
        </div>
        <div class="form-group mb-4">
            <label class="form-label text-white text-form-global mb-2">Password</label>
            <input type="password" id="password" class="form-control form-global" placeholder="Isi password kamu disini">
        </div>

        <p class="resend-otp">Tidak menerima verifikasi email?<br/>
            <a href="#" class="resend-otp resend-link">Kirim ulang kode</a>
            atau
            <a href="#" class="resend-otp resend-link">Atur ulang email</a>
            <!-- <  span id="timer" class="resend-otp resend-link ms-1">60s</span> -->
        </p>
        <button id="btnLogin" class="btn btn-primary btn-regist w-100 mb-3">Login</button>
    </div>
</section>

<script>

</script>

@endsection