@extends('layouts.app')
@section('title','Verifikasi KTP')
@section('content')

<section class="auth-wrapper">
    <div class="container text-start">
        @include('components.step-header', [
            'step' => $step,
            'hideBack' => true
        ])
        <div class="mb-5">
            <h3 class="head-lanjut text-white mb-2">Ambil Foto KTP</h3>
            <p class="desc-lanjut mb-0">Perhatikan panduan berikut dalam pengambilan  foto KTP.</p>
        </div>
        <div class="icons mb-4"><img src="{{ asset('storage/ktp_images.png') }}" class="w-100"></div>
        <form method="POST" action="{{ route('verifikasi.ktp.process') }}" enctype="multipart/form-data">
            @csrf
            <input type="file" name="ktp_image" accept="image/*" class="btn btn-outline-primary form-global w-100" required>
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