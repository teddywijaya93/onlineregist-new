@extends('layouts.app')
@section('title','Data Personal')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="auth-wrapper">
    <div class="container text-start">
        @include('components.step-header', [
            'step' => $step,
            'hideBack' => true
        ])
        <div class="mb-5">
            <h3 class="head-lanjut text-white mb-2">Identitas Diri</h3>
            <p class="desc-lanjut mb-0">Data identitas diri diwajibkan oleh Otoritas Jasa Keuangan (OJK) dan dilindungi kerahasiaannya.</p>
        </div>
        <form id="personalForm" method="POST" action="{{ route('data.personal.submit') }}">
            @csrf
            <input type="hidden" name="process_type" value="{{ $isUpdate ? 'UPDATE' : 'CREATE' }}">
            <input type="hidden" name="birthLocation" value="{{ old('birthLocation', $data['birthLocation'] ?? '') }}">
            <input type="hidden" name="gender" value="{{ old('gender', $data['gender'] ?? '') }}">
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Nama Sesuai e-KTP</label>
                <input type="text" name="name" id="name" class="form-control form-global alphabet-only" value="{{ old('name', $data['name'] ?? '') }}" placeholder="Nama Sesuai e-KTP">
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Nomor e-KTP</label>
                <input type="text" name="identificationNumber" id="identificationNumber" class="form-control form-global numeric-only" value="{{ old('identificationNumber', $data['identificationNumber'] ?? '') }}" maxlength="16" placeholder="Nomor e-KTP">
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Tanggal Lahir</label>
                <div class="date-wrapper">
                    <input type="text" name="dateOfBirth" id="dateOfBirth" class="form-control form-global" placeholder="Pilih Tanggal Lahir" value="{{ old('dateOfBirth', $data['dateOfBirth'] ?? '') }}">
                </div>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Agama</label>
                <div class="select-wrapper">
                    <select name="religion" id="religionSelect" class="form-control form-global" data-selected="{{ $data['religion'] ?? '' }}">
                        <option value="">Pilih Agama</option>
                    </select>
                </div>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Status Perkawinan</label>
                <div class="select-wrapper">
                    <select name="maritalStatus" id="maritalSelect" class="form-control form-global" data-selected="{{ $data['maritalStatus'] ?? '' }}">
                        <option value="">Pilih Status Perkawinan</option>
                    </select>
                </div>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Nama Gadis Ibu Kandung</label>
                <input type="text" name="motherMaidenName" id="motherMaidenName" class="form-control form-global alphabet-only" value="{{ old('motherMaidenName', $data['motherMaidenName'] ?? '') }}" placeholder="Nama Gadis Ibu Kandung">
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Alamat Sesuai e-KTP</label>
                <textarea rows="3" name="address" id="address" class="form-control form-global" placeholder="Alamat Sesuai e-KTP">{{ old('address', $data['address'] ?? '') }}</textarea>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Kelurahan</label>
                <input type="hidden" name="city" id="citySelect" class="form-control form-global" readonly>
                <input type="hidden" name="kecamatan" id="kecamatanSelect" class="form-control form-global" readonly>
                <div class="custom-select-wrapper">
                    <div class="select-wrapper">
                        <input type="text" name="kelurahan" id="kelurahanSearch" class="form-control form-global" value="{{ old('kelurahan', $data['kelurahan'] ?? '') }}" placeholder="Cari Kelurahan">
                        <div id="kelurahanDropdown" class="dropdown-list"></div>
                    </div>
                </div>
            </div>
            <div class="form-group mb-4">  
                <label class="form-label text-white text-form-global mb-2">Kode Pos</label>
                <input type="text" name="postalCode" id="postalCode" class="form-control form-global" value="{{ old('postalCode', $data['postalCode'] ?? '') }}" readonly>
            </div>
            <!-- Checklist -->
            <div class="form-group mb-4">
                <input class="form-check-input me-2" type="checkbox" id="sameAddress">
                <label class="form-label text-white text-form-global mb-0" for="sameAddress">Alamat tinggal sesuai e-KTP</label>
            </div>
            <!-- Checklist -->
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Alamat Domisili Sesuai e-KTP</label>
                <textarea rows="3" name="residenceAddress" id="residenceAddress" class="form-control form-global" placeholder="Alamat Sesuai e-KTP Domisili">{{ old('residenceAddress', $data['residenceAddress'] ?? '') }}</textarea>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Kelurahan Domisili</label>
                <input type="hidden" name="residenceCity" id="residenceCity" class="form-control form-global" readonly>
                <input type="hidden" name="residenceKecamatan" id="residenceKecamatan" class="form-control form-global" readonly>
                <div class="custom-select-wrapper">
                    <div class="select-wrapper">
                        <input type="text" name="residenceKelurahan" id="residenceKelurahan" class="form-control form-global" value="{{ old('residenceKelurahan', $data['residenceKelurahan'] ?? '') }}" placeholder="Kelurahan Domisili">
                        <div id="residenceKelurahanDropdown" class="dropdown-list"></div>
                    </div>
                </div>
            </div>
            <div class="form-group mb-4">  
                <label class="form-label text-white text-form-global mb-2">Kode Pos Domisili</label>
                <input type="text" name="residencePostalCode" id="residencePostalCode" class="form-control form-global" value="{{ old('residencePostalCode', $data['residencePostalCode'] ?? '') }}" placeholder="Kode Pos Domisili" readonly>
            </div>
            <button type="submit" class="btn btn-primary btn-regist w-100">
                {{ $isUpdate ? 'Ubah' : 'Lanjutkan' }}
            </button>
        </form>
    </div>
</section>

<script>
window.routes = {
    marital     : "{{ route('master.marital') }}",
    religion    : "{{ route('master.religion') }}",
    kelurahan   : "{{ route('master.all.kelurahan') }}"
};
</script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="{{ asset('js/personal.js') }}"></script>
<script src="{{ asset('js/kelurahanAjax.js') }}"></script>

@endsection