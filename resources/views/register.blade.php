<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pendaftaran</title>
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
            padding:40px;
            border-radius:16px;
            box-shadow:0 10px 40px rgba(0,0,0,.12);
        }
        label{
            font-weight:600;
            display:block;
            margin-bottom:8px;
            font-size:16px;
        }
        input{
            width:100%;
            padding:14px;
            border-radius:8px;
            border:1px solid #cfd4dc;
            font-size:15px;
            margin-bottom:24px;
        }
        input:focus{
            outline:none;
            border-color:#3b82f6;
        }
        .btn{
            display:block;
            margin:0 auto;
            background:#3b82f6;
            color:#fff;
            border:none;
            padding:14px 48px;
            font-size:16px;
            border-radius:28px;
            cursor:pointer;
        }
        .btn:hover{
            background:#2563eb;
        }
        .note{
            text-align:center;
            margin-top:28px;
            font-size:14px;
        }
        .note a{
            color:#2563eb;
            text-decoration:underline;
        }
    </style>
</head>
<body>

<div class="card">
    <form method="POST" action="/start-registration">
        @csrf

        <label>Kode ID</label>
        <input type="number" name="idLink" placeholder="Masukkan Kode ID">

        <label>Kode Referral</label>
        <input type="text" name="personRefferal" placeholder="Masukkan Kode Referral">

        <button class="btn" type="submit">Lanjut</button>
    </form>

    <div class="note">
        Jika tidak memiliki kode, klik
        <a href="/Customer-Type?RefcoExist=0">disini</a>
        untuk lanjut
    </div>
</div>

</body>
</html>
