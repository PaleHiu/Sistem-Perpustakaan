<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SIPUS Dashboard</title>
    <link rel="stylesheet" href="{{ asset('dashboard_assets/style.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        /* Samakan style logout admin dengan member */
        .sidebar-footer form button {
            width: 100%;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
            padding: 10px;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
            transition: background 0.2s;
        }
        .sidebar-footer form button:hover {
            background: rgba(255,255,255,0.2);
        }
        /* Topbar user area admin */
        .admin-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 30px;
            transition: background 0.2s;
        }
        .admin-profile:hover { background: #e8f4f0; }
        .admin-profile .nama-admin {
            font-size: 14px;
            font-weight: 600;
            color: #2d3748;
            text-align: right;
        }
        .admin-profile .role-admin {
            font-size: 11px;
            color: #718096;
            text-align: right;
        }
    </style>
</head>
<body>
<div class="dashboard-container">

    <!-- SIDEBAR ADMIN — desain sama dengan member -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2 style="color:white;letter-spacing:2px;">SIPUS</h2>
            {{-- Garis bawah pakai wosh-logo.svg sama seperti member --}}
            <img src="{{ asset('ui_auth/wosh-logo.svg') }}"
                 alt="swoosh"
                 style="width:80px;margin:2px 0 4px;display:block;opacity:0.85;">
            <p style="font-size:12px;opacity:0.6;color:white;">Admin Portal</p>
        </div>

        <nav class="sidebar-menu">
            <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-table-columns"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('books.index') }}" class="menu-item {{ request()->routeIs('books.index') ? 'active' : '' }}">
                <i class="fa-solid fa-book"></i>
                <span>Books</span>
            </a>
            <a href="{{ route('members.index') }}" class="menu-item {{ request()->routeIs('members.index') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i>
                <span>Member</span>
            </a>
            <a href="{{ route('borrowing.index') }}" class="menu-item {{ request()->routeIs('borrowing.index') ? 'active' : '' }}">
                <i class="fa-solid fa-handshake"></i>
                <span>Borrowing</span>
            </a>
        </nav>

        {{-- Logout sama desainnya dengan member --}}
        <div class="sidebar-footer" style="margin-top:auto;padding-bottom:20px;">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <main class="main-content">

        <!-- TOPBAR ADMIN — hanya avatar + nama, tanpa icon notif/pesan -->
        <header class="topbar">
            <div>
                <p class="breadcrumb">Pages / <span>Dashboard</span></p>
            </div>

            {{-- Hanya avatar + nama admin --}}
            <div class="admin-profile">
                <div style="text-align:right;">
                    <div class="nama-admin">{{ Auth::user()->email }}</div>
                    <div class="role-admin">Super Administrator</div>
                </div>
                <div style="width:40px;height:40px;background:#1fcf8e;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:700;color:white;flex-shrink:0;">
                    {{ strtoupper(substr(Auth::user()->email, 0, 1)) }}
                </div>
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

                    @php
                        $chartData = $chartData ?? [0,0,0,0,0,0,0];
                        $hari      = ['Mo','Tu','We','Th','Fr','Sa','Su'];
                        $maxValue  = max($chartData) > 0 ? max($chartData) : 1;
                    @endphp

                    <div style="display:flex;align-items:flex-end;justify-content:space-around;height:180px;margin-top:20px;border-bottom:2px dashed #f0f0f0;padding-bottom:10px;">
                        @foreach($chartData as $index => $value)
                        @php $heightPercent = ($value / $maxValue) * 100; @endphp
                        <div style="display:flex;flex-direction:column;align-items:center;justify-content:flex-end;height:100%;width:10%;">
                            <div title="{{ $value }} Peminjaman"
                                style="width:100%;height:{{ $heightPercent }}%;background:linear-gradient(to top,#1fcf8e,#4ade80);border-radius:6px 6px 0 0;transition:background 0.3s;"
                                onmouseover="this.style.background='#18a871'"
                                onmouseout="this.style.background='linear-gradient(to top,#1fcf8e,#4ade80)'">
                            </div>
                            <span style="margin-top:10px;color:#888;font-size:0.85rem;">{{ $hari[$index] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>Top Members</h3>
                    </div>
                    <div class="members">
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
                                <p style="font-size:12px;color:#718096;">Member</p>
                            </div>
                            <span>{{ $member->peminjaman_count }} pinjam</span>
                        </div>
                        @empty
                        <div style="width:100%;text-align:center;padding:20px;color:#999;font-size:0.9rem;background:#f8fbfc;border-radius:14px;">
                            <i class="fa-solid fa-users-slash" style="font-size:1.5rem;margin-bottom:8px;color:#ccc;"></i><br>
                            Belum ada data member peringkat atas.
                        </div>
                        @endforelse
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>Library Activity</h3>
                        <a href="{{ route('borrowing.index') }}">View All</a>
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
                            @forelse($recentActivities ?? [] as $activity)
                            <tr>
                                <td style="font-size:13px;font-weight:600;color:#2d3748;">
                                    {{ $activity->detailPeminjaman->first()?->buku?->judul ?? '-' }}
                                </td>
                                <td style="font-size:13px;color:#718096;">
                                    {{ $activity->anggota?->nama_lengkap ?? '-' }}
                                </td>
                                <td style="font-size:13px;color:#718096;">
                                    {{ $activity->tanggal_pinjam ? \Carbon\Carbon::parse($activity->tanggal_pinjam)->format('d M Y') : '-' }}
                                    /
                                    {{ $activity->batas_pengembalian ? \Carbon\Carbon::parse($activity->batas_pengembalian)->format('d M Y') : '-' }}
                                </td>
                                <td style="font-size:13px;color:#718096;">
                                    {{ $activity->tanggal_dikembalikan ? \Carbon\Carbon::parse($activity->tanggal_dikembalikan)->format('d M Y') : '-' }}
                                </td>
                                <td>
                                    @php
                                        $st        = $activity->status_transaksi;
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
                                        <span class="status" style="background:#fff3e0;color:#e65100;padding:5px 12px;border-radius:20px;font-size:12px;">MENUNGGU OTP</span>
                                    @else
                                        <span class="status" style="background:#f7fafc;color:#718096;padding:5px 12px;border-radius:20px;font-size:12px;">{{ strtoupper($st) }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" style="text-align:center;padding:30px;color:#999;">
                                    <i class="fa-solid fa-folder-open" style="font-size:1.5rem;margin-bottom:8px;color:#ccc;display:block;"></i>
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
                    <h2>{{ $totalBooks ?? 0 }}</h2>
                    <div class="progress green"></div>
                </div>
                <div class="stat-card">
                    <h4>Total Members</h4>
                    <h2>{{ $totalMembers ?? 0 }}</h2>
                    <div class="progress green"></div>
                </div>
                <div class="stat-card">
                    <h4>Overdue Books</h4>
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

        <button class="floating-btn">
            <i class="fa-solid fa-plus"></i>
        </button>

    </main>
</div>
</body>
</html>