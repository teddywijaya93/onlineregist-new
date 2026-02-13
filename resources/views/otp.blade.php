@extends('layouts.app')
@section('title','Verifikasi OTP')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="auth-wrapper">
    <div class="container">
        <div class="mb-4">
            <div class="otp-icon">
                <i class="fa-solid fa-mobile-screen"></i>
            </div>
        </div>
        <h3 class="text-white congrats-text fw-bold mb-2">Verifikasi Nomor Ponsel</h3>
        <p class="silahkan-cek-text mb-4">
            Masukan 6 digit kode yang telah kami kirimkan ke nomor kamu<br/>
            <span class="text-primary email-text"> {{ Str::mask(session('register_phone'), '*', 3, 6) }} </span>
        </p>
        <div class="d-flex gap-2 mb-4">
            @for ($i = 0; $i < 6; $i++)
                <input type="text" maxlength="1" class="form-control text-center otp-box" style="width:50px;height:55px;font-size:20px;">
            @endfor
        </div>
        <p class="resend-otp">Tidak menerima kode?<br>
            <a href="#" id="resendLink" class="resend-otp resend-link" >Kirim ulang</a>
            <span id="timerText" class="ms-2"></span>
        </p>
        <button id="btnContinue" class="btn btn-primary w-100">Lanjutkan</button>
    </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function(){

    const inputs = document.querySelectorAll('.otp-box');
    const btn = document.getElementById("btnContinue");
    const resendLink = document.getElementById("resendLink");
    const timerText  = document.getElementById("timerText");

    let cooldown = 60;
    let countdownInterval;
    let isCooldown = true;

    // Focus input pertama
    inputs[0].focus();

    // ===============================
    // OTP INPUT BEHAVIOR
    // ===============================

    inputs.forEach((input, index) => {

        input.addEventListener("input", function() {

            this.value = this.value.replace(/[^0-9]/g, '');

            if (this.value && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }

            checkAutoSubmit();
        });

        input.addEventListener("keydown", function(e){
            if (e.key === "Backspace" && !this.value && index > 0) {
                inputs[index - 1].focus();
            }
        });

        input.addEventListener("paste", function(e){
            e.preventDefault();

            const pasteData = (e.clipboardData || window.clipboardData)
                                .getData('text')
                                .replace(/\D/g, '');

            if (pasteData.length === 6) {
                pasteData.split('').forEach((digit, i) => {
                    if (inputs[i]) {
                        inputs[i].value = digit;
                    }
                });
                checkAutoSubmit();
            }
        });

    });

    function getOtp() {
        return Array.from(inputs).map(i => i.value).join('');
    }

    function resetOtp() {
        inputs.forEach(i => i.value = "");
        inputs[0].focus();
    }

    function checkAutoSubmit() {
        const otp = getOtp();
        if (otp.length === 6) {
            verifyOtp(otp);
        }
    }

    // ===============================
    // VERIFY OTP
    // ===============================

    async function verifyOtp(otp) {

        btn.disabled = true;
        btn.innerText = "Memverifikasi...";

        try {

            const response = await fetch("{{ route('otp.verify') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .content,
                    "Accept": "application/json"
                },
                body: JSON.stringify({ otp })
            });

            const data = await response.json();

            if (!response.ok) throw data;

            if (data.status === true) {

                if (data.registrationStep === "uploadKTP") {
                    window.location.replace("{{ route('verifikasi.ktp') }}");
                    return;
                }

                window.location.href = "/dashboard";
                return;
            }

            throw data;

        } catch (err) {

            btn.disabled = false;
            btn.innerText = "Lanjutkan";

            Swal.fire({
                icon: "error",
                title: "OTP Salah",
                text: err.message || "Kode OTP tidak valid"
            });

            resetOtp();
        }
    }

    btn.addEventListener("click", function(){
        const otp = getOtp();
        if (otp.length !== 6) {
            Swal.fire("OTP harus 6 digit");
            return;
        }
        verifyOtp(otp);
    });

    // ===============================
    // RESEND OTP
    // ===============================

    function startCooldown() {

        isCooldown = true;
        resendLink.style.pointerEvents = "none";
        resendLink.style.opacity = "0.6";
        timerText.innerText = `(${cooldown}s)`;

        countdownInterval = setInterval(() => {
            cooldown--;
            timerText.innerText = `(${cooldown}s)`;

            if (cooldown <= 0) {
                clearInterval(countdownInterval);
                isCooldown = false;
                cooldown = 60;
                timerText.innerText = "";
                resendLink.style.pointerEvents = "auto";
                resendLink.style.opacity = "1";
            }
        }, 1000);
    }

    startCooldown();

    resendLink.addEventListener("click", async function(e){
        e.preventDefault();

        if (isCooldown) return;

        resendLink.innerText = "Mengirim...";

        try {

            const response = await fetch("{{ route('otp.resend') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .content,
                    "Accept": "application/json"
                }
            });

            const data = await response.json();

            if (!response.ok) throw data;

            if (data.status === true) {

                resendLink.innerText = "Kirim ulang";
                startCooldown();

                Swal.fire({
                    icon: "success",
                    title: "Berhasil",
                    text: data.message
                });

            } else {
                throw data;
            }

        } catch (err) {

            resendLink.innerText = "Kirim ulang";

            Swal.fire({
                icon: "error",
                title: "Gagal",
                text: err.message || "Gagal mengirim ulang OTP"
            });
        }
    });

});
</script>

@endsection