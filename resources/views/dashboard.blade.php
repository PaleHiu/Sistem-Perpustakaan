<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SIPUS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('ui_auth/style.css') }}">
    <style>
        /* Tambahan khusus Dashboard agar tidak merusak style.css asli */
        body {
            background: rgb(32, 62, 105);
            justify-content: flex-start;
            align-items: flex-start;
            display: block;
            padding: 20px;
        }

        .dashboard-container {
            display: flex;
            gap: 20px;
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        /* Sidebar Glassmorphism */
        .sidebar {
            width: 280px;
            min-height: 90vh;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar h2 { font-size: 1.8rem; margin-bottom: 40px; color: #48cbe8; }
        .menu-item {
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 15px;
            cursor: pointer;
            transition: 0.3s;
            color: rgba(255, 255, 255, 0.8);
        }
        .menu-item:hover, .menu-item.active {
            background: rgba(72, 203, 232, 0.2);
            color: #fff;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .top-nav {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            padding: 20px 40px;
            border-radius: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(5px);
            padding: 25px;
            border-radius: 25px;
            border-left: 5px solid #48cbe8;
        }

        .stat-card h4 { font-weight: 300; font-size: 0.9rem; opacity: 0.8; }
        .stat-card p { font-size: 2rem; font-weight: 700; }

        /* Table Area */
        .table-container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            padding: 30px;
            flex: 1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th { text-align: left; padding: 15px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); color: #48cbe8; }
        td { padding: 15px; border-bottom: 1px solid rgba(255, 255, 255, 0.05); font-size: 0.9rem; }

        .status-badge {
            padding: 5px 12px;
            border-radius: 10px;
            font-size: 0.75rem;
            background: rgba(72, 203, 232, 0.2);
            color: #48cbe8;
        }
    </style>
</head>
<body>

    <div class="dashboard-container">
        <div class="sidebar">
            <h2>SIPUS</h2>
            <div class="menu-item active">Dashboard</div>
            <div class="menu-item">Data Buku</div>
            <div class="menu-item">Data Anggota</div>
            <div class="menu-item">Transaksi (OTP)</div>
            <div class="menu-item">Laporan</div>
            <div class="menu-item" style="margin-top: 50px; color: #ff5f5f;">Logout</div>
        </div>

        <div class="main-content">
            <div class="top-nav">
                <h3>Admin Overview</h3>
                <div class="admin-profile">
                    <strong>Admin123</strong>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <h4>Total Buku</h4>
                    <p>1,240</p>
                </div>
                <div class="stat-card">
                    <h4>Anggota Aktif</h4>
                    <p>850</p>
                </div>
                <div class="stat-card">
                    <h4>OTP Menunggu</h4>
                    <p>12</p>
                </div>
                <div class="stat-card">
                    <h4>Peminjaman Hari Ini</h4>
                    <p>45</p>
                </div>
            </div>

            <div class="table-container">
                <h3>Antrian Validasi OTP (O2O)</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Kode OTP</th>
                            <th>Nama Anggota</th>
                            <th>Buku</th>
                            <th>Waktu Booking</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>#88219</strong></td>
                            <td>Arif Ahmad Muzakky</td>
                            <td>Menjadi Vibe Coding</td>
                            <td>Sudah Lama Seperti Ini</td>
                            <td><span class="status-badge">Validasi</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>