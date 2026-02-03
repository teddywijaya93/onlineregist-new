@extends('layouts.app')
@section('title','Profits Anywhere')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="auth-wrapper">
    <div class="container text-center">
        <div class="text-start mb-5">
            <a href="{{ url()->previous() }}" class="btn-back"><i class="fa-solid fa-arrow-left text-white"></i></a>
        </div>
        <h6 class="text-start referral-text mb-3">Langkah 2 dari 4</h6>
        <h4 class="text-white text-start start-text mb-3">Pilih tipe rekening saham</h4>
        <p class="text-start insight-text mb-5">Pilih tipe produk yang paling sesuai dengan visi dan kepribadian kamu.</p>
        <form id="productForm">
            <div class="product-wrapper">
                <!-- REGULAR -->
                <label class="customer-type-card">
                    <input type="radio" id="accountType" name="accountType" value="REGULAR">
                    <div class="product-content text-start text-white">
                        <div class="rek-saham-type mb-3"><span class="icon">📈</span> Regular</div>
                        <div class="rek-saham-txt mb-3">Fleksibel untuk Semua Transaksi Saham</div>
                        <p class="rek-saham-desc mb-0">Gunakan Rekening Dana Nasabah reguler untuk bertransaksi saham dan produk pasar modal secara umum dan pilihan emiten yang lengkap.</p>
                    </div>
                    <div class="radio-circle"></div>
                </label>

                <!-- SYARIAH -->
                <label class="customer-type-card">
                    <input type="radio" id="accountType" name="accountType" value="SYARIAH">
                    <div class="product-content text-start text-white">
                        <div class="rek-saham-type mb-3"><span class="icon">☪</span> Syariah</div>
                        <div class="rek-saham-txt mb-3">Investasi Sesuai Prinsip Syariah</div>
                        <p class="rek-saham-desc mb-0">Rekening Dana Nasabah berbasis syariah untuk transaksi saham yang masuk Daftar Efek Syariah (DES), bebas riba dan sesuai prinsip Islam.</p>
                    </div>
                    <div class="radio-circle"></div>
                </label>
            </div>  
           <button type="button" onclick="saveAccountType()" class="btn btn-primary btn-regist w-100 mb-3">Lanjutkan</button>
        </form>
    </div>
</section>

<script>
function saveAccountType() {
    const selected = document.querySelector('input[name="accountType"]:checked');

    if (!selected) {
        alert('Pilih tipe akun');
        return;
    }

    fetch("{{ route('step.account-type') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            accountType: selected.value
        })
    })
    .then(async res => {
        const text = await res.text();

        try {
            return JSON.parse(text);
        } catch {
            console.error('NOT JSON:', text);
            throw 'Invalid JSON';
        }
    })
    .then(data => {
        if (data.status) {
            window.location.href = "{{ route('check-nik-name') }}";
        } else {
            alert(data.message);
        }
    })
    .catch(err => {
        console.error(err);
        alert('Server error');
    });
}
</script>

@endsection