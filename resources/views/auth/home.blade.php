@extends('layouts.app')
@section('title','Profits Anywhere')
@section('content')

<section class="auth-wrapper">
    <div class="container text-center">
        <div class="icons mb-4"><img src="{{ asset('storage/register.png') }}" class="w-100"></div>
        <h4 class="text-white text-start start-text mb-3">Mulai Perjalanan Investasi bersama Profits</h4>
        <p class="text-start insight-text mb-5">Produk, fitur, informasi dan layanan personal terlengkap dan terbaik untuk Anda.</p>

        <a href="{{ route('email') }}" class="btn btn-primary btn-regist w-100 mb-3">Daftar dengan Email</a>
        <div class="atau-text mb-0">Sudah punya akun ?</div>
        <a href="{{ route('login') }}" class="btn btn-outline-light btn-login w-100 mt-3">Login</a>
    </div>
</section>

@endsection