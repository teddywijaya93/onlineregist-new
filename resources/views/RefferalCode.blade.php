@extends('layout.Master')
@section('title', 'Profits Registrasi')
@section('content')
<div class="container mt-5" style="margin-bottom: 100px">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm" style="border-radius: 15px; padding: 10px;">
                <div class="card-body">
                    <form id="refferalForm" action="" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="idLink"><strong>Kode ID</strong></label>
                            <input type="number" class="form-control no-spin" id="idLink" name="idLink" placeholder="Masukkan Kode ID">
                            <small>error message</small>
                        </div>
                        <div class="form-group" style="margin-top: 21px;">
                            <label for="personRefferal"><strong>Kode Referral</strong></label>
                            <input type="text" class="form-control" id="personRefferal" name="personRefferal" placeholder="Masukkan Kode Referral">
                            <small>error message</small>
                        </div>
                        @if (session('refferalError'))
                            <div class="alert alert-danger" style="margin-top: 30px">
                                {{ session('refferalError') }}
                            </div>
                        @endif
                        <div class="text-center" style="margin-top: 41px; ">
                            <button type="submit" class="btn btn-primary" style="background-color: rgb(24, 121, 229); border-radius: 19px; width: 100px; height: 40px;">Lanjut</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- CSS -->
<style>
    .no-spin::-webkit-inner-spin-button,
    .no-spin::-webkit-outer-spin-button{
        -webkit-appearance: none;
        appearance: none;
    }
    :root{
        --success-color: #23cc71;
        --error-color: #e74c3c;
    }
    .form-group.success input{border-color: var(--success-color);}
    .form-group.error input {border-color: var(--error-color);}
    .form-group small{
        color: var(--error-color);
        position: absolute;
        visibility: hidden;
    }
    .form-group.error small{visibility: visible;}
</style>
<!-- JS -->
<script src="{{ asset('js/errorMessage.js') }}"></script>
<script src="{{ asset('js/onkeyUp.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);
        const lc = urlParams.get('lc');
        const pc = urlParams.get('pc');
        const hasReferralError = @json(session('refferalError') !== null);

        if (lc) {
            document.getElementById('idLink').value = lc;
            
        }

        if (pc) {
            document.getElementById('personRefferal').value = pc;
        }

        // Auto-submit form if parameters are present and no error session exists
        if ((lc || pc) && !hasReferralError) {
            const form = document.getElementById('refferalForm');
            form.submit();
        }
    });
</script>
@endsection