document.addEventListener("DOMContentLoaded", async () => {
    initReferenceRelation();
    initKtpUpload();
    initValidation();

    await loadEmployment();
    const employmentSelect = document.getElementById("employmentSelect");

    // FORCE LOAD BUSINESSLINE SETELAH EMPLOYMENT KE-SET
    if (employmentSelect && employmentSelect.value) {
        loadBusinessline(employmentSelect.value);
    }

    employmentSelect?.addEventListener("change", function () {
        loadBusinessline(this.value);
    });
});

// RELATION RULE
const RELATION_RULES = [
    { gender: "1", marital: "1", options: [{ id: "1", label: "Istri", title: "Data Istri" }] },
    { gender: "2", marital: "1", options: [{ id: "2", label: "Suami", title: "Data Suami" }] },
    // Belum Menikah
    {
        gender: "1", marital: "2",
        options: [
            { id: "3", label: "Ayah", title: "Data Orang Tua / Saudara / Wali" },
            { id: "4", label: "Ibu", title: "Data Orang Tua / Saudara / Wali" },
            { id: "5", label: "Saudara", title: "Data Orang Tua / Saudara / Wali" },
            { id: "6", label: "Wali", title: "Data Orang Tua / Saudara / Wali" }
        ]
    },
    {
        gender: "2", marital: "2",
        options: [
            { id: "3", label: "Ayah", title: "Data Orang Tua / Saudara / Wali" },
            { id: "4", label: "Ibu", title: "Data Orang Tua / Saudara / Wali" },
            { id: "5", label: "Saudara", title: "Data Orang Tua / Saudara / Wali" },
            { id: "6", label: "Wali", title: "Data Orang Tua / Saudara / Wali" }
        ]
    },
    // Janda / Duda
    {
        gender: "1", marital: "4",
        options: [
            { id: "3", label: "Ayah", title: "Data Orang Tua / Saudara / Wali" },
            { id: "4", label: "Ibu", title: "Data Orang Tua / Saudara / Wali" },
            { id: "5", label: "Saudara", title: "Data Orang Tua / Saudara / Wali" },
            { id: "7", label: "Anak", title: "Data Orang Tua / Saudara / Wali" },
            { id: "6", label: "Wali", title: "Data Orang Tua / Saudara / Wali" }
        ]
    },
    {
        gender: "2", marital: "3",
        options: [
            { id: "3", label: "Ayah", title: "Data Orang Tua / Saudara / Wali" },
            { id: "4", label: "Ibu", title: "Data Orang Tua / Saudara / Wali" },
            { id: "5", label: "Saudara", title: "Data Orang Tua / Saudara / Wali" },
            { id: "7", label: "Anak", title: "Data Orang Tua / Saudara / Wali" },
            { id: "6", label: "Wali", title: "Data Orang Tua / Saudara / Wali" }
        ]
    }
];

// INIT RELATION
function initReferenceRelation() {
    const gender = document.getElementById('gender')?.value;
    const marital = document.getElementById('maritalStatus')?.value;
    const select = document.getElementById('beneficiaryRelationSelect');

    if (!select) return;

    const selected = select.dataset.selected || '';

    select.innerHTML = `<option value="">Pilih Hubungan</option>`;

    const rule = RELATION_RULES.find(r =>
        r.gender === gender && r.marital === marital
    );

    if (!rule) {
        console.warn('No relation rule found:', { gender, marital });
        return;
    }

    rule.options.forEach(optData => {
        const opt = document.createElement('option');
        opt.value = optData.id;
        opt.textContent = optData.label;

        if (String(optData.id) === String(selected)) {
            opt.selected = true;
        }

        select.appendChild(opt);
    });

    updateReferenceTitle(rule);
}

// UPDATE TITLE
function updateReferenceTitle(rule) {
    const title = document.getElementById('referenceTitle');
    if (!title) return;

    title.innerText = rule.options[0]?.title || 'Data Referensi';
}

