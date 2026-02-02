@extends('layouts.app')
@section('title','Profits Anywhere')
@section('content')

<section class="auth-wrapper">
    <div class="container text-center">
        <a href="{{ url()->previous() }}" class="btn-back">
<i class="fa-solid fa-arrow-left"></i> </a>
        <h6 class="text-start referral-text mb-3">Langkah 3 dari 4</h6>
        <h4 class="text-white text-start start-text mb-3">Isi data pribadi kamu, yuk!</h4>
        <p class="text-start insight-text mb-5">Lengkapi identitas kamu untuk trading lancar. Pastikan data yang kamu masukkan sudah benar.</p>
        <div class="form-group text-start mb-4">
            <label class="form-label text-white text-form-global mb-2">Nama Lengkap</label>
            <input type="text" name="kode_id" class="form-control form-global" placeholder="Isi nama lengkap kamu" autocomplete="off" required>
        </div>
        <div class="form-group text-start mb-4">
            <label class="form-label text-white text-form-global mb-2">Nomor e-KTP</label>
            <input type="text" name="kode_id" class="form-control form-global" placeholder="NIK (Nomor Induk Kependudukan)" autocomplete="off" required>
        </div>
        <a href="{{ route('create-account') }}" class="btn btn-primary btn-regist w-100 mb-3">Lanjutkan</a>
    </div>
</section>

@endsection