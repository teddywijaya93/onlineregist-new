@extends('layouts.app')
@section('title','Data Universitas')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="auth-wrapper">
    <div class="container text-start">
        @include('components.step-header', [
            'step' => 7,
            'back' => route('data.profil.resiko')
        ])
        <div class="mb-5">
            <h3 class="head-lanjut text-white mb-2">Data Unversitas/h3>
            <p class="desc-lanjut mb-0"></p>
        </div>
        <form method="POST" action="">
            @csrf
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Nama Universitas</label>
                <input type="text" name="nama" id="nama" class="form-control form-global" value="{{ session('personalData.nama') }}" disabled style="background:#42526D; border:unset;">
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Alamat Universitas</label>
                <textarea rows="3" name="officeAddress" id="officeAddress" class="form-control form-global" placeholder="Alamat Sesuai e-KTP Domisili">{{ old('officeAddress', $data['officeAddress'] ?? '') }}</textarea>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Lama Kuliah</label>
                <div class="row">
                    <div class="col-6">
                        <input type="text" name="employmentDurationYear" id="employmentDurationYear" value="{{ old('employmentDurationYear', $employmentData['employmentDurationYear'] ?? '') }}" class="form-control form-global numeric-only" maxlength="2" placeholder="Tahun">
                    </div>
                    <div class="col-6">
                        <input type="text" name="employmentDurationMonth" id="employmentDurationMonth" value="{{ old('employmentDurationMonth', $employmentData['employmentDurationMonth'] ?? '') }}" class="form-control form-global numeric-only" maxlength="2" placeholder="Bulan">
                    </div>
                </div>
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