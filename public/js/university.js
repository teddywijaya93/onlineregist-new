document.addEventListener("DOMContentLoaded", () => {
    initValidation();

    const monthInput = document.getElementById("employmentDurationMonth");
    if (monthInput) {
        monthInput.addEventListener("input", function () {
            // hanya angka
            this.value = this.value.replace(/\D/g, "");

            // kalau 0 → kosongkan
            if (this.value === "0") {
                this.value = "";
                return;
            }

            // max 12
            if (parseInt(this.value) > 12) {
                this.value = "12";
            }
        });
    }
});

function initValidation() {
    const form = document.getElementById("universityForm");
    if (!form) return;

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        clearErrors();

        let isValid = true;

        isValid &= validateRequired("employer", "Nama Universitas Wajib Diisi");
        isValid &= validateRequired("officeAddress", "Alamat Universitas Wajib Diisi");
        isValid &= validateRequired("employmentDurationYear", "Tahun Wajib Diisi");
        isValid &= validateRequired("employmentDurationMonth", "Bulan Wajib Diisi");

        if (!isValid) {
            scrollToFirstError();
            return;
        }

        form.submit();
    });

    // realtime clear error
    const fields = [
        "employer",
        "officeAddress",
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