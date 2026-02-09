@extends('layouts.app')
@section('title','Penghasilan Nasabah')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="auth-wrapper">
    <div class="container text-start">
        <div class="mb-5">
            <h3 class="head-lanjut text-white mb-2">Financial Profile</h3>
            <p class="desc-lanjut mb-0">Data dibawah ini diwajibkan oleh OJK dan akan kami lindungi kerahasiaannya.</p>
        </div>
        <div class="form-group mb-4">
            <label class="form-label text-white text-form-global mb-2">Penghasilan Per bulan</label>
            <select id="incomeRangeSelect" class="form-control form-global">
                <option value="">Pilih Penghasilan Per bulan</option>
            </select>
        </div>
        <div class="form-group mb-4">
            <label class="form-label text-white text-form-global mb-2">Sumber Dana</label>
            <select id="primaryFundSelect" class="form-control form-global">
                <option value="">Pilih Sumber Dana</option>
            </select>
        </div>
        <div class="form-group mb-4">
            <label class="form-label text-white text-form-global mb-2">Tujuan Investasi</label>
            <select id="investmentObjectiveSelect" class="form-control form-global">
                <option value="">Pilih Tujuan Investasi</option>
            </select>
        </div>
        <button id="btnNext" class="btn btn-primary btn-regist w-100 mb-3">Lanjutkan</button>
    </div>
</section>

<script>
window.routes = {
    incomeRange        : "{{ route('master.incomeRange') }}",
    primaryFundSource  : "{{ route('master.primaryFundSOurce') }}",
    investmentObjective: "{{ route('master.investmentObjective') }}"
};
</script>

<script src="{{ asset('js/penghasilan.js') }}"></script>
@endsection