@extends('layouts.app')
@section('title','Profits Anywhere')
@section('content')

<section class="auth-wrapper">
    <div class="container text-center">
        <h6 class="text-start referral-text mb-3">Langkah 4 dari 4</h6>
        <h4 class="text-white text-start start-text mb-3">Buat akun trading kamu</h4>
        <p class="text-start insight-text mb-5">Silakan buat username terlebih dahulu, kemudian gunakan email dan nomor telepon yang masih aktif.</p>
        <div class="form-group text-start mb-4">
            <label class="form-label text-white text-form-global mb-2">Username</label>
            <input type="text" name="kode_id" class="form-control form-global" placeholder="Buat Username" autocomplete="off" required>
        </div>
        <div class="form-group text-start mb-4">
            <label class="form-label text-white text-form-global mb-2">Email</label>
            <input type="text" name="kode_id" class="form-control form-global" placeholder="e.g the-east@gmail.com" autocomplete="off" required>
        </div>
        <div class="form-group text-start mb-4">
            <label class="form-label text-white text-form-global mb-2">Nomor Telepon</label>
            <input type="text" name="kode_id" class="form-control form-global" placeholder="e.g +6281212345678" autocomplete="off" required>
        </div>
        <a href="" class="btn btn-primary btn-regist w-100 mb-3">Lanjutkan</a>
    </div>
</section>

@endsection