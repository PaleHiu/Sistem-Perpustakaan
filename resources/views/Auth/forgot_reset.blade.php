<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - SIPUS</title>
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
        .input-group { position: relative; margin-bottom: 16px; }
        .input-group i.icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #a0aec0; }
        .toggle-pw { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #a0aec0; cursor: pointer; background: none; border: none; }
        input { width: 100%; padding: 14px 45px; border: 1.5px solid #edf2f7; border-radius: 12px; font-size: 14px; outline: none; background: #fafafa; }
        input:focus { border-color: #1fcf8e; background: white; }
        .btn { width: 100%; padding: 14px; background: #1fcf8e; color: white; border: none; border-radius: 12px; font-size: 15px; font-weight: 700; cursor: pointer; margin-top: 8px; }
        .btn:disabled { background: #a0aec0; cursor: not-allowed; }
        .error { color: #e53e3e; font-size: 13px; margin-bottom: 15px; background: #fff5f5; padding: 10px 15px; border-radius: 8px; border: 1px solid #fed7d7; }
        .strength { font-size: 12px; margin-top: 5px; margin-bottom: 10px; }
    </style>
</head>
<body>
<div class="card">
    <div class="logo">
        <h1>SIPUS</h1>
        <p>Library Management System</p>
    </div>

    <h2>Password Baru</h2>
    <p class="desc">Masukkan password baru kamu. Minimal 8 karakter.</p>

    @if($errors->any())
        <div class="error">
            <i class="fa-solid fa-circle-xmark"></i>
            @foreach($errors->all() as $error){{ $error }} @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('sipus.forgot.reset.save') }}">
        @csrf

        <div class="input-group">
            <i class="fa-solid fa-lock icon"></i>
            <input type="password" name="password" id="pw1"
                   placeholder="Password baru" required minlength="8">
            <button type="button" class="toggle-pw" onclick="togglePw('pw1', this)">
                <i class="fa-regular fa-eye"></i>
            </button>
        </div>

        <div class="input-group">
            <i class="fa-solid fa-lock icon"></i>
            <input type="password" name="password_confirmation" id="pw2"
                   placeholder="Konfirmasi password baru" required>
            <button type="button" class="toggle-pw" onclick="togglePw('pw2', this)">
                <i class="fa-regular fa-eye"></i>
            </button>
        </div>

        <button type="submit" class="btn"
                onclick="this.disabled=true; this.textContent='Menyimpan...'; this.closest('form').submit();">
            Simpan Password Baru
        </button>
    </form>
</div>

<script>
function togglePw(id, btn) {
    const input = document.getElementById(id);
    const icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
</body>
</html>
