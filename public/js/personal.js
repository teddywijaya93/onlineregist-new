document.addEventListener("DOMContentLoaded", async () => {
    await initSelects();
    initSameAddress();
    restrictMaritalByGender();
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