<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SIPUS - Katalog Buku</title>
    
    <!-- 1. Panggil style utama Dashboard Member untuk mengunci Sidebar & Layout Utama -->
    <link rel="stylesheet" href="{{ asset('dashboard_assets/member_style.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- 2. Style Khusus Konten Katalog (Agar tidak merusak grid Dashboard) -->
    <style>
        /* Topbar Pencarian */
        .topbar { display: flex; gap: 10px; margin-bottom: 20px; }
        .topbar input { flex: 1; padding: 12px 20px; border-radius: 25px; border: 1px solid #ccc; font-family: 'Inter', sans-serif; }
        .topbar button { padding: 10px 20px; border-radius: 25px; border: none; background: #1f3b4d; color: white; cursor: pointer; }
        
        /* Filter Kategori */
        .filters { margin-bottom: 25px; display: flex; gap: 10px; flex-wrap: wrap; }
        .filters button { padding: 8px 16px; border-radius: 20px; border: 1px solid #ccc; background: white; cursor: pointer; color: #555; }
        .filters button.active { background: #2bb673; color: white; border: none; }
        
        /* Grid Khusus Katalog (4 Kolom) */
        .katalog-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
        
        /* Kartu Buku */
        .book-card { background: white; border-radius: 16px; padding: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.03); display: flex; flex-direction: column; }
        .cover { height: 160px; background: #2bb673; border-radius: 10px; margin-bottom: 15px; }
        .book-card h3 { font-size: 15px; margin-bottom: 5px; color: #333; line-height: 1.4; }
        .book-card p { font-size: 13px; color: #888; margin-bottom: 15px; flex: 1; }
        
        /* Status Stok */
        .status-badge { display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; text-align: center; }
        .status-badge.tersedia { background: #e0f5ec; color: #2bb673; }
        .status-badge.habis { background: #ffe5e5; color: #e74c3c; }
        .book-card.disabled { opacity: 0.6; }
    </style>
</head>
<body>

<div class="container">

    <!-- ==========================================
         SIDEBAR (Dikunci dengan member_style.css)
         ========================================== -->
    <div class="sidebar">
        <div>
            <!-- Logo -->
            <div class="logo-container" style="margin-bottom: 20px; padding: 10px;">
                <img src="{{ asset('ui_auth/logo.svg') }}" alt="Logo SIPUS" style="width: 150px; height: auto;">
            </div>
            <p class="sub">Member Portal</p>

            <!-- Navigasi -->
            <ul>
                <li>
                    <a href="{{ route('member.dashboard') }}" style="text-decoration: none; color: inherit; display: block;">Dashboard</a>
                </li>
                
                <!-- Class "active" dipindah ke Katalog -->
                <li class="active">
                    <a href="{{ route('katalog') }}" style="text-decoration: none; color: inherit; display: block;">Katalog</a>
                </li>
                
                <li>
                    <a href="#" style="text-decoration: none; color: inherit; display: block;">Keranjang</a>
                </li>
                <li>
                    <a href="#" style="text-decoration: none; color: inherit; display: block;">Peminjaman Saya</a>
                </li>
                <li>
                    <a href="#" style="text-decoration: none; color: inherit; display: block;">Riwayat</a>
                </li>
                <li>
                    <a href="#" style="text-decoration: none; color: inherit; display: block;">Profil</a>
                </li>
            </ul>
        </div>

        <!-- Profil & Logout -->
        <div class="user">
            <div style="margin-bottom: 15px;">
                <strong>{{ Auth::user()->anggota->nama_lengkap ?? 'Member SIPUS' }}</strong>
                <p style="font-size: 12px; color: #ccc;">Member Silver</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="button" onclick="event.preventDefault(); this.closest('form').submit();">Logout</button>
            </form>
        </div>
    </div>
    <!-- END SIDEBAR -->


    <!-- ==========================================
         MAIN CONTENT (Area Katalog)
         ========================================== -->
    <div class="main">
        
        <!-- Breadcrumb ala Dashboard -->
        <div class="breadcrumb">
            SIPUS > <span>Katalog Buku</span>
        </div>

        <h2 style="margin-bottom: 20px; color: #1f3b4d;">Eksplorasi Koleksi</h2>

        <!-- SEARCH -->
        <div class="topbar">
            <input type="text" placeholder="Cari judul buku, penulis, atau penerbit...">
            <button>Cari Buku</button>
        </div>

        <!-- FILTER -->
        <div class="filters">
            <button class="active">Semua</button>
            <button>Fiksi</button>
            <button>Sains & Alam</button>
            <button>Teknologi Informasi</button>
            <button>Sejarah</button>
            <button>Pengembangan Diri</button>
        </div>

        <!-- GRID KATALOG BUKU -->
        <div class="katalog-grid">
            
            <!-- Kartu 1 -->
            <div class="book-card">
                <div class="cover"></div>
                <h3>Pengembangan Web Modern dengan Next.js</h3>
                <p>Andi Wijaya</p>
                <span class="status-badge tersedia">Tersedia (3)</span>
            </div>

            <!-- Kartu 2 (Habis) -->
            <div class="book-card disabled">
                <div class="cover"></div>
                <h3>Algoritma & Struktur Data Lanjut</h3>
                <p>Prof. Sastro</p>
                <span class="status-badge habis">Habis Terpinjam</span>
            </div>

            <!-- Kartu 3 -->
            <div class="book-card">
                <div class="cover"></div>
                <h3>Desain UI/UX: Fondasi Kreativitas</h3>
                <p>Maya Putri</p>
                <span class="status-badge tersedia">Tersedia (5)</span>
            </div>

            <!-- Kartu 4 -->
            <div class="book-card">
                <div class="cover"></div>
                <h3>Filosofi Teras: Stoisisme</h3>
                <p>Henry Manampiring</p>
                <span class="status-badge tersedia">Tersedia (2)</span>
            </div>

            <!-- Kartu 5 -->
            <div class="book-card">
                <div class="cover"></div>
                <h3>Panduan Praktis Manajemen Basis Data</h3>
                <p>Rendi Kurnia</p>
                <span class="status-badge tersedia">Tersedia (8)</span>
            </div>

            <!-- Kartu 6 -->
            <div class="book-card">
                <div class="cover"></div>
                <h3>Sejarah Dunia dalam 100 Objek</h3>
                <p>Neil MacGregor</p>
                <span class="status-badge tersedia">Tersedia (1)</span>
            </div>
            
            <!-- Kartu 7 (Habis) -->
            <div class="book-card disabled">
                <div class="cover"></div>
                <h3>Machine Learning Dasar</h3>
                <p>Lukas Tanaur</p>
                <span class="status-badge habis">Habis Terpinjam</span>
            </div>

            <!-- Kartu 8 -->
            <div class="book-card">
                <div class="cover"></div>
                <h3>Psikologi Komunikasi Modern</h3>
                <p>Dr. Jalaluddin</p>
                <span class="status-badge tersedia">Tersedia (4)</span>
            </div>

        </div>
    </div>
    <!-- END MAIN CONTENT -->

</div>

</body>
</html>