document.addEventListener("DOMContentLoaded", function () {
    const otpInputs = document.querySelectorAll(".otp-box");
    const btnNext = document.getElementById("btnNext");
    const resendLink = document.getElementById("resendLink");
    const timerText = document.getElementById("timerText");
    const email = document.getElementById("email").value;

    const token = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    // AUTO MOVE OTP
    otpInputs.forEach((input, index) => {
        input.addEventListener("input", function () {
            if (this.value.length === 1 &&
                index < otpInputs.length - 1) {

                otpInputs[index + 1].focus();
            }
        });
    });

    // GET OTP
    function getOtp() {
        let otp = "";

        otpInputs.forEach(i => {
            otp += i.value;
        });
        return otp;
    }

    // COUNTDOWN 60s
    let timeLeft = 60;
    let timer;
    function startTimer() {
        resendLink.style.pointerEvents = "none";
        timer = setInterval(() => {
            timeLeft--;
            timerText.innerText = "(" + timeLeft + "s)";

            if (timeLeft <= 0) {
                clearInterval(timer);

                resendLink.style.pointerEvents = "auto";
                timerText.innerText = "";

                timeLeft = 60;
            }
        }, 1000);
    }
    startTimer();

    // VERIFY OTP
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
            window.location.href = "/next-step";

        } catch {
            Swal.fire({
                icon: "error",
                title: "Internal Server Error"
            });

        } finally {
            btnNext.disabled = false;
        }
    });

    // RESEND OTP
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
});