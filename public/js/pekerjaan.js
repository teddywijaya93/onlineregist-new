document.addEventListener('DOMContentLoaded', () => {
    const employmentType = document.getElementById("employmentType")?.value;

    // console.log("EMPLOYMENT TYPE:", employmentType);

    if (employmentType) {
        loadPosition(employmentType);
        loadBusinessline(employmentType);
    }
});

function extractArray(res) {
    if (Array.isArray(res?.data)) return res.data;
    if (Array.isArray(res?.datas)) return res.datas;
    return [];
}

function loadPosition(employmentId) {
    const select = document.getElementById('positionSelect');
    if (!select) return;

    select.innerHTML = `<option value="">Pilih Jabatan</option>`;

    fetch(`${window.routes.position}?employment_id=${employmentId}`)
        .then(r => r.json())
        .then(res => {
            // console.log("POSITION RES:", res);

            const list = extractArray(res);
            const selectedValue = select.dataset.selected;
            list.forEach(item => {
                if (!item.positionId) return;
                const isSelected = selectedValue == item.positionId ? 'selected' : '';
                select.innerHTML += `<option value="${item.positionId}" ${isSelected}>${item.description}</option>`;
            });
        })
        .catch(err => console.error('Position Error:', err));
}

function loadBusinessline(employmentId) {
    const select = document.getElementById('businesslineSelect');
    if (!select) return;

    select.innerHTML = `<option value="">Pilih Bidang Usaha</option>`;

    fetch(`${window.routes.businessline}?employment_id=${employmentId}`)
        .then(r => r.json())
        .then(res => {
            // console.log("BUSINESS RES:", res);

            const list = extractArray(res);
            const selectedValue = select.dataset.selected;
            list.forEach(item => {
                if (!item.businessLineId) return;
                const isSelected = selectedValue == item.businessLineId ? 'selected' : '';
                select.innerHTML += `<option value="${item.businessLineId}" ${isSelected}>${item.description}</option>`;
            });
        })
        .catch(err => console.error('Business Error:', err));
}