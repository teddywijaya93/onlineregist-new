@extends('layouts.app')
@section('title','Profits Anywhere')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="auth-wrapper">
    <div class="container">
        <div class="text-start mb-5">
            <a href="{{ url()->previous() }}" class="btn-back"><i class="fa-solid fa-arrow-left text-white"></i></a>
        </div>
        <h3 class="text-white congrats-text text-start mb-2">Buat PIN Trading</h3>
        <p class="silahkan-cek-text text-start mb-5">Tentukan 6 digit PIN Trading.</p>
       <button type="button" onclick="submitAccount()" class="btn btn-primary btn-regist w-100 mb-3">Lanjutkan</button>
    </div>
</section>

<script src="{{ asset('js/registerCreateAccount.js') }}"></script>

@endsection