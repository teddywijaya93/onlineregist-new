document.addEventListener("DOMContentLoaded", () => {
    loadSelect("genderSelect", window.routes.gender);
    loadSelect("religionSelect", window.routes.religion);
    loadSelect("maritalSelect", window.routes.marital);
    loadSelect("educationSelect", window.routes.education);
});

function loadSelect(id, url) {
    const select = document.getElementById(id);
    if (!select) return;

    const selected = (select.dataset.selected || '').toUpperCase();

    fetch(url)
        .then(r => r.json())
        .then(res => {
            const list = res.data || res.datas || [];

            select.innerHTML = `<option value="">Pilih</option>`;

            list.forEach(item => {
                const opt = document.createElement("option");
                opt.value = item.description; // API pakai description
                opt.textContent = item.description;

                if (item.description.toUpperCase() === selected) {
                    opt.selected = true;
                }

                select.appendChild(opt);
            });
        });
}