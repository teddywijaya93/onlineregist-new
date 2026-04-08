document.addEventListener("DOMContentLoaded", function () {
    const bankSelect = document.getElementById("bankSelect");

    fetch(window.routes.bank)
        .then(res => res.json())
        .then(res => {
            const selectedValue = (bankSelect.dataset.selected || '').toLowerCase().trim();

            bankSelect.innerHTML = "<option value=''>Pilih Bank</option>";

            res.data.forEach(item => {
                const itemId   = String(item.id);
                const itemDesc = (item.description || '').toLowerCase().trim();

                const isSelected = selectedValue === itemId || selectedValue === itemDesc
                    ? 'selected'
                    : '';

                bankSelect.innerHTML += `<option value="${itemId}" ${isSelected}>${item.description}</option>`;
            });
        });
});