@extends('layouts.app')
@section('title','Profits Anywhere')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="auth-wrapper">
    <div class="container">
        <div class="text-start mb-5">
            <!-- <a href="{{ url()->previous() }}" class="btn-back"><i class="fa-solid fa-arrow-left text-white"></i></a> -->
        </div>
        <div class="text-start mb-4"><img class="icon-regist" src="{{ asset('storage/mobile.svg') }}"></div>
        <h3 class="text-white congrats-text text-start mb-2">Nomor Ponsel</h3>
        <p class="silahkan-cek-text text-start mb-5">Masukkan nomor ponsel Anda yang terhubung dengan Whatsapp.</p>
        <div class="form-group text-start mb-5">
           <div class="d-flex phone-input">
                <div class="phone-prefix">+62</div>
                <input type="text" id="phone" name="phone" class="form-control form-global phone-number" placeholder="Masukkan Nomor Ponsel" maxlength="11" autocomplete="off">
            </div>
        </div>
       <button type="button" id="btnNext" class="btn btn-primary btn-regist w-100 mb-3">Lanjutkan</button>
    </div>
</section>

<script src="{{ asset('js/registerCreateAccount.js') }}"></script>

@endsection