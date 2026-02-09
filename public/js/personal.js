document.addEventListener("DOMContentLoaded", function () {
    loadGender();
    loadReligion();
    loadMarital();
});

function normalize(text) {
    return text
        .toUpperCase()
        .replace(/\s+/g, '')
        .replace('-', '');
}

function extractArray(res) {
    if (Array.isArray(res?.data)) return res.data;
    if (Array.isArray(res?.datas)) return res.datas;
    return [];
}

function loadGender() {
    const select = document.getElementById("genderSelect");
    if (!select) return;

    const ocrValue = normalize(select.dataset.selected);

    fetch(window.routes.gender)
        .then(res => res.json())
        .then(res => {
            const list = extractArray(res);
            if (!list.length) {
                console.error('Gender Master Empty / Invalid', res);
                return;
            }
            select.innerHTML = "<option value=''>Pilih Jenis Kelamin</option>";
            list.forEach(item => {
                const option = document.createElement("option");
                option.value = item.id;
                option.textContent = item.description;

                const master = normalize(item.description);

                if (
                    (ocrValue.includes("LAKI") && master === "PRIA") ||
                    (ocrValue.includes("PEREMPUAN") && master === "WANITA")
                ) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
        })
        .catch(err => console.error('Gender fetch error', err));
}

function loadReligion() {
    const select = document.getElementById("religionSelect");
    const selectedOCR = normalize(select.dataset.selected || '');

    fetch(window.routes.religion)
        .then(res => res.json())
        .then(res => {
            select.innerHTML = "<option value=''>Pilih Agama</option>";
            res.data.forEach(item => {
                const option = document.createElement("option");
                option.value = item.id;
                option.textContent = item.description;
                if (normalize(item.description) === selectedOCR) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
        });
}

function loadMarital() {
    const select = document.getElementById("maritalSelect");
    if (!select) return;

    const ocrValue = normalize(select.dataset.selected);

    fetch(window.routes.marital)
        .then(res => res.json())
        .then(res => {
            const list = extractArray(res);
            if (!list.length) {
                console.error('Marital Master Empty / Invalid', res);
                return;
            }
            select.innerHTML = "<option value=''>Pilih Status Perkawinan</option>";
            list.forEach(item => {
                const option = document.createElement("option");
                option.value = item.id;
                option.textContent = item.description;

                const master = normalize(item.description);

                if (
                    (ocrValue === "KAWIN" && master === "MENIKAH") ||
                    (ocrValue === "BELUMKAWIN" && master === "BELUMMENIKAH") ||
                    (ocrValue === "JANDA" && master === "JANDA") ||
                    (ocrValue === "DUDA" && master === "DUDA")
                ) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
        })
        .catch(err => console.error('Marital fetch error', err));
}