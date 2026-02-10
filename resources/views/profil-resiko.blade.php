@extends('layouts.app')
@section('title','Profil Resiko')
@section('content')

<section class="auth-wrapper">
    <div class="container text-start">
        <div class="mb-5">
            <h3 class="head-lanjut text-white mb-2">Cari Tau Profil Risiko Kamu, Yuk!</h3>
            <p class="desc-lanjut mb-0">Isi data di bawah ini untuk mengetahui profil risiko kamu</p>
        </div>
        <form method="POST" action="{{ route('profil.resiko.submit') }}">
            @csrf
            <div class="form-group mb-4">
                <label class="form-label text-white mb-2">Berapa usia Anda?</label>
                <select name="q1" class="form-select form-global" required>
                    <option value="">Pilih usia</option>
                    <option value="1">Di atas 60 tahun</option>
                    <option value="2">46 – 60 tahun</option>
                    <option value="3">31 – 45 tahun</option>
                    <option value="4">Di bawah 30 tahun</option>
                </select>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white mb-2">Apa tujuan utama investasi Anda?</label>
                <select name="q2" class="form-select form-global" required>
                    <option value="">Pilih tujuan investasi</option>
                    <option value="1">Perlindungan nilai pokok</option>
                    <option value="2">Pendapatan rutin</option>
                    <option value="3">Pertumbuhan jangka menengah</option>
                    <option value="4">Pertumbuhan agresif jangka panjang</option>
                </select>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white mb-2">Berapa lama rencana investasi Anda?</label>
                <select name="q3" class="form-select form-global" required>
                    <option value="">Pilih jangka waktu</option>
                    <option value="1">Kurang dari 1 tahun</option>
                    <option value="2">1 – 3 tahun</option>
                    <option value="3">3 – 5 tahun</option>
                    <option value="4">Lebih dari 5 tahun</option>
                </select>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white mb-2">Toleransi terhadap fluktuasi nilai investasi?</label>
                <select name="q4" class="form-select form-global" required>
                    <option value="">Pilih toleransi</option>
                    <option value="1">Tidak nyaman</option>
                    <option value="2">Fluktuasi kecil</option>
                    <option value="3">Fluktuasi sedang</option>
                    <option value="4">Nyaman fluktuasi besar</option>
                </select>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white mb-2">Pengalaman investasi Anda?</label>
                <select name="q5" class="form-select form-global" required>
                    <option value="">Pilih pengalaman</option>
                    <option value="1">Tidak ada</option>
                    <option value="2">Deposito</option>
                    <option value="3">Reksa dana / obligasi</option>
                    <option value="4">Saham / derivatif</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-regist w-100 mb-3">Lanjutkan</button>
        </form>
    </div>
</section>

@endsection