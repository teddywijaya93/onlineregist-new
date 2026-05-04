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
            <div class="row mb-3">
                <div class="col-12 mobile-only mb-3">
                    <button type="button" id="btnCamera" class="btn btn-primary btn-regist w-100">Gunakan Kamera</button>
                </div>
                <div class="col-12 mb-3">
                    <label class="btn btn-primary btn-regist w-100 m-0 text-center">
                        Upload Dari Galeri
                        <input type="file" name="ktp_image" id="fileInput" accept="image/*" hidden required>
                    </label>
                </div>
            </div>

            <!-- CAMERA VIEW -->
            <div id="cameraWrapper" class="position-relative d-none mb-3">
                <video id="video" class="w-100 rounded" autoplay playsinline></video>
                <div id="ktpFrame"></div>
            </div>
            <canvas id="canvas" class="d-none"></canvas>

            <!-- CAPTURE -->
            <button type="button" id="btnCapture" class="btn btn-primary btn-regist w-100 mb-2 d-none">Capture Foto</button>

            <!-- PREVIEW -->
            <div class="mb-3">
                <img id="previewImage" style="width:100%; height:500px; object-fit:contain; background:#000; display:none;">
            </div>
            <button type="button" id="btnRetake" class="btn btn-primary btn-retake w-100 mb-3 d-none">Ambil Ulang</button>
            <button id="btnNext" class="btn btn-primary btn-regist w-100 mb-3">Lanjutkan</button>
        </form>
    </div>
</section>

<script>
const fileInput  = document.getElementById('fileInput');
const preview    = document.getElementById('previewImage');
const video      = document.getElementById('video');
const canvas     = document.getElementById('canvas');
const ctx        = canvas.getContext('2d');
const btnCamera  = document.getElementById('btnCamera');
const btnCapture = document.getElementById('btnCapture');
const btnRetake  = document.getElementById('btnRetake');
const btnNext    = document.getElementById('btnNext');
const wrapper    = document.getElementById('cameraWrapper');

let stream = null;

/* PREVIEW */
function showPreview(file) {
    const reader = new FileReader();
    reader.onload = function(e) {
        preview.src = e.target.result;
        preview.style.display = 'block';
        btnNext.disabled = false;
    };
    reader.readAsDataURL(file);
}

/* UPLOAD */
fileInput.addEventListener('change', function(e) {
    stopCamera();

    const file = e.target.files[0];
    if (!file) return;

    showPreview(file);
});

/* START CAMERA */
btnCamera.onclick = async () => {
    fileInput.value = '';
    preview.style.display = 'none';

    try {
        stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: { exact: "environment" } }
        });
    } catch {
        stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: "environment" }
        });
    }

    video.srcObject = stream;
    wrapper.classList.remove('d-none');

    btnCamera.classList.add('d-none');
    btnCapture.classList.remove('d-none');
};

/* CAPTURE */
btnCapture.onclick = () => {
    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;

    ctx.drawImage(video, 0, 0);

    canvas.toBlob(blob => {
        const file = new File([blob], "ktp.jpg", { type: "image/jpeg" });

        const dt = new DataTransfer();
        dt.items.add(file);
        fileInput.files = dt.files;

        showPreview(file);
    }, "image/jpeg", 0.9);

    stopCamera();

    wrapper.classList.add('d-none');
    btnCapture.classList.add('d-none');
    btnRetake.classList.remove('d-none');
};

/* RETAKE */
btnRetake.onclick = async () => {
    preview.style.display = 'none';
    btnRetake.classList.add('d-none');

    try {
        stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: "environment" }
        });
    } catch {}

    video.srcObject = stream;
    wrapper.classList.remove('d-none');

    btnCapture.classList.remove('d-none');
};

/* STOP CAMERA */
function stopCamera() {
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        stream = null;
    }
}
</script>

@endsection