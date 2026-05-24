<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SIPUS Dashboard</title>
    <!-- Memanggil CSS menggunakan helper asset Laravel -->
    <link rel="stylesheet" href="{{ asset('dashboard_assets/style.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>

    <div class="dashboard-container">

        <!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-header">
        <h2>LIBRARY<br>MANAGER</h2>
        <p>Admin Portal</p>
    </div>

    <nav class="sidebar-menu">
        <a href="{{ route('dashboard') }}" class="menu-item active">
            <i class="fa-solid fa-table-columns"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('books.index') }}" class="menu-item">
            <i class="fa-solid fa-book"></i>
            <span>Books</span>
        </a>
        <a href="{{ route('members.index') }}" class="menu-item">
            <i class="fa-solid fa-users"></i>
            <span>Member</span>
        </a>
        <a href="{{ route('borrowing.index') }}" class="menu-item">
            <i class="fa-solid fa-handshake"></i>
            <span>Borrowing</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <a href="#" class="menu-item" style="color: #ff4d4d;"
                onclick="event.preventDefault(); this.closest('form').submit();">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </a>
        </form>
    </div>

</aside>

    <!-- MAIN CONTENT -->
        <main class="main-content">

            <!-- TOP HEADER -->
            <header class="topbar">
                <div>
                    <p class="breadcrumb">Pages / <span>Dashboard</span></p>
                </div>

                <div class="user-area">
                    <i class="fa-regular fa-comment"></i>
                    <i class="fa-regular fa-bell"></i>
                    <div class="user-info">
                        <!-- Menampilkan nama email Admin yang sedang login secara dinamis -->
                        <strong>{{ Auth::user()->email }}</strong>
                        <small>Super Administrator</small>
                    </div>
                    <img src="https://i.pravatar.cc/50?img=12" alt="User">
                </div>
            </header>

            <!-- GRID CONTENT -->
            <section class="content-grid">

                <!-- LEFT -->
                <div class="left-panel">
                    <div class="card chart-card">
                        <div class="card-header">
                            <h3>Library Uses</h3>
                            <button>Weekly View</button>
                        </div>

                        <!-- PURE CSS CHART AREA -->
                            @php
                                // Menggunakan data asli dari Controller, default ke 0 semua jika belum ada data
                                $chartData = $chartData ?? [0, 0, 0, 0, 0, 0, 0]; 
                                $hari = ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'];
                                
                                // Mencari nilai tertinggi agar batang tertinggi menyentuh atas.
                                // Jika belum ada peminjaman (semua 0), set max ke 1 agar tidak error dibagi 0.
                                $maxValue = max($chartData) > 0 ? max($chartData) : 1; 
                            @endphp

                        <div style="display: flex; align-items: flex-end; justify-content: space-around; height: 180px; margin-top: 20px; border-bottom: 2px dashed #f0f0f0; padding-bottom: 10px;">
                            
                            @foreach($chartData as $index => $value)
                                @php 
                                    // Menghitung persentase tinggi batang
                                    $heightPercent = ($value / $maxValue) * 100; 
                                @endphp
                                
                                <div style="display: flex; flex-direction: column; align-items: center; justify-content: flex-end; height: 100%; width: 10%;">
                                    
                                    <!-- Tooltip sederhana menggunakan atribut title -->
                                    <div title="{{ $value }} Peminjaman" 
                                        style="width: 100%; 
                                                height: {{ $heightPercent }}%; 
                                                background: linear-gradient(to top, #1fcf8e, #4ade80); 
                                                border-radius: 6px 6px 0 0; 
                                                transition: background 0.3s;"
                                        onmouseover="this.style.background='#18a871'"
                                        onmouseout="this.style.background='linear-gradient(to top, #1fcf8e, #4ade80)'">
                                    </div>
                                    
                                    <span style="margin-top: 10px; color: #888; font-size: 0.85rem;">{{ $hari[$index] }}</span>
                                </div>
                            @endforeach

                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3>Top Members</h3>
                        </div>

                        <div class="members">
                            {{-- Blade akan mengecek variabel $topMembers nanti. Jika kosong, tampilkan pesan @empty --}}
                            @forelse($topMembers ?? [] as $member)
                            <div class="member-card">
                                @if($member->foto_profil)
                                    <img src="{{ asset('storage/' . $member->foto_profil) }}"
                                        style="width:45px;height:45px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                                @else
                                    <div style="width:45px;height:45px;border-radius:50%;background:#1f3c45;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <span style="color:white;font-size:16px;font-weight:700;">
                                            {{ strtoupper(substr($member->nama_lengkap, 0, 1)) }}
                                        </span>
                                    </div>
                                @endif
                                <div>
                                    <strong>{{ $member->nama_lengkap }}</strong>
                                    <p style="font-size: 12px; color: #718096;">Member</p>
                                </div>
                                <span>{{ $member->peminjaman_count }} pinjam</span>
                            </div>
                            @empty
                            <div style="width: 100%; text-align: center; padding: 20px; color: #999; font-size: 0.9rem; background: #f8fbfc; border-radius: 14px;">
                                <i class="fa-solid fa-users-slash" style="font-size: 1.5rem; margin-bottom: 8px; color: #ccc;"></i><br>
                                Belum ada data member peringkat atas.
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3>Library Activity</h3>
                            <a href="#">View All</a>
                        </div>

                        <table>
                            <thead>
                                <tr>
                                    <th>Books</th>
                                    <th>Member Info</th>
                                    <th>Issue/Due</th>
                                    <th>Return</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                {{-- Blade akan mengecek variabel $recentActivities nanti. Jika kosong, tampilkan pesan @empty --}}
                                @forelse($recentActivities ?? [] as $activity)
                                <tr>
                                    <td style="font-size: 13px; font-weight: 600; color: #2d3748;">
                                        {{-- Ambil judul buku pertama dari detail --}}
                                        {{ $activity->detailPeminjaman->first()?->buku?->judul ?? '-' }}
                                    </td>
                                    <td style="font-size: 13px; color: #718096;">
                                        {{ $activity->anggota?->nama_lengkap ?? '-' }}
                                    </td>
                                    <td style="font-size: 13px; color: #718096;">
                                        {{ $activity->tanggal_pinjam
                                            ? \Carbon\Carbon::parse($activity->tanggal_pinjam)->format('d M Y')
                                            : '-' }}
                                        /
                                        {{ $activity->batas_pengembalian
                                            ? \Carbon\Carbon::parse($activity->batas_pengembalian)->format('d M Y')
                                            : '-' }}
                                    </td>
                                    <td style="font-size: 13px; color: #718096;">
                                        {{ $activity->tanggal_dikembalikan
                                            ? \Carbon\Carbon::parse($activity->tanggal_dikembalikan)->format('d M Y')
                                            : '-' }}
                                    </td>
                                    <td>
                                        @php
                                            $st = $activity->status_transaksi;
                                            $terlambat = $st === 'Dipinjam'
                                                && $activity->batas_pengembalian
                                                && \Carbon\Carbon::parse($activity->batas_pengembalian)->isPast();
                                        @endphp
                                        @if($terlambat)
                                            <span class="status late">TERLAMBAT</span>
                                        @elseif($st === 'Dipinjam')
                                            <span class="status pending">DIPINJAM</span>
                                        @elseif($st === 'Selesai')
                                            <span class="status done">SELESAI</span>
                                        @elseif($st === 'Menunggu OTP')
                                            <span class="status" style="background:#fff3e0; color:#e65100; padding:5px 12px; border-radius:20px; font-size:12px;">MENUNGGU OTP</span>
                                        @else
                                            <span class="status" style="background:#f7fafc; color:#718096; padding:5px 12px; border-radius:20px; font-size:12px;">{{ strtoupper($st) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 30px; color: #999;">
                                        <i class="fa-solid fa-folder-open" style="font-size: 1.5rem; margin-bottom: 8px; color: #ccc;"></i><br>
                                        Belum ada aktivitas peminjaman buku saat ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>

                <!-- RIGHT -->
                <div class="right-panel">

                    <div class="stat-card">
                        <h4>Total Books</h4>
                        <!-- Menampilkan total buku -->
                        <h2>{{ $totalBooks ?? 0 }}</h2>
                        <div class="progress green"></div>
                    </div>

                    <div class="stat-card">
                        <h4>Total Members</h4>
                        <!-- Menampilkan total member -->
                        <h2>{{ $totalMembers ?? 0 }}</h2>
                        <div class="progress green"></div>
                    </div>

                    <div class="stat-card">
                        <h4>Overdue Books</h4>
                        <!-- Menampilkan total buku terlambat -->
                        <h2>{{ $overdueBooks ?? 0 }}</h2>
                        <div class="progress red"></div>
                    </div>

                    <div class="report-card">
                        <h3>Generate Monthly Report</h3>
                        <p>Export all library analytics for the current month in PDF format</p>
                        <button>Download Report</button>
                    </div>

                </div>

            </section>

            <!-- FLOAT BUTTON -->
            <button class="floating-btn">
                <i class="fa-solid fa-plus"></i>
            </button>

        </main>
    </div>

</body>
</html>