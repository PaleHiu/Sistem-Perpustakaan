<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Library MS - Member Portal</title>
    <!-- Pemanggilan CSS Laravel Asset -->
    <link rel="stylesheet" href="{{ asset('dashboard_assets/member_style.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<div class="container">

<!-- SIDEBAR PENGUNCIAN MEMBER -->
    <div class="sidebar">
        <div>
            <div class="logo-container" style="margin-bottom: 20px; padding: 10px;">
                <img src="{{ asset('ui_auth/logo.svg') }}" alt="Logo SIPUS" style="width: 150px; height: auto;">
            </div>
            <p class="sub">Member Portal</p>

            <ul>
                <!-- Gunakan tag <a> dan helper route() untuk navigasi -->
                <li class="active">
                    <a href="{{ route('member.dashboard') }}" style="text-decoration: none; color: inherit; display: block; width: 100%;">Dashboard</a>
                </li>
                <li>
                    <a href="{{ route('katalog') }}" style="text-decoration: none; color: inherit; display: block; width: 100%;">Katalog</a>
                </li>
                <li>
                    <a href="#" style="text-decoration: none; color: inherit; display: block; width: 100%;">Keranjang</a>
                </li>
                <li>
                    <a href="#" style="text-decoration: none; color: inherit; display: block; width: 100%;">Peminjaman Saya</a>
                </li>
                <li>
                    <a href="#" style="text-decoration: none; color: inherit; display: block; width: 100%;">Riwayat</a>
                </li>
                <li>
                    <a href="#" style="text-decoration: none; color: inherit; display: block; width: 100%;">Profil</a>
                </li>
            </ul>
        </div>
        <div class="user">
            <div class="user-profile">
                <h3 class="user-name">
                    {{ Auth::user()->anggota->nama_lengkap ?? 'Member SIPUS' }}
                </h3>
                <p class="user-role">
                    Member Silver
                </p>
            </div>
            
            <!-- Fungsi Logout Laravel -->
            <form method="POST" action="{{ route('logout') }}" style="margin-top: 10px;">
                @csrf
                <button type="button" onclick="event.preventDefault(); this.closest('form').submit();">Logout</button>
            </form>
        </div>
    </div>

    <!-- MAIN -->
    <div class="main">

        <div class="breadcrumb">
            SIPUS > <span>Dashboard Member</span>
        </div>

        <!-- ALERT (Nanti disetting muncul otomatis jika denda > 0) -->
        @if(($totalDenda ?? 15000) > 0)
        <div class="alert">
            Kamu memiliki keterlambatan — <b>Denda Rp {{ number_format($totalDenda ?? 15000, 0, ',', '.') }}</b>. Segera kembalikan buku.
        </div>
        @endif

        <div class="grid">

            <!-- LEFT -->
            <div>

                <!-- PINJAMAN -->
                <div class="card">
                    <div class="card-head">
                        <h3>Pinjaman Aktif</h3>
                        <span style="color:#2bb673; cursor:pointer; font-size:14px;">Lihat Semua →</span>
                    </div>

                    <div class="book">
                        <img src="https://via.placeholder.com/60" alt="Cover">
                        <div>
                            <h4>Atomic Habits</h4>
                            <p>James Clear</p>
                            <small>Dipinjam: 01 Jan - 08 Jan</small>
                            <div class="progress red"></div>
                        </div>
                    </div>

                    <div class="book">
                        <img src="https://via.placeholder.com/60" alt="Cover">
                        <div>
                            <h4>Thinking, Fast and Slow</h4>
                            <p>Daniel Kahneman</p>
                            <small>Dipinjam: 08 Jan - 15 Jan</small>
                            <div class="progress green"></div>
                        </div>
                    </div>

                    <button class="btn">+ Pinjam Buku Baru</button>
                </div>

                <!-- AKTIVITAS -->
                <div class="card">
                    <h3 style="margin-bottom: 15px;">Aktivitas Terbaru</h3>

                    <table>
                        <tr>
                            <td>The Alchemist</td>
                            <td>01 Jan 2024</td>
                            <td style="text-align: right;"><span class="badge blue">Dipinjam</span></td>
                        </tr>
                        <tr>
                            <td>Deep Work</td>
                            <td>25 Des 2023</td>
                            <td style="text-align: right;"><span class="badge green">Selesai</span></td>
                        </tr>
                        <tr>
                            <td>Grit</td>
                            <td>18 Des 2023</td>
                            <td style="text-align: right;"><span class="badge green">Selesai</span></td>
                        </tr>
                    </table>
                </div>

            </div>

            <!-- RIGHT -->
            <div>

                <div class="stat">
                    <p>Sedang Dipinjam</p>
                    <h2>{{ $sedangDipinjam ?? 2 }}</h2>
                </div>

                <div class="stat">
                    <p>Total Riwayat</p>
                    <h2>{{ $totalRiwayat ?? 12 }}</h2>
                </div>

                <div class="stat red-border">
                    <p>Total Denda</p>
                    <h2 class="danger">Rp {{ number_format($totalDenda ?? 15000, 0, ',', '.') }}</h2>
                </div>

                <div class="member">
                    <h4 style="font-size:13px; margin-bottom:5px;">INFORMASI MEMBER</h4>
                    <p style="font-size:13px;">Masa berlaku berakhir pada <br><b style="font-size:15px; display:inline-block; margin-top:5px;">31 Des 2024</b></p>
                    <button>Perpanjang Keanggotaan</button>
                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>