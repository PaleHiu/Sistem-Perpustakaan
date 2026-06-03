<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - SIPUS</title>
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
        .input-group { position: relative; margin-bottom: 20px; }
        .input-group i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #a0aec0; }
        input { width: 100%; padding: 14px 14px 14px 45px; border: 1.5px solid #edf2f7; border-radius: 12px; font-size: 14px; outline: none; transition: border-color 0.2s; background: #fafafa; }
        input:focus { border-color: #1fcf8e; background: white; }
        .btn { width: 100%; padding: 14px; background: #1fcf8e; color: white; border: none; border-radius: 12px; font-size: 15px; font-weight: 700; cursor: pointer; transition: background 0.2s; }
        .btn:hover { background: #18a871; }
        .btn:disabled { background: #a0aec0; cursor: not-allowed; }
        .error { color: #e53e3e; font-size: 13px; margin-bottom: 15px; background: #fff5f5; padding: 10px 15px; border-radius: 8px; border: 1px solid #fed7d7; }
        .success { color: #38a169; font-size: 13px; margin-bottom: 15px; background: #f0fff4; padding: 10px 15px; border-radius: 8px; border: 1px solid #c6f6d5; }
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

    <h2>Lupa Password?</h2>
    <p class="desc">Masukkan email yang terdaftar. Kami akan mengirimkan kode OTP untuk mereset password kamu.</p>

    @if(session('success'))
        <div class="success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif
    @error('email')
        <div class="error"><i class="fa-solid fa-circle-xmark"></i> {{ $message }}</div>
    @enderror

    <form method="POST" action="{{ route('sipus.forgot.password.send') }}">
        @csrf
        <div class="input-group">
            <i class="fa-regular fa-envelope"></i>
            <input type="email" name="email" placeholder="Masukkan email kamu"
                   value="{{ old('email') }}" required autofocus>
        </div>
        <button type="submit" class="btn"
                onclick="this.disabled=true; this.textContent='Mengirim OTP...'; this.closest('form').submit();">
            Kirim Kode OTP
        </button>
    </form>

    <div class="back-link">
        <a href="{{ route('login') }}">← Kembali ke Login</a>
    </div>
</div>
</body>
</html>
