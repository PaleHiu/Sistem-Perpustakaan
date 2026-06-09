<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP - SIPUS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #1f3c45, #2d6a5a); font-family: Arial, sans-serif; }
        .card { background: white; border-radius: 20px; padding: 40px; width: 100%; max-width: 420px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        .logo { text-align: center; margin-bottom: 30px; }
        .logo h1 { color: #1f3c45; font-size: 28px; letter-spacing: 3px; }
        .logo p { color: #718096; font-size: 13px; margin-top: 5px; }
        h2 { color: #2d3748; font-size: 22px; margin-bottom: 8px; }
        p.desc { color: #718096; font-size: 14px; margin-bottom: 25px; line-height: 1.6; }
        .otp-input { width: 100%; padding: 18px; border: 2px solid #c6f6d5; border-radius: 12px; font-size: 32px; font-weight: 800; text-align: center; letter-spacing: 12px; outline: none; font-family: monospace; background: #f0fff4; color: #1f3c45; margin-bottom: 20px; }
        .otp-input:focus { border-color: #1fcf8e; }
        .btn { width: 100%; padding: 14px; background: #1fcf8e; color: white; border: none; border-radius: 12px; font-size: 15px; font-weight: 700; cursor: pointer; }
        .btn:disabled { background: #a0aec0; cursor: not-allowed; }
        .error { color: #e53e3e; font-size: 13px; margin-bottom: 15px; background: #fff5f5; padding: 10px 15px; border-radius: 8px; border: 1px solid #fed7d7; }
        .email-info { background: #f0fff4; border: 1px solid #c6f6d5; border-radius: 10px; padding: 12px 15px; margin-bottom: 20px; font-size: 13px; color: #38a169; text-align: center; }
        .timer { text-align: center; font-size: 13px; color: #e53e3e; margin-bottom: 15px; font-weight: 600; }
        .back-link { text-align: center; margin-top: 20px; font-size: 14px; color: #718096; }
        .back-link a { color: #1fcf8e; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
<div class="card">
    <div class="logo">
        <h1>SIPUS</h1>
        <p>Library Management System</p>
    </div>

    <h2>Masukkan Kode OTP</h2>
    <p class="desc">Kode OTP telah dikirim ke email kamu. Berlaku selama 10 menit.</p>

    <div class="email-info">
        <i class="fa-regular fa-envelope"></i>
        {{ session('reset_email') }}
    </div>

    @error('otp')
        <div class="error"><i class="fa-solid fa-circle-xmark"></i> {{ $message }}</div>
    @enderror

    <form method="POST" action="{{ route('sipus.forgot.otp.verify') }}">
        @csrf
        <input type="text" name="otp" class="otp-input"
               maxlength="6" placeholder="------"
               oninput="this.value=this.value.replace(/[^0-9]/g,'')"
               autofocus required>

        <div class="timer">
            ⏱ Berlaku: <span id="countdown">10:00</span>
        </div>

        <button type="submit" class="btn"
                onclick="this.disabled=true; this.textContent='Memverifikasi...'; this.closest('form').submit();">
            Verifikasi OTP
        </button>
    </form>

    <div class="back-link">
        <a href="{{ route('sipus.forgot.password') }}">← Kirim ulang OTP</a>
    </div>
</div>

<script>
let waktu = 600; // 10 menit
const el = document.getElementById('countdown');
const interval = setInterval(() => {
    if (waktu <= 0) {
        clearInterval(interval);
        el.textContent = 'KADALUARSA';
        el.style.color = '#a0aec0';
        return;
    }
    waktu--;
    const m = Math.floor(waktu / 60);
    const s = waktu % 60;
    el.textContent = String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
}, 1000);
</script>
</body>
</html>
