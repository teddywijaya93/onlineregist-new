document.addEventListener('DOMContentLoaded', async () => {
    let employmentType = document.getElementById("employmentType")?.value;

    if (!employmentType) return;

    const employmentId = await getEmploymentId(employmentType);

    console.log("Employment ID:", employmentId);

    if (!employmentId) return;

    loadBusinessline(employmentId);
    loadPosition(employmentId);
});

async function getEmploymentId(text) {
    const res = await fetch(window.routes.employment);
    const json = await res.json();
    const list = extractArray(json);

    const target = normalizeText(text);

    const found = list.find(item => {
        const desc = normalizeText(item.description);
        return desc === target;
    });

    return found ? String(found.id) : null;
}

function normalizeText(str) {
    return (str || '')
        .toLowerCase()
        .replace(/dan/g, '')
        .replace(/,/g, '')
        .replace(/\s+/g, ' ')
        .trim();
}


function extractArray(res) {
    if (Array.isArray(res?.data)) return res.data;
    if (Array.isArray(res?.datas)) return res.datas;
    return [];
}

function loadBusinessline(employmentId) {
    const select = document.getElementById('businesslineSelect');
    if (!select) return;

    select.innerHTML = `<option value="">Pilih Bidang Usaha</option>`;

    fetch(`${window.routes.businessline}?employment_id=${employmentId}`)
        .then(r => r.json())
        .then(res => {
            console.log("BUSINESS RES:", res);

            const list = extractArray(res);
            const selectedValue = (select.dataset.selected || '').toLowerCase().trim();

            list.forEach(item => {
                if (!item.businessLineId) return;

                const itemId   = String(item.businessLineId);
                const itemDesc = (item.description || '').toLowerCase().trim();

                const isSelected = selectedValue === itemId || selectedValue === itemDesc
                    ? 'selected'
                    : '';

                select.innerHTML += `<option value="${itemId}" ${isSelected}>${item.description}</option>`;
            });
        })
        .catch(err => console.error('Business Error:', err));
}

function loadPosition(employmentId) {
    const select = document.getElementById('positionSelect');
    if (!select) return;

    select.innerHTML = `<option value="">Pilih Jabatan</option>`;

    fetch(`${window.routes.position}?employment_id=${employmentId}`)
        .then(r => r.json())
        .then(res => {
            console.log("POSITION RES:", res);

            const list = extractArray(res);
            const selectedValue = (select.dataset.selected || '').toLowerCase().trim();

            list.forEach(item => {
                if (!item.positionId) return;

                const itemId   = String(item.positionId);
                const itemDesc = (item.description || '').toLowerCase().trim();

                const isSelected = selectedValue === itemId || selectedValue === itemDesc
                    ? 'selected'
                    : '';

                select.innerHTML += `<option value="${itemId}" ${isSelected}>${item.description}</option>`;
            });
        })
        .catch(err => console.error('Position Error:', err));
}