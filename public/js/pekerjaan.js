document.addEventListener('DOMContentLoaded', async () => {
    let employmentType = document.getElementById("employmentType")?.value;
    if (employmentType) {
        const employmentId = await getEmploymentId(employmentType);

        if (employmentId) {
            loadBusinessline(employmentId);
            loadPosition(employmentId);
        }
    }

    initValidation();

    const monthInput = document.getElementById("employmentDurationMonth");
    if (monthInput) {
        monthInput.addEventListener("input", function () {
            let val = this.value.replace(/\D/g, "");
            if (val.length > 1 && val.startsWith("0")) {
                val = val.replace(/^0+/, "");
            }

            // allow "0"
            if (val === "0") {
                this.value = "0";
                return;
            }

            // max 12
            if (parseInt(val) > 12) {
                val = "12";
            }

            this.value = val;
        });
    }

    const yearInput = document.getElementById("employmentDurationYear");
    if (yearInput) {
        yearInput.addEventListener("input", function () {
            let val = this.value.replace(/\D/g, "");
            if (val.length > 1 && val.startsWith("0")) {
                val = val.replace(/^0+/, "");
            }

            // allow "0"
            if (val === "0") {
                this.value = "0";
                return;
            }

            this.value = val;
        });
    }
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

function initValidation() {
    const form = document.getElementById("employmentForm");
    if (!form) return;

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        clearErrors();

        let isValid = true;

        isValid &= validateRequired("employer", "Nama Perusahaan/Tempat Bekerja Wajib Diisi");
        isValid &= validateRequired("businesslineSelect", "Bidang Usaha Wajib Diisi");
        isValid &= validateRequired("positionSelect", "Jabatan Wajib Diisi");
        isValid &= validateRequired("officeAddress", "Alamat Perusahaan Wajib Diisi");
        isValid &= validateRequired("officeTelephone", "Telepon Kantor Wajib Diisi");
        isValid &= validateMinLength("officeTelephone", 9, "Minimal Telepon Kantor 9 Digit");
        isValid &= validateRequired("employmentDurationYear", "Tahun Bekerja Wajib Diisi");
        isValid &= validateRequired("employmentDurationMonth", "Bulan Bekerja Wajib Diisi");

        if (!isValid) {
            scrollToFirstError();
            return;
        }

        form.submit();
    });

    // realtime clear error
    const fields = [
        "employer",
        "businessLine",
        "employmentPosition",
        "officeAddress",
        "officeTelephone",
        "employmentDurationYear",
        "employmentDurationMonth",
    ];

    fields.forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;

        el.addEventListener("input", () => clearFieldError(el));
        el.addEventListener("change", () => clearFieldError(el));
    });
}

function validateMinLength(id, min, message) {
    const el = document.getElementById(id);
    if (!el) return true;

    const val = el.value.replace(/\D/g, ""); // ambil angka saja

    if (val.length < min) {
        el.classList.add("is-invalid");

        let error = el.parentElement.querySelector(".invalid-feedback");

        if (!error) {
            error = document.createElement("div");
            error.className = "invalid-feedback";
            el.parentElement.appendChild(error);
        }

        error.innerText = message;
        return false;
    }
    return true;
}