document.addEventListener("DOMContentLoaded", () => {
    initSelects();
    initSameAddress();
    initValidation();
    initInputFilters();
});

function initSelects() {
    loadSelect("genderSelect", window.routes.gender);
    loadSelect("religionSelect", window.routes.religion);
    loadSelect("maritalSelect", window.routes.marital);
    loadSelect("educationSelect", window.routes.education);
}

function loadSelect(id, url) {
    const select = document.getElementById(id);
    if (!select) return;

    const selected = (select.dataset.selected || '').toUpperCase();

    fetch(url)
        .then(r => r.json())
        .then(res => {
            const list = res.data || res.datas || [];
            select.innerHTML = `<option value="">Pilih</option>`;
            list.forEach(item => {
                const opt = document.createElement("option");
                opt.value = item.description;
                opt.textContent = item.description;
                if (item.description.toUpperCase() === selected) {
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

function initValidation() {
    const form = document.querySelector('form');
    if (!form) return;
    form.addEventListener('submit', function(e) {
        const fields = [
            ['nama','Nama sesuai e-KTP'],
            ['nik','Nomor e-KTP'],
            ['motherMaidenName','Nama gadis ibu kandung'],
            ['tempatLahir','Tempat lahir'],
            ['tanggalLahir','Tanggal lahir'],
            ['genderSelect','Jenis kelamin'],
            ['religionSelect','Agama'],
            ['educationSelect','Pendidikan terakhir'],
            ['maritalSelect','Status perkawinan'],
            ['alamat','Alamat sesuai e-KTP'],
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

        const errors = [];
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
                html: `<ul style="text-align:left">${errors.map(e=>`<li>${e}</li>`).join('')}</ul>`
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

document.addEventListener("DOMContentLoaded", function () {

    // ===== SweetAlert message dari session =====
    if (window.apiMessage) {
        Swal.fire({
            icon: 'info',
            title: 'Informasi',
            text: window.apiMessage
        });
    }

    // ===== Validation submit =====
    const form = document.querySelector('form');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        let errors = [];
        function check(id, label) {
            const el = document.getElementById(id);
            if (!el || !el.value.trim()) {
                errors.push(label + ' wajib diisi');
            }
        }

        check('nama','Nama sesuai e-KTP');
        check('nik','Nomor e-KTP');
        check('motherMaidenName','Nama Gadis Ibu Kandung');
        check('tempatLahir','Tempat Lahir');
        check('tanggalLahir','Tanggal Lahir');
        check('genderSelect','Jenis Kelamin');
        check('religionSelect','Agama');
        check('educationSelect','Pendidikan Terakhir');
        check('maritalSelect','Status Perkawinan');
        check('alamat','Alamat Sesuai e-KTP');
        check('rt','RT');
        check('rw','RW');
        check('kota','Kota');
        check('kelurahan','Kelurahan');
        check('kecamatan','Kecamatan');
        check('residenceAddress','Alamat Domisili');
        check('residenceRT','RT Domisili');
        check('residenceRW','RW Domisili');
        check('residenceCity','Kota Domisili');
        check('residenceKelurahan','Kelurahan Domisili');
        check('residenceKecamatan','Kecamatan Domisili');

        if (errors.length > 0) {
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
});