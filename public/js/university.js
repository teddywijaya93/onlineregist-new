document.addEventListener("DOMContentLoaded", () => {
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

        const year  = parseInt(document.getElementById("employmentDurationYear").value || 0);
        const month = parseInt(document.getElementById("employmentDurationMonth").value || 0);
        if (year === 0 && month === 0) {
            showError(
                document.getElementById("employmentDurationMonth"),
                "Lama Kuliah Min 1 Bulan"
            );
            isValid = false;
        }

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