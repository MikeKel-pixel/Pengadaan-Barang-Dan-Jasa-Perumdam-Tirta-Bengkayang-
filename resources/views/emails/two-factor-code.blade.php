<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Verifikasi 2FA</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1e40af;
            padding-bottom: 15px;
        }
        .logo {
            height: 60px;
            margin-bottom: 10px;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            color: #1e40af;
        }
        .code-box {
            background: #f3f4f6;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 8px;
            color: #1e40af;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
            margin-top: 20px;
        }
        .info {
            font-size: 14px;
            color: #4b5563;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/logo-perumdam.png') }}" alt="Logo PERUMDAM" class="logo">
            <div class="title">Kode Verifikasi 2FA</div>
        </div>

        <p style="color: #4b5563;">Halo, <strong>{{ $name }}</strong></p>
        <p style="color: #4b5563;">Gunakan kode verifikasi di bawah ini untuk melanjutkan login ke sistem PERUMDAM.</p>

        <div class="code-box">{{ $code }}</div>

        <p class="info">Kode ini berlaku selama <strong>10 menit</strong>.</p>
        <p class="info" style="margin-top: 5px;">Jika Anda tidak merasa melakukan login, abaikan email ini.</p>

        <div class="footer">
            <p>PERUMDAM Tirta Bengkayang - Sistem Pengadaan Barang/Jasa</p>
            <p style="margin-top: 5px;">© {{ date('Y') }} PERUMDAM. All rights reserved.</p>
        </div>
    </div>
</body>
</html>