@extends('layouts.app')
@section('title','Profits Anywhere')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="auth-wrapper d-flex align-items-center min-vh-100">
    <div class="container">
         <div class="mb-5   text-center">
            <div class="text-center mb-4"><img src="{{ asset('storage/success.svg') }}"></div>
            <h3 class="head-lanjut text-white mb-3">Terima kasih! Registrasi Anda Sedang Diproses</h3>
            <p class="desc-lanjut mb-0">Anda sudah dapat login dan menjelajahi fitur Profits. Cek secara berkala proses verifikasi registrasi Anda melalui Profits.</p>
        </div>
        <button type="button" id="btnOpenApp" class="btn btn-primary btn-regist w-100 mb-3">Login ke Profits</button>
    </div>
</section>

<script>
document.getElementById("btnOpenApp").addEventListener("click", function () {

    const ua = navigator.userAgent || navigator.vendor || window.opera;

    const isAndroid = /android/i.test(ua);
    const isIOS = /iPad|iPhone|iPod/.test(ua);
    const isDesktop = !isAndroid && !isIOS;

    // GANTI INI dengan deep link app lo
    const deepLink = "profits://home"; 
    const playStore = "https://play.google.com/store/apps/details?id=com.pt.sekuritas.profits.anywhere&pli=1";
    const appStore = "https://apps.apple.com/id/app/profits-anywhere/id1417870013";

    if (isAndroid || isIOS) {
        // coba buka app
        window.location.href = deepLink;

        // fallback kalau app tidak terinstall
        setTimeout(() => {
            if (isAndroid) {
                window.location.href = playStore;
            } else if (isIOS) {
                window.location.href = appStore;
            }
        }, 1500);
    } else {
        // desktop → arahkan ke web app
        window.location.href = "https://www.profits.co.id/file/download/profits-setup.exe";
    }
});
</script>

@endsection