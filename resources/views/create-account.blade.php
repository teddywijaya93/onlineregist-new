@extends('layouts.app')
@section('title','Profits Anywhere')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="auth-wrapper">
    <div class="container">
        <div class="text-start mb-5">
            <a href="{{ url()->previous() }}" class="btn-back"><i class="fa-solid fa-arrow-left text-white"></i></a>
        </div>
        <h3 class="text-white congrats-text text-start mb-2">Buat Akun Profits</h3>
        <p class="silahkan-cek-text text-start mb-5">Tentukan Username dan Password untuk akun Profits Anda.</p>
        <div class="form-group text-start mb-5">
            <label class="form-label text-white text-form-global mb-2">Username</label>
            <input type="text" id="username" class="form-control form-global" placeholder="Buat Username" autocomplete="off" required>
            <!-- CHECKLIST -->
            <div class="d-flex justify-content-between mt-2">
                <div class="username-rule">
                    <div id="rule-alphanumeric" class="rule-item">
                        <span class="rule-icon">✔</span>
                        <span>Mengandung huruf dan angka</span>
                    </div>
                    <div id="rule-nosymbol" class="rule-item">
                        <span class="rule-icon">✔</span>
                        <span>Tidak mengandung simbol</span>
                    </div>
                </div>
                <div id="charCounter" class="text-secondary small">0/15</div>
            </div>
            <!-- ERROR -->
            <small id="username-error" class="text-danger d-block mt-2"></small>

            <!-- RECOMMENDATION -->
            <div id="username-suggest-box" class="username-suggest d-none">
                <div class="suggest-header">Pilihan username untuk kamu
                    <i id="closeSuggest" class="fa fa-times"></i>
                </div>
                <div id="suggestList"></div>
            </div>
        </div>
        <div class="form-group text-start mb-4">
            <label class="form-label text-white text-form-global mb-2">Password</label>
            <div class="input-eye">
                <input type="password" id="password" name="password" class="form-control form-global" placeholder="Buat Password" autocomplete="off" required>
                <i class="fa fa-eye eye-btn" data-target="password"></i>
            </div>
            <!-- CHECKLIST -->
            <div class="d-flex justify-content-between mt-2">
                <div class="username-rule">
                    <div id="rule-pass-length" class="rule-item">
                        <span class="rule-icon">✔</span>
                        <span>Minimal 8 Karakter</span>
                    </div>
                    <div id="rule-pass-number" class="rule-item">
                        <span class="rule-icon">✔</span>
                        <span>Mengandung angka (0-9)</span>
                    </div>
                    <div id="rule-pass-symbol" class="rule-item">
                        <span class="rule-icon">✔</span>
                        <span>Mengandung karakter khusus (!@#$%^&*)</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group text-start mb-4">
            <label class="form-label text-white text-form-global mb-2">Konfirmasi Password</label>
            <div class="input-eye">
                <input type="password" id="confirmPassword" name="confirmPassword" class="form-control form-global" placeholder="Konfirmasi Password" autocomplete="off" required/>
                <i class="fa fa-eye eye-btn" data-target="confirmPassword"></i>
            </div>
            <!-- CHECKLIST -->
            <div class="d-flex justify-content-between mt-2">
                <div class="username-rule">
                    <div id="rule-pass-match" class="rule-item">
                        <span class="rule-icon">✔</span>
                        <span id="rule-pass-text">Password sudah sama</span>
                    </div>
                </div>
            </div>
        </div>
       <button type="button" onclick="submitAccount()" class="btn btn-primary btn-regist w-100 mb-3">Lanjutkan</button>
    </div>
</section>

<script>
window.routes = {
    checkUsername: "{{ route('check.username') }}",
    createAccount: "{{ route('create.account') }}"
};
</script>
<script src="{{ asset('js/registerCreateAccount.js') }}"></script>

@endsection