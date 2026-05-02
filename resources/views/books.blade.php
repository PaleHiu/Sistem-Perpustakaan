<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Buku - SIPUS</title>
    
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
            <a href="{{ route('books.index') }}" class="menu-item active">
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
                <p class="breadcrumb">Pages / <span>Books</span></p>
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
                <input type="text" placeholder="Search by title, author, or ISBN..." class="search-input">
                <button class="btn-outline"><i class="fa-solid fa-filter"></i> Filter</button>
                <button class="btn-outline"><i class="fa-solid fa-arrow-down-a-z"></i> Sort</button>
                <button class="btn-primary">+ Add Book</button>
            </div>
        </div>

        <!-- TABLE -->
        <div class="card table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Cover</th>
                        <th>Title & Author</th>
                        <th>Category</th>
                        <th>Publisher</th>
                        <th>Year</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($books ?? [] as $index => $book)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><img src="{{ asset('storage/' . $book->cover) }}" alt="Cover" class="book-cover"></td>
                            <td>
                                <strong class="text-dark">{{ $book->judul }}</strong><br>
                                <small class="text-muted">{{ $book->penulis }}</small>
                            </td>
                            <td class="text-muted">{{ $book->kategori }}</td>
                            <td class="text-muted">{{ $book->penerbit }}</td>
                            <td class="text-muted">{{ $book->tahun_terbit }}</td>
                            <td><strong>{{ $book->stok }}</strong></td>
                            <td>
                                <span class="status {{ $book->stok > 0 ? 'done' : 'late' }}">
                                    {{ $book->stok > 0 ? 'Tersedia' : 'Habis' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="#" class="action-icon icon-detail" title="Detail"><i class="fa-solid fa-eye"></i></a>
                                <a href="#" class="action-icon icon-edit" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="empty-state">
                                <i class="fa-solid fa-book-open-reader"></i><br>
                                Belum ada data buku yang ditambahkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- FOOTER TABLE PAGINATION -->
        <div class="pagination-container">
            <span class="pagination-info">Showing {{ count($books ?? []) }} results</span>

            <div class="pagination-links">
                <span class="page-item active">1</span>
                <span class="page-item outline">2</span>
                <span class="page-item outline">3</span>
            </div>
        </div>

        <!-- STATS CARDS -->
        <div class="grid-4" style="grid-template-columns: repeat(4, 1fr); margin-top: 20px;">
            <div class="stat-card">
                <h4>Total Titles</h4>
                <h2>{{ $totalTitles ?? 0 }}</h2>
            </div>
            <div class="stat-card">
                <h4>Total Items</h4>
                <h2>{{ $totalItems ?? 0 }}</h2>
            </div>
            <div class="stat-card">
                <h4>Low Stock</h4>
                <h2>{{ $lowStock ?? 0 }}</h2>
            </div>
            <div class="stat-card">
                <h4>Reservations</h4>
                <h2>{{ $reservations ?? 0 }}</h2>
            </div>
        </div>

    </main>
</div>

</body>
</html>