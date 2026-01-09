<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tipe Akun</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body{
            margin:0;
            font-family: Arial, sans-serif;
            background: linear-gradient(180deg, #eaf6ff 60%, #7aa7e9 100%);
            min-height:100vh;
        }
        .card{
            max-width:700px;
            margin:80px auto;
            background:#fff;
            padding:50px 40px;
            border-radius:20px;
            box-shadow:0 10px 40px rgba(0,0,0,.12);
            text-align:center;
        }
        h1{
            font-size:28px;
            margin-bottom:10px;
            color:#222;
        }
        p{
            font-size:16px;
            color:#555;
            margin-bottom:40px;
        }
        .btn{
            display:block;
            width:260px;
            margin:0 auto 20px;
            padding:16px 0;
            border:none;
            border-radius:40px;
            font-size:18px;
            color:#fff;
            cursor:pointer;
        }
        .regular{
            background:#3b82f6;
        }
        .syariah{
            background:#6edc4f;
        }
        .derivatif{
            background:#9b2c2c;
        }
        .btn:hover{
            opacity:.9;
        }
    </style>
</head>
<body>

<div class="card">
    <h1>Tipe Akun</h1>
    <p>Silakan pilih tipe akun yang diinginkan</p>

    <form method="POST" action="/Customer-Type">
        @csrf
        <input type="hidden" name="aoCode" value="{{ session('aoName') }}">
        <input type="hidden" name="accountType" value="REGULAR">
        <button class="btn regular" type="submit">Reguler</button>
    </form>

    <form method="POST" action="/Customer-Type">
        @csrf
        <input type="hidden" name="aoCode" value="{{ session('aoName') }}">
        <input type="hidden" name="accountType" value="SYARIAH">
        <button class="btn syariah" type="submit">Syariah</button>
    </form>

    <form method="POST" action="/Customer-Type">
        @csrf
        <input type="hidden" name="aoCode" value="{{ session('aoName') }}">
        <input type="hidden" name="accountType" value="DERIVATIF">
        <button class="btn derivatif" type="submit">Derivatif</button>
    </form>
</div>

</body>
</html>
