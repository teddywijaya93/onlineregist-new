@extends('layouts.app')
@section('title','Pemilik Rekening')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="auth-wrapper">
    <div class="container text-start">
        @include('components.step-header', [
            'step' => 6,
            'back' => route('data.profil.resiko')
        ])
        <div class="mb-5">
            <h3 class="head-lanjut text-white mb-2">Hampir Selesai! Lengkapi Data Rekening Anda Lalu Mulai Investasi</h3>
            <p class="desc-lanjut mb-0">Pastikan rekening benar untuk penarikan dana.</p>
        </div>
        <form method="POST" action="">
            @csrf
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Nama Pemilik Rekening</label>
                <input type="text" name="nama" id="nama" class="form-control form-global" value="{{ session('personalData.nama') }}" disabled style="background:#42526D; border:unset;">
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Bank Tujuan Penarikan</label>
                <select name="bank" id="bankSelect" class="form-control form-global">
                    <option value="">Pilih Bank</option>
                </select>
            </div> 
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Nomor Rekening</label>
                <input type="text" name="nomor_rekening" id="nomor_rekening" class="form-control form-global" placeholder="Tulis Nomor Rekening">
            </div>
            <button type="submit" id="btnNext" class="btn btn-primary btn-regist w-100 mb-3">Lanjutkan</button>
        </form>
    </div>
</section>

<script>
window.routes = {
    bank   : "{{ route('master.bank') }}",
};
</script>
<script src="{{ asset('js/bank.js') }}"></script>

@endsection