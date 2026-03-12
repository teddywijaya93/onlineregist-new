@extends('layouts.app')
@section('title','Pekerjaan Nasabah')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="auth-wrapper">
    <div class="container text-start">
        @include('components.step-header', [
            'step' => 4,
            'back' => route('data.personal')
        ])
        <div class="mb-5">
            <h3 class="head-lanjut text-white mb-2">Pekerjaan Kamu Saat Ini</h3>
            <p class="desc-lanjut mb-0">Data dibawah ini diwajibkan oleh OJK dan akan kami lindungi kerahasiaannya.</p>
        </div>
        <form method="POST" action="{{ route('data.pekerjaan.submit') }}">
            @csrf
            <input type="hidden" name="process_type" value="{{ $isUpdate ? 'UPDATE' : 'CREATE' }}">
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Pekerjaan Nasabah</label>
                <input type="hidden" id="genderSelect" value="{{ session('personalData.jenisKelamin') }}">
                <select name="employmentType" id="employmentSelect" data-selected="{{ old('employmentType', $employmentData['employmentType'] ?? '') }}" class="form-control form-global">
                    <option value="">Pilih Pekerjaan Nasabah</option>
                </select>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Pendidikan Terakhir</label>
                <select name="education" id="educationSelect" data-selected="{{ old('employmentType', $employmentData['education'] ?? '') }}" class="form-control form-global">
                    <option value="">Pilih Pendidikan Terakhir</option>
                </select>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Penghasilan Per bulan</label>
                <select name="mainIncomeRange" id="incomeRangeSelect" data-selected="{{ old('mainIncomeRange', $financialData['mainIncomeRange'] ?? '') }}" class="form-control form-global">
                    <option value="">Pilih Penghasilan Per bulan</option>
                </select>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Sumber Dana</label>
                <select name="primaryFundSources" id="primaryFundSelect" data-selected="{{ old('primaryFundSources', $financialData['primaryFundSources'] ?? '') }}" class="form-control form-global">
                    <option value="">Pilih Sumber Dana</option>
                </select>
            </div>
            <!-- <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Nama Perusahaan/Tempat Bekerja</label>
                <input type="text" name="employer" id="employer" value="{{ old('employer', $employmentData['employer'] ?? '') }}" class="form-control form-global alphabet-only" placeholder="Tulis Nama Perusahaan">
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Jabatan</label>
                <select name="employmentPosition" id="positionSelect" data-selected="{{ old('employmentPosition', $employmentData['employmentPosition'] ?? '') }}" class="form-control form-global">
                    <option value="">Pilih Jabatan</option>
                </select>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Bidang Usaha</label>
                <select name="businessLine" id="businesslineSelect" data-selected="{{ old('businessLine', $employmentData['businessLine'] ?? '') }}" class="form-control form-global">
                    <option value="">Pilih Bidang Usaha</option>
                </select>
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
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Alamat Perusahaan</label>
                <input type="text" name="officeAddress" id="officeAddress" value="{{ old('officeAddress', $employmentData['officeAddress'] ?? '') }}" class="form-control form-global alphabet-only" placeholder="Tulis Alamat Perusahaan">
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Kode Pos</label>
                <input type="text" name="officePostalCode" id="officePostalCode" value="{{ old('officePostalCode', $employmentData['officePostalCode'] ?? '') }}" class="form-control form-global numeric-only" maxlength="5" placeholder="Tulis Kode Pos">
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Telepon Kantor</label>
                <input type="text" name="officeTelephone" id="officeTelephone" value="{{ old('officeTelephone', $employmentData['officeTelephone'] ?? '') }}" class="form-control form-global numeric-only" maxlength="13" placeholder="Tulis Telepon Kantor">
            </div> -->
            <button type="submit" class="btn btn-primary btn-regist w-100">
                {{ $isUpdate ? 'Ubah' : 'Lanjutkan' }}
            </button>
        </form>
    </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const message = @json(session('api_message'));
    const status  = @json(session('api_status'));

    if (message) {
        let iconType = 'info';
        if (status === true || status === 'true') {
            iconType = 'success';
        } else if (status === false || status === 'false') {
            iconType = 'warning';
        }
        Swal.fire({
            icon: iconType,
            title: 'Informasi',
            text: message,
            confirmButtonColor: '#3085d6'
        });
    }
});
window.routes = {
    education          : "{{ route('master.education') }}",
    incomeRange        : "{{ route('master.incomeRange') }}",
    primaryFundSource  : "{{ route('master.primaryFundSOurce') }}",
    employment         : "{{ route('master.employment') }}",
    position           : "{{ route('master.position') }}",
    businessline       : "{{ route('master.businessline') }}"
};
</script>
<script src="{{ asset('js/pekerjaan.js') }}"></script>
<script src="{{ asset('js/penghasilan.js') }}"></script>

@endsection