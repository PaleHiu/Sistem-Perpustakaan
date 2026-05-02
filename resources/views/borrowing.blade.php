<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman Buku - SIPUS</title>
    
    <link rel="stylesheet" href="{{ asset('dashboard_assets/books_style.css') }}">
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
            <a href="{{ route('dashboard') }}" class="menu-item">
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
            <a href="{{ route('borrowing.index') }}" class="menu-item active">
                <i class="fa-solid fa-handshake"></i>
                <span>Borrowing</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="#" class="menu-item">
                <i class="fa-solid fa-user"></i>
                <span>User Profile</span>
            </a>
            
            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <a href="#" class="menu-item text-danger" onclick="event.preventDefault(); this.closest('form').submit();">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Logout</span>
                </a>
            </form>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">

        <!-- TOPBAR -->
        <header class="topbar">
            <div>
                <p class="breadcrumb">Pages / <span>Borrowing</span></p>
            </div>

            <div class="user-area">
                <i class="fa-regular fa-comment"></i>
                <i class="fa-regular fa-bell"></i>
                <div class="user-info">
                    <strong>{{ Auth::user()->email }}</strong>
                    <small>Super Administrator</small>
                </div>
                <img src="https://i.pravatar.cc/50?img=12" alt="User">
            </div>
        </header>

        <!-- FILTER BAR -->
        <div class="card">
            <div class="filter-bar">
                <input type="text" placeholder="Cari nama / kode buku..." class="search-input">
                <button class="btn-outline">Urutkan</button>
                <button class="btn-outline">Status</button>
                <button class="btn-primary">+ Add Borrow Book</button>
            </div>
        </div>

        <!-- TABLE -->
        <div class="card table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Anggota</th>
                        <th>Kode Buku</th>
                        <th>Waktu Pinjam</th>
                        <th>Waktu Kembali</th>
                        <th>Terlambat</th>
                        <th>Denda</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($borrowings ?? [] as $index => $borrow)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><span class="text-dark">{{ $borrow->anggota->nama_lengkap ?? '-' }}</span></td>
                            <td class="text-muted">{{ $borrow->buku->kode_buku ?? '-' }}</td>
                            <td class="text-muted">{{ $borrow->tgl_pinjam }}</td>
                            <td class="text-muted">{{ $borrow->batas_kembali }}</td>
                            
                            <td class="text-danger">
                                {{ $borrow->hari_terlambat > 0 ? $borrow->hari_terlambat . ' Hari' : '-' }}
                            </td>
                            
                            <td class="text-muted">Rp {{ number_format($borrow->denda ?? 0, 0, ',', '.') }}</td>
                            
                            <td>
                                @if($borrow->status == 'Dipinjam')
                                    <span class="status pending">DIPINJAM</span>
                                @elseif($borrow->status == 'Selesai')
                                    <span class="status done">SELESAI</span>
                                @else
                                    <span class="status late">TERLAMBAT</span>
                                @endif
                            </td>
                            
                            <td class="text-center">
                                <a href="#" class="action-icon icon-detail" title="Detail"><i class="fa-solid fa-eye"></i></a>
                                <a href="#" class="action-icon icon-process" title="Proses Pengembalian"><i class="fa-solid fa-check-to-slot"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="empty-state">
                                <i class="fa-solid fa-hand-holding-hand"></i><br>
                                Belum ada data transaksi peminjaman buku.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- FOOTER TABLE PAGINATION -->
        <div class="pagination-container">
            <span class="pagination-info">Menampilkan {{ count($borrowings ?? []) }} data</span>

            <div class="pagination-links">
                <span class="page-item active">1</span>
            </div>
        </div>
    </main>
</div>

</body>
</html>