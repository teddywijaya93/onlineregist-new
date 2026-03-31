document.addEventListener("DOMContentLoaded", async () => {
    await initSelects();
    await initKelurahanAutoFill();
    initSameAddress();
    initInputFilters();
    showApiMessage();
    initFormValidation();

    restrictMaritalByGender();
    document.getElementById("genderSelect")?.addEventListener("change", restrictMaritalByGender);
});

function showApiMessage() {
    if (window.apiMessage) {
        Swal.fire({
            icon: 'info',
            title: 'Informasi',
            text: window.apiMessage
        });
    }
}

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

            // MATCH BY ID OR TEXT (OCR SAFE)
            if (
                String(value) === String(selected) ||
                String(text).toLowerCase() === String(selected).toLowerCase()
            ) {
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
    await loadSelect("genderSelect", window.routes.gender, "Pilih Jenis Kelamin");
    await loadSelect("religionSelect", window.routes.religion, "Pilih Agama");
    await loadSelect("maritalSelect", window.routes.marital, "Pilih Status Perkawinan");
}

async function initKelurahanAutoFill() {
    const kelurahanSelect = document.getElementById("kelurahanSelect");
    const citySelect = document.getElementById("citySelect");
    const kecamatanSelect = document.getElementById("kecamatanSelect");
    const postalInput = document.getElementById("postalCode");

    if (!kelurahanSelect) return;

    try {
        const res = await fetch(window.routes.kelurahan);
        const json = await res.json();
        const list = json.data || [];

        kelurahanSelect.innerHTML = '<option value="">Pilih Kelurahan</option>';

        list.forEach(item => {
            const opt = document.createElement("option");

            opt.value = item.kelurahan;
            opt.textContent = item.label;
            opt.dataset.full = item.label;
            opt.dataset.city = item.city;
            opt.dataset.kecamatan = item.kecamatan;
            opt.dataset.postal = item.postalCode;

            kelurahanSelect.appendChild(opt);
        });

        // FUNCTION RESTORE FULL LABEL
        function restoreFullOptions(select) {
            Array.from(select.options).forEach(opt => {
                if (opt.dataset.full) {
                    opt.textContent = opt.dataset.full;
                }
            });
        }

        // SAAT PILIH → JADI PENDEK
        kelurahanSelect.addEventListener("change", function () {
            const selected = this.options[this.selectedIndex];
            if (!selected) return;

            // tampil pendek
            selected.textContent = selected.value;

            // isi field lain
            if (citySelect) citySelect.value = selected.dataset.city || '';
            if (kecamatanSelect) kecamatanSelect.value = selected.dataset.kecamatan || '';
            if (postalInput) postalInput.value = selected.dataset.postal || '';
        });

        // SAAT MAU BUKA DROPDOWN → BALIK FULL
        kelurahanSelect.addEventListener("mousedown", function () {
            restoreFullOptions(this);
        });

        kelurahanSelect.addEventListener("keydown", function () {
            restoreFullOptions(this);
        });

    } catch (err) {
        console.error("Kelurahan load error", err);
    }
}

