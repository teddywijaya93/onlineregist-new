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

            <!-- <div id="storeButtons">
                <h3 class="head-lanjut text-white mb-3">Download Aplikasi Profits Anywhere</h3>
                <a href="https://apps.apple.com/id/app/profits-anywhere/id1417870013"><img src="{{ asset('storage/ICON_APPLE.svg') }}" width="50px" height="50px" class="me-3"></a>
                <a href="https://play.google.com/store/apps/details?id=com.pt.sekuritas.profits.anywhere"><img src="{{ asset('storage/ICON_GOOGLEPLAY.svg') }}" width="50px" height="50px" class="ms-3"></a>
            </div> -->
        </div>
        <div class="verification-list shadow-lg mb-5">
            <div class="step-verification shadow-lg mb-4">
                <div class="step-verification-wrapper">
                    <div class="step-verification-item active">
                        <div class="step-verification-circle done">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div class="step-verification-label mt-2">Pengajuan</div>
                    </div>

                    <!-- Line -->
                    <div class="step-verification-line active"></div>
                    <div class="step-verification-item active">
                        <div class="step-verification-circle current"></div>
                        <div class="step-verification-label mt-2">Verifikasi</div>
                    </div>

                    <!-- Line -->
                    <div class="step-verification-line"></div>
                    <div class="step-verification-item">
                        <div class="step-verification-circle"></div>
                        <div class="step-verification-label mt-2">Selesai</div>
                    </div>
                </div>
            </div>
            <div class="verification-item">
                <div class="d-flex align-items-center">
                    <div class="icon-box me-3">
                        <img src="{{ asset('storage/pending_verif.svg') }}" width="24px" height="24px">
                    </div>
                    <div class="verification-title">Verifikasi Data dan Dokumen</div>
                </div>
                <i class="fa-solid fa-circle-check" style="font-size: 22px; color:#17D98C"></i>
            </div>

            <div class="verification-item">
                <div class="d-flex align-items-center">
                    <div class="icon-box me-3">
                        <img src="{{ asset('storage/pending_sre.svg') }}" width="24px" height="24px">
                    </div>
                    <div class="verification-title">Verifikasi Rekening Efek</div>
                </div>
                <i class="fa-solid fa-circle-check" style="font-size: 22px; color:#A6AEBB"></i>
            </div>

            <div class="verification-item">
                <div class="d-flex align-items-center">
                    <div class="icon-box me-3">
                        <img src="{{ asset('storage/pending_rdn.svg') }}" width="24px" height="24px">
                    </div>
                    <div class="verification-title">Verifikasi Rekening Dana Nasabah</div>
                </div>
                <i class="fa-solid fa-circle-check" style="font-size: 22px; color:#A6AEBB"></i>
            </div>

            <div class="alert alert-warning d-flex align-items-start gap-3 mb-0 alert-bank">
                <div style="font-size:20px; color:#E6D112;"><i class="fa fa-circle-exclamation"></i></div>
                <div style="font-size:12px; font-weight:500; letter-spacing:2%; color:#B3B9C4">
                    Proses verifikasi memerlukan waktu 1-2 hari kerja. Cek berkala proses verifikasi melalui Profits. Kami akan menginformasikan kepada Anda setelah verifikasi selesai.
                </div>  
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
    const loginBtn = document.getElementById("btnOpenApp");

    loginBtn.addEventListener("click", function () {
        // Desktop
        if (!isMobile) {
            window.location.replace("https://next-dev.profits.co.id/");
            return;
        }

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