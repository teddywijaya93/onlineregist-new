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
        <form method="POST" action="">
            @csrf
            <div class="form-group mb-4">
                <label class="form-label text-white text-form-global mb-2">Hubungan dengan Nasabah</label>
                <select name="referenceRelation" id="referenceSelect" data-selected="{{ old('referenceRelation', session('referensi_perseorangan.referenceRelation')) }}" class="form-control form-global">
                    <option value="">Pilih Hubungan dengan relasi</option>
                </select>
            </div>
            <button id="btnNext" class="btn btn-primary btn-regist w-100 mb-3">Lanjutkan</button>
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
    referenceRelation           : "{{ route('master.referenceRelation') }}",
    jenis_kelamin_relasi        : "{{ route('master.gender') }}",
    status_perkawinan_relasi    : "{{ route('master.marital') }}",
};
</script>
<script src="{{ asset('js/referenceRelation.js') }}"></script>

@endsection