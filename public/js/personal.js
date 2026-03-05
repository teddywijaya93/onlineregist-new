document.addEventListener("DOMContentLoaded", async () => {
    await initSelects();
    initKecamatanKelurahan();
    initCityKecamatan();
    initSameAddress();
    initInputFilters();
    showApiMessage();
    initFormValidation();
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
    await loadSelect("educationSelect", window.routes.education, "Pilih Pendidikan Terakhir");
    await loadSelect("citySelect", window.routes.city, "Pilih Kota");

    initCityKecamatan();
}

// CITY -> KECAMATAN
function initCityKecamatan() {
    const citySelect = document.getElementById("citySelect");
    const kecamatanSelect = document.getElementById("kecamatanSelect");

    if (!citySelect || !kecamatanSelect) return;

    kecamatanSelect.innerHTML = '<option value="">Pilih Kota terlebih dahulu</option>';
    kecamatanSelect.disabled = true;

    citySelect.addEventListener("change", async function () {
        const cityId = this.value;
        if (!cityId) {
            kecamatanSelect.innerHTML = '<option value="">Pilih Kota terlebih dahulu</option>';
            kecamatanSelect.disabled = true;
            return;
        }
        kecamatanSelect.disabled = false;
        kecamatanSelect.innerHTML = '<option value="">Loading...</option>';

        try {
            const response = await fetch(`${window.routes.kecamatan}?city_id=${cityId}`);
            const json = await response.json();
            const list = json.data || [];
            const selected = kecamatanSelect.dataset.selected || '';

            kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';

            list.forEach(item => {
                const opt = document.createElement("option");
                opt.value = item.id;
                opt.textContent = item.name;

                if (String(item.id) === String(selected)) {
                    opt.selected = true;
                }
                kecamatanSelect.appendChild(opt);
            });

            // AUTO LOAD KELURAHAN SAAT EDIT
            if (selected) {
                kecamatanSelect.dispatchEvent(new Event('change'));
            }

        } catch (err) {
            console.error("Load kecamatan error", err);
            kecamatanSelect.innerHTML = '<option value="">Gagal load</option>';
        }
    });
    // TRIGGER IF EDIT MODE
    if (citySelect.value) {
        citySelect.dispatchEvent(new Event('change'));
    }
}

// KECAMATAN → KELURAHAN
function initKecamatanKelurahan() {
    const kecamatanSelect = document.getElementById("kecamatanSelect");
    const kelurahanSelect = document.getElementById("kelurahanSelect");

    if (!kecamatanSelect || !kelurahanSelect) return;

    kelurahanSelect.innerHTML = '<option value="">Pilih Kecamatan terlebih dahulu</option>';
    kelurahanSelect.disabled = true;

    kecamatanSelect.addEventListener("change", async function () {
        const kecamatanId = this.value;
        if (!kecamatanId) {
            kelurahanSelect.innerHTML = '<option value="">Pilih Kecamatan terlebih dahulu</option>';
            kelurahanSelect.disabled = true;
            return;
        }
        kelurahanSelect.disabled = false;
        kelurahanSelect.innerHTML = '<option value="">Loading...</option>';

        try {
            const response = await fetch(`${window.routes.kelurahan}?kecamatan_id=${kecamatanId}`);
            const json = await response.json();
            const list = json.data || [];
            const selected = kelurahanSelect.dataset.selected || '';

            kelurahanSelect.innerHTML = '<option value="">Pilih Kelurahan</option>';

            list.forEach(item => {
                const opt = document.createElement("option");
                opt.value = item.id;
                opt.textContent = item.name;

                if (String(item.id) === String(selected)) {
                    opt.selected = true;
                }

                kelurahanSelect.appendChild(opt);
            });

        } catch (err) {
            console.error("Load kelurahan error", err);
            kelurahanSelect.innerHTML = '<option value="">Gagal load</option>';
        }
    });
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
        ['tempatLahir','Tempat lahir'],
        ['tanggalLahir','Tanggal lahir'],
        ['genderSelect','Jenis kelamin'],
        ['religionSelect','Agama'],
        ['educationSelect','Pendidikan'],
        ['maritalSelect','Status kawin'],
        ['alamat','Alamat'],
        ['rt','RT'],
        ['rw','RW'],
        ['citySelect','Kota'],
        ['kecamatanSelect','Kecamatan'],
        ['kelurahanSelect','Kelurahan'],
        ['residenceAddress','Alamat domisili'],
        ['residenceRT','RT domisili'],
        ['residenceRW','RW domisili'],
        ['residenceCity','Kota domisili'],
        ['residenceKelurahan','Kelurahan domisili'],
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

function initInputFilters() {
    document.querySelectorAll('.alphabet-only').forEach(input => {
        input.addEventListener('input', () => {
            input.value = input.value.replace(/[^a-zA-Z\s]/g,'');
        });
    });
    document.querySelectorAll('.numeric-only').forEach(input => {
        const max = input.maxLength || 16;
        input.addEventListener('input', () => {
            input.value = input.value.replace(/\D/g,'').slice(0,max);
        });
    });
}