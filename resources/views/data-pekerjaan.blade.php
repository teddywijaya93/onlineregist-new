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
                <label class="form-label text-white text-form-global mb-2">Alamat Perusahaan/Tempat Bekerja</label>
                <textarea rows="3" name="officeAddress" id="officeAddress" class="form-control form-global" placeholder="Tulis Alamat Perusahaan">{{ old('officeAddress', $employmentData['officeAddress'] ?? '') }}</textarea>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Kelurahan Perusahaan/Tempat Bekerja</label>
                <input type="hidden" name="officeCity" id="officeCity" class="form-control form-global" readonly>
                <input type="hidden" name="officeKecamatan" id="officeKecamatan" class="form-control form-global" readonly>
                <div class="custom-select-wrapper">
                    <div class="select-wrapper">
                        <input type="text" name="officeKelurahan" id="officeKelurahan" class="form-control form-global" value="{{ old('officeKelurahan', $employmentData['officeKelurahan'] ?? '') }}" placeholder="Tulis Kelurahan Perusahaan">
                        <div id="officeKelurahanDropdown" class="dropdown-list"></div>
                    </div>
                </div>
            </div>
            <div class="form-group mb-4">  
                <label class="form-label text-white text-form-global mb-2">Kode Pos Perusahaan/Tempat Bekerja</label>
                <input type="text" name="officePostalCode" id="officePostalCode" class="form-control form-global" value="{{ old('officePostalCode', $employmentData['officePostalCode'] ?? '') }}" placeholder="Kode Pos Perusahaan" readonly>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Telepon Perusahaan/Tempat Bekerja</label>
                <input type="text" name="officeTelephone" id="officeTelephone" value="{{ old('officeTelephone', $employmentData['officeTelephone'] ?? '') }}" class="form-control form-global" minlength="13" placeholder="Tulis Telepon Perusahaan">
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Lama Berkerja</label>
                <div class="row">
                    <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                        <div class="input-group">
                            <input type="text" name="employmentDurationYear" id="employmentDurationYear" class="form-control form-global numeric-only" value="{{ old('employmentDurationYear', $employmentData['employmentDurationYear'] ?? '') }}" maxlength="2" placeholder="Tahun">
                            <span class="input-group-text">Tahun</span>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="input-group">
                            <input type="text" name="employmentDurationMonth" id="employmentDurationMonth" class="form-control form-global numeric-only" value="{{ old('employmentDurationMonth', $employmentData['employmentDurationMonth'] ?? '') }}" maxlength="2" placeholder="Bulan">
                            <span class="input-group-text">Bulan</span>
                        </div>
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
    businessline       : "{{ route('master.businessline') }}",
    kelurahan          : "{{ route('master.all.kelurahan') }}"
};
</script>
<script src="{{ asset('js/pekerjaan.js') }}"></script>
<script src="{{ asset('js/kelurahanAjax.js') }}"></script>

@endsection