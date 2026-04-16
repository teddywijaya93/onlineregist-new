@extends('layouts.app')
@section('title','Profits Anywhere')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="auth-wrapper">
    <div class="container">
        <div class="text-start mb-5">
            <a href="{{ url()->previous() }}" id="btnBack"><i class="fa-solid fa-arrow-left text-white"></i></a>
        </div>
        <div class="text-center mb-4"><img class="icon-regist" src="{{ asset('storage/pin.svg') }}"></div>
        <h3 id="titlePin" class="text-white congrats-text text-center mb-2">Buat PIN Trading</h3>
        <p id="descPin" class="silahkan-cek-text text-center mb-5">Tentukan 6 digit PIN Trading.</p>

        <div class="pin-wrapper mb-4">
            <input type="text" maxlength="1" class="pin-input" inputmode="numeric">
            <input type="text" maxlength="1" class="pin-input" inputmode="numeric">
            <input type="text" maxlength="1" class="pin-input" inputmode="numeric">
            <input type="text" maxlength="1" class="pin-input" inputmode="numeric">
            <input type="text" maxlength="1" class="pin-input" inputmode="numeric">
            <input type="text" maxlength="1" class="pin-input" inputmode="numeric">
        </div>
        <div class="text-center mt-3 mb-5">
            <button type="button" id="togglePin" class="btn btn-sm btn-secondary">Show PIN</button>
        </div>
        <div class="pin-info mb-4">Jangan bagikan PIN Trading Anda kepada siapa pun. PIN Trading diperlukan untuk menjaga keamanan transaksi.</div>
        <button id="btnPin" class="btn btn-primary btn-regist w-100 mb-3" onclick="submitPin()"> Lanjutkan</button>
    </div>
</section>

<script>
    window.accountId = "{{ session('accountId') }}";
</script>
<script src="{{ asset('js/createPin.js') }}"></script>

@endsection