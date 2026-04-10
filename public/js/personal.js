document.addEventListener("DOMContentLoaded", async () => {
    await initSelects();

    initSameAddress();
    restrictMaritalByGender();
    initValidation();

    document.getElementById("genderSelect")?.addEventListener("change", restrictMaritalByGender);
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

            // ✅ ONLY MATCH BY ID
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

function restrictMaritalByGender() {
    const gender = document.getElementById("genderSelect")?.value;
    const maritalSelect = document.getElementById("maritalSelect");

    if (!gender || !maritalSelect) return;

    const jandaOption = maritalSelect.querySelector('option[value="3"]');
    const dudaOption  = maritalSelect.querySelector('option[value="4"]');

    if (gender === "1") { // PRIA
        if (jandaOption) jandaOption.style.display = "none";

        if (dudaOption)  dudaOption.style.display = "";

        if (maritalSelect.value === "3") {
            maritalSelect.value = "";
        }
    } else if (gender === "2") { // WANITA
        if (dudaOption)  dudaOption.style.display = "none";

        if (jandaOption) jandaOption.style.display = "";

        if (maritalSelect.value === "4") {
            maritalSelect.value = "";
        }
    }
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
        isValid &= validateRequired("maritalSelect", "Status Perkawinan Diisi");
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