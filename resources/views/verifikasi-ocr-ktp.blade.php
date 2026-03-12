@extends('layouts.app')
@section('title','Verifikasi KTP')
@section('content')

<section class="auth-wrapper">
    <div class="container text-start">
        @include('components.step-header', [
            'step' => 1,
        ])
        <div class="mb-5">
            <h3 class="head-lanjut text-white mb-2">Ambil Foto KTP</h3>
            <p class="desc-lanjut mb-0">Perhatikan panduan berikut dalam pengambilan  foto KTP.</p>
        </div>
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