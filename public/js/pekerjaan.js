document.addEventListener("DOMContentLoaded", function () {
    const employmentSelect   = document.getElementById("employmentSelect");
    const positionSelect     = document.getElementById("positionSelect");
    const businesslineSelect = document.getElementById("businesslineSelect");

    if (!employmentSelect) return;
    fetch(window.routes.employment)
        .then(res => res.json())
        .then(res => {
            employmentSelect.innerHTML ="<option value=''>Pilih Pekerjaan</option>";
            res.data.forEach(item => {
                employmentSelect.innerHTML += 
                `<option value="${item.id}">
                    ${item.description}
                </option>`;
            });
        });

        employmentSelect.addEventListener("change", function () {
        const employmentId = this.value;

        // Reset
        positionSelect.innerHTML ="<option value=''>Pilih Jabatan</option>";
        businesslineSelect.innerHTML ="<option value=''>Pilih Bidang Usaha</option>";

        if (!employmentId) return;
        // Load Master Jabatan
        fetch(`${window.routes.position}?employment_id=${employmentId}`)
            .then(res => res.json())
            .then(res => {
                res.data.forEach(item => {
                    positionSelect.innerHTML +=
                    `<option value="${item.positionId}">
                        ${item.description}
                    </option>`;
                });
            });

        // Load Bidang Usaha
        fetch(`${window.routes.businessline}?employment_id=${employmentId}`)
            .then(res => res.json())
            .then(res => {
                res.data.forEach(item => {
                    businesslineSelect.innerHTML +=
                    `<option value="${item.businessLineId}">
                        ${item.description}
                    </option>`;
                });
            });
    });
});