document.addEventListener("DOMContentLoaded", function () {
    // Login Submit
    const form = document.getElementById("loginForm");
    if (!form) return;

    form.addEventListener("submit", async function (e) {
        e.preventDefault();

        const username = document.getElementById("username").value.trim();
        const password = document.getElementById("password").value.trim();
        const loginUrl = form.dataset.loginUrl;
        try {
            const response = await fetch(loginUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .content,
                    "Accept": "application/json"
                },
                body: JSON.stringify({ username, password })
            });

            const data = await response.json();
            if (!response.ok) throw data;

            if (data.status === true) {
                await Swal.fire({
                    icon: "success",
                    title: "Berhasil",
                    text: data.message || "Login Berhasil",
                    confirmButtonColor: "#3085d6"
                });
                window.location.replace(data.redirect);
                return;
            }

            await Swal.fire({
                icon: "error",
                title: "Gagal",
                text: data.message || "Username / Password Salah",
                confirmButtonColor: "#d33"
            });
        } catch (err) {
            await Swal.fire({
                icon: "error",
                title: "Error",
                text: err.message || "Server tidak dapat dihubungi",
                confirmButtonColor: "#d33"
            });
        }
    });

    // Password Toogle
    document.querySelectorAll(".password-toggle").forEach(wrapper => {
        const input = wrapper.querySelector("input");
        if (!input) return;

        const button = document.createElement("button");
        button.type = "button";

        const eyeOpen = `
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke="currentColor">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/>
                <circle cx="12" cy="12" r="3"/>
            </svg>
        `;

        const eyeClosed = `
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <path d="M2 2l20 20"/>
                <path d="M10.58 10.58a2 2 0 002.83 2.83"/>
                <path d="M9.88 5.08A10.94 10.94 0 0112 4c7 0 11 8 11 8a21.86 21.86 0 01-4.42 5.08"/>
                <path d="M6.61 6.61A21.86 21.86 0 001 12s4 8 11 8c2.06 0 3.93-.5 5.39-1.39"/>
            </svg>
        `;

        button.innerHTML = eyeOpen;
        wrapper.appendChild(button);

        button.addEventListener("click", function () {
            const isHidden = input.type === "password";
            input.type = isHidden ? "text" : "password";
            button.innerHTML = isHidden ? eyeClosed : eyeOpen;
        });
    });

    // OTP
    const inputs = document.querySelectorAll('.otp-box');
    const btn = document.getElementById("btnContinue");
    const resendLink = document.getElementById("resendLink");
    const timerText  = document.getElementById("timerText");

    let cooldown = 60;
    let countdownInterval;
    let isCooldown = true;

    // Focus input pertama
    inputs[0].focus();

    // OTP INPUT BEHAVIOR
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

    // VERIFY OTP
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

                window.location.href = "/data-personal";
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

    // RESEND OTP
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