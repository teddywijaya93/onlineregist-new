@extends('layouts.app')
@section('title','Data Universitas')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="auth-wrapper">
    <div class="container text-start">
        @include('components.step-header', [
            'step' => $step,
            'hideBack' => $hideBack
        ])
        <div class="mb-5">
            <h3 class="head-lanjut text-white mb-2">Data Unversitas</h3>
            <p class="desc-lanjut mb-0"></p>
        </div>
        <form id="universityForm" method="POST" action="{{ route('data.universitas.submit') }}">
            @csrf
            <!-- <input type="hidden" name="process_type" value="{{ $isUpdate ? 'UPDATE' : 'CREATE' }}"> -->
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Nama Universitas</label>
                <input type="text" name="employer" id="employer" class="form-control form-global" value="{{ old('employer', $universitasData['employer'] ?? '') }}" placeholder="Tulis Nama Universitas">
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Alamat Universitas</label>
                <textarea rows="3" name="officeAddress" id="officeAddress" class="form-control form-global" placeholder="Masukan Alamat Universitas">{{ old('officeAddress', $universitasData['officeAddress'] ?? '') }}</textarea>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Lama Kuliah</label>
                <div class="row">
                    <div class="col-6">
                        <input type="text" name="employmentDurationYear" id="employmentDurationYear" class="form-control form-global numeric-only" value="{{ old('employmentDurationYear', ($universitasData['employmentDurationYear'] ?? '') ?: '') }}" maxlength="2" placeholder="Tahun">
                    </div>
                    <div class="col-6">
                        <input type="text" name="employmentDurationMonth" id="employmentDurationMonth" class="form-control form-global numeric-only" value="{{ old('employmentDurationMonth', ($universitasData['employmentDurationMonth'] ?? '') ?: '') }}" maxlength="2" placeholder="Bulan">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-regist w-100">
                {{ $isUpdate ? 'Ubah' : 'Lanjutkan' }}
            </button>
        </form>
    </div>
</section>

<script src="{{ asset('js/university.js') }}"></script>

@endsection