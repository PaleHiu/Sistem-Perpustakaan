<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terjadi Kesalahan - SIPUS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #0D1F2D;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
            overflow: hidden;
        }
        .error-container {
            max-width: 500px;
            padding: 50px 40px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.01) 100%);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }
        .error-code {
            font-size: 6rem;
            font-weight: 800;
            color: #4A9DB5;
            margin: 0;
            line-height: 1;
            letter-spacing: -2px;
        }
        .error-title {
            font-size: 1.6rem;
            margin: 25px 0 12px;
            font-weight: 600;
        }
        .error-message {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 35px;
            line-height: 1.6;
        }
        .btn-back {
            display: inline-block;
            padding: 14px 35px;
            background-color: #4A9DB5;
            color: #ffffff;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(74, 157, 181, 0.3);
        }
        .btn-back:hover {
            background-color: #3b8296;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(74, 157, 181, 0.5);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1 class="error-code">404</h1> 
        <h2 class="error-title">Gagal Memproses Permintaan</h2>
        <p class="error-message">
            Mohon maaf, terjadi kendala internal pada sistem saat memproses fungsi tersebut. Silakan kembali ke halaman utama atau coba beberapa saat lagi.
        </p>
        <a href="{{ url('/') }}" class="btn-back">Kembali ke Beranda</a>
    </div>
</body>
</html>