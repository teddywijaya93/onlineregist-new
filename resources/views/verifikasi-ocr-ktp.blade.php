@extends('layouts.app')
@section('title','Verifikasi KTP')
@section('content')

<section class="auth-wrapper">
    <div class="container">
        <h3 class="text-white mb-4">Upload Foto e-KTP</h3>
        
        @if ($errors->any())
            <p class="text-danger">{{ $errors->first() }}</p>
        @endif

        <form method="POST" action="{{ route('verifikasi.ktp.process') }}" enctype="multipart/form-data">
            @csrf
            <input type="file" name="ktp_image" accept="image/*" class="form-control mb-3" required>
            <button id="btnNext" class="btn btn-primary btn-regist w-100 mb-3">Lanjutkan</button>
        </form>
    </div>
</section>

@endsection