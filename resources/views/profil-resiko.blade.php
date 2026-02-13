@extends('layouts.app')
@section('title','Profil Resiko')
@section('content')

<section class="auth-wrapper">
    <div class="container text-start">
        @include('components.step-header', [
            'step' => 5,
            'back' => route('data.referensi.perseorangan')
        ])
        <div class="mb-5">
            <h3 class="head-lanjut text-white mb-2">Cari Tau Profil Risiko Kamu, Yuk!</h3>
            <p class="desc-lanjut mb-0">Isi data di bawah ini untuk mengetahui profil risiko kamu</p>
        </div>
        <form method="POST" action="{{ route('profil.resiko.submit') }}">
            @csrf
            <div class="form-group mb-4">
                <label class="form-label text-white mb-2">Berapa usia Anda?</label>
                @php $q1 = old('q1', session('profil_resiko.q1')); @endphp
                <select name="q1" data-selected="{{ old('q1', session('profil_resiko.q1')) }}" class="form-select form-global" required>
                    <option value="">Pilih usia</option>
                    <option value="1" {{ $q1 == 1 ? 'selected' : '' }}>Di atas 60 tahun</option>
                    <option value="2" {{ $q1 == 2 ? 'selected' : '' }}>46 – 60 tahun</option>
                    <option value="3" {{ $q1 == 3 ? 'selected' : '' }}>31 – 45 tahun</option>
                    <option value="4" {{ $q1 == 4 ? 'selected' : '' }}>Di bawah 30 tahun</option>
                </select>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white mb-2">Apa tujuan utama investasi Anda?</label>
                @php $q2 = old('q2', session('profil_resiko.q2')); @endphp
                <select name="q2" data-selected="{{ old('q2', session('profil_resiko.q2')) }}" class="form-select form-global" required>
                    <option value="">Pilih tujuan investasi</option>
                    <option value="1" {{ $q2 == 1 ? 'selected' : '' }}>Perlindungan nilai pokok</option>
                    <option value="2" {{ $q2 == 2 ? 'selected' : '' }}>Pendapatan rutin</option>
                    <option value="3" {{ $q2 == 3 ? 'selected' : '' }}>Pertumbuhan jangka menengah</option>
                    <option value="4" {{ $q2 == 4 ? 'selected' : '' }}>Pertumbuhan agresif jangka panjang</option>
                </select>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white mb-2">Berapa lama rencana investasi Anda?</label>
                @php $q3 = old('q3', session('profil_resiko.q3')); @endphp
                <select name="q3" data-selected="{{ old('q3', session('profil_resiko.q3')) }}" class="form-select form-global" required>
                    <option value="">Pilih jangka waktu</option>
                    <option value="1" {{ $q3 == 1 ? 'selected' : '' }}>Kurang dari 1 tahun</option>
                    <option value="2" {{ $q3 == 2 ? 'selected' : '' }}>1 – 3 tahun</option>
                    <option value="3" {{ $q3 == 3 ? 'selected' : '' }}>3 – 5 tahun</option>
                    <option value="4" {{ $q3 == 4 ? 'selected' : '' }}>Lebih dari 5 tahun</option>
                </select>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white mb-2">Toleransi terhadap fluktuasi nilai investasi?</label>
                @php $q4 = old('q4', session('profil_resiko.q4')); @endphp
                <select name="q4" data-selected="{{ old('q4', session('profil_resiko.q4')) }}" class="form-select form-global" required>
                    <option value="">Pilih toleransi</option>
                    <option value="1" {{ $q4 == 1 ? 'selected' : '' }}>Tidak nyaman</option>
                    <option value="2" {{ $q4 == 2 ? 'selected' : '' }}>Fluktuasi kecil</option>
                    <option value="3" {{ $q4 == 3 ? 'selected' : '' }}>Fluktuasi sedang</option>
                    <option value="4" {{ $q4 == 4 ? 'selected' : '' }}>Nyaman fluktuasi besar</option>
                </select>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white mb-2">Pengalaman investasi Anda?</label>
                @php $q5 = old('q5', session('profil_resiko.q5')); @endphp
                <select name="q5" data-selected="{{ old('q5', session('profil_resiko.q5')) }}" class="form-select form-global" required>
                    <option value="">Pilih pengalaman</option>
                    <option value="1" {{ $q5 == 1 ? 'selected' : '' }}>Tidak ada</option>
                    <option value="2" {{ $q5 == 2 ? 'selected' : '' }}>Deposito</option>
                    <option value="3" {{ $q5 == 3 ? 'selected' : '' }}>Reksa dana / obligasi</option>
                    <option value="4" {{ $q5 == 4 ? 'selected' : '' }}>Saham / derivatif</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-regist w-100 mb-3">Lanjutkan</button>
        </form>
    </div>
</section>

@endsection