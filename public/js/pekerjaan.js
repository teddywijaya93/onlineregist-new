document.addEventListener('DOMContentLoaded', () => {
    loadEmployment();

    const employmentSelect = document.getElementById("employmentSelect");

    employmentSelect.addEventListener('change', function () {
        const value = this.value;
        toggleWorkFields(value);
        loadPosition(value);
        loadBusinessline(value);
    });
});

function toggleWorkFields(employmentId) {   
    const employer                = document.getElementById("employer");
    const employmentDurationMonth = document.getElementById("employmentDurationMonth");
    const employmentDurationYear  = document.getElementById("employmentDurationYear");
    const officeAddress           = document.getElementById("officeAddress");
    const officePostalCode        = document.getElementById("officePostalCode");
    const officeTelephone         = document.getElementById("officeTelephone");
    const positionSelect          = document.getElementById("positionSelect");
    const businesslineSelect      = document.getElementById("businesslineSelect");
    const isIRT = parseInt(employmentId) === 4;
    [employer,employmentDurationMonth,employmentDurationYear,officeAddress,officePostalCode,officeTelephone]
    .forEach(field => {
        if (!field) return;
        const wrapper = field.closest(".form-group");

        if (isIRT) {
            wrapper.style.display = "none";
            field.value = "";
        } else {
            wrapper.style.display = "";
        }
    });

    if (isIRT) {
        if (positionSelect) positionSelect.selectedIndex = 0;
        if (businesslineSelect) businesslineSelect.selectedIndex = 0;
    }
}

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
            if (select.value) {
                toggleWorkFields(select.value);
                loadPosition(select.value);
                loadBusinessline(select.value);
            }
        });
}

function loadPosition(employmentId) {
    const select = document.getElementById('positionSelect');
    select.innerHTML = `<option value="">Pilih Jabatan</option>`;

    if (!employmentId) return;
    fetch(`${window.routes.position}?employment_id=${employmentId}`)
        .then(r => r.json())
        .then(res => {
            const list = extractArray(res);
            const selectedValue = select.dataset.selected;
            list.forEach(item => {
                if (!item.positionId) return;
                const isSelected = selectedValue == item.positionId ? 'selected' : '';
                select.innerHTML += `<option value="${item.positionId}" ${isSelected}>${item.description}</option>`;
            });
        });
}

function loadBusinessline(employmentId) {
    const select = document.getElementById('businesslineSelect');
    select.innerHTML = `<option value="">Pilih Bidang Usaha</option>`;

    if (!employmentId) return;
    fetch(`${window.routes.businessline}?employment_id=${employmentId}`)
        .then(r => r.json())
        .then(res => {
            const list = extractArray(res);
            const selectedValue = select.dataset.selected;
            list.forEach(item => {
                if (!item.businessLineId) return;
                const isSelected = selectedValue == item.businessLineId ? 'selected' : '';
                select.innerHTML += `<option value="${item.businessLineId}" ${isSelected}>${item.description}</option>`;
            });
        });
}