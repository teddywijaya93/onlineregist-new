document.addEventListener("DOMContentLoaded", function () {
    const btnNext = document.getElementById("btnNext");
    const emailInput = document.getElementById("email");

    if (!btnNext) return;

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
            const token = document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content");

            // CHECK EMAIL
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
                title: checkData.message || "Response",
                confirmButtonText: "OK"
            });

            if (!checkData.status) {
                btnNext.disabled = false;
                return;
            }

            // SEND OTP
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
                title: otpData.message || "OTP Response",
                confirmButtonText: "OK"
            });

            if (!otpData.status) {
                btnNext.disabled = false;
                return;
            }

            // REDIRECT OTP PAGE
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
});