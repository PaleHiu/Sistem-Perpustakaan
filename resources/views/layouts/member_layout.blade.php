<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPUS - @yield('title')</title>
    <link rel="stylesheet" href="{{ asset('dashboard_assets/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .dashboard-container { display: flex; min-height: 100vh; overflow: hidden; }
        .sidebar {
            width: 260px;
            position: fixed;
            height: 100vh;
            background: #1f3c45;
            z-index: 100;
        }
        .main-content {
            flex: 1;
            margin-left: 260px;
            background: #f3f6f9;
            min-height: 100vh;
            padding: 25px;
        }
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .user-profile-link {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 30px;
            transition: background 0.2s;
        }
        .user-profile-link:hover { background: #e8f4f0; }
        .user-profile-link .nama-user {
            font-size: 14px;
            font-weight: 600;
            color: #2d3748;
        }
    </style>
</head>
<body>
<div class="dashboard-container">

    <aside class="sidebar">
        <div class="sidebar-header">
            <h2 style="color:white;letter-spacing:2px;">SIPUS</h2>
            {{-- Garis bawah pakai wosh-logo.svg --}}
            <img src="{{ asset('ui_auth/wosh-logo.svg') }}"
                 alt="swoosh"
                 style="width:80px;margin:2px 0 4px;display:block;opacity:0.85;">
            <p style="font-size:12px;opacity:0.6;color:white;">Library Management</p>
        </div>

        <nav class="sidebar-menu" style="margin-top:40px;">
            <a href="{{ route('member.dashboard') }}" class="menu-item {{ request()->routeIs('member.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-table-columns"></i> <span>Dashboard</span>
            </a>
            <a href="{{ route('member.katalog') }}" class="menu-item {{ request()->routeIs('member.katalog') ? 'active' : '' }}">
                <i class="fa-solid fa-book"></i> <span>Katalog</span>
            </a>
            <a href="{{ route('member.keranjang') }}" class="menu-item {{ request()->routeIs('member.keranjang') ? 'active' : '' }}">
                <i class="fa-solid fa-cart-shopping"></i> <span>Keranjang</span>
            </a>
            <a href="{{ route('member.peminjaman') }}" class="menu-item {{ request()->routeIs('member.peminjaman') ? 'active' : '' }}">
                <i class="fa-solid fa-rectangle-list"></i> <span>Peminjaman Saya</span>
            </a>
            <a href="{{ route('member.riwayat') }}" class="menu-item {{ request()->routeIs('member.riwayat') ? 'active' : '' }}">
                <i class="fa-solid fa-clock-rotate-left"></i> <span>Riwayat</span>
            </a>
            <a href="{{ route('member.profil') }}" class="menu-item {{ request()->routeIs('member.profil') ? 'active' : '' }}">
                <i class="fa-solid fa-user"></i> <span>Profil</span>
            </a>
        </nav>

        <div class="sidebar-footer" style="margin-top:auto;padding-bottom:20px;">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        style="width:100%;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);color:white;padding:10px;border-radius:10px;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div class="breadcrumb" style="color:#7d8a95;">
                SIPUS > <span style="color:#1fcf8e;font-weight:bold;">@yield('title')</span>
            </div>
            @php
                $fotoProfil = Auth::user()->anggota?->foto_profil;
                $namaUser   = Auth::user()->anggota?->nama_lengkap ?? Auth::user()->name ?? 'User';
                $inisial    = strtoupper(substr($namaUser, 0, 1));
            @endphp
            <a href="{{ route('member.profil') }}" class="user-profile-link">
                @if($fotoProfil)
                    <img src="{{ asset('storage/' . $fotoProfil) }}"
                         style="width:40px;height:40px;border-radius:50%;object-fit:cover;flex-shrink:0;" alt="User">
                @else
                    <div style="width:40px;height:40px;background:#1fcf8e;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:700;color:white;flex-shrink:0;">
                        {{ $inisial }}
                    </div>
                @endif
                <span class="nama-user">{{ $namaUser }}</span>
            </a>
        </header>

        @yield('content')
    </main>
</div>
</body>
</html>