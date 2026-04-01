document.addEventListener("DOMContentLoaded", () => {
    initKelurahanSearch();
});

function initKelurahanSearch() {
    const input = document.getElementById("kelurahanSearch");
    const dropdown = document.getElementById("kelurahanDropdown");

    const city = document.getElementById("citySelect");
    const kecamatan = document.getElementById("kecamatanSelect");
    const postal = document.getElementById("postalCode");

    if (!input || !dropdown) return;

    // =========================
    // DEBOUNCE FUNCTION
    // =========================
    function debounce(func, delay = 400) {
        let timeout;
        return (...args) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => func(...args), delay);
        };
    }

    // =========================
    // FETCH DATA
    // =========================
    async function fetchKelurahan(keyword) {
        try {
            dropdown.style.display = "block";
            dropdown.innerHTML = `<div class="dropdown-loading">Mencari Data Kelurahan</div>`;

            const res = await fetch(`${window.routes.kelurahan}?q=${keyword}`);
            const json = await res.json();

            return json.data || [];
        } catch (err) {
            console.error("Fetch error:", err);
            dropdown.innerHTML = `<div class="dropdown-error">Gagal Load Data</div>`;
            return [];
        }
    }

    // =========================
    // RENDER LIST
    // =========================
    function renderDropdown(list) {
        dropdown.innerHTML = "";

        if (!list.length) {
            dropdown.innerHTML = `<div class="dropdown-empty">Tidak ditemukan</div>`;
            return;
        }

        list.forEach(item => {
            const div = document.createElement("div");
            div.className = "dropdown-item";

            div.innerHTML = `
                <div class="item-main">${item.kelurahan}</div>
                <div class="item-sub">${item.kecamatan}, ${item.city}</div>
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

    // =========================
    // SEARCH HANDLER
    // =========================
    const handleSearch = debounce(async (e) => {
        const keyword = e.target.value.trim();

        if (keyword.length < 2) {
            dropdown.innerHTML = "";
            dropdown.style.display = "none";
            return;
        }

        const data = await fetchKelurahan(keyword);
        renderDropdown(data);
    }, 400);

    input.addEventListener("input", handleSearch);

    // =========================
    // CLICK OUTSIDE
    // =========================
    document.addEventListener("click", (e) => {
        if (!e.target.closest(".custom-select-wrapper")) {
            dropdown.style.display = "none";
        }
    });

    // =========================
    // FOCUS → SHOW AGAIN
    // =========================
    input.addEventListener("focus", () => {
        if (dropdown.innerHTML !== "") {
            dropdown.style.display = "block";
        }
    });
}