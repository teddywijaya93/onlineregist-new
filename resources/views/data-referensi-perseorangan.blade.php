@extends('layouts.app')
@section('title','Penghasilan Nasabah')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="auth-wrapper">
    <div class="container text-start">
        <div class="mb-5">
            <h3 class="head-lanjut text-white mb-2">Data Orang Tua/Saudara/Wali</h3>
            <p class="desc-lanjut mb-0">Tenang, kontak ini disimpan untuk keadaan darurat dan hanya akan dihubungi bila diperlukan.</p>
        </div>
        <form method="POST">
            @csrf
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Hubungan dengan Nasabah</label>
                <!-- <input type="text" name="nama_relasi" id="nama_relasi" class="form-control form-global" placeholder="Tulis nama lengkap relasi"> -->
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Nama</label>
                <input type="text" name="nama_relasi" id="nama_relasi" class="form-control form-global" placeholder="Tulis nama lengkap relasi">
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Nomor Ponsel</label>
                <input type="text" name="nomor_ponsel_relasi" id="nomor_ponsel_relasi" class="form-control form-global" placeholder="Tulis nomor ponsel relasi">
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Email</label>
                <input type="email" name="email_relasi" id="email_relasi" class="form-control form-global" placeholder="Tulis email relasi">
            </div>
            <button id="btnNext" class="btn btn-primary btn-regist w-100 mb-3">Lanjutkan</button>
        </form>
    </div>
</section>

<script>

</script>

@endsection