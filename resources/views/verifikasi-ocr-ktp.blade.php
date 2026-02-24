@extends('layouts.app')
@section('title','Verifikasi KTP')
@section('content')

<section class="auth-wrapper">
    <div class="container">
        <h3 class="text-white mb-4">Upload Foto e-KTP</h3>
        
        @if ($errors->any())
            <p class="text-danger">{{ $errors->first() }}</p>
        @endif

        <form method="POST" action="{{ route('verifikasi.ktp.process') }}" enctype="multipart/form-data">
            @csrf
            <input type="file" name="ktp_image" accept="image/*" class="form-control mb-3" required>
            <div class="mb-3">
                <img id="previewImage" src="" alt="Preview KTP" style="width:100%; height:500px; object-fit:contain; background:#000; display:none;">
            </div>
            <button id="btnNext" class="btn btn-primary btn-regist w-100 mb-3">Lanjutkan</button>
        </form>
    </div>
</section>

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