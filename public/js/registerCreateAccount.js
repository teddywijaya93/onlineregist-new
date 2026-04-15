document.addEventListener("DOMContentLoaded", function () {
    const token = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");
    const path = window.location.pathname;
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
                    credentials: "same-origin",
                    body: JSON.stringify({
                        email: email
                    })
                });
                const checkData = await checkRes.json();

                sessionStorage.setItem("registrationStatus", checkData.registrationStatus);
                sessionStorage.setItem("registrationStep", checkData.registrationStep);

                // jangan simpan null
                if (checkData.redirect && checkData.redirect !== "null") {
                    sessionStorage.setItem("redirect", checkData.redirect);
                } else {
                    sessionStorage.removeItem("redirect"); // bersihin kalau kosong
                }

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
                    credentials: "same-origin",
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

   if (otpInputs.length > 0 && path.includes("/otp")) {
        const email = emailHidden?.value || "";

        // AUTO MOVE
        otpInputs.forEach((input, index) => {
            input.addEventListener("input", function () {
                this.value = this.value.replace(/\D/g, "");

                if (this.value.length === 1 && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
            });

            // BACKSPACE
            input.addEventListener("keydown", function (e) {
                if (e.key === "Backspace") {
                    if (this.value === "" && index > 0) {
                        otpInputs[index - 1].focus();
                        otpInputs[index - 1].value = "";
                    }
                }
            });

            // ENABLED PASTE
            input.addEventListener("paste", function (e) {
                e.preventDefault();

                const pasteData = e.clipboardData.getData("text").replace(/\D/g, "");

                if (!pasteData) return;
                otpInputs.forEach((inp, i) => {
                    inp.value = pasteData[i] || "";
                });

                // fokus ke terakhir yang keisi
                const lastIndex = Math.min(pasteData.length, otpInputs.length) - 1;
                if (lastIndex >= 0) {
                    otpInputs[lastIndex].focus();
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
                        credentials: "same-origin",
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

                    // cek status dari session backend
                    const status   = sessionStorage.getItem("registrationStatus");
                    const redirect = sessionStorage.getItem("redirect");

                    if (status === "NEW" && redirect && redirect !== "null") {
                        window.location.href = redirect;
                    } else {
                        window.location.href = "/mobile";
                    }

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

    // Username Check
    const usernameInput = document.getElementById("username");
    const counter = document.getElementById("charCounter");
    const ruleAlpha = document.getElementById("rule-alphanumeric");
    const ruleNoSymbol = document.getElementById("rule-nosymbol");
    const errorText = document.getElementById("username-error");

    if (usernameInput) {
        usernameInput.addEventListener("input", async function () {
            let val = this.value;

            // max 15
            if (val.length > 15) {
                val = val.slice(0, 15);
                this.value = val;
            }

            // counter
            if (counter) {
                counter.innerText = val.length + "/15";
            }

            // rule huruf + angka
            const hasLetter = /[a-zA-Z]/.test(val);
            const hasNumber = /[0-9]/.test(val);

            if (hasLetter && hasNumber) {
                ruleAlpha.style.color = "#00ff9c";
            } else {
                ruleAlpha.style.color = "#999";
            }

            // no symbol
            const noSymbol = /^[a-zA-Z0-9]*$/.test(val);
            if (noSymbol) {
                ruleNoSymbol.style.color = "#00ff9c";
            } else {
                ruleNoSymbol.style.color = "#999";
            }

            if (!(hasLetter && hasNumber && noSymbol && val.length >= 5)) {
                errorText.innerText = "";
                this.classList.remove("input-error");

                // sembunyikan rekomendasi kalau ada
                if (typeof suggestBox !== "undefined") {
                    suggestBox.classList.add("d-none");
                }

                return;
            }

            try {
                const res = await fetch(window.routes.checkUsername, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": token
                    },
                    credentials: "same-origin",
                    body: JSON.stringify({
                        username: val
                    })
                });

                const data = await res.json();
                if (data.status) {
                    errorText.innerText = "";
                } else {
                    errorText.innerText = data.messageBody || "Username Tidak Tersedia";
                }

            } catch {
                errorText.innerText = "Error Cek Username";
            }
        });
    }

    // Username Recommendation
    const suggestBox = document.getElementById("username-suggest-box");
    const suggestList = document.getElementById("suggestList");
    const closeSuggest = document.getElementById("closeSuggest");

    if (closeSuggest) {
        closeSuggest.onclick = () => {
            suggestBox.classList.add("d-none");
        };
    }

    if (usernameInput) {
        usernameInput.addEventListener("input",async function () {
            let val = this.value;

            if (val.length > 15) {
                val = val.slice(0, 15);
                this.value = val;
            }
            counter.innerText = val.length + "/15";

            // VALIDASI RULE (WAJIB TAMBAH)
            const hasLetter = /[a-zA-Z]/.test(val);
            const hasNumber = /[0-9]/.test(val);
            const noSymbol  = /^[a-zA-Z0-9]*$/.test(val);

            // STOP kalau belum valid semua
            if (!(hasLetter && hasNumber && noSymbol && val.length >= 5)) {
                errorText.innerText = "";
                this.classList.remove("input-error");
                suggestBox.classList.add("d-none");
                return;
            }

            try {
                const res = await fetch(window.routes.checkUsername,{
                    method: "POST",
                    headers: {
                        "Content-Type":  "application/json",
                        "X-CSRF-TOKEN": token
                    },
                    body: JSON.stringify({
                        username: val
                    })
                });
                const data = await res.json();

                if (data.status) {
                    errorText.innerText = "";
                    this.classList.remove("input-error");
                    suggestBox.classList.add("d-none");
                } else {
                    errorText.innerText ="Username sudah digunakan";
                    this.classList.add("input-error");

                    const r1 = val + Math.floor(Math.random() *9999);
                    const r2 = val + Math.floor(Math.random() *999);
                    const r3 = val + "123";
                    const arr = [r1, r2, r3];

                    suggestList.innerHTML = "";

                    arr.forEach( name => {
                        const div = document.createElement("div");
                        div.className = "suggest-item";
                        div.innerHTML =`<span>${name}</span>
                        <span class="use-btn">Gunakan</span>`;

                        div.querySelector(".use-btn").onclick =() => {
                            usernameInput.value = name;
                            suggestBox.classList.add("d-none");
                            usernameInput.classList.remove("input-error");
                            errorText.innerText ="";
                        };
                        suggestList.appendChild(div);
                    });
                    suggestBox.classList.remove("d-none");
                }
            } catch {}
        });
    }

    // Password Validation
    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("confirmPassword");
    const rulePassLength = document.getElementById("rule-pass-length");
    const rulePassNumber = document.getElementById("rule-pass-number");
    const rulePassSymbol = document.getElementById("rule-pass-symbol");
    const rulePassMatch = document.getElementById("rule-pass-match");
    const rulePassText = document.getElementById("rule-pass-text");

    if (password) {
        password.addEventListener("input", function () {
            const val = this.value;

            const hasLength = val.length >= 8;
            const hasNumber = /[0-9]/.test(val);
            const hasSymbol = /[!@#$%^&*]/.test(val);

            rulePassLength.style.color = hasLength ? "#00ff9c" : "#999";
            rulePassNumber.style.color = hasNumber ? "#00ff9c" : "#999";
            rulePassSymbol.style.color = hasSymbol ? "#00ff9c" : "#999";
        });
    }

    if (confirmPassword) {
        confirmPassword.addEventListener("input",function () {
            if ( password.value === confirmPassword.value && password.value.length > 0) {
                rulePassMatch.style.color = "#00ff9c";
                rulePassText.innerText = "Password Sudah Sama";
            } else {
                rulePassMatch.style.color = "#999";
                rulePassText.innerText = "Password Tidak Sama";
            }
        });
    }

    // See Password
    document.querySelectorAll(".eye-btn").forEach(btn => {
        btn.addEventListener("click",function () {
            const id = this.dataset.target;
            const input = document.getElementById(id);

            if (input.type === "password") {
                input.type = "text";
                this.classList.remove("fa-eye");
                this.classList.add("fa-eye-slash");
            } else {
                input.type ="password";
                this.classList.remove("fa-eye-slash");
                this.classList.add("fa-eye");
            }
        });
    });

    // SAVE PHONE → OTP MOBILE
    if (phone && btnNext && !emailInput) {
        btnNext.addEventListener("click",async function (){

            const val = phone.value;
            if (!val) {
                Swal.fire({
                    icon: "warning",
                    title: "Nomor wajib diisi"
                });
                return;
            }
            const full = "+62" + val;

            try {
                await fetch( "/save-phone",{
                    method: "POST",
                    headers: {
                        "Content-Type" : "application/json",
                        "X-CSRF-TOKEN" : token
                    },
                    credentials: "same-origin",
                    body: JSON.stringify({
                        phone: full
                    })
                });
                window.location.href ="/otp-mobile";

            } catch {
                Swal.fire({
                    icon: "error",
                    title: "Gagal simpan phone"
                });

            }}
        );
    }

    // OTP MOBILE → CREATE ACCOUNT
    if (otpInputs.length > 0 && window.location.pathname.includes( "otp-mobile")) {
        btnNext.addEventListener("click",async function () {
            try {
                await fetch("/verify-otp-mobile",{
                    method: "POST",
                    headers: {
                        "Content-Type" : "application/json",
                        "X-CSRF-TOKEN" : token
                    },
                    credentials: "same-origin",
                });

                window.location.href = "/create-account";

            } catch {
                Swal.fire({
                    icon: "error",
                    title: "OTP gagal"
                });
            }
        });
    }

    
});

async function submitAccount() {
    const token = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");

    const usernameInput = document.getElementById("username");
    const username = usernameInput.value;
    const password = document.getElementById("password").value;
    const confirm = document.getElementById("confirmPassword").value;

    // VALIDASI USERNAME
    const hasLetter = /[a-zA-Z]/.test(username);
    const hasNumber = /[0-9]/.test(username);
    const noSymbol  = /^[a-zA-Z0-9]*$/.test(username);

    if (!(hasLetter && hasNumber && noSymbol && username.length >= 5)) {
        Swal.fire({
            icon: "warning",
            title: "Username belum memenuhi syarat"
        });
        return;
    }

    // VALIDASI BASIC
    if (!username || !password) {
        Swal.fire({
            icon: "warning",
            title: "Username & Password wajib diisi"
        });
        return;
    }

    if (password !== confirm) {
        Swal.fire({
            icon: "warning",
            title: "Password tidak sama"
        });
        return;
    }

    try {
        const res = await fetch(window.routes.createAccount, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": token
            },
            credentials: "same-origin",
            body: JSON.stringify({
                username: username,
                password: password
            })
        });

        const data = await res.json();
        if (data.status) {
            await Swal.fire({
                icon: "success",
                title: "Account berhasil dibuat"
            });

            const redirect = sessionStorage.getItem("redirect");
            if (redirect && redirect !== "null") {
                window.location.href = redirect;
            } else {
                window.location.href = "/create-pin";
            }

        } else {
            Swal.fire({
                icon: "error",
                title: data.message || "Gagal create account"
            });
        }

    } catch (e) {
        Swal.fire({
            icon: "error",
            title: "Server error"
        });
    }
}