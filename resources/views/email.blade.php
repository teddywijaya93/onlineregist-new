<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Email</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body{
            margin:0;
            font-family: Arial, sans-serif;
            background: linear-gradient(180deg, #eaf6ff 60%, #7aa7e9 100%);
            min-height:100vh;
        }
        .card{
            max-width:560px;
            margin:80px auto;
            background:#fff;
            padding:48px 40px;
            border-radius:18px;
            box-shadow:0 12px 40px rgba(0,0,0,.12);
            text-align:center;
        }
        h2{
            margin:0 0 10px;
            font-size:26px;
            font-weight:700;
        }
        p{
            margin:0 0 36px;
            color:#666;
            font-size:15px;
        }
        label{
            display:block;
            text-align:left;
            font-weight:600;
            margin-bottom:8px;
            font-size:15px;
        }
        input[type="email"]{
            width:100%;
            padding:16px;
            border-radius:10px;
            border:1px solid #cfd4dc;
            font-size:16px;
            margin-bottom:36px;
        }
        input:focus{
            outline:none;
            border-color:#3b82f6;
        }
        button{
            padding:16px 56px;
            background:#3b82f6;
            color:#fff;
            border:none;
            border-radius:32px;
            font-size:18px;
            cursor:pointer;
        }
        button:hover{
            background:#2563eb;
        }
        .info{
            margin-top:32px;
            font-size:13px;
            color:#888;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>Email</h2>
    <p>Silakan masukkan alamat email Anda</p>

    <form method="POST" action="/Email">
        @csrf

        {{-- parameter yang harus ikut --}}
        <input type="hidden" name="aoCode" value="{{ $aoCode }}">
        <input type="hidden" name="accountType" value="{{ $accountType }}">

        <label>Email</label>
        <input
            type="email"
            name="email"
            placeholder="Masukkan email"
            required
        >

        <button type="submit">Lanjut</button>
    </form>

    {{-- info debug (boleh dihapus) --}}
    <div class="info">
        Account Type: {{ $accountType }} <br>
        AO Code: {{ $aoCode ?: '-' }}
    </div>
</div>

</body>
</html>
