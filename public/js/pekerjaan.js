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
            const selectedValue = select.dataset.selected;

            select.innerHTML = `<option value="">Pilih Pekerjaan Nasabah</option>`;
            list.forEach(item => {
                if (!item.id) return;
                const isSelected = selectedValue == item.id ? 'selected' : '';
                select.innerHTML += `<option value="${item.id}" ${isSelected}>${item.description}</option>`;
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
            const selectedValue = select.dataset.selected;

            select.innerHTML = `<option value="">Pilih Jabatan</option>`;
            list.forEach(item => {
                if (!item.positionId) return;
                const isSelected = selectedValue == item.positionId ? 'selected' : '';
                select.innerHTML += `<option value="${item.positionId}" ${isSelected}>${item.description}</option>`;
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
            const selectedValue = select.dataset.selected;

            select.innerHTML = `<option value="">Pilih Bidang Usaha</option>`;
            list.forEach(item => {
                if (!item.businessLineId) return;
                const isSelected = selectedValue == item.businessLineId ? 'selected' : '';
                select.innerHTML += `<option value="${item.businessLineId}" ${isSelected}>${item.description}</option>`;
            });
        });
}