// LOAD EMPLOYMENT
async function loadEmployment() {
    const select = document.getElementById("employmentSelect");
    if (!select) return;

    const selected = select.dataset.selected || '';

    try {
        const res = await fetch(window.routes.employment);
        const json = await res.json();
        const list = json.data || json.datas || [];

        select.innerHTML = `<option value="">Pilih Pekerjaan</option>`;

        list.forEach(item => {
            const opt = document.createElement("option");
            opt.value = item.id;
            opt.textContent = item.description;

            if (String(item.id) === String(selected)) {
                opt.selected = true;
            }

            select.appendChild(opt);
        });

        if (selected) {
            select.value = selected;
        }

    } catch (err) {
        console.error("Employment Error:", err);
    }
}

// LOAD BUSINESS LINE
function loadBusinessline(employmentId) {
    const select = document.getElementById("businesslineSelect");
    if (!select) return;

    const selected = select.dataset.selected || '';

    select.innerHTML = `<option value="">Pilih Bidang Usaha</option>`;

    fetch(`${window.routes.businessline}?employment_id=${employmentId}`)
        .then(res => res.json())
        .then(res => {
            const list = res.data || res.datas || [];

            list.forEach(item => {
                if (!item.businessLineId) return;

                const opt = document.createElement("option");
                opt.value = item.businessLineId;
                opt.textContent = item.description;

                if (String(item.businessLineId) === String(selected)) {
                    opt.selected = true;
                }

                select.appendChild(opt);
            });
        })
        .catch(err => console.error("BusinessLine Error:", err));
}

function initKtpUpload() {
    const input = document.getElementById("ktpFileInput");
    const btn = document.getElementById("btnUploadKtp");
    const preview = document.getElementById("ktpPreview");
    const wrapper = document.getElementById("ktpPreviewWrapper");
    const removeBtn = document.getElementById("removeKtp");

    const fileNameInput = document.getElementById("beneficiaryKtpFileName");
    const imageInput = document.getElementById("beneficiaryKtpImage");

    if (!input || !btn) return;

    // klik button → trigger file input
    btn.addEventListener("click", () => input.click());

    // saat file dipilih
    input.addEventListener("change", function () {
        const file = this.files[0];
        if (!file) return;

        // VALIDASI TYPE
        if (!file.type.startsWith("image/")) {
            alert("File harus berupa gambar");
            return;
        }

        // VALIDASI SIZE (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert("Ukuran maksimal 2MB");
            return;
        }

        // preview image
        const reader = new FileReader();

        reader.onload = function (e) {
            preview.src = e.target.result;
            wrapper.classList.remove("d-none");

            // SET KE INPUT (BASE64)
            imageInput.value = e.target.result;
            fileNameInput.value = file.name;
        };

        reader.readAsDataURL(file);
    });

    // hapus gambar
    removeBtn?.addEventListener("click", () => {
        input.value = "";
        preview.src = "";
        wrapper.classList.add("d-none");

        imageInput.value = "";
        fileNameInput.value = "";
    });
}

function initValidation() {
    const form = document.getElementById("relationForm");
    if (!form) return;

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        clearErrors();

        let isValid = true;

        isValid &= validateRequired("beneficiaryName", "Nama Referensi Perorangan Wajib Diisi");
        isValid &= validateRequired("beneficiaryRelationSelect", "Hubungan dengan Nasabah Wajib Diisi");
        isValid &= validateRequired("employmentSelect", "Pekerjaan Referensi Perorangan Wajib Diisi");
        isValid &= validateRequired("businesslineSelect", "Bidang Usaha Referensi Perorangan Wajib Diisi");
        isValid &= validateRequired("beneficiaryOwnerEmployerName", "Pekerjaan Referensi Perorangan Wajib Diisi");
        isValid &= validateRequired("beneficiaryOwnerOfficeAddress", "Alamat Referensi Perorangan Wajib Diisi");
        isValid &= validateRequired("beneficiaryKtpImage", "Upload KTP Referensi Perorangan Wajib Diisi");

        if (!isValid) {
            scrollToFirstError();
            return;
        }

        form.submit();
    });

    // realtime clear error
    const fields = [
        "beneficiaryName",
        "beneficiaryRelationSelect",
        "employmentSelect",
        "businesslineSelect",
        "beneficiaryOwnerEmployerName",
        "beneficiaryOwnerOfficeAddress",
        "beneficiaryKtpImage"
    ];

    fields.forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;

        el.addEventListener("input", () => clearFieldError(el));
        el.addEventListener("change", () => clearFieldError(el));
    });
}