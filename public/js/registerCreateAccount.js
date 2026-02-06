const usernameInput = document.getElementById("username");
const emailInput    = document.getElementById("email");
const phoneInput    = document.getElementById("mobilePhone");

const ruleAlphaNum  = document.getElementById("rule-alphanumeric");
const ruleNoSymbol  = document.getElementById("rule-nosymbol");
const charCounter   = document.getElementById("charCounter");

const recEl     = document.getElementById("username-recommendation");
const btnSubmit = document.querySelector("button[onclick='submitAccount()']");

let typingTimer;
const delay = 700;
let minAlertShown = false;   // ⬅ pindah ke global

btnSubmit.disabled = true;

// Helper
function validateAll() {
    if (
        usernameInput.dataset.valid === "true" &&
        emailInput.dataset.valid === "true" &&
        phoneInput.dataset.valid === "true"
    ) {
        btnSubmit.disabled = false;
    } else {
        btnSubmit.disabled = true;
    }
}

// Username Input + Checklist
usernameInput.addEventListener("input", function () {
    // hanya huruf & angka
    this.value = this.value.replace(/[^a-zA-Z0-9]/g, "");
    let val = this.value;

    // maksimal 15 karakter
    if (val.length > 15) {
        val = val.substring(0, 15);
        this.value = val;
    }

    // update counter
    charCounter.innerText = `${val.length}/15`;

    // rule tidak mengandung simbol
    if (val.length > 0 && /^[a-zA-Z0-9]+$/.test(val)) {
        ruleNoSymbol.classList.add("active");
    } else {
        ruleNoSymbol.classList.remove("active");
    }

    // rule mengandung huruf & angka
    if (/[a-zA-Z]/.test(val) && /[0-9]/.test(val)) {
        ruleAlphaNum.classList.add("active");
    } else {
        ruleAlphaNum.classList.remove("active");
    }

    // hanya cek API jika minimal 7 karakter
    clearTimeout(typingTimer);

    if (val.length >= 7) {
        typingTimer = setTimeout(() => {
            checkUsername(val);
        }, 700);
    }
});

// HIT API Username
function checkUsername(username) {
    Swal.fire({
        title: "Memeriksa username...",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    fetch(window.routes.checkUsername, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN":
                document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ username })
    })
    .then(res => res.json())
    .then(data => {
        Swal.close();
        recEl.innerHTML = "";

        if (data.status === true) {
            Swal.fire("Username tersedia", "", "success");
            usernameInput.dataset.valid = "true";
        } else {
            Swal.fire("Username tidak tersedia", "", "error");
            usernameInput.dataset.valid = "false";

            if (data.alternatives?.length) {
                renderAlternatives(data.alternatives);
            }
        }
        validateAll();
    })
    .catch(() => {
        Swal.close();
        Swal.fire("Error", "Gagal cek username", "error");
    });
}

// Rekomendasi Username
function renderAlternatives(list) {
    let html = `
        <small class="text-white">Rekomendasi username:</small>
        <div class="d-flex flex-wrap gap-2 mt-2">
    `;
    list.forEach(username => {
        html += `
            <button type="button"
                class="btn btn-outline-light btn-sm"
                onclick="useUsername('${username}')">
                ${username}
            </button>
        `;
    });
    html += `</div>`;
    recEl.innerHTML = html;
}

function useUsername(username) {
    usernameInput.value = username;
    recEl.innerHTML = "";
    checkUsername(username);
}

// Email Validation
emailInput.addEventListener("input", function () {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    this.dataset.valid = regex.test(this.value) ? "true" : "false";
    validateAll();
});

// Phone Validation
phoneInput.addEventListener("focus", function () {
    if (this.value.trim() === "") {
        this.value = "+628";
    }
});
phoneInput.addEventListener("input", function () {
    // Jika user hapus sampai kurang dari +628
    if (this.value.length < 4) {
        this.value = "+628";
    }

    // Pisahkan prefix & angka
    let raw = this.value.replace("+628", "");
    raw = raw.replace(/[^0-9]/g, "");

    this.value = "+628" + raw;

    // validasi panjang setelah +628
    if (raw.length >= 7 && raw.length <= 15) {
        phoneInput.dataset.valid = "true";
    } else {
        phoneInput.dataset.valid = "false";
    }
    validateAll();
});

// Local Storage
function saveDraftAccount() {
    const draft = JSON.parse(localStorage.getItem("registerDraft")) || {};

    draft.username    = usernameInput.value;
    draft.email       = emailInput.value;
    draft.mobilePhone = phoneInput.value;

    localStorage.setItem("registerDraft", JSON.stringify(draft));
}
usernameInput.addEventListener("input", saveDraftAccount);
emailInput.addEventListener("input", saveDraftAccount);
phoneInput.addEventListener("input", saveDraftAccount);

document.addEventListener("DOMContentLoaded", () => {

    const draft = JSON.parse(localStorage.getItem("registerDraft"));

    if (!draft) return;

    if (draft.username) {
        usernameInput.value = draft.username;
        usernameInput.dispatchEvent(new Event("input"));
    }

    if (draft.email) emailInput.value = draft.email;

    if (draft.mobilePhone) {
        phoneInput.value = draft.mobilePhone;
        phoneInput.dispatchEvent(new Event("input"));
    }
});

// SUBMIT
function submitAccount() {
    const payload = {
        username: usernameInput.value,
        email: emailInput.value,
        mobilePhone: phoneInput.value
    };

    // MODAL KONFIRMASI
    Swal.fire({
        title: "Apakah Data yang Kamu Masukkan Sudah Benar?",
        html: `
            <div style="text-align:left;margin-top:15px">
                <p><b>Username</b><br>${payload.username}</p>
                <p><b>Email</b><br>${payload.email}</p>
                <p><b>Nomor Ponsel</b><br>${payload.mobilePhone}</p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: "Lanjutkan",
        cancelButtonText: "Kembali",
        reverseButtons: true
    }).then((result) => {

        if (!result.isConfirmed) return;

        // SUBMIT KE SERVER
        fetch(window.routes.submitAccount, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN":
                    document.querySelector('meta[name="csrf-token"]').content,
                "Accept": "application/json",
                "Content-Type": "application/json"
            },
            body: JSON.stringify(payload)
        })
        .then(r => r.json())
        .then(res => {
            if (!res?.status) {
                Swal.fire("Gagal", res.message || "Gagal membuat akun", "error");
                return;
            }
            Swal.fire({
                icon: "success",
                title: "Akun Berhasil Dibuat",
                text: "Silakan login menggunakan akun Anda",
                confirmButtonText: "Ke Halaman Login"
            }).then(() => {
                window.location.href = window.routes.login; 
            });
        })
        .catch(() => {
            Swal.fire("Error", "Terjadi kesalahan jaringan", "error");
        });
    });
}