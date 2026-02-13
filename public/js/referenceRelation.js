document.addEventListener("DOMContentLoaded", function () {
    const genderSelect  = document.getElementById("genderSelect");
    const maritalSelect = document.getElementById("maritalSelect");
    const select        = document.getElementById("referenceSelect");
    const title         = document.getElementById("referenceTitle");
    const selectedValue = select.dataset.selected;

    // Load Master Gender
    if (genderSelect) {
        const selectedValue = genderSelect.dataset.selected;
        fetch(window.routes.jenis_kelamin_relasi)
            .then(res => res.json())
            .then(res => {
                const list = res.datas ?? res.data ?? [];
                genderSelect.innerHTML = "<option value=''>Pilih Jenis Kelamin</option>";
                list.forEach(item => {
                    const isSelected = selectedValue == item.id ? 'selected' : '';
                    genderSelect.innerHTML += `<option value="${item.id}" ${isSelected}>${item.description}</option>`;
                });
            });
    }

    // Load Master Gender
    if (maritalSelect) {
        const selectedValue = maritalSelect.dataset.selected;
        fetch(window.routes.status_perkawinan_relasi)
            .then(res => res.json())
            .then(res => {
                const list = res.datas ?? res.data ?? [];
                maritalSelect.innerHTML = "<option value=''>Pilih Status Perkawinan</option>";
                list.forEach(item => {
                    const isSelected = selectedValue == item.id ? 'selected' : '';
                    maritalSelect.innerHTML += `<option value="${item.id}" ${isSelected}>${item.description}</option>`;
                });
            });
    }

    // Load Master Reference Relation
    fetch(window.routes.referenceRelation)
        .then(res => res.json())
        .then(res => {
            if (title && res.title) {
                title.innerText = res.title;
            }

            // Aktifkan form sesuai tipe
            if (res.form_type === 'spouse') {
                document.getElementById('formSpouse').style.display = 'block';
                document.getElementById('formFamily').style.display = 'none';
            } else {
                document.getElementById('formSpouse').style.display = 'none';
                document.getElementById('formFamily').style.display = 'block';
            }
            
            const list = res.datas ?? [];
            select.innerHTML = "<option value=''>Pilih Hubungan dengan Relasi</option>";
            list.forEach(item => {
                const isSelected = selectedValue == item.id ? 'selected' : '';
                select.innerHTML += `<option value="${item.id}" ${isSelected}>${item.description} </option>`;
            });
        });
});

function activateForm(activeId, inactiveId) {
    const activeForm   = document.getElementById(activeId);
    const inactiveForm = document.getElementById(inactiveId);

    // show / hide
    activeForm.style.display = 'block';
    inactiveForm.style.display = 'none';

    // enable active inputs
    activeForm.querySelectorAll('input, select, textarea').forEach(el => {
        el.disabled = false;
    });

    // disable inactive inputs (PENTING supaya tidak ikut submit)
    inactiveForm.querySelectorAll('input, select, textarea').forEach(el => {
        el.disabled = true;
    });
}