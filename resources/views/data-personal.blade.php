@extends('layouts.app')
@section('title','Data Personal')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
@php
    $rt = '';
    $rw = '';
    if (!empty($data['rt_rw'])) {
        [$rt, $rw] = array_pad(explode('/', $data['rt_rw']), 2, '');
    }
@endphp

<section class="auth-wrapper">
    <div class="container text-start">
        @include('components.step-header', [
            'step' => 1,
            'back' => route('data.personal')
        ])
        <div class="mb-5">
            <h3 class="head-lanjut text-white mb-2">Cek dan Pastikan Data Sudah Benar Sebelum Melanjutkan</h3>
            <p class="desc-lanjut mb-0">Data dibawah ini diwajibkan oleh OJK dan akan kami lindungi kerahasiaannya.</p>
        </div>
        <form method="POST" action="{{ route('data.personal.submit') }}">
            @csrf
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Nama Sesuai e-KTP</label>
                <input type="text" name="nama" id="nama" value="{{ $data['nama'] ?? '' }}" class="form-control form-global" placeholder="Nama Sesuai e-KTP">
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Nomor e-KTP</label>
                <input type="text" name="nik" id="nik" value="{{ $data['nik'] ?? '' }}" class="form-control form-global" placeholder="Nomor e-KTP">
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Nama Gadis Ibu Kandung</label>
                <input type="text" name="motherMaidenName" id="motherMaidenName" value="{{ $data['motherMaidenName'] ?? '' }}" class="form-control form-global"  placeholder="Nama Gadis Ibu Kandung">
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Tempat Lahir</label>
                <input type="text" name="tempatLahir" id="tempatLahir" value="{{ $data['tempatLahir'] ?? '' }}" class="form-control form-global" placeholder="Tempat Lahir">
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Tanggal Lahir</label>
                <input type="date" name="tanggalLahir" id="tanggalLahir" value="{{ $data['tanggalLahir'] ?? '' }}"  class="form-control form-global" placeholder="Tanggal Lahir">
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Jenis Kelamin</label>
                <select name="jenisKelamin" id="genderSelect" class="form-control" data-selected="{{ $data['jenisKelamin'] ?? '' }}">
                    <option value="">Pilih Jenis Kelamin</option>
                </select>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Agama</label>
                <select name="agama" id="religionSelect" class="form-control" data-selected="{{ $data['agama'] ?? '' }}">
                    <option value="">Pilih Agama</option>
                </select>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Pendidikan Terakhir</label>
                <select name="education" id="educationSelect" data-selected="{{ $data['education'] ?? '' }}" class="form-control form-global">
                    <option value="">Pilih Pendidikan Terakhir</option>
                </select>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Status Perkawinan</label>
                <select name="statusPerkawinan" id="maritalSelect" class="form-control" data-selected="{{ $data['statusPerkawinan   '] ?? '' }}">
                    <option value="">Pilih Status Perkawinan</option>
                </select>
            </div>
           
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Alamat Sesuai e-KTP</label>
                <input type="text" name="alamat" id="alamat" value="{{ $data['alamat'] ?? '' }}" class="form-control form-global" placeholder="Alamat Sesuai e-KTP">
            </div>
            <div class="form-group mb-4">
                <div class="row">
                    <div class="col-6">
                        <label class="form-label text-white text-form-global mb-2">RT</label>
                        <input type="text" name="rt" id="rt" class="form-control" value="{{ $rt }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label text-white text-form-global mb-2">RW</label>
                        <input type="text" name="rw" id="rw" class="form-control" value="{{ $rw }}">
                    </div>
                </div>
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Kota</label>
                <input type="text" name="kota" id="kota" value="{{ $data['kota'] ?? '' }}" class="form-control form-global" placeholder="Kota">
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Kelurahan</label>
                <input type="text" name="kelurahan" id="kelurahan" value="{{ $data['kelurahan'] ?? '' }}" class="form-control form-global" placeholder="Kelurahan">
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Kecamatan</label>
                <input type="text" name="kecamatan" id="kecamatan" value="{{ $data['kecamatan'] ?? '' }}" class="form-control form-global" placeholder="Kecamatan">
            </div>
            <button type="submit" id="btnNext" class="btn btn-primary btn-regist w-100 mb-3">Lanjutkan</button>
        </form>
    </div>
</section>

<script>
window.routes = {
    gender        : "{{ route('master.gender') }}",
    religion      : "{{ route('master.religion') }}",
    marital       : "{{ route('master.marital') }}",
    education     : "{{ route('master.education') }}",
};
</script>
<script src="{{ asset('js/personal.js') }}"></script>

@endsection