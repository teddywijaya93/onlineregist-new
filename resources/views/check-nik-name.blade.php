@extends('layouts.app')
@section('title','Profits Anywhere')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="auth-wrapper"> 
    <div class="container text-center"> 
        <div class="text-start mb-5"> 
            <a href="{{ url()->previous() }}" class="btn-back"><i class="fa-solid fa-arrow-left text-white"></i></a> 
        </div> 
        <h6 class="text-start referral-text mb-3">Langkah 3 dari 4</h6>
        <h4 class="text-white text-start start-text mb-3">Isi data pribadi kamu, yuk!</h4> 
        <p class="text-start insight-text mb-5">Lengkapi identitas kamu untuk trading lancar. Pastikan data yang kamu masukkan sudah benar.</p> 
        <div class="form-group text-start mb-4"> 
            <label class="form-label text-white text-form-global mb-2">Nama Lengkap</label> 
            <input type="text" id="name" name="name" class="form-control form-global" placeholder="Isi nama lengkap kamu" required> 
        </div>
        <div class="form-group text-start mb-4"> 
            <label class="form-label text-white text-form-global mb-2">Nomor e-KTP</label> 
            <input type="text" id="nik" name="nik" class="form-control form-global" placeholder="NIK (Nomor Induk Kependudukan)" autocomplete="off" maxlength="16" required> 
            <small id="nik-status" class="text-white mt-2 d-block"></small> 
        </div> 
        <button id="btnNext" type="button" disabled onclick="saveNikName()" class="btn btn-primary btn-regist w-100 mb-3">Lanjutkan</button> 
    </div>
</section>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const nikInput  = document.getElementById('nik');
const nikStatus = document.getElementById('nik-status');
const btnNext   = document.getElementById('btnNext');

let nikTimer;

nikInput.addEventListener('input', () => {
    clearTimeout(nikTimer);
    nikStatus.innerHTML = '';
    btnNext.disabled = true;

    const nik = nikInput.value.trim();

    if (nik.length < 16) {
        nikStatus.innerHTML =
            '<span class="text-warning">Masukkan 16 digit NIK</span>';
        return;
    }

    if (!/^\d{16}$/.test(nik)) {
        nikStatus.innerHTML =
            '<span class="text-danger">NIK harus angka</span>';
        return;
    }

    nikTimer = setTimeout(() => {
        checkNik(nik);
    }, 500);
});

function checkNik(nik) {
    nikStatus.innerHTML =
        '<span class="text-info">Memeriksa NIK...</span>';

    fetch("{{ route('check.nik') }}", {
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
        if (data.available === false) {
            nikStatus.innerHTML =
                `<span class="text-success">${data.message}</span>`;
            btnNext.disabled = false;
        } else {
            nikStatus.innerHTML =
                `<span class="text-danger">${data.message}</span>`;
            btnNext.disabled = true;
        }
    })
    .catch(() => {
        nikStatus.innerHTML =
            '<span class="text-danger">Gagal menghubungi server</span>';
        btnNext.disabled = true;
    });
}

function saveNikName() {
    fetch("{{ route('step.identity') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN':
                document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            name: document.getElementById('name').value,
            nik: document.getElementById('nik').value
        })
    })
    .then(res => res.json())
    .then(() => {
        window.location.href = "{{ route('create-account') }}";
    });
}
</script>

@endsection