document.addEventListener("DOMContentLoaded", function () {
    const incomeRangeSelect        = document.getElementById("incomeRangeSelect");
    const primaryFundSelect        = document.getElementById("primaryFundSelect");
    const investmentObjectiveSelect = document.getElementById("investmentObjectiveSelect");

    if (!incomeRangeSelect) return;
    // Load Master Income Range
    fetch(window.routes.incomeRange)
        .then(res => res.json())
        .then(res => {
            incomeRangeSelect.innerHTML ="<option value=''>Pilih Penghasilan Per Bulan</option>";
            res.data.forEach(item => {
                incomeRangeSelect.innerHTML += 
                `<option value="${item.id}">
                    ${escapeHtml(item.description)}
                </option>`;
            });
        });

    // Load Master Primary Fund Source
    fetch(window.routes.primaryFundSource)
        .then(res => res.json())
        .then(res => {
            primaryFundSelect.innerHTML ="<option value=''>Pilih Sumber Dana</option>";
            res.data.forEach(item => {
                primaryFundSelect.innerHTML += 
                `<option value="${item.id}">
                    ${item.description}
                </option>`;
            });
        });

    // Load Master Investment Objective
    fetch(window.routes.investmentObjective)
        .then(res => res.json())
        .then(res => {
            investmentObjectiveSelect.innerHTML ="<option value=''>Pilih Tujuan Investasi</option>";
            res.data.forEach(item => {
                investmentObjectiveSelect.innerHTML += 
                `<option value="${item.id}">
                    ${item.description}
                </option>`;
            });
        });
});

function escapeHtml(text) {
    return text
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;");
}