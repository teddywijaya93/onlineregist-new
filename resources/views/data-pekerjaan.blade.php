@extends('layouts.app')
@section('title','Data Pekerjaan')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="auth-wrapper">
    <div class="container text-start">
        @include('components.step-header', [
            'step' => $step,
            'hideBack' => $hideBack
        ])
        <div class="mb-5">
            <h3 class="head-lanjut text-white mb-2">Data Pekerjaan</h3>
            <p class="desc-lanjut mb-0"></p>
        </div>
        <form id="employmentForm" method="POST" action="{{ route('data.pekerjaan.submit') }}">
            @csrf
            <!-- <input type="hidden" name="process_type" value="{{ $isUpdate ? 'UPDATE' : 'CREATE' }}"> -->
            <input type="hidden" name="employmentType" id="employmentType" value="{{ old('employmentType', session('financialData.employmentType')) }}">
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Nama Perusahaan/Tempat Bekerja</label>
                <input type="text" name="employer" id="employer" value="{{ old('employer', $employmentData['employer'] ?? '') }}" class="form-control form-global" placeholder="Tulis Nama Perusahaan">
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Bidang Usaha</label>
                <div class="select-wrapper">
                    <select name="businessLine" id="businesslineSelect" data-selected="{{ old('businessLine', $employmentData['businessLine'] ?? '') }}" class="form-control form-global">
                        <option value="">Pilih Bidang Usaha</option>
                    </select>
                </div>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Jabatan</label>
                <div class="select-wrapper">
                    <select name="employmentPosition" id="positionSelect" data-selected="{{ old('employmentPosition', $employmentData['employmentPosition'] ?? '') }}" class="form-control form-global">
                        <option value="">Pilih Jabatan</option>
                    </select>
                </div>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Alamat Perusahaan</label>
                <textarea rows="3" name="officeAddress" id="officeAddress" class="form-control form-global" placeholder="Tulis Alamat Perusahaan">{{ old('officeAddress', $employmentData['officeAddress'] ?? '') }}</textarea>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Telepon Kantor</label>
                <input type="text" name="officeTelephone" id="officeTelephone" value="{{ old('officeTelephone', $employmentData['officeTelephone'] ?? '') }}" class="form-control form-global numeric-only" minlength="13" placeholder="Tulis Telepon Kantor">
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Lama Berkerja</label>
                <div class="row">
                    <div class="col-6">
                        <input type="text" name="employmentDurationYear" id="employmentDurationYear" value="{{ old('employmentDurationYear', $employmentData['employmentDurationYear'] ?? '') }}" class="form-control form-global numeric-only" maxlength="2" placeholder="Tahun">
                    </div>
                    <div class="col-6">
                        <input type="text" name="employmentDurationMonth" id="employmentDurationMonth" value="{{ old('employmentDurationMonth', $employmentData['employmentDurationMonth'] ?? '') }}" class="form-control form-global numeric-only" maxlength="2" placeholder="Bulan">
                    </div>
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
    position           : "{{ route('master.position') }}",
    businessline       : "{{ route('master.businessline') }}"
};
</script>
<script src="{{ asset('js/pekerjaan.js') }}"></script>

@endsection