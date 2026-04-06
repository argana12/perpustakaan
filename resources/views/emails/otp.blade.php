<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
        }
        .wrapper {
            max-width: 520px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .header {
            padding: 28px 32px;
            text-align: center;
        }
        .header-register { background: linear-gradient(135deg, #6366f1, #4f46e5); }
        .header-reset    { background: linear-gradient(135deg, #f59e0b, #d97706); }

        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .header p {
            color: rgba(255,255,255,0.85);
            margin: 6px 0 0;
            font-size: 14px;
        }
        .body {
            padding: 32px;
        }
        .body p {
            color: #4b5563;
            font-size: 15px;
            line-height: 1.6;
            margin: 0 0 20px;
        }
        .otp-box {
            background: #f3f4f6;
            border: 2px dashed #d1d5db;
            border-radius: 10px;
            text-align: center;
            padding: 20px;
            margin: 24px 0;
        }
        .otp-box .otp-label {
            font-size: 13px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 0 8px;
        }
        .otp-box .otp-code {
            font-size: 42px;
            font-weight: 800;
            letter-spacing: 10px;
            color: #111827;
            font-family: 'Courier New', monospace;
        }
        .otp-box .otp-valid {
            font-size: 12px;
            color: #9ca3af;
            margin: 8px 0 0;
        }
        .warning {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            border-radius: 6px;
            padding: 12px 16px;
            font-size: 13px;
            color: #92400e;
            margin-bottom: 20px;
        }
        .footer {
            border-top: 1px solid #e5e7eb;
            padding: 20px 32px;
            text-align: center;
        }
        .footer p {
            font-size: 12px;
            color: #9ca3af;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header {{ $type === 'register' ? 'header-register' : 'header-reset' }}">
            @if ($type === 'register')
                <h1>📚 Verifikasi Email</h1>
                <p>Perpustakaan Sekolah — Pendaftaran Akun</p>
            @else
                <h1>🔑 Reset Kata Sandi</h1>
                <p>Perpustakaan Sekolah — Pemulihan Akun</p>
            @endif
        </div>

        <div class="body">
            @if ($type === 'register')
                <p>Halo! Kamu baru saja mendaftar ke sistem perpustakaan kami. Gunakan kode berikut untuk memverifikasi alamat email kamu:</p>
            @else
                <p>Halo! Kami menerima permintaan reset kata sandi untuk akun ini. Gunakan kode berikut untuk melanjutkan proses:</p>
            @endif

            <div class="otp-box">
                <p class="otp-label">Kode OTP</p>
                <div class="otp-code">{{ $otp }}</div>
                <p class="otp-valid">Berlaku selama <strong>5 menit</strong></p>
            </div>

            <div class="warning">
                ⚠️ <strong>Jangan bagikan kode ini kepada siapapun</strong>, termasuk petugas perpustakaan. Kami tidak pernah meminta kode OTP kamu.
            </div>

            <p>Jika kamu tidak merasa melakukan tindakan ini, abaikan email ini. Akun kamu tetap aman.</p>
        </div>

        <div class="footer">
            <p>Email ini dikirim otomatis. Jangan balas email ini.</p>
            <p style="margin-top: 4px;">© {{ date('Y') }} Perpustakaan Sekolah</p>
        </div>
    </div>
</body>
</html>
