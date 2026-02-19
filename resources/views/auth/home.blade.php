@extends('layouts.app')
@section('title','Profits Anywhere')
@section('content')

<section class="auth-wrapper">
    <div class="container text-center">
        <h3 class="register-text text-white mb-3">Registrasi Akun</h3>
        <div class="icons mb-4"><img src="{{ asset('storage/regis.svg') }}" class="w-100"></div>

        <h4 class="text-white text-start start-text mb-3">Mulai perjalanan trading kamu bersama Profits</h4>
        <p class="text-start insight-text mb-5">Insight, rekomendasi, dan analisis yang mudah dipahami sejak langkah pertama.</p>

        <a href="{{ route('referral-form') }}" class="btn btn-primary btn-regist w-100 mb-3">Daftar</a>
        <div class="atau-text mb-0">Atau</div>
        <a href="{{ route('login') }}" class="btn btn-outline-light btn-login w-100 mt-3">Login</a>
    </div>
</section>

@endsection