const kelurahanCache = {};
let lastResult = [];

document.addEventListener("DOMContentLoaded", () => {
    initKelurahanSearch();
});

function initKelurahanSearch() {

    function debounce(func, delay = 400) {
        let timeout;
        return (...args) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => func(...args), delay);
        };
    }

    function normalize(text) {
        return (text || '').toLowerCase().replace(/[^a-z]/g, '');
    }

    async function fetchKelurahan(keyword, dropdown, silent = false) {
        try {
            const key = keyword.toLowerCase();

            // Kalau sudah pernah fetch → pakai cache
            if (kelurahanCache[key]) {
                return kelurahanCache[key];
            }

            if (!silent) {
                dropdown.style.display = "block";
                dropdown.innerHTML = `<div class="dropdown-loading">Mencari Data Kelurahan</div>`;
            }

            const res = await fetch(`${window.routes.kelurahan}?q=${keyword}`);
            const json = await res.json();

            const data = json.data || [];

            // simpan ke cache
            kelurahanCache[key] = data;
            lastResult = data;

            return data;

        } catch (err) {
            if (!silent) {
                dropdown.innerHTML = `<div class="dropdown-error">Gagal Load Data</div>`;
            }
            return [];
        }
    }

    // GENERIC DROPDOWN ENGINE
    function bindDropdown(config) {
        const {
            inputId,
            dropdownId,
            cityId,
            kecamatanId,
            postalId
        } = config;

        const input = document.getElementById(inputId);
        const dropdown = document.getElementById(dropdownId);
        const city = document.getElementById(cityId);
        const kecamatan = document.getElementById(kecamatanId);
        const postal = document.getElementById(postalId);

        if (!input || !dropdown) return;

        function render(list) {
            dropdown.innerHTML = "";

            if (!list.length) {
                dropdown.innerHTML = `<div class="dropdown-empty">Tidak ditemukan</div>`;
                return;
            }

            list.forEach(item => {
                const div = document.createElement("div");
                div.className = "dropdown-item";

                div.innerHTML = `
                    <div class="item-main">${item.kelurahan}, ${item.kecamatan}, ${item.city}</div>
                    <div class="item-sub">Kode Pos: ${item.postalCode}</div>
                `;

                div.addEventListener("click", () => {
                    input.value = item.kelurahan;

                    city.value = item.city || "";
                    kecamatan.value = item.kecamatan || "";
                    postal.value = item.postalCode || "";

                    dropdown.innerHTML = "";
                    dropdown.style.display = "none";
                });

                dropdown.appendChild(div);
            });
        }

        const handleSearch = debounce(async (e) => {
            const keyword = e.target.value.trim();

            if (keyword.length < 2) {
                dropdown.innerHTML = "";
                dropdown.style.display = "none";
                return;
            }

            const data = await fetchKelurahan(keyword, dropdown);
            render(data);
        }, 400);

        input.addEventListener("input", handleSearch);

        input.addEventListener("focus", () => {
            if (dropdown.innerHTML !== "") {
                dropdown.style.display = "block";
            }
        });

        document.addEventListener("click", (e) => {
            if (!e.target.closest(".custom-select-wrapper")) {
                dropdown.style.display = "none";
            }
        });

        // AUTO FILL OCR SUPPORT
        setTimeout(async () => {
            const val = input.value.trim();
            if (!val) return;

            let list = lastResult;
            if (!list.length) {
                list = await fetchKelurahan(val, dropdown, true);
            }

            if (!list.length) return;
            const match = list.find(i =>
                normalize(i.kelurahan) === normalize(val)
            ) || list[0];

            city.value = match.city || "";
            kecamatan.value = match.kecamatan || "";
            postal.value = match.postalCode || "";
        }, 300);
    }

    // INIT 2 FIELD

    // Main
    bindDropdown({
        inputId: "kelurahanSearch",
        dropdownId: "kelurahanDropdown",
        cityId: "citySelect",
        kecamatanId: "kecamatanSelect",
        postalId: "postalCode"
    });

    // Domisili
    bindDropdown({
        inputId: "residenceKelurahan",
        dropdownId: "residenceKelurahanDropdown",
        cityId: "residenceCity",
        kecamatanId: "residenceKecamatan",
        postalId: "residencePostalCode"
    });

    // Domisili
    bindDropdown({
        inputId: "officeKelurahan",
        dropdownId: "officeKelurahanDropdown",
        cityId: "officeCity",
        kecamatanId: "officeKecamatan",
        postalId: "officePostalCode"
    });
}