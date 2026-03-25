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
                <div id="charCounter" class="text-secondary small">
                    0/15
                </div>
            </div>
            <!-- ERROR -->
            <small id="username-error" class="text-danger d-block mt-2"></small>

            <!-- RECOMMENDATION -->
            <div id="username-recommendation" class="mt-2"></div>
        </div>
        <div class="form-group text-start mb-4">
            <label class="form-label text-white text-form-global mb-2">Email</label>
            <input type="text" id="email" name="email" class="form-control form-global" placeholder="e.g the-east@gmail.com" autocomplete="off" required>
        </div>
        <div class="form-group text-start mb-4">
            <label class="form-label text-white text-form-global mb-2">Nomor Ponsel</label>
            <input type="text" id="mobilePhone" name="mobilePhone" class="form-control form-global" placeholder="e.g 081212345678" autocomplete="off" maxlength=15 required/>
        </div>
       <button type="button" onclick="submitAccount()" class="btn btn-primary btn-regist w-100 mb-3">Buat Akun</button>
    </div>
</section>

<script>
window.routes = {
    checkUsername: "{{ route('check.username') }}",
    submitAccount: "{{ route('create.account.submit') }}",
    login: "{{ route('login') }}"
};
</script>
<script src="{{ asset('js/registerCreateAccount.js') }}"></script>

@endsection