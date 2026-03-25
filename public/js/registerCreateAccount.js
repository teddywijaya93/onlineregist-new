document.addEventListener("DOMContentLoaded", function () {
    const token = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");

    // Phone Validation
    const phone = document.getElementById("phone");

    if (phone) {
        phone.addEventListener("input", function () {
            this.value = this.value.replace(/\D/g, "");

            // harus mulai 8
            if (this.value.length === 1 && this.value !== "8") {
                this.value = "";
            }

            // max 11 digit setelah +62
            if (this.value.length > 11) {
                this.value = this.value.slice(0, 11);
            }
        });
    }

    // Email Check + Send OTP
    const btnNext = document.getElementById("btnNext");
    const emailInput = document.getElementById("email");

    if (btnNext && emailInput) {
        btnNext.addEventListener("click", async function () {
            const email = emailInput.value.trim();
            if (!email) {
                Swal.fire({
                    icon: "warning",
                    title: "Email wajib diisi"
                });
                return;
            }

            try {
                btnNext.disabled = true;

                // Check Email
                const checkRes = await fetch(window.routes.checkEmail, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": token
                    },
                    body: JSON.stringify({
                        email: email
                    })
                });

                const checkData = await checkRes.json();
                await Swal.fire({
                    icon: checkData.status ? "success" : "error",
                    title: checkData.message || "Response"
                });

                if (!checkData.status) {
                    btnNext.disabled = false;
                    return;
                }

                // Send OTP
                const otpRes = await fetch(window.routes.sendOtp, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": token
                    },
                    body: JSON.stringify({
                        email: email
                    })
                });

                const otpData = await otpRes.json();
                await Swal.fire({
                    icon: otpData.status ? "success" : "error",
                    title: otpData.message || "OTP Response"
                });

                if (!otpData.status) {
                    btnNext.disabled = false;
                    return;
                }
                window.location.href = window.routes.otpPage;

            } catch (err) {
                console.error(err);

                Swal.fire({
                    icon: "error",
                    title: "Terjadi kesalahan sistem"
                });
            } finally {
                btnNext.disabled = false;
            }
        });
    }

    // OTP PAGE SCRIPT
    const otpInputs = document.querySelectorAll(".otp-box");
    const resendLink = document.getElementById("resendLink");
    const timerText = document.getElementById("timerText");
    const emailHidden = document.getElementById("email");

    if (otpInputs.length > 0) {
        const email = emailHidden?.value || "";

        // AUTO MOVE
        otpInputs.forEach((input, index) => {
            input.addEventListener("input", function () {
                if (this.value.length === 1 &&
                    index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
            });
        });

        function getOtp() {
            let otp = "";
            otpInputs.forEach(i => {
                otp += i.value;
            });

            return otp;
        }

        // TIMER
        let timeLeft = 60;
        let timer;

        function startTimer() {
            if (!resendLink) return;

            resendLink.style.pointerEvents = "none";

            timer = setInterval(() => {
                timeLeft--;
                if (timerText) {
                    timerText.innerText = "(" + timeLeft + "s)";
                }

                if (timeLeft <= 0) {
                    clearInterval(timer);
                    resendLink.style.pointerEvents = "auto";

                    if (timerText) {
                        timerText.innerText = "";
                    }
                    timeLeft = 60;
                }
            }, 1000);
        }
        startTimer();

        // VERIFY OTP
        if (btnNext) {
            btnNext.addEventListener("click", async function () {
                const otp = getOtp();
                if (otp.length !== 6) {
                    Swal.fire({
                        icon: "warning",
                        title: "OTP harus 6 digit"
                    });

                    return;
                }

                try {
                    btnNext.disabled = true;
                    const res = await fetch("/verify-otp", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": token
                        },
                        body: JSON.stringify({
                            email: email,
                            otp: otp
                        })
                    });

                    const data = await res.json();
                    await Swal.fire({
                        icon: data.status ? "success" : "error",
                        title: data.message
                    });

                    if (!data.status) {
                        btnNext.disabled = false;
                        return;
                    }
                    window.location.href = "/mobile";

                } catch {
                    Swal.fire({
                        icon: "error",
                        title: "Internal Server Error"
                    });

                } finally {
                    btnNext.disabled = false;
                }
            });
        }

        // Resend OTP
        if (resendLink) {
            resendLink.addEventListener("click", async function (e) {
                e.preventDefault();

                if (timeLeft !== 60) return;

                try {
                    const res = await fetch("/send-otp", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": token
                        },
                        body: JSON.stringify({
                            email: email
                        })
                    });

                    const data = await res.json();
                    await Swal.fire({
                        icon: data.status ? "success" : "error",
                        title: data.message
                    });
                    startTimer();

                } catch {
                    Swal.fire({
                        icon: "error",
                        title: "Gagal Resend OTP"
                    });
                }
            });
        }
    }
});