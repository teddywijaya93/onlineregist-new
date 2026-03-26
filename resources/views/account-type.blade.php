@extends('layouts.app')
@section('title','Profits Anywhere')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="auth-wrapper">
    <div class="container">
        <div class="mb-5">
            <!-- <a href="{{ url()->previous() }}" class="btn-back"><i class="fa-solid fa-arrow-left text-white"></i></a> -->
        </div>
        <h3 class="text-white congrats-text text-start mb-5">Pilih Tipe Akun</h3>
        <form id="productForm">
            <input type="hidden" id="registrationId" value="{{ session('registrationId') }}">
            <div class="product-wrapper">
                <!-- REGULAR -->
                <label class="customer-type-card">
                    <input type="radio" id="accountType" name="accountType" value="REGULAR">
                    <div class="product-content text-start text-white">
                        <div class="rek-saham-type mb-3"><span class="icon">📈</span> Regular</div>
                        <div class="rek-saham-txt mb-3">Fleksibel untuk Semua Transaksi Saham</div>
                        <p class="rek-saham-desc mb-0">Gunakan Rekening Dana Nasabah reguler untuk bertransaksi saham dan produk pasar modal secara umum dan pilihan emiten yang lengkap.</p>
                    </div>
                    <div class="radio-circle"></div>
                </label>

                <!-- SYARIAH -->
                <label class="customer-type-card">
                    <input type="radio" id="accountType" name="accountType" value="SYARIAH">
                    <div class="product-content text-start text-white">
                        <div class="rek-saham-type mb-3"><span class="icon">☪</span> Syariah</div>
                        <div class="rek-saham-txt mb-3">Investasi Sesuai Prinsip Syariah</div>
                        <p class="rek-saham-desc mb-0">Rekening Dana Nasabah berbasis syariah untuk transaksi saham yang masuk Daftar Efek Syariah (DES), bebas riba dan sesuai prinsip Islam.</p>
                    </div>
                    <div class="radio-circle"></div>
                </label>
            </div>  
           <button type="button" onclick="saveAccountType()" class="btn btn-primary btn-regist w-100 mb-3">Lanjutkan</button>
        </form>
    </div>
</section>

<script>
    window.registrationId = "{{ session('registrationId') }}";
</script>
<script src="{{ asset('js/accountType.js') }}"></script>

@endsection