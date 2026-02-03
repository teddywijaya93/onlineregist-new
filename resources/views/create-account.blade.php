@extends('layouts.app')
@section('title','Profits Anywhere')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="auth-wrapper">
    <div class="container text-center">
        <div class="text-start mb-5">
            <a href="{{ url()->previous() }}" class="btn-back"><i class="fa-solid fa-arrow-left text-white"></i></a>
        </div>
        <h6 class="text-start referral-text mb-3">Langkah 4 dari 4</h6>
        <h4 class="text-white text-start start-text mb-3">Buat akun trading kamu</h4>
        <p class="text-start insight-text mb-5">Silakan buat username terlebih dahulu, kemudian gunakan email dan nomor telepon yang masih aktif.</p>
        <div class="form-group text-start mb-4">
            <label class="form-label text-white text-form-global mb-2">Username</label>
            <input type="text" id="username" name="username" class="form-control form-global" placeholder="Buat Username" autocomplete="off" required>
            <small id="username-status" class="text-white d-block mt-2"></small>
            <!-- Rekomendasi -->
            <div id="username-recommendation" class="mt-2"></div>
        </div>
        <div class="form-group text-start mb-4">
            <label class="form-label text-white text-form-global mb-2">Email</label>
            <input type="text" id="email" name="email" class="form-control form-global" placeholder="e.g the-east@gmail.com" autocomplete="off" required>
        </div>
        <div class="form-group text-start mb-4">
            <label class="form-label text-white text-form-global mb-2">Nomor Telepon</label>
            <input type="text" id="mobilePhone" name="mobilePhone" class="form-control form-global" placeholder="e.g 081212345678" autocomplete="off" required pattern="^[0-9]{10,}$" minlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '')"/>
        </div>
       <button type="button" onclick="submitAccount()" class="btn btn-primary btn-regist w-100 mb-3">Buat Akun</button>
    </div>
</section>

<!-- JS -->
<script>
let typingTimer;
const delay = 700;

const usernameInput = document.getElementById('username');
const statusEl = document.getElementById('username-status');
const recEl = document.getElementById('username-recommendation');

usernameInput.addEventListener('keyup', () => {
    clearTimeout(typingTimer);

    const username = usernameInput.value.trim();
    statusEl.innerHTML = '';
    recEl.innerHTML = '';

    if (username.length < 5) {
        statusEl.innerHTML = '<span class="text-warning">Username harus 7 sampai 15 karakter, kombinasi huruf dan angka, tanpa simbol</span>';
        return;
    }

    typingTimer = setTimeout(() => {
        checkUsername(username);
    }, delay);
});

function checkUsername(username) {
    statusEl.innerHTML = '<span class="text-info">Memeriksa username...</span>';

    fetch("{{ route('check.username') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ username })
    })
    .then(res => res.json())
    .then(data => {
        recEl.innerHTML = '';

        if (data.status === true) {
            statusEl.innerHTML = '<span class="text-success">Username tersedia ✓</span>';
        } else {
            statusEl.innerHTML =
                `<span class="text-danger">${data.messageBody}</span>`;

            if (data.alternatives && data.alternatives.length) {
                renderAlternatives(data.alternatives);
            }
        }
    })
    .catch(() => {
        statusEl.innerHTML =
            '<span class="text-danger">Gagal cek username</span>';
    });
}

function renderAlternatives(list) {
    let html = `
        <small class="text-white">Rekomendasi username:</small>
        <div class="d-flex flex-wrap gap-2 mt-2">
    `;

    list.forEach(username => {
        html += `
            <button type="button"
                    class="btn btn-outline-light btn-sm"
                    onclick="useUsername('${username}')">
                ${username}
            </button>
        `;
    });

    html += '</div>';
    recEl.innerHTML = html;
}

function useUsername(username) {
    usernameInput.value = username;
    recEl.innerHTML = '';
    checkUsername(username);
}

function submitAccount() {

    const username    = document.getElementById('username')?.value ?? "";
    const email       = document.getElementById('email')?.value ?? "";
    const mobilePhone = document.getElementById('mobilePhone')?.value ?? "";

    const payload = { username, email, mobilePhone };

    fetch("{{ route('create.account.submit') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(async r => {

        // Jika response tidak JSON, berarti Laravel kirim HTML error page
        const text = await r.text();
        try {
            return JSON.parse(text);
        } catch {
            throw new Error("INVALID_JSON");
        }
    })
    .then(res => {

        if (!res?.status) {
            alert(res?.message || "Gagal membuat akun.");
            return;
        }

        const cred = res?.data?.data ?? {};

        alert(
            "Akun berhasil!\n\n" +
            "Username: " + (cred.username ?? "-") + "\n" +
            "Password: " + (cred.password ?? "-") + "\n" +
            "PIN: " + (cred.pin ?? "-")
        );
    })
    .catch(err => {
        console.error("ERROR:", err);
        alert("Terjadi kesalahan jaringan / response tidak valid.");
    });
}
</script>

@endsection