document.addEventListener("DOMContentLoaded", function () {
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

    const selectedValue = select.dataset.selected || '';

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

                const isSelected =
                    normSelected === normalize(value) ||
                    normSelected === normText ||
                    normText.includes(normSelected) ||
                    normSelected.includes(normText);

                const opt = document.createElement("option");
                opt.value = value;
                opt.textContent = text;

                if (isSelected) {
                    opt.selected = true;
                }

                select.appendChild(opt);
            });
        });
}

function normalize(str) {
    return String(str)
        .toLowerCase()
        .replace(/[\/,]/g, ' ')
        .replace(/\s+/g, ' ')
        .trim();
}