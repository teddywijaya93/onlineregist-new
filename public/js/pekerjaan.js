document.addEventListener('DOMContentLoaded', () => {
    loadEmployment();

    document.getElementById('employmentSelect')
        .addEventListener('change', function () {
            loadPosition(this.value);
            loadBusinessline(this.value);
        });
});

function extractArray(res) {
    if (Array.isArray(res?.data)) return res.data;
    if (Array.isArray(res?.datas)) return res.datas;
    return [];
}

function loadEmployment() {
    fetch(window.routes.employment)
        .then(r => r.json())
        .then(res => {
            const list = extractArray(res);
            const select = document.getElementById('employmentSelect');

            select.innerHTML = `<option value="">Pilih Pekerjaan Nasabah</option>`;
            list.forEach(item => {
                select.innerHTML += `<option value="${item.id}">${item.description}</option>`;
            });
        });
}

function loadPosition(employmentId) {
    if (!employmentId) return;

    fetch(`${window.routes.position}?employment_id=${employmentId}`)
        .then(r => r.json())
        .then(res => {
            const list = extractArray(res);
            const select = document.getElementById('positionSelect');

            select.innerHTML = `<option value="">Pilih Jabatan</option>`;
            list.forEach(item => {
                select.innerHTML += `<option value="${item.id}">${item.description}</option>`;
            });
        });
}

function loadBusinessline(employmentId) {
    if (!employmentId) return;

    fetch(`${window.routes.businessline}?employment_id=${employmentId}`)
        .then(r => r.json())
        .then(res => {
            const list = extractArray(res);
            const select = document.getElementById('businesslineSelect');

            select.innerHTML = `<option value="">Pilih Bidang Usaha</option>`;
            list.forEach(item => {
                select.innerHTML += `<option value="${item.id}">${item.description}</option>`;
            });
        });
}