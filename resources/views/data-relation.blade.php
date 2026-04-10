@extends('layouts.app')
@section('title','Data Relasi')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="auth-wrapper">
    <div class="container text-start">
        @include('components.step-header', [
            'step' => $step,
            'hideBack' => $hideBack
        ])
        <div class="mb-5">
            <h3 id="referenceTitle" class="head-lanjut text-white mb-2">Data Referensi</h3>
            <p class="desc-lanjut mb-0"></p>
        </div>
        <form id="relationForm" method="POST" action="{{ route('data.relation.submit') }}">
            @csrf
            <input type="hidden" name="process_type" value="{{ $isUpdate ? 'UPDATE' : 'CREATE' }}">
            <input type="hidden" name="gender" id="gender" value="{{ $genderId }}">
            <input type="hidden" name="maritalStatus" id="maritalStatus" value="{{ $maritalId }}">
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Nama Referensi Perorangan</label>
                <input type="text" name="beneficiaryName" id="beneficiaryName" class="form-control form-global alphabet-only" value="{{ old('beneficiaryName', $relationData['beneficiaryName'] ?? '') }}" placeholder="Tulis Nama Lengkap Referensi">
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Hubungan dengan Nasabah</label>
                <select name="beneficiaryRelation" id="beneficiaryRelationSelect" data-selected="{{ old('beneficiaryRelation', $relationData['beneficiaryRelation'] ?? '') }}" class="form-control form-global">
                    <option value="">Pilih Hubungan</option>
                </select>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Upload KTP Referensi Perorangan</label>
                <input type="hidden" name="beneficiaryKtpFileName" id="beneficiaryKtpFileName">
                <input type="hidden" name="beneficiaryKtpImage" id="beneficiaryKtpImage">

                <!-- Button Upload -->
                <div class="upload-box" id="uploadKtpBox">
                    <input type="file" id="ktpFileInput" accept="image/*" hidden>
                    <button type="button" class="btn btn-outline-primary form-global w-100" id="btnUploadKtp">Upload KTP</button>
                </div>

                <!-- Preview -->
                <div id="ktpPreviewWrapper" class="mt-3 d-none">
                    <img id="ktpPreview" style="width:100%; border-radius:8px;" />
                    <button type="button" class="btn btn-sm btn-danger mt-2 w-100" id="removeKtp">Hapus</button>
                </div>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Pekerjaan Referensi Perorangan</label>
                <select name="beneficiaryEmploymentPosition" id="employmentSelect" data-selected="{{ old('beneficiaryEmploymentPosition', $relationData['beneficiaryEmploymentPosition'] ?? '') }}" class="form-control form-global">
                    <option value="">Pilih Pekerjaan</option>
                </select>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Bidang Usaha Referensi Perorangan</label>
                <select name="beneficiaryOwnerBusinessLine" id="businesslineSelect" data-selected="{{ old('beneficiaryOwnerBusinessLine', $relationData['beneficiaryOwnerBusinessLine'] ?? '') }}" class="form-control form-global">
                    <option value="">Pilih Bidang Usaha</option>
                </select>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Nama Perusahaan/Institusi/Pendidikan</label>
                <input type="text" name="beneficiaryOwnerEmployerName" id="beneficiaryOwnerEmployerName" class="form-control form-global alphabet-only" value="{{ old('beneficiaryOwnerEmployerName', $relationData['beneficiaryOwnerEmployerName'] ?? '') }}" placeholder="Tulis Nama Institusi">
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Alamat Kantor Referensi Perorangan</label>
                <textarea rows="3" name="beneficiaryOwnerOfficeAddress" id="beneficiaryOwnerOfficeAddress" class="form-control form-global alphabet-only" placeholder="Tulis Alamat Kantor Institusi">{{ old('beneficiaryOwnerOfficeAddress', $relationData['beneficiaryOwnerOfficeAddress'] ?? '') }}</textarea>
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
    businessline       : "{{ route('master.businessline') }}"
};
</script>
<script src="{{ asset('js/relation.js') }}"></script>

@endsection