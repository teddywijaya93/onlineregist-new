@extends('layouts.app')
@section('title','Profil Risiko')
@section('content')

<section class="auth-wrapper">
    <div class="container text-center">
        <div class="mb-5">
            <h3 class="head-lanjut text-white mb-2">Hasil Profil Risiko Kamu</h3>
        </div>
        <div class="card bg-dark text-white">
            <div class="card-body">
                <table class="table table-borderless text-white mb-0">
                    <tr>
                        <th width="40%">Total Skor</th>
                        <td>{{ session('profil_risiko_total') }}</td>
                    </tr>
                    <tr>
                        <th>Profil Risiko</th>
                        <td>{{ session('profil_risiko_hasil') }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <a href="{{ route('data.profil.resiko') }}" class="btn btn-primary btn-regist w-100 mb-3">Ulangi Pengisian</a>
    </div>
</section>

@endsection