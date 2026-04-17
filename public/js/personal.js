document.addEventListener("DOMContentLoaded", async () => {
    await initSelects();
    initDatePicker();
    initSameAddress();
    initValidation();
    restrictMaritalByGender();
});

async function loadSelect(id, url, placeholder = "Pilih") {
    const select = document.getElementById(id);
    if (!select) return;

    const selected = select.dataset.selected || '';

    try {
        const response = await fetch(url);
        const json = await response.json();
        const list = json.data || [];

        select.innerHTML = `<option value="">${placeholder}</option>`;
        list.forEach(item => {
            const value = item.id ?? '';
            const text  = item.name ?? item.description ?? '';

            const opt = document.createElement("option");
            opt.value = value;
            opt.textContent = text;

            // ONLY MATCH BY ID
            if (String(value) === String(selected)) {
                opt.selected = true;
            }
            select.appendChild(opt);
        });

    } catch (err) {
        console.error("Load select error:", id, err);
    }
}

// INIT MASTER DROPDOWNS
async function initSelects() {
    await loadSelect("maritalSelect", window.routes.marital, "Pilih Status Perkawinan");
    await loadSelect("religionSelect", window.routes.religion, "Pilih Status Agama");
    restrictMaritalByGender();
}

function initDatePicker() {
    if (typeof flatpickr === "undefined") return;

    flatpickr("#dateOfBirth", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d-m-Y",
        maxDate: new Date().fp_incr(-365 * 17), // min umur 17
        disableMobile: true
    });
}

function initSameAddress() {
    const checkbox = document.getElementById('sameAddress');
    if (!checkbox) return;
    const cache = {};

    const map = [
        ['address','residenceAddress'],
        ['kelurahanSearch','residenceKelurahan'],
        ['postalCode','residencePostalCode'],
        ['citySelect','residenceCity'],
        ['kecamatanSelect','residenceKecamatan']
    ];

    checkbox.addEventListener('change', function () {
        map.forEach(([src, dest]) => {
            const source = document.getElementById(src);
            const target = document.getElementById(dest);
            if (!source || !target) return;
            if (this.checked) {
                cache[dest] = target.value;
                target.value = source.value;
                target.readOnly = true;
            } else {
                target.value = cache[dest] || '';
                target.readOnly = false;
            }
        });
    });

    // AUTO SYNC (REAL TIME)
    map.forEach(([src, dest]) => {
        const source = document.getElementById(src);
        const target = document.getElementById(dest);
        if (!source || !target) return;

        source.addEventListener('input', () => {
            if (checkbox.checked) {
                target.value = source.value;
            }
        });
    });
}

function initValidation() {
    const form = document.getElementById("personalForm");
    if (!form) return;

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        clearErrors();

        let isValid = true;

        isValid &= validateRequired("name", "Nama Lengkap Wajib Diisi");
        isValid &= validateRequired("identificationNumber", "NIK Wajib Diisi");
        isValid &= validateRequired("dateOfBirth", "Tanggal Lahir Wajib Diisi");
        isValid &= validateAge("dateOfBirth", " Umur Minimal 17 Tahun");
        isValid &= validateRequired("maritalSelect", "Status Perkawinan Wajib Diisi");
        isValid &= validateRequired("religionSelect", "Agama Wajib Diisi");
        isValid &= validateRequired("motherMaidenName", "Nama Gadis Ibu Kandung Wajib Diisi");
        isValid &= validateRequired("address", "Alama Wajib Diisi");
        isValid &= validateRequired("kelurahanSearch", "Kelurahan Wajib Diisi");
        isValid &= validateRequired("postalCode", "Postal Kode Wajib Diisi");
        isValid &= validateRequired("residenceAddress", "Residence Alamat Wajib Diisi");
        isValid &= validateRequired("residenceKelurahan", "Residence Kelurahan Wajib Diisi");
        isValid &= validateRequired("residencePostalCode", "Residence Postal Kode Wajib Diisi");

        if (!isValid) {
            scrollToFirstError();
            return;
        }

        form.submit();
    });

    // realtime clear error
    const fields = [
        "name",
        "identificationNumber",
        "dateOfBirth",
        "maritalStatus",
        "motherMaidenName",
        "address",
        "kelurahan",
        "postalCode",
        "residenceAddress",
        "residenceKelurahan",
        "residencePostalCode"
    ];

    fields.forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;

        el.addEventListener("input", () => clearFieldError(el));
        el.addEventListener("change", () => clearFieldError(el));
    });
}

function restrictMaritalByGender() {
    const gender = document.querySelector('input[name="gender"]')?.value;
    const maritalSelect = document.getElementById("maritalSelect");

    if (!gender || !maritalSelect) return;

    Array.from(maritalSelect.options).forEach(opt => {
        opt.style.display = "";
        // Pria & Duda -> Janda Hide
        if (gender === "1" && opt.value === "3") {
            opt.style.display = "none";
        }
        // Wanita & Janda -> Duda Hide
        if (gender === "2" && opt.value === "4") {
            opt.style.display = "none";
        }
    });

    if (
        (gender === "1" && maritalSelect.value === "3") ||
        (gender === "2" && maritalSelect.value === "4")
    ) {
        maritalSelect.value = "";
    }
}

function validateAge(id, message) {
    const el = document.getElementById(id);
    if (!el || !el.value) return true;

    const parts = el.value.split("-");
    const dob = new Date(parts[0], parts[1] - 1, parts[2]);
    const today = new Date();

    let age = today.getFullYear() - dob.getFullYear();
    const m = today.getMonth() - dob.getMonth();

    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
        age--;
    }

    if (age < 17) {
        el.classList.add("is-invalid");

        let error = el.parentElement.querySelector(".invalid-feedback");

        if (!error) {
            error = document.createElement("div");
            error.className = "invalid-feedback";
            el.parentElement.appendChild(error);
        }
        error.innerText = message;

        return false;
    }
    return true;
}