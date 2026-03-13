@extends('layouts.app')
@section('title','Data Personal')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- @php
    $rt = '';
    $rw = '';
    if (!empty($data['rt_rw'])) {
        [$rt, $rw] = array_pad(explode('/', $data['rt_rw']), 2, '');
    }
@endphp -->

<section class="auth-wrapper">
    <div class="container text-start">
        @include('components.step-header', [
            'step' => 3,
            'back' => route('data.personal')
        ])
        <div class="mb-5">
            <h3 class="head-lanjut text-white mb-2">Cek dan Pastikan Data Sudah Benar Sebelum Melanjutkan</h3>
            <p class="desc-lanjut mb-0">Data dibawah ini diwajibkan oleh OJK dan akan kami lindungi kerahasiaannya.</p>
        </div>
        <form method="POST" action="{{ route('data.personal.submit') }}">
            @csrf
            <input type="hidden" name="process_type" value="{{ $isUpdate ? 'UPDATE' : 'CREATE' }}">
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Nama Sesuai e-KTP</label>
                <input type="text" name="nama" id="nama" value="{{ old('nama', $data['nama'] ?? '') }}" class="form-control form-global" placeholder="Nama Sesuai e-KTP">
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Nomor e-KTP</label>
                <input type="text" name="nik" id="nik" value="{{ old('nik', $data['nik'] ?? '') }}" class="form-control form-global numeric-only" inputmode="numeric" maxlength="16" placeholder="Nomor e-KTP">
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Nama Gadis Ibu Kandung</label>
                <input type="text" name="motherMaidenName" id="motherMaidenName" value="{{ old('motherMaidenName', $data['motherMaidenName'] ?? '') }}" class="form-control form-global alphabet-only"  placeholder="Nama Gadis Ibu Kandung">
            </div>
            <!-- <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Tempat Lahir</label>
                <input type="text" name="tempatLahir" id="tempatLahir" value="{{ old('tempatLahir', $data['tempatLahir'] ?? '') }}" class="form-control form-global alphabet-only" placeholder="Tempat Lahir">
            </div> -->
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Tanggal Lahir</label>
                <input type="date" name="tanggalLahir" id="tanggalLahir" value="{{ old('tanggalLahir', $data['tanggalLahir'] ?? '') }}"  class="form-control form-global" placeholder="Tanggal Lahir">
            </div>
            <!-- <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Jenis Kelamin</label>
                <select name="jenisKelamin" id="genderSelect" class="form-control" data-selected="{{ old('jenisKelamin', $data['jenisKelamin'] ?? '') }}">
                    <option value="">Pilih Jenis Kelamin</option>
                </select>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Agama</label>
                <select name="agama" id="religionSelect" class="form-control" data-selected="{{ old('agama', $data['agama'] ?? '') }}">
                    <option value="">Pilih Agama</option>
                </select>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Pendidikan Terakhir</label>
                <select name="education" id="educationSelect" data-selected="{{ old('education', $data['education'] ?? '') }}" class="form-control form-global">
                    <option value="">Pilih Pendidikan Terakhir</option>
                </select>
            </div> -->
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Status Perkawinan</label>
                <select name="statusPerkawinan" id="maritalSelect" class="form-control" data-selected="{{ old('statusPerkawinan', $data['statusPerkawinan'] ?? '') }}">
                    <option value="">Pilih Status Perkawinan</option>
                </select>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Alamat Sesuai e-KTP</label>
                <input type="text" name="alamat" id="alamat" value="{{ old('alamat', $data['alamat'] ?? '') }}" class="form-control form-global" placeholder="Alamat Sesuai e-KTP">
            </div>
            <!-- <div class="form-group mb-4">
                <div class="row">
                    <div class="col-6">
                        <label class="form-label text-white text-form-global mb-2">RT</label>
                        <input type="text" name="rt" id="rt" class="form-control form-global numeric-only" maxlength="3" value="{{ old('rt', $data['rt'] ?? '') }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label text-white text-form-global mb-2">RW</label>
                        <input type="text" name="rw" id="rw" class="form-control form-global numeric-only" maxlength="3" value="{{ old('rw', $data['rw'] ?? '') }}">
                    </div>
                </div>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Kota</label>
                <select name="kota" id="citySelect" class="form-control" data-selected="{{ old('kota', $data['kota'] ?? '') }}">
                    <option value="">Pilih Kota</option>
                </select>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Kecamatan</label>
                <select name="kecamatan" id="kecamatanSelect" class="form-control" data-selected="{{ old('kecamatan', $data['kecamatan'] ?? '') }}">
                    <option value="">Pilih Kecamatan</option>
                </select>
            </div> -->
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Kelurahan</label>
                <select name="kelurahan" id="kelurahanSelect" class="form-control" data-selected="{{ old('kelurahan', $data['kelurahan'] ?? '') }}">
                    <option value="">Pilih Kelurahan</option>
                </select>
            </div>
            <div class="form-group mb-4">
                <input class="form-check-input me-2" type="checkbox" id="sameAddress">
                <label class="form-label text-white text-form-global mb-0" for="sameAddress">Alamat tinggal sesuai e-KTP</label>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Alamat Domisili Sesuai e-KTP</label>
                <input type="text" name="residenceAddress" id="residenceAddress" value="{{ old('residenceAddress', $data['residenceAddress'] ?? '') }}" class="form-control form-global" placeholder="Alamat Sesuai e-KTP">
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Kecamatan Domisili</label>
                <input type="text" name="residenceKecamatan" id="residenceKecamatan" value="{{ old('residenceKecamatan', $data['residenceKecamatan'] ?? '') }}" class="form-control form-global" placeholder="Kecamatan">
            </div>
            <button type="submit" class="btn btn-primary btn-regist w-100">
                {{ $isUpdate ? 'Ubah' : 'Lanjutkan' }}
            </button>
        </form>
    </div>
</section>

<script>
window.routes = {
    gender        : "{{ route('master.gender') }}",
    religion      : "{{ route('master.religion') }}",
    marital       : "{{ route('master.marital') }}",
    education     : "{{ route('master.education') }}",
    city          : "{{ route('master.city') }}",
    kecamatan     : "{{ route('master.kecamatan') }}",
    kelurahan     : "{{ route('master.kelurahan') }}",
};
window.apiMessage = @json(session('api_message'));
</script>
<script src="{{ asset('js/personal.js') }}"></script>

@endsection