function initSameAddress() {
    const checkbox = document.getElementById('sameAddress');
    if (!checkbox) return;
    const cache = {};
    checkbox.addEventListener('change', function () {
        const map = [
            ['alamat','residenceAddress'],
            ['rt','residenceRT'],
            ['rw','residenceRW'],
            ['citySelect','residenceCity'],
            ['kelurahan','residenceKelurahan'],
            ['kecamatan','residenceKecamatan']
        ];
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
}

function initFormValidation() {
    const form = document.querySelector('form');
    if (!form) return;
    const fields = [
        ['nama','Nama'],
        ['nik','NIK'],
        ['motherMaidenName','Nama ibu'],
        // ['tempatLahir','Tempat lahir'],
        ['tanggalLahir','Tanggal lahir'],
        ['genderSelect','Jenis kelamin'],
        ['religionSelect','Agama'],
        ['maritalSelect','Status kawin'],
        ['alamat','Alamat'],
        ['kelurahanSelect','Kelurahan'],
        ['residenceAddress','Alamat domisili'],
        ['residenceKecamatan','Kecamatan domisili']
    ];

    form.addEventListener('submit', function(e) {
        let errors = [];

        fields.forEach(([id,label]) => {
            const el = document.getElementById(id);
            if (!el || !el.value.trim()) {
                errors.push(label + ' wajib diisi');
            }
        });

        if (errors.length) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Data belum lengkap',
                html: 
                '<ul style="text-align:left">' +
                    errors.map(err => '<li>'+err+'</li>').join('') +
                '</ul>'
            });
        }
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

// CITY -> KECAMATAN
// function initCityKecamatan() {
//     const citySelect = document.getElementById("citySelect");
//     const kecamatanSelect = document.getElementById("kecamatanSelect");

//     if (!citySelect || !kecamatanSelect) return;

//     kecamatanSelect.innerHTML = '<option value="">Pilih Kota terlebih dahulu</option>';
//     kecamatanSelect.disabled = true;

//     citySelect.addEventListener("change", async function () {
//         const cityId = this.value;
//         if (!cityId) {
//             kecamatanSelect.innerHTML = '<option value="">Pilih Kota terlebih dahulu</option>';
//             kecamatanSelect.disabled = true;
//             return;
//         }
//         kecamatanSelect.disabled = false;
//         kecamatanSelect.innerHTML = '<option value="">Loading...</option>';

//         try {
//             const response = await fetch(`${window.routes.kecamatan}?city_id=${cityId}`);
//             const json = await response.json();
//             const list = json.data || [];
//             const selected = kecamatanSelect.dataset.selected || '';

//             kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';

//             list.forEach(item => {
//                 const opt = document.createElement("option");
//                 opt.value = item.id;
//                 opt.textContent = item.name;

//                 if (String(item.id) === String(selected) || item.name.toLowerCase() === selected.toLowerCase()) {
//                     opt.selected = true;
//                 }
//                 kecamatanSelect.appendChild(opt);
//             });

//             // AUTO LOAD KELURAHAN SAAT EDIT
//             if (selected) { 
//                 kecamatanSelect.dispatchEvent(new Event('change'));
//             }

//         } catch (err) {
//             console.error("Load kecamatan error", err);
//             kecamatanSelect.innerHTML = '<option value="">Gagal load</option>';
//         }
//     });
//     // TRIGGER IF EDIT MODE
//     if (citySelect.value) {
//         citySelect.dispatchEvent(new Event('change'));
//     }
// }

// KECAMATAN → KELURAHAN
// function initKecamatanKelurahan() {
//     const kecamatanSelect = document.getElementById("kecamatanSelect");
//     const kelurahanSelect = document.getElementById("kelurahanSelect");

//     if (!kecamatanSelect || !kelurahanSelect) return;

//     kelurahanSelect.innerHTML = '<option value="">Pilih Kecamatan terlebih dahulu</option>';
//     kelurahanSelect.disabled = true;

//     kecamatanSelect.addEventListener("change", async function () {
//         const kecamatanId = this.value;
//         if (!kecamatanId) {
//             kelurahanSelect.innerHTML = '<option value="">Pilih Kecamatan terlebih dahulu</option>';
//             kelurahanSelect.disabled = true;
//             return;
//         }
//         kelurahanSelect.disabled = false;
//         kelurahanSelect.innerHTML = '<option value="">Loading...</option>';

//         try {
//             const response = await fetch(`${window.routes.kelurahan}?kecamatan_id=${kecamatanId}`);
//             const json = await response.json();
//             const list = json.data || [];
//             const selected = kelurahanSelect.dataset.selected || '';

//             kelurahanSelect.innerHTML = '<option value="">Pilih Kelurahan</option>';

//             list.forEach(item => {
//                 const opt = document.createElement("option");
//                 opt.value = item.id;
//                 opt.textContent = item.name;

//                 if (String(item.id) === String(selected) || item.name.toLowerCase() === selected.toLowerCase()) {
//                     opt.selected = true;
//                 }

//                 kelurahanSelect.appendChild(opt);
//             });

//         } catch (err) {
//             console.error("Load kelurahan error", err);
//             kelurahanSelect.innerHTML = '<option value="">Gagal load</option>';
//         }
//     });
// }