@extends('layouts.app')
@section('title','Penghasilan Nasabah')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="auth-wrapper">
    <div class="container text-start">
        @include('components.step-header', [
            'step' => 4,
            'back' => route('data.penghasilan')
        ])
        <div class="mb-5">
            <h3 id="referenceTitle" class="head-lanjut text-white mb-2">Data Referensi</h3>
            <p class="desc-lanjut mb-0">Tenang, kontak ini disimpan untuk keadaan darurat dan hanya akan dihubungi bila diperlukan.</p>
        </div>
        <form method="POST" action="{{ route('data.referensi.perseorangan.submit') }}">
            @csrf
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Hubungan dengan Nasabah</label>
                <select name="referenceRelation" id="referenceSelect" data-selected="{{ old('referenceRelation', session('referensi_perseorangan.referenceRelation')) }}" class="form-control form-global">
                    <option value="">Pilih Hubungan dengan relasi</option>
                </select>
            </div>
            <div id="formSpouse" style="display:none;">
                <!-- Perseorangan -->
                @include('partials.data-referensi-spouse')
            </div>
            <div id="formFamily" style="display:none;">
                <!-- Non Perseorangan -->
                @include('partials.data-referensi-family')
            </div>
            <button id="btnNext" class="btn btn-primary btn-regist w-100 mb-3">Lanjutkan</button>
        </form>
    </div>
</section>

<script>
window.routes = {
    referenceRelation           : "{{ route('master.referenceRelation') }}",
    jenis_kelamin_relasi        : "{{ route('master.gender') }}",
    status_perkawinan_relasi    : "{{ route('master.marital') }}",
};
</script>
<script src="{{ asset('js/referenceRelation.js') }}"></script>

@endsection