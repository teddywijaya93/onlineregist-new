@extends('layouts.app')
@section('title','Data Penghasilan')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="auth-wrapper">
    <div class="container text-start">
        @include('components.step-header', [
            'step' => $step,
            'hideBack' => $hideBack
        ])
        <div class="mb-5">
            <h3 class="head-lanjut text-white mb-2">Profil Keuangan</h3>
            <p class="desc-lanjut mb-0"></p>
        </div>
        <form id="financialForm" method="POST" action="{{ route('data.penghasilan.submit') }}">
            @csrf
            <!-- <input type="hidden" name="process_type" value="{{ $isUpdate ? 'UPDATE' : 'CREATE' }}"> -->
            <input type="hidden" name="gender" id="gender" value="{{ $genderId }}">
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Pekerjaan Nasabah</label>
                <div class="select-wrapper">
                    <select name="employmentType" id="employmentSelect" data-selected="{{ old('employmentType', $financialData['employmentType'] ?? '') }}" class="form-control form-global">
                        <option value="">Pilih Pekerjaan</option>
                    </select>
                </div>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Pendidikan Terakhir</label>
                <div class="select-wrapper">
                    <select name="education" id="educationSelect" data-selected="{{ old('education', $financialData['education'] ?? '') }}" class="form-control form-global">
                        <option value="">Pilih Pendidikan</option>
                    </select>
                </div>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Penghasilan Per bulan</label>
                <div class="select-wrapper">
                    <select name="mainIncomeRange" id="incomeRangeSelect" data-selected="{{ old('mainIncomeRange', $financialData['mainIncomeRange'] ?? '') }}" class="form-control form-global">
                        <option value="">Pilih Penghasilan Per Bulan</option>
                    </select>
                </div>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Sumber Dana</label>
                <div class="select-wrapper">
                    <select name="primaryFundSources" id="primaryFundSelect" data-selected="{{ old('primaryFundSources', $financialData['primaryFundSources'] ?? '') }}" class="form-control form-global">
                        <option value="">Pilih Sumber Dana</option>
                    </select>
                </div>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Tujuan Investasi</label>
                <div class="select-wrapper">
                    <select name="investmentObjective" id="investmentObjectiveSelect" data-selected="{{ old('investmentObjective', $financialData['investmentObjective'] ?? '') }}" class="form-control form-global">
                        <option value="">Pilih Tujuan Investasi</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-regist w-100">
                {{ $isUpdate ? 'Ubah' : 'Lanjutkan' }}
            </button>
        </form>
    </div>
</section>

<script>
window.routes = {
    employment         : "{{ route('master.employment') }}",
    education          : "{{ route('master.education') }}",
    incomeRange        : "{{ route('master.incomeRange') }}",
    primaryFundSource  : "{{ route('master.primaryFundSOurce') }}",
    investmentObjective: "{{ route('master.investmentObjective') }}"
};
</script>
<script src="{{ asset('js/penghasilan.js') }}"></script>

@endsection