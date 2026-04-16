let step = 1;
let firstPin = "";
let showPin = false;

document.addEventListener("DOMContentLoaded", () => {
    const btnBack = document.getElementById("btnBack");
    if (btnBack) btnBack.style.display = "none";

    const inputs = document.querySelectorAll(".pin-input");
    const btn = document.getElementById("btnPin");
    const toggleBtn = document.getElementById("togglePin");

    if (toggleBtn) {
        toggleBtn.addEventListener("click", () => {
            showPin = !showPin;

            toggleBtn.innerText = showPin ? "Hide PIN" : "Show PIN";

            inputs.forEach(input => {
                if (showPin) {
                    input.classList.add("show-pin");
                } else {
                    input.classList.remove("show-pin");
                }
            });
            checkPin();
        });
    }

    inputs.forEach((input, index) => {
        input.addEventListener("input", () => {
            input.value = input.value.replace(/\D/g, "");
            if (input.value.length === 1 && index < 5) {
                inputs[index + 1].focus();
            }
            input.classList.toggle("filled", input.value !== "");
            checkPin();
        });

        // BACKSPACE
        input.addEventListener("keydown", (e) => {
            input.addEventListener("keydown", (e) => {
                if (e.key === "Backspace") {
                    if (input.value !== "") {
                        // hapus isi sendiri dulu
                        input.value = "";
                        input.classList.remove("filled");
                    } else if (index > 0) {
                        // kalau kosong → pindah ke kiri
                        inputs[index - 1].focus();
                        inputs[index - 1].value = "";
                        inputs[index - 1].classList.remove("filled");
                    }
                    checkPin();
                    e.preventDefault();
                }
            });
        });

        // DISABLED PASTE
        input.addEventListener("paste", e => e.preventDefault());
    });

    function checkPin() {
        let pin = "";
        inputs.forEach(i => pin += i.value);
        btn.disabled = pin.length !== 6;

        const display = document.getElementById("pinDisplay");
        if (display) {
            let view = "";
            inputs.forEach(i => {
                if (showPin) {
                    view += (i.value ? i.value : "-") + " ";
                } else {
                    view += (i.value ? "●" : "○") + " ";
                }
            });
            display.innerText = view.trim();
        }
    }
});

function getPin() {
    const inputs = document.querySelectorAll(".pin-input");
    let pin = "";
    inputs.forEach(i => pin += i.value);
    return pin;
}

function clearPin() {
    const inputs = document.querySelectorAll(".pin-input");
    inputs.forEach(i => {
        i.value = "";
        i.classList.remove("filled");
    });

    const display = document.getElementById("pinDisplay");
    if (display) {
        display.innerText = "● ● ● ● ● ●";
    }

    inputs[0].focus();
}

async function submitPin() {
    const token = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    const pin = getPin();

    if (pin.length !== 6) {
        Swal.fire({
            icon: "warning",
            title: "PIN Harus 6 Digit"
        });
        return;
    }

    if (!/^[0-9]+$/.test(pin)) {
        Swal.fire({
            icon: "warning",
            title: "PIN Harus Angka"
        });
        return;
    }

    // ================= STEP 1 =================
    if (step === 1) {
        firstPin = pin;
        step = 2;
        clearPin();

        document.getElementById("titlePin").innerText ="Konfirmasi PIN Trading";
        document.getElementById("descPin").innerText ="Masukkan ulang 6 digit PIN Trading";
        document.getElementById("btnBack").style.display = "inline-block";

        return;
    }

    // ================= STEP 2 =================
    if (step === 2) {
        if (pin !== firstPin) {
            Swal.fire({
                icon: "warning",
                title: "PIN Tidak Sama"
            });
            clearPin();

            return;
        }

        try {
            const res = await fetch("/create-pin", {
                method: "POST",
                credentials: "same-origin", 
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": token
                },
                body: JSON.stringify({
                    pin: pin
                })
            });
        
            const data = await res.json();
            await Swal.fire({
                icon: data.status ? "success" : "error",
                title: data.message || "Response"
            });

            if (!data.status) return;

            window.location.href = "/account-type";

        } catch (e) {
            Swal.fire({
                icon: "error",
                title: "Terjadi Kesalahan Sistem"
            });
        }
    }
}