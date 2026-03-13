@extends('layouts.app')
@section('title','Verifikasi Wajah')
@section('content')

<section class="auth-wrapper">
    <div class="container text-start">
        @include('components.step-header', [
            'step' => $step,
            'hideBack' => true
        ])
        <div class="mb-5">
            <h3 class="head-lanjut text-white mb-2">Ambil Foto Selfie</h3>
            <p class="desc-lanjut mb-0">Perhatian panduan berikut dalam pengambilan foto selfie.</p>
        </div>
        <form id="selfieForm" method="POST" action="{{ route('verifikasi.wajah.process') }}">
            @csrf
            <input type="hidden" name="image" id="imageInput">

            <!-- LIVE CAMERA -->
            <video id="video" class="w-100 mb-3 d-none rounded" autoplay playsinline></video>
            <canvas id="canvas" class="d-none"></canvas>

            <!-- BUTTON -->
            <button type="button" id="btnCamera" class="btn btn-secondary btn-regist w-100 mb-2">Aktifkan Kamera</button>
            <button type="button" id="btnCapture" class="btn btn-primary btn-regist w-100 mb-2 d-none">Capture Foto</button>

            <!-- PREVIEW IMAGE -->
            <div id="imageBox" class="d-none mb-3">
                <h6 class="text-white mb-2">Hasil Foto</h6>
                <img id="resultImage" class="w-100 rounded border">
            </div>
            <button type="button" id="btnRetake" class="btn btn-warning btn-regist w-100 mb-2 d-none">Ambil Ulang</button>
            <button type="submit" id="btnSubmit" class="btn btn-success btn-regist w-100 mb-3 d-none">Lanjutkan</button>
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

const video      = document.getElementById('video');
const canvas     = document.getElementById('canvas');
const ctx        = canvas.getContext('2d');
const btnCamera  = document.getElementById('btnCamera');
const btnCapture = document.getElementById('btnCapture');
const btnRetake  = document.getElementById('btnRetake');
const btnSubmit  = document.getElementById('btnSubmit');
const resultImg  = document.getElementById('resultImage');
const imageBox   = document.getElementById('imageBox');
const imageInput = document.getElementById('imageInput');

let stream = null;
let imageData = null;

/* AKTIFKAN KAMERA */
btnCamera.onclick = async () => {
    try {
        stream = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: "user"
            },
            audio: false
        });

        video.srcObject = stream;
        video.classList.remove('d-none');
    
        btnCamera.classList.add('d-none');
        btnCapture.classList.remove('d-none');

    } catch (err) {
        console.log(err);
        alert('Izin kamera ditolak: ' + err.message);
    }
};

/* CAPTURE FOTO */
btnCapture.onclick = () => {
    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    ctx.drawImage(video, 0, 0);

    imageData = canvas.toDataURL('image/jpeg');

    resultImg.src = imageData;
    imageBox.classList.remove('d-none');

    btnCapture.classList.add('d-none');
    btnRetake.classList.remove('d-none');
    btnSubmit.classList.remove('d-none');

    stopCamera();
};

/* AMBIL ULANG */
btnRetake.onclick = async () => {
    resetAll();
    await btnCamera.onclick();
};

/* SUBMIT FORM */
document.getElementById('selfieForm').addEventListener('submit', function (e) {
    if (!imageData) {
        e.preventDefault();
        alert('Foto belum diambil');
        return;
    }
    imageInput.value = imageData;
});

/* STOP CAMERA */
function stopCamera() {
    if (stream) {
        stream.getTracks().forEach(t => t.stop());
        stream = null;
    }
}

/* RESET */
function resetAll() {
    imageData = null;
    resultImg.src = '';
    imageBox.classList.add('d-none');

    btnRetake.classList.add('d-none');
    btnSubmit.classList.add('d-none');
    btnCamera.classList.remove('d-none');
}
</script>

@endsection