@extends('layouts.app')
@section('title','Pekerjaan Nasabah')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="auth-wrapper">
    <div class="container text-start">
        <div class="mb-5">
            <h3 class="head-lanjut text-white mb-2">Pekerjaan Kamu Saat Ini</h3>
            <p class="desc-lanjut mb-0">Data dibawah ini diwajibkan oleh OJK dan akan kami lindungi kerahasiaannya.</p>
        </div>
        <div class="form-group mb-4">
            <label class="form-label text-white text-form-global mb-2">Pekerjaan Nasabah</label>
            <select id="employmentSelect" class="form-control form-global">
                <option value="">Pilih Pekerjaan Nasabah</option>
            </select>
        </div>
        <div class="form-group mb-4">
            <label class="form-label text-white text-form-global mb-2">Nama Perusahaan/Tempat Bekerja</label>
            <input type="text" id="username" class="form-control form-global" placeholder="Tulis Nama Perusahaan">
        </div>
        <div class="form-group mb-4">
            <label class="form-label text-white text-form-global mb-2">Jabatan</label>
            <select id="positionSelect" class="form-control form-global">
                <option value="">Pilih Jabatan</option>
            </select>
        </div>
        <div class="form-group mb-4">
            <label class="form-label text-white text-form-global mb-2">Bidang Usaha</label>
            <select id="businesslineSelect" class="form-control form-global">
                <option value="">Pilih Bidang Usaha</option>
            </select>
        </div>
        <div class="form-group mb-4">
            <label class="form-label text-white text-form-global mb-2">Lama Berkerja</label>
            <div class="row">
                <div class="col-6">
                    <input type="text" id="" class="form-control form-global" placeholder="Tahun">
                </div>
                <div class="col-6">
                    <input type="text" id="" class="form-control form-global" placeholder="Bulan">
                </div>
            </div>
        </div>
        <div class="form-group mb-4">
            <label class="form-label text-white text-form-global mb-2">Alamat Perusahaan</label>
            <input type="text" id="" class="form-control form-global" placeholder="Tulis Alamat Perusahaan">
        </div>
        <div class="form-group mb-4">
            <label class="form-label text-white text-form-global mb-2">Kode Pos</label>
            <input type="text" id="" class="form-control form-global" placeholder="Tulis Kode Pos">
        </div>
        <div class="form-group mb-4">
            <label class="form-label text-white text-form-global mb-2">Telepon Kantor</label>
            <input type="text" id="" class="form-control form-global" placeholder="Tulis Telepon Kantor">
        </div>
        <button id="btnNext" class="btn btn-primary btn-regist w-100 mb-3">Lanjutkan</button>
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