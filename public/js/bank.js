document.addEventListener("DOMContentLoaded", function () {
    initValidation();

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

            $('#bankSelect').select2({
                placeholder: 'Pilih Bank',
                width: '100%',
                dropdownAutoWidth: true,
                minimumResultsForSearch: 0,
               
            });

            $(document).on('select2:open', function () {
                document.querySelector('.select2-search__field').placeholder = 'Cari Bank';
            });
        });
});

function initValidation() {
    const form = document.getElementById("bankForm");
    if (!form) return;

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        clearErrors();

        let isValid = true;

        isValid &= validateRequired("bankSelect", "Bank Tujuan Penarikan Wajib Diisi");
        isValid &= validateRequired("bankAccountOwner", "Nomor Pemilik Rekening Wajib Diisi");
        isValid &= validateRequired("bankAccountNumber", "Nomor Rekening Wajib Diisi");

        if (!isValid) {
            scrollToFirstError();
            return;
        }

        form.submit();
    });

    // realtime clear error
    const fields = [
        "bankName",
        "bankAccountOwner",
        "bankAccountNumber",
    ];

    fields.forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;

        el.addEventListener("input", () => clearFieldError(el));
        el.addEventListener("change", () => clearFieldError(el));
    });
}