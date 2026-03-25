@extends('layouts.app')
@section('title','Upload KTP OCR RAW')
@section('content')

<section class="auth-wrapper">
    <div class="container">
        <h3 class="text-white mb-4">Upload Foto e-KTP (RAW OCR)</h3>
        @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('verifikasi.ktp.advance.raw') }}" enctype="multipart/form-data">
            @csrf
            <input type="file" name="ktp_image" class="form-control mb-3" required>
            <div class="mb-3">
                <img id="previewImage" src="" alt="Preview KTP" style="width:100%; height:500px; object-fit:contain; background:#000; display:none;">
            </div>
            <button class="btn btn-primary w-100">Upload & Run OCR</button>
            @if(isset($raw) && $raw)
                <h5 class="text-white">RAW Response:</h5>

                <pre style="color:#FFF;">
                {{ json_encode(json_decode($raw), JSON_PRETTY_PRINT) }}
                </pre>
            @endif
        </form>
    </div>
</section>

<div class="container">
    <h3 class="text-white mb-4">DATA BANK</h3>
    <form method="POST" action="{{ route('bank.account.check') }}">
        @csrf
        <div class="form-group mb-4">
            <label class="form-label text-white text-form-global mb-2">Bank Tujuan Penarikan</label>
            <select name="bank" class="form-control">
                <option value="">Pilih Bank</option>
                <option value="BCA">BCA</option>
                <option value="BNI">BNI</option>
                <option value="BRI">BRI</option>
                <option value="MANDIRI">Mandiri</option>
            </select>
        </div> 
        <div class="form-group mb-4">
            <label class="form-label text-white text-form-global mb-2">Nomor Rekening</label>
            <input type="text" name="nomor_rekening" id="nomor_rekening" class="form-control form-global" placeholder="Tulis Nomor Rekening">
        </div>
        <button class="btn btn-primary w-100">Upload & Run OCR</button>
    </form>
    @if(session('bank_raw'))
        <h5 class="text-white mt-4">Bank Check Response:</h5>

        <pre style="color:#FFF;">
        {{ json_encode(json_decode(session('bank_raw')), JSON_PRETTY_PRINT) }}
        </pre>
    @endif
</div>

<!-- OCR -->
<script>
document.querySelector('input[name="ktp_image"]').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('previewImage');

    if (!file) {
        preview.style.display = 'none';
        return;
    }

    const reader = new FileReader();

    reader.onload = function(event) {
        preview.src = event.target.result;
        preview.style.display = 'block';
    };

    reader.readAsDataURL(file);
});
</script>
@endsection