@extends('layouts.app')
@section('title','Verifikasi Wajah')
@section('content')

<section class="auth-wrapper">
    <div class="container">
        <h3 class="text-white mb-4">Verifikasi Wajah | Test API Verihubs Pasif</h3>
        <p class="text-white-50">
            Aktifkan kamera, pastikan wajah terlihat jelas, lalu lanjutkan.
        </p>

        <!-- LIVE CAMERA -->
        <video id="video" class="w-100 mb-3 d-none rounded" autoplay playsinline></video>
        <canvas id="canvas" class="d-none"></canvas>

        <!-- BUTTON -->
        <button id="btnCamera" class="btn btn-secondary w-100 mb-2">Aktifkan Kamera</button>
        <button id="btnCapture" class="btn btn-primary w-100 mb-2 d-none">Capture Foto</button>

        <!-- PREVIEW IMAGE -->
        <div id="imageBox" class="d-none mb-3">
            <h6 class="text-white mb-2">Hasil Foto</h6>
            <img id="resultImage" class="w-100 rounded border">
        </div>
        <button id="btnRetake" class="btn btn-warning w-100 mb-2 d-none">Ambil Ulang</button>
        <button id="btnSubmit" class="btn btn-success w-100 mb-3 d-none">Kirim & Lihat Hasil Raw</button>

        <!-- INFO BOX -->
        <div id="infoBox" class="d-none mb-3">
            <ul class="list-group">
                <li class="list-group-item" id="infoStatus"></li>
                <li class="list-group-item" id="infoDetail"></li>
            </ul>
        </div>

        <!-- RAW JSON -->
        <div id="jsonBox" class="d-none">
            <h6 class="text-white mt-3">Response JSON (Debug)</h6>
            <pre id="jsonResult" class="text-white" style="font-size:11px;white-space:pre-wrap"></pre>
        </div>
    </div>
</section>

<script>
const video      = document.getElementById('video');
const canvas     = document.getElementById('canvas');
const ctx        = canvas.getContext('2d');

const btnCamera  = document.getElementById('btnCamera');
const btnCapture = document.getElementById('btnCapture');
const btnRetake  = document.getElementById('btnRetake');
const btnSubmit  = document.getElementById('btnSubmit');

const imageBox   = document.getElementById('imageBox');
const resultImg  = document.getElementById('resultImage');

const infoBox    = document.getElementById('infoBox');
const infoStatus = document.getElementById('infoStatus');
const infoDetail = document.getElementById('infoDetail');

const jsonBox    = document.getElementById('jsonBox');
const jsonResult = document.getElementById('jsonResult');

let stream = null;
let imageData = null;

/* AKTIFKAN KAMERA */
btnCamera.onclick = async () => {
    try {
        stream = await navigator.mediaDevices.getUserMedia({
            video: true,
            audio: false
        });

        video.srcObject = stream;
        video.classList.remove('d-none');

        btnCamera.classList.add('d-none');
        btnCapture.classList.remove('d-none');

    } catch {
        alert('Izin kamera ditolak');
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

    infoStatus.innerHTML = '📸 Foto berhasil diambil';
    // infoDetail.innerHTML = '📦 Ukuran data: <b>${imageData.length}</b> karakter';
    infoBox.classList.remove('d-none');
};

/* AMBIL ULANG */
btnRetake.onclick = async () => {
    resetAll();
    await btnCamera.onclick();
};

/* KIRIM KE API */
btnSubmit.onclick = () => {
    fetch("{{ route('verifikasi.wajah.process') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ image: imageData })
    })
    .then(r => r.json())
    .then(res => {
        jsonResult.textContent = JSON.stringify(res, null, 2);
        jsonBox.classList.remove('d-none');

        if (res.liveness?.status) {
            infoStatus.innerHTML =
                '✅ Wajah Asli (Liveness ${res.liveness.probability}%)';
        } else {
            infoStatus.innerHTML =
                '❌ Liveness tidak valid';
        }
    })
    .catch(() => alert('Gagal menghubungi server'));
};

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
    infoBox.classList.add('d-none');
    jsonBox.classList.add('d-none');

    btnRetake.classList.add('d-none');
    btnSubmit.classList.add('d-none');
    btnCamera.classList.remove('d-none');
}
</script>

@endsection