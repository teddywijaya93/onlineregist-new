@extends('layouts.app')
@section('title','Pekerjaan Nasabah')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="auth-wrapper">
    <div class="container text-start">
        @include('components.step-header', [
            'step' => 2,
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
                <select name="employment" id="employmentSelect" data-selected="{{ old('employment', $employmentData['employmentType'] ?? '') }}" class="form-control form-global">
                    <option value="">Pilih Pekerjaan Nasabah</option>
                </select>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Nama Perusahaan/Tempat Bekerja</label>
                <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $employmentData['employer'] ?? '') }}" class="form-control form-global" placeholder="Tulis Nama Perusahaan">
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Jabatan</label>
                <select name="position" id="positionSelect" data-selected="{{ old('position', $employmentData['employmentPosition'] ?? '') }}" class="form-control form-global">
                    <option value="">Pilih Jabatan</option>
                </select>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Bidang Usaha</label>
                <select name="businessline" id="businesslineSelect" data-selected="{{ old('businessline', $employmentData['businessLine'] ?? '') }}" class="form-control form-global">
                    <option value="">Pilih Bidang Usaha</option>
                </select>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Lama Berkerja</label>
                <div class="row">
                    <div class="col-6">
                        <input type="text" name="work_year" id="work_year" value="{{ old('work_year', $employmentData['employmentDurationYear'] ?? '') }}" class="form-control form-global" placeholder="Tahun">
                    </div>
                    <div class="col-6">
                        <input type="text" name="work_month" id="work_month" value="{{ old('work_month', $employmentData['employmentDurationMonth'] ?? '') }}" class="form-control form-global" placeholder="Bulan">
                    </div>
                </div>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Alamat Perusahaan</label>
                <input type="text" name="office_address" id="office_address" value="{{ old('office_address', $employmentData['officeAddress'] ?? '') }}" class="form-control form-global" placeholder="Tulis Alamat Perusahaan">
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Kode Pos</label>
                <input type="text" name="office_postal_code" id="office_postal_code" value="{{ old('office_postal_code', $employmentData['officePostalCode'] ?? '') }}" class="form-control form-global" placeholder="Tulis Kode Pos">
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Telepon Kantor</label>
                <input type="text" name="office_phone" id="office_phone" value="{{ old('office_phone', $employmentData['officeTelephone'] ?? '') }}" class="form-control form-global" placeholder="Tulis Telepon Kantor">
            </div>
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
    employment   : "{{ route('master.employment') }}",
    position     : "{{ route('master.position') }}",
    businessline : "{{ route('master.businessline') }}"
};
</script>
<script src="{{ asset('js/pekerjaan.js') }}"></script>

@endsection