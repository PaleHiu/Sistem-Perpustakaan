<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Anggota - SIPUS</title>
    
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
            <a href="{{ route('members.index') }}" class="menu-item active">
                <i class="fa-solid fa-users"></i>
                <span>Member</span>
            </a>
            <a href="{{ route('borrowing.index') }}" class="menu-item">
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
                <p class="breadcrumb">Pages / <span>Members</span></p>
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
                <input type="text" placeholder="Cari nama / NIK..." class="search-input">
                <button class="btn-outline">Filter Status</button>
                <button class="btn-primary">+ Tambah Anggota</button>
            </div>
        </div>

        <!-- TABLE -->
        <div class="card table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>NIK</th>
                        <th>No HP</th>
                        <th>Alamat</th>
                        <th>Pekerjaan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members ?? [] as $index => $member)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong class="text-dark">{{ $member->nama_lengkap }}</strong></td>
                            <td class="text-muted">{{ $member->user->email ?? '-' }}</td>
                            <td class="text-muted">{{ $member->nik }}</td>
                            <td class="text-muted">{{ $member->no_hp }}</td>
                            <td class="text-muted">{{ $member->alamat }}</td>
                            <td class="text-muted">{{ $member->pekerjaan }}</td>
                            <td class="text-center">
                                <a href="#" class="action-icon icon-detail" title="Detail"><i class="fa-solid fa-eye"></i></a>
                                <a href="#" class="action-icon icon-edit" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty-state">
                                <i class="fa-solid fa-user-xmark"></i><br>
                                Belum ada data anggota yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- FOOTER TABLE PAGINATION -->
        <div class="pagination-container">
            <span class="pagination-info">Menampilkan {{ count($members ?? []) }} data</span>

            <div class="pagination-links">
                <span class="page-item active">1</span>
            </div>
        </div>


    </main>
</div>

</body>
</html>