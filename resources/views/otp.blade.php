<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>OTP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body{
            margin:0;
            font-family: Arial, sans-serif;
            background: linear-gradient(180deg, #eaf6ff 60%, #7aa7e9 100%);
            min-height:100vh;
        }
        .card{
            max-width:600px;
            margin:80px auto;
            background:#fff;
            padding:48px;
            border-radius:18px;
            box-shadow:0 12px 40px rgba(0,0,0,.12);
            text-align:center;
        }
        h2{
            margin-bottom:10px;
        }
        p{
            color:#555;
            margin-bottom:28px;
        }
        .email{
            color:#2563eb;
            font-weight:600;
            margin-bottom:28px;
            display:block;
        }
        .otp-box{
            display:flex;
            justify-content:center;
            gap:12px;
            margin-bottom:32px;
        }
        .otp-box input{
            width:48px;
            height:56px;
            font-size:22px;
            text-align:center;
            border-radius:10px;
            border:1px solid #cfd4dc;
        }
        .otp-box input:focus{
            outline:none;
            border-color:#3b82f6;
        }
        button{
            width:100%;
            padding:16px;
            background:#6fa3ff;
            color:#fff;
            border:none;
            border-radius:10px;
            font-size:18px;
            cursor:pointer;
        }
        .timer{
            margin-top:20px;
            font-size:14px;
            color:#333;
        }
        .resend{
            margin-top:12px;
            font-size:14px;
            color:#bbb;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>Masukkan Kode OTP</h2>
    <p>Kode verifikasi telah dikirimkan ke email Anda</p>
    <span class="email">{{ $email }}</span>

    <form method="POST" action="/OTP" onsubmit="combineOtp()">
        @csrf

        <div class="otp-box">
            @for ($i = 0; $i < 6; $i++)
                <input type="text" maxlength="1" oninput="moveNext(this)">
            @endfor
        </div>

        <input type="hidden" name="otp" id="otp">

        <button type="submit">Verifikasi</button>
    </form>

    <div class="timer" id="timer">Waktu: 1 menit 00 detik</div>
    <div class="resend">Kirim Ulang OTP</div>
</div>

<script>
    const inputs = document.querySelectorAll('.otp-box input');

    function moveNext(el) {
        if (el.value && el.nextElementSibling) {
            el.nextElementSibling.focus();
        }
    }

    function combineOtp() {
        let otp = '';
        inputs.forEach(i => otp += i.value);
        document.getElementById('otp').value = otp;
    }

    // countdown
    let time = 60;
    const timerEl = document.getElementById('timer');
    setInterval(() => {
        if (time <= 0) return;
        time--;
        timerEl.innerText = `Waktu: 0 menit ${time} detik`;
    }, 1000);
</script>

</body>
</html>
