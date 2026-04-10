@extends('layouts.app')
@section('title','Data Bank')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="auth-wrapper">
    <div class="container text-start">
        @include('components.step-header', [
            'step' => $step,
            'hideBack' => $hideBack
        ])
        <div class="mb-5">
            <h3 class="head-lanjut text-white mb-2">Rekening Bank Pribadi</h3>
            <p class="desc-lanjut mb-0"></p>
        </div>
        <form id="bankForm" method="POST" action="{{ route('data.bank.submit') }}">
            @csrf
            <input type="hidden" name="process_type" value="{{ $isUpdate ? 'UPDATE' : 'CREATE' }}">
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Nama Pemilik Rekening</label>
                <input type="text" name="bankAccountOwner" id="bankAccountOwner" class="form-control form-global" value="{{ session('personalData.name') }}" readonly style="background:#42526D; border:unset;">
            </div>
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Bank Tujuan Penarikan</label>
                <select name="bankName" id="bankSelect" data-selected="{{ old('bankName', $bankData['bankName'] ?? '') }}"     class="form-control form-global">
                    <option value="">Pilih Bank</option>
                </select>
            </div> 
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Nomor Rekening</label>
                <input type="text" name="bankAccountNumber" id="bankAccountNumber" value="{{ old('bankAccountNumber', $bankData['bankAccountNumber'] ?? '') }}" class="form-control form-global" placeholder="Tulis Nomor Rekening">
            </div>
            <button type="submit" class="btn btn-primary btn-regist w-100">
                {{ $isUpdate ? 'Ubah' : 'Lanjutkan' }}
            </button>
        </form>
    </div>
</section>

<script>
window.routes = {
    bank   : "{{ route('master.bank') }}",
};
</script>
<script src="{{ asset('js/bank.js') }}"></script>

@endsection