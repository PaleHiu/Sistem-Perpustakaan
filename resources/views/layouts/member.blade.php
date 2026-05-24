<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPUS - @yield('title')</title>
    <link rel="stylesheet" href="{{ asset('dashboard_assets/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        /* CSS pengunci agar layout tidak berantakan saat pindah halaman */
        .dashboard-container { display: flex; min-height: 100vh; overflow: hidden; }
        .sidebar { width: 260px; position: fixed; height: 100vh; background: #1f3c45; z-index: 100; display: flex; flex-direction: column; }
        .main-content { flex: 1; margin-left: 260px; background: #f3f6f9; min-height: 100vh; padding: 25px; }
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .menu-item.active { background: #2ca57c; color: white; border-radius: 8px; }
    </style>
</head>
<body>
<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-header" style="padding: 20px;">
            <h2 style="color: white; letter-spacing: 2px;">SIPUS</h2>
            <p style="font-size: 12px; opacity: 0.6; color: white;">Library Management</p>
        </div>

        <nav class="sidebar-menu" style="margin-top: 20px; padding: 0 15px;">
            <a href="{{ route('member.dashboard') }}" class="menu-item {{ request()->routeIs('member.dashboard') ? 'active' : '' }}" style="display: block; padding: 12px; color: white; text-decoration: none;">
                <i class="fa-solid fa-table-columns"></i> <span>Dashboard</span>
            </a>
            <a href="{{ route('member.katalog') }}" class="menu-item {{ request()->routeIs('member.katalog') ? 'active' : '' }}" style="display: block; padding: 12px; color: white; text-decoration: none;">
                <i class="fa-solid fa-book"></i> <span>Katalog</span>
            </a>
            <!-- Tambahkan menu lainnya di sini -->
        </nav>

        <div class="sidebar-footer" style="margin-top: auto; padding: 20px;">
            <div class="user-info" style="color: white; margin-bottom: 15px;">
                <strong>{{ Auth::user()->name }}</strong>
                <p style="font-size: 11px; opacity: 0.7;">Member Silver</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="width: 100%; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; padding: 10px; border-radius: 10px; cursor: pointer;">Logout</button>
            </form>
        </div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div class="breadcrumb" style="color: #7d8a95;">
                SIPUS > <span style="color: #1fcf8e; font-weight: bold;">@yield('title')</span>
            </div>
            <div class="user-area" style="display: flex; gap: 20px; align-items: center;">
                <i class="fa-regular fa-bell" style="font-size: 20px; color: #7d8a95;"></i>
                <i class="fa-regular fa-comment" style="font-size: 20px; color: #7d8a95;"></i>
                <img src="https://i.pravatar.cc/40" style="border-radius: 50%;" alt="User">
            </div>
        </header>

        @yield('content')
    </main>
</div>
</body>
</html>