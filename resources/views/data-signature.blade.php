@extends('layouts.app')
@section('title','Upload Signature')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="auth-wrapper">
    <div class="container text-start">
        @include('components.step-header', [
            'step' => $step,
            'hideBack' => $hideBack
        ])
        <div class="mb-5">
            <h3 class="head-lanjut text-white text-center mb-2">Spesimen Tanda Tangan</h3>
            <p class="desc-lanjut mb-0"></p>
        </div>
        <form method="POST" action="{{ route('data.signature.submit') }}" id="signatureForm">
            @csrf
            <input type="hidden" name="image" id="imageInput">
            <input type="file" id="fileInput" accept="image/*" hidden>
            <div class="signature-card text-center mb-4">
                <!-- Preview -->
                <div id="previewMode"><img src="{{ asset('storage/ttd-area.svg') }}" class="w-100"></div>

                <!-- Draw -->
                <div id="drawMode" class="d-none">
                    <div class="signature-pad-wrapper"><canvas id="signature-pad"></canvas></div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-6">
                    <button type="button" id="btnLeft" class="btn btn-primary btn-take w-100 mb-3">Ambil dari galeri</button>
                </div>
                <div class="col-12 col-lg-6">
                    <button type="button" id="btnRight" class="btn btn-primary btn-regist w-100 mb-3">Mulai ambil tanda tangan</button>
                </div>
            </div>
        </form>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script src="{{ asset('js/signature.js') }}"></script>
@endsection