document.addEventListener("DOMContentLoaded", () => {
    initSelects();
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

function initSelects() {
    loadSelect("genderSelect", window.routes.gender);
    loadSelect("religionSelect", window.routes.religion);
    loadSelect("maritalSelect", window.routes.marital);
    loadSelect("educationSelect", window.routes.education);
}

function loadSelect(id, url) {
    const select = document.getElementById(id);
    if (!select) return;

    const selected = select.dataset.selected || '';

    fetch(url)
        .then(r => r.json())
        .then(res => {
            const list = res.data || res.datas || [];
            select.innerHTML = `<option value="">Pilih</option>`;
            list.forEach(item => {
                const opt = document.createElement("option");
                opt.value = item.id;
                opt.textContent = item.description;

                if (String(item.id) === String(selected)) {
                    opt.selected = true;
                }
                select.appendChild(opt);
            });
        })
        .catch(err => console.error("Load select error", id, err));
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
            ['kota','residenceCity'],
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
        ['kota','Kota'],
        ['kelurahan','Kelurahan'],
        ['kecamatan','Kecamatan'],
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