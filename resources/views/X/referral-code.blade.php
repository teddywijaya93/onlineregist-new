@extends('layouts.app')
@section('title','Profits Anywhere')
@section('content')

<section class="auth-wrapper">
    <div class="container text-center">
        <div class="text-start mb-5">
            <a href="{{ url()->previous() }}" class="btn-back"><i class="fa-solid fa-arrow-left text-white"></i></a>
        </div>
        <h6 class="text-start referral-text mb-3">Langkah 1 dari 4</h6>
        <h4 class="text-white text-start start-text mb-3">Masukan kode referral kamu</h4>
        <p class="text-start insight-text mb-5">Masukkan Kode ID dan kode referral (opsional).</p>
        <div class="form-group text-start mb-4">
            <label class="form-label text-white text-form-global mb-2">Kode ID</label>
            <input type="text" id="idLinkCode" name="idLinkCode" class="form-control form-global" placeholder="Masukan Kode ID disini" autocomplete="off" required>
        </div>
        <div class="form-group text-start mb-4">
            <label class="form-label text-white text-form-global mb-2">Kode Referral</label>
            <input type="text" id="referralCode" name="referralCode" class="form-control form-global" placeholder="Masukan Kode Referral disini" autocomplete="off" required>
        </div>
        <a href="" class="btn btn-primary btn-regist w-100 mb-3">Lanjutkan</a>
        <a href="{{ route('customer-type') }}" class="lewati-text mb-0">Lewati</a>
    </div>
</section>

@endsection