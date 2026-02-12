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
});

function loadMasterDropdown(selectId, url, placeholder) {
    const select = document.getElementById(selectId);
    if (!select) return;

    const selectedValue = select.dataset.selected;
    fetch(url)
        .then(res => res.json())
        .then(res => {
            const list = res.data ?? res.datas ?? [];
            select.innerHTML = `<option value="">${placeholder}</option>`;
            list.forEach(item => {
                const value = item.id;
                if (!value) return;
                const isSelected = selectedValue == value ? 'selected' : '';
                select.innerHTML += `<option value="${value}" ${isSelected}> ${escapeHtml(item.description)}</option>`;
            });
        });
}

function escapeHtml(text) {
    return text
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;");
}