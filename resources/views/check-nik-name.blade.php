@extends('layouts.app')
@section('title','Profits Anywhere')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="auth-wrapper"> 
    <div class="container text-center"> 
        <div class="text-start mb-5"> 
            <a href="{{ url()->previous() }}" class="btn-back"><i class="fa-solid fa-arrow-left text-white"></i></a> 
        </div> 
        <h6 class="text-start referral-text mb-3">Langkah 3 dari 4</h6>
        <h4 class="text-white text-start start-text mb-3">Isi data pribadi kamu, yuk!</h4> 
        <p class="text-start insight-text mb-5">Lengkapi identitas kamu untuk trading lancar. Pastikan data yang kamu masukkan sudah benar.</p> 
        <div class="form-group text-start mb-4"> 
            <label class="form-label text-white text-form-global mb-2">Nama sesuai e-KTP</label>
            <input type="text" id="name" name="name" class="form-control form-global" placeholder="Isi Nama sesuai e-KTP kamu" required>
        </div>
        <div class="form-group text-start mb-4"> 
            <label class="form-label text-white text-form-global mb-2">Nomor e-KTP</label>
            <input type="text" id="nik" name="nik" class="form-control form-global" placeholder="NIK (Nomor Induk Kependudukan)" autocomplete="off" pattern="^[0-9]{10,}$" oninput="this.value = this.value.replace(/[^0-9]/g, '')" maxlength="16" required>
        </div> 
        <button id="btnNext" type="button" onclick="saveNikName()" class="btn btn-primary btn-regist w-100 mb-3">Lanjutkan</button> 
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
window.routes = {
    checkNik: "{{ route('check.nik') }}",
    saveNikName: "{{ route('step.identity') }}",
    createAccount: "{{ route('create-account') }}"
};
</script>
<script src="{{ asset('js/registerNikName.js') }}"></script>

@endsection