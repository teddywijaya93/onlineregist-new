document.addEventListener("DOMContentLoaded", function () {
    const bankSelect        = document.getElementById("bankSelect");

    // Load Master Investment Objective
    fetch(window.routes.bank)
        .then(res => res.json())
        .then(res => {
            bankSelect.innerHTML ="<option value=''>Pilih Bank</option>";
            res.data.forEach(item => {
                bankSelect.innerHTML += 
                `<option value="${item.id}">
                    ${item.description}
                </option>`;
            });
        });
});