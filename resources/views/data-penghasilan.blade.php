@extends('layouts.app')
@section('title','Penghasilan Nasabah')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="auth-wrapper">
    <div class="container text-start">
        @include('components.step-header', [
            'step' => 5,
            'back' => route('data.pekerjaan')
        ])
        <div class="mb-5">
            <h3 class="head-lanjut text-white mb-2">Financial Profile</h3>
            <p class="desc-lanjut mb-0">Data dibawah ini diwajibkan oleh OJK dan akan kami lindungi kerahasiaannya.</p>
        </div>
        <form method="POST" action="{{ route('data.penghasilan.submit') }}">
            @csrf
            <input type="hidden" name="process_type" value="{{ $isUpdate ? 'UPDATE' : 'CREATE' }}">
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
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Tujuan Investasi</label>
                <select name="investmentObjective" id="investmentObjectiveSelect" data-selected="{{ old('investmentObjective', $financialData['investmentObjective'] ?? '') }}" class="form-control form-global">
                    <option value="">Pilih Tujuan Investasi</option>
                </select>
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
    incomeRange        : "{{ route('master.incomeRange') }}",
    primaryFundSource  : "{{ route('master.primaryFundSOurce') }}",
    investmentObjective: "{{ route('master.investmentObjective') }}"
};
</script>
<script src="{{ asset('js/penghasilan.js') }}"></script>

@endsection