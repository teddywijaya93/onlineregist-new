const nikInput  = document.getElementById('nik');
let nikTimer;

// Disabled Button
btnNext.disabled = true;

// Validasi Name A-Z
const nameInput = document.getElementById("name");
nameInput.addEventListener("input", function () {
    // Hanya huruf A-Z dan spasi
    this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
});

// Input NIK
nikInput.addEventListener('input', () => {
    clearTimeout(nikTimer);
    btnNext.disabled = true;
    const nik = nikInput.value.trim();

    // kosong
    if (nik.length === 0) {
        return;
    }

    // harus 16 digit
    if (nik.length < 16) {
        return;
    }

    // harus angka
    if (!/^\d{16}$/.test(nik)) {
        return;
    }

    nikTimer = setTimeout(() => {
        checkNik(nik);
    }, 500);
});

// Check NIK Via API
function checkNik(nik) {

    Swal.fire({
        title: 'Memeriksa NIK...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch(window.routes.checkNik, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ identity: nik })
    })
    .then(res => res.json())
    .then(data => {
        Swal.close();
        if (data.available === false) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: data.message
            });
            btnNext.disabled = false;
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: data.message
            });
            btnNext.disabled = true;
        }
    })
    .catch(() => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Gagal menghubungi server'
        });

        btnNext.disabled = true;
    });
}

// Save NIK & Name
function saveNikName() {
    const name = document.getElementById('name').value.trim();
    const nik  = document.getElementById('nik').value.trim();
    if (!name || !nik) {
        Swal.fire({
            icon: 'warning',
            title: 'Lengkapi Data',
            text: 'Nama dan NIK wajib diisi'
        });
        return;
    }

    fetch(window.routes.saveNikName, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN':
                document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            name: name,
            nik: nik
        })
    })
    .then(res => res.json())
    .then(() => {
        window.location.href = window.routes.createAccount;
    });
}