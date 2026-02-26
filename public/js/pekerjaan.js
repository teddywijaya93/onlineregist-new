document.addEventListener('DOMContentLoaded', () => {
    loadEmployment();
    document.getElementById('employmentSelect')
        .addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const employmentId = selectedOption?.dataset.id;

            loadPosition(employmentId);
            loadBusinessline(employmentId);
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
            const selectedDesc = select.dataset.selected;

            select.innerHTML = `<option value="">Pilih Pekerjaan Nasabah</option>`;
            list.forEach(item => {
                if (!item.id || !item.description) return;
                const isSelected = selectedDesc == item.description ? 'selected' : '';
                select.innerHTML += `
                <option value="${item.description}" data-id="${item.id}" ${isSelected}>
                    ${item.description}
                </option>`;
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
            const selectedDesc = select.dataset.selected;

            select.innerHTML = `<option value="">Pilih Jabatan</option>`;
            list.forEach(item => {
                if (!item.positionId || !item.description) return;
                const isSelected = selectedDesc == item.description ? 'selected' : '';
                select.innerHTML += `
                <option value="${item.description}" data-id="${item.positionId}" ${isSelected}>
                    ${item.description}
                </option>`;
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
            const selectedDesc = select.dataset.selected;
            
            select.innerHTML = `<option value="">Pilih Bidang Usaha</option>`;
            list.forEach(item => {
                if (!item.businessLineId || !item.description) return;
                const isSelected = selectedDesc == item.description ? 'selected' : '';
                select.innerHTML += `
                <option value="${item.description}" data-id="${item.businessLineId}" ${isSelected}>
                    ${item.description}
                </option>`;
            });
        });
}