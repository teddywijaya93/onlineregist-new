@extends('layouts.app')
@section('title','Profits Anywhere')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="auth-wrapper">
    <div class="container">
        <div class="d-flex justify-content-end mb-5">
            <a href="https://api.whatsapp.com/send/?phone=628119560188&text=Hi+Profits+Saya+ada+Kendala.+Apakah+bisa+dibantu&type=phone_number&app_absent=0" class="btn-headset"><i class="fa-solid fa-headset text-white step-headset"></i></a>
        </div>
        <h3 class="head-lanjut text-white mb-3">Registrasi melalui Referral</h3>
        <p class="desc-lanjut mb-5">Halaman ini diakses melalui QR atau link eksklusif. Lanjutkan untuk memulai registrasi.</p>
        <div class="card card-referral mb-5">
            <div class="mb-4">
                <h5 class="title-referral mb-2">Nama Event</h5>
                <h4 class="text-referral mb-0">{{ $eventDisplayName ?? '-' }}</h4>
            </div>
            <div>
                <h5 class="title-referral mb-2">Referensi Oleh</h5>
                <h4 class="text-referral mb-0">{{ $aoName ?? '-' }}</h4>
            </div>
        </div>
        <a href="{{ route('email') }}" class="btn btn-primary btn-regist w-100 mt-3">Lanjutkan</a>
    </div>
</section>

@endsection