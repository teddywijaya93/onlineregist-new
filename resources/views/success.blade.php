@extends('layouts.app')
@section('title','Profits Anywhere')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="auth-wrapper d-flex align-items-center min-vh-100">
    <div class="container">
         <div id="downloadSection" class="mb-5 text-center">
            <div class="text-center mb-4"><img src="{{ asset('storage/success.svg') }}"></div>
            <h3 class="head-lanjut text-white mb-3">Terima Kasih! Registrasi Anda Sedang Diproses</h3>
            <p class="desc-lanjut mb-5">Anda sudah dapat login dan menjelajahi fitur Profits. Cek secara berkala proses verifikasi registrasi Anda melalui Profits.</p>

            <div id="storeButtons">
                <h3 class="head-lanjut text-white mb-3">Download Aplikasi Profits Anywhere</h3>
                <a href="https://apps.apple.com/id/app/profits-anywhere/id1417870013"><img src="{{ asset('storage/ICON_APPLE.svg') }}" width="50px" height="50px" class="me-3"></a>
                <a href="https://play.google.com/store/apps/details?id=com.pt.sekuritas.profits.anywhere"><img src="{{ asset('storage/ICON_GOOGLEPLAY.svg') }}" width="50px" height="50px" class="ms-3"></a>
            </div>
        </div>
        <button type="button" id="btnOpenApp" class="btn btn-primary btn-regist w-100 mb-3">Login ke Profits</button>
    </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const ua = navigator.userAgent;
    const isAndroid = /android/i.test(ua);
    const isIOS = /iPhone|iPad|iPod/i.test(ua);
    const isSmallScreen = window.innerWidth <= 768;
    const isMobile = isAndroid || isIOS || isSmallScreen;
    const storeButtons = document.getElementById("storeButtons");
    const loginBtn = document.getElementById("btnOpenApp");

    if (isMobile) {
        storeButtons.style.display = "none";
        loginBtn.style.display = "block";
    } else {
        storeButtons.style.display = "block";
        loginBtn.style.display = "none";
    }

    loginBtn.addEventListener("click", function () {
        // ANDROID
        if (isAndroid) {
            const appLink = "intent://home#Intent;scheme=profits;package=com.pt.sekuritas.profits.anywhere;end";
            const fallback = "https://play.google.com/store/apps/details?id=com.pt.sekuritas.profits.anywhere";

            window.location.href = appLink;

            setTimeout(() => {
                window.location.href = fallback;
            }, 1500);
        }

        // IOS
        else if (isIOS) {
            const appLink = "profits://home";
            const fallback = "https://apps.apple.com/id/app/profits-anywhere/id1417870013";

            window.location.href = appLink;

            setTimeout(() => {
                window.location.href = fallback;
            }, 1500);
        }
    });
});
</script>

@endsection