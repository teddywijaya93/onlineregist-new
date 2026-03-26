let step = 1;
let firstPin = "";

document.addEventListener("DOMContentLoaded", () => {
    const inputs = document.querySelectorAll(".pin-input");
    const btn = document.getElementById("btnPin");

    inputs.forEach((input, index) => {
        input.addEventListener("input", () => {
            input.value = input.value.replace(/\D/g, "");
            if (input.value.length === 1 && index < 5) {
                inputs[index + 1].focus();
            }
            checkPin();
        });
    });

    function checkPin() {
        let pin = "";
        inputs.forEach(i => pin += i.value);
        btn.disabled = pin.length !== 6;
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
    inputs.forEach(i => i.value = "");
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