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
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Pekerjaan Nasabah</label>
                <select name="employment" id="employmentSelect" data-selected="{{ old('employment', session('employment_data.employment')) }}" class="form-control form-global">
                    <option value="">Pilih Pekerjaan Nasabah</option>
                </select>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Nama Perusahaan/Tempat Bekerja</label>
                <input type="text" name="company_name" id="company_name" value="{{ old('company_name', session('employment_data.company_name')) }}" class="form-control form-global" placeholder="Tulis Nama Perusahaan">
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Jabatan</label>
                <select name="position" id="positionSelect" data-selected="{{ old('position', session('employment_data.position')) }}" class="form-control form-global">
                    <option value="">Pilih Jabatan</option>
                </select>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Bidang Usaha</label>
                <select name="businessline" id="businesslineSelect" data-selected="{{ old('businessline', session('employment_data.businessline')) }}" class="form-control form-global">
                    <option value="">Pilih Bidang Usaha</option>
                </select>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Lama Berkerja</label>
                <div class="row">
                    <div class="col-6">
                        <input type="text" name="work_year" id="work_year" value="{{ old('work_year', session('employment_data.work_year')) }}" class="form-control form-global" placeholder="Tahun">
                    </div>
                    <div class="col-6">
                        <input type="text" name="work_month" id="work_month" value="{{ old('work_month', session('employment_data.work_month')) }}" class="form-control form-global" placeholder="Bulan">
                    </div>
                </div>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Alamat Perusahaan</label>
                <input type="text" name="office_address" id="office_address" value="{{ old('office_address', session('employment_data.office_address')) }}" class="form-control form-global" placeholder="Tulis Alamat Perusahaan">
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Kode Pos</label>
                <input type="text" name="office_postal_code" id="office_postal_code" value="{{ old('office_postal_code', session('employment_data.office_postal_code')) }}" class="form-control form-global" placeholder="Tulis Kode Pos">
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Telepon Kantor</label>
                <input type="text" name="office_phone" id="office_phone" value="{{ old('office_phone', session('employment_data.office_phone')) }}" class="form-control form-global" placeholder="Tulis Telepon Kantor">
            </div>
            <button type="submit" id="btnNext" class="btn btn-primary btn-regist w-100 mb-3">Lanjutkan</button>
        </form>
    </div>
</section>

<script>
window.routes = {
    employment   : "{{ route('master.employment') }}",
    position     : "{{ route('master.position') }}",
    businessline : "{{ route('master.businessline') }}"
};
</script>
<script src="{{ asset('js/pekerjaan.js') }}"></script>

@endsection