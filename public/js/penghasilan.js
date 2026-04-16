document.addEventListener("DOMContentLoaded", function () {
    initValidation();

    loadMasterDropdown(
        "incomeRangeSelect",
        window.routes.incomeRange,
        "Pilih Penghasilan Per Bulan"
    );

    loadMasterDropdown(
        "primaryFundSelect",
        window.routes.primaryFundSource,
        "Pilih Sumber Dana"
    );

    loadMasterDropdown(
        "investmentObjectiveSelect",
        window.routes.investmentObjective,
        "Pilih Tujuan Investasi"
    );

    loadMasterDropdown(
        "educationSelect",
        window.routes.education,
        "Pilih Pendidikan Terakhir"
    );

    loadMasterDropdown(
        "employmentSelect",
        window.routes.employment,
        "Pilih Pekerjaan"
    );
});

function loadMasterDropdown(selectId, url, placeholder) {
    const select = document.getElementById(selectId);
    if (!select) return;

    const selectedValue = (select.dataset.selected || '').trim();
    const hasSelected = selectedValue !== '';
    const gender = document.querySelector('input[name="gender"]')?.value;

    fetch(url)
        .then(res => res.json())
        .then(res => {
            const list = res.data ?? res.datas ?? [];

            select.innerHTML = `<option value="">${placeholder}</option>`;

            list.forEach(item => {
                const value = String(item.id);
                const text  = item.description || '';

                const normSelected = normalize(selectedValue);
                const normText     = normalize(text);

               const isSelected = hasSelected && (
                    normSelected === normalize(value) ||
                    normSelected === normText ||
                    normText.includes(normSelected) ||
                    normSelected.includes(normText)
                );

                const opt = document.createElement("option");
                opt.value = value;
                opt.textContent = text;

                if (isSelected) {
                    opt.selected = true;
                }

                select.appendChild(opt);
            });

            if (selectedValue && !select.value) {
                Array.from(select.options).forEach(opt => {
                    if (normalize(opt.text).includes(normalize(selectedValue))) {
                        select.value = opt.value;
                    }
                });
            }
        });
}

function normalize(str) {
    return String(str)
        .toLowerCase()
        .replace(/[\/,]/g, ' ')
        .replace(/\s+/g, ' ')
        .trim();
}

function initValidation() {
    const form = document.getElementById("financialForm");
    if (!form) return;

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        clearErrors();

        let isValid = true;

        isValid &= validateRequired("employmentSelect", "Pekerjaan Nasabah Wajib Diisi");
        isValid &= validateRequired("educationSelect", "Pendidikan Terakhir Wajib Diisi");
        isValid &= validateRequired("incomeRangeSelect", "Penghasilan Per Bulan Wajib Diisi");
        isValid &= validateRequired("primaryFundSelect", "Sumber Dana Wajib Diisi");
        isValid &= validateRequired("investmentObjectiveSelect", "Tujuan Investasi Wajib Diisi");

        if (!isValid) {
            scrollToFirstError();
            return;
        }

        form.submit();
    });

    // realtime clear error
    const fields = [
        "employmentType",
        "education",
        "mainIncomeRange",
        "primaryFundSources",
        "investmentObjective",
    ];

    fields.forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;

        el.addEventListener("input", () => clearFieldError(el));
        el.addEventListener("change", () => clearFieldError(el));
    });
}