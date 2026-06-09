<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Buku - SIPUS</title>
    <link rel="stylesheet" href="{{ asset('dashboard_assets/books_style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        @keyframes popIn { from{transform:scale(.95);opacity:0} to{transform:scale(1);opacity:1} }
        .form-label { font-size:11px;font-weight:700;letter-spacing:.08em;color:#a0aec0;text-transform:uppercase;display:block;margin-bottom:6px; }
        .form-input { width:100%;padding:10px 14px;border-radius:10px;border:1px solid #e2e8f0;font-size:13px;outline:none;transition:border-color .2s; }
        .form-input:focus { border-color:#1fcf8e;box-shadow:0 0 0 3px rgba(31,207,142,.15); }
        #tabelBuku thead th { position: sticky; top: 0; background-color: white; z-index: 10; border-bottom: 2px solid #e2e8f0; }  
        
        /* --- FIX PAGINATION LARAVEL --- */
        .pagination-links svg {
            width: 20px;
            height: 20px;
        }

        .pagination-links nav {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        /* Memperbaiki barisan angka dan panah agar menyamping rapi */
        .pagination-links nav > div:not(:first-child) {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        /* (Opsional) Sembunyikan teks "Showing 1 to 5..." bawaan Laravel 
        karena kamu sudah membuat teks "Menampilkan..." sendiri */
        .pagination-links nav > div:first-child p,
        .pagination-links .hidden {
            display: none;
        }

        /* --- PAGINATION BUTTON STYLE --- */

        /* 1. Sembunyikan teks "Showing 1 to 5..." bawaan Laravel */
        .pagination-links nav > div:first-child,
        .pagination-links p {
            display: none !important;
        }

        /* 2. Container untuk jejeran tombol angka */
        .pagination-links .relative.z-0.inline-flex {
            display: flex;
            gap: 8px; /* Jarak antar tombol */
            box-shadow: none !important; /* Hilangkan garis bayangan bawaan */
        }

        /* 3. Style dasar untuk semua tombol (Angka dan Panah) */
        .pagination-links .relative.z-0.inline-flex a,
        .pagination-links .relative.z-0.inline-flex span[aria-disabled="true"] span,
        .pagination-links .relative.z-0.inline-flex span[aria-current="page"] span {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            width: 70px;
            height: 70px;
            border-radius: 10px !important; /* Membuat kotak bersudut tumpul */
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid #e2e8f0;
            background-color: white;
            color: #4a5568;
            transition: all 0.2s ease;
            cursor: pointer;
            padding: 0 !important; /* Menghapus padding bawaan */
            margin: 0 !important; /* Menghapus jarak negatif bawaan */
        }

        /* 4. Efek saat kursor diarahkan ke tombol (Hover) */
        .pagination-links .relative.z-0.inline-flex a:hover {
            background-color: #f0fff4;
            border-color: #1fcf8e;
            color: #1fcf8e;
            transform: translateY(-2px); /* Efek tombol terangkat */
        }

        /* 5. Style untuk halaman yang sedang aktif */
        .pagination-links .relative.z-0.inline-flex span[aria-current="page"] span {
            background-color: #1fcf8e;
            color: white;
            border-color: #1fcf8e;
            box-shadow: 0 4px 10px rgba(31, 207, 142, 0.3);
        }

        /* 6. Style untuk panah yang tidak bisa diklik (Disabled) */
        .pagination-links .relative.z-0.inline-flex span[aria-disabled="true"] span {
            background-color: #f7fafc;
            color: #cbd5e0;
            cursor: not-allowed;
            border-color: #edf2f7;
        }

        /* 7. Perbaiki ukuran ikon panah SVG di dalam tombol */
        .pagination-links svg {
            width: 18px;
            height: 18px;
        }
    </style>
</head>
<body>
<div class="dashboard-container">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2 style="color:white;letter-spacing:2px;">SIPUS</h2>
            <img src="{{ asset('ui_auth/wosh-logo.svg') }}" alt="swoosh"
                 style="width:80px;margin:2px 0 4px;display:block;opacity:0.85;">
            <p style="font-size:12px;opacity:0.6;color:white;">Admin Portal</p>
        </div>
        <nav class="sidebar-menu">
            <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-table-columns"></i><span>Dashboard</span>
            </a>
            <a href="{{ route('books.index') }}" class="menu-item {{ request()->routeIs('books.index') ? 'active' : '' }}">
                <i class="fa-solid fa-book"></i><span>Books</span>
            </a>
            <a href="{{ route('members.index') }}" class="menu-item {{ request()->routeIs('members.index') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i><span>Member</span>
            </a>
            <a href="{{ route('borrowing.index') }}" class="menu-item {{ request()->routeIs('borrowing.index') ? 'active' : '' }}">
                <i class="fa-solid fa-handshake"></i><span>Borrowing</span>
            </a>
        </nav>
        <div class="sidebar-footer" style="margin-top:auto;padding-bottom:20px;">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        style="width:100%;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);color:white;padding:10px;border-radius:10px;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;font-size:14px;">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">

        <!-- TOPBAR -->
        <header class="topbar">
            <div><p class="breadcrumb">Pages / <span>Books</span></p></div>
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="text-align:right;">
                    <div style="font-size:14px;font-weight:600;color:#2d3748;">{{ Auth::user()->email }}</div>
                    <div style="font-size:11px;color:#718096;">Super Administrator</div>
                </div>
                <div style="width:40px;height:40px;background:#1fcf8e;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:700;color:white;flex-shrink:0;">
                    {{ strtoupper(substr(Auth::user()->email, 0, 1)) }}
                </div>
            </div>
        </header>

        {{-- NOTIFIKASI --}}
        @if(session('success'))
        <div style="background:#f0fff4;border:1px solid #c6f6d5;border-radius:10px;padding:12px 20px;margin-bottom:15px;color:#38a169;display:flex;align-items:center;gap:10px;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
        @endif

        <!-- FILTER BAR -->
        <div class="card">
            <div class="filter-bar">
                <input type="text" id="searchInput" oninput="filterBuku()"
                       placeholder="Search by title, author..." class="search-input">
                <button class="btn-outline"><i class="fa-solid fa-filter"></i> Filter</button>
                <button class="btn-outline"><i class="fa-solid fa-arrow-down-a-z"></i> Sort</button>
                <button class="btn-primary"
                        onclick="document.getElementById('modal-tambah-buku').style.display='flex'">
                    + Add Book
                </button>
            </div>
        </div>

        <!-- TABLE -->
        <div class="card table-wrapper" style="max-height: 450px; overflow-y: auto; position: relative;">
            <table id="tabelBuku">
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
                    <tr data-judul="{{ strtolower($book->judul) }}"
                        data-penulis="{{ strtolower($book->penulis) }}">
                        <td>{{ $index + 1 }}</td>
                        <td>
                            @if($book->cover)
                                <img src="{{ asset('storage/' . $book->cover) }}" alt="Cover" class="book-cover">
                            @else
                                <div style="width:40px;height:55px;background:#e2e8f0;border-radius:4px;display:flex;align-items:center;justify-content:center;">
                                    <i class="fa-solid fa-book" style="color:#a0aec0;font-size:16px;"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <strong class="text-dark">{{ $book->judul }}</strong><br>
                            <small class="text-muted">{{ $book->penulis }}</small>
                        </td>
                        <td class="text-muted">{{ $book->kategori->nama_kategori ?? '-' }}</td>
                        <td class="text-muted">{{ $book->penerbit }}</td>
                        <td class="text-muted">{{ $book->tahun_terbit }}</td>
                        <td><strong>{{ $book->stok_tersedia }}</strong></td>
                        <td>
                            <span class="status {{ $book->stok_tersedia > 0 ? 'done' : 'late' }}">
                                {{ $book->stok_tersedia > 0 ? 'Tersedia' : 'Habis' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="#" class="action-icon icon-detail" title="Detail"
                               onclick="bukaModalDetail(
                                   '{{ addslashes($book->judul) }}',
                                   '{{ addslashes($book->penulis) }}',
                                   '{{ addslashes($book->penerbit) }}',
                                   '{{ $book->tahun_terbit }}',
                                   '{{ addslashes($book->kategori->nama_kategori ?? '-') }}',
                                   {{ $book->stok_total }},
                                   {{ $book->stok_tersedia }},
                                   '{{ $book->cover ? asset('storage/'.$book->cover) : '' }}'
                               )">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="#" class="action-icon icon-edit" title="Edit"
                               onclick="bukaModalEdit(
                                   {{ $book->id }},
                                   '{{ addslashes($book->judul) }}',
                                   '{{ addslashes($book->penulis) }}',
                                   '{{ addslashes($book->penerbit) }}',
                                   '{{ $book->tahun_terbit }}',
                                   {{ $book->kategori_id }},
                                   {{ $book->stok_total }},
                                   {{ $book->stok_tersedia }}
                               )">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <a href="#" class="action-icon" title="Hapus"
                               style="color:#e53e3e;margin-left:4px;"
                               onclick="konfirmasiHapusBuku({{ $book->id }}, '{{ addslashes($book->judul) }}')">
                                <i class="fa-solid fa-trash"></i>
                            </a>
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

        <!-- PAGINATION -->
        <div class="pagination-container" style="display:flex; flex-direction:column; gap:15px; margin-top:20px;">
            <!-- Teks custom kamu dipertahankan -->
            <span class="pagination-info" style="font-size:13px; color:#718096; text-align:center;">
                Menampilkan {{ $books->firstItem() ?? 0 }} - {{ $books->lastItem() ?? 0 }} dari total {{ $books->total() ?? 0 }} buku
            </span>
            
            <!-- Wrapper utama pagination -->
            <div class="pagination-links" style="display: flex; justify-content: center;">
                {{ $books->links() }} 
            </div>
        </div>

        <!-- STATS -->
        <div class="grid-4" style="grid-template-columns:repeat(4,1fr);margin-top:20px;">
            <div class="stat-card"><h4>Total Titles</h4><h2>{{ $totalTitles ?? 0 }}</h2></div>
            <div class="stat-card"><h4>Total Items</h4><h2>{{ $totalItems ?? 0 }}</h2></div>
            <div class="stat-card"><h4>Low Stock</h4><h2>{{ $lowStock ?? 0 }}</h2></div>
            <div class="stat-card"><h4>Reservations</h4><h2>{{ $reservations ?? 0 }}</h2></div>
        </div>

    </main>
</div>

{{-- Form hapus tersembunyi --}}
<form id="form-hapus-buku" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

{{-- MODAL TAMBAH BUKU --}}
<div id="modal-tambah-buku" onclick="tutupModal('modal-tambah-buku', event)"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.35);z-index:999;justify-content:center;align-items:center;padding:20px;">
    <div style="background:white;border-radius:16px;width:100%;max-width:560px;padding:28px;box-shadow:0 12px 30px rgba(0,0,0,.15);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <h3 style="font-size:18px;font-weight:700;color:#1a202c;">Tambah Buku Baru</h3>
            <button onclick="document.getElementById('modal-tambah-buku').style.display='none'"
                    style="background:none;border:none;font-size:22px;color:#a0aec0;cursor:pointer;">×</button>
        </div>
        <form method="POST" action="{{ route('books.store') }}" enctype="multipart/form-data">
            @csrf
            <div style="display:flex;flex-direction:column;gap:14px;">
                <div>
                    <label class="form-label">Judul Buku</label>
                    <input type="text" name="judul" class="form-input" placeholder="Masukkan judul lengkap buku" required value="{{ old('judul') }}">
                </div>
                <div style="display:flex;gap:12px;">
                    <div style="flex:1;">
                        <label class="form-label">Penulis</label>
                        <input type="text" name="penulis" class="form-input" placeholder="Nama penulis" required value="{{ old('penulis') }}">
                    </div>
                    <div style="flex:1;">
                        <label class="form-label">Penerbit</label>
                        <input type="text" name="penerbit" class="form-input" placeholder="Nama penerbit" required value="{{ old('penerbit') }}">
                    </div>
                </div>
                <div style="display:flex;gap:12px;">
                    <div style="flex:2;">
                        <label class="form-label">Kategori</label>
                        <select name="kategori_id" class="form-input" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($kategoris ?? [] as $k)
                                <option value="{{ $k->id }}" {{ old('kategori_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="flex:1;">
                        <label class="form-label">Tahun Terbit</label>
                        <input type="text" name="tahun_terbit" class="form-input" placeholder="YYYY" maxlength="4" required value="{{ old('tahun_terbit') }}">
                    </div>
                    <div style="flex:1;">
                        <label class="form-label">Stok Total</label>
                        <input type="number" name="stok_total" class="form-input" value="{{ old('stok_total', 0) }}" min="0" required>
                    </div>
                </div>
                <div>
                    <label class="form-label">Upload Cover Buku</label>
                    <div style="border:2px dashed #e2e8f0;border-radius:12px;padding:24px;text-align:center;cursor:pointer;"
                         onclick="document.getElementById('input-cover-tambah').click()">
                        <i class="fa-solid fa-cloud-arrow-up" style="font-size:28px;color:#a0aec0;margin-bottom:8px;display:block;"></i>
                        <p style="font-size:13px;color:#718096;"><span style="color:#1fcf8e;font-weight:600;">Klik untuk upload</span> atau drag and drop</p>
                        <small style="color:#a0aec0;">PNG, JPG up to 5MB</small>
                        <p id="nama-file-tambah" style="margin-top:8px;font-size:12px;color:#1fcf8e;display:none;"></p>
                    </div>
                    <input type="file" id="input-cover-tambah" name="cover" accept="image/png,image/jpeg" style="display:none;"
                           onchange="tampilNamaFile(this, 'nama-file-tambah')">
                </div>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:24px;">
                <button type="button" onclick="document.getElementById('modal-tambah-buku').style.display='none'"
                        style="padding:11px 24px;background:#f7fafc;border:1px solid #e2e8f0;border-radius:10px;cursor:pointer;font-size:14px;color:#4a5568;">Batal</button>
                <button type="submit"
                        onclick="this.disabled=true;this.textContent='Menyimpan...';this.closest('form').submit();"
                        style="padding:11px 24px;background:#1fcf8e;color:white;border:none;border-radius:10px;cursor:pointer;font-size:14px;font-weight:600;">Simpan Buku</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL DETAIL BUKU --}}
<div id="modal-detail-buku" onclick="tutupModal('modal-detail-buku', event)"
    style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.35);z-index:999;justify-content:center;align-items:center;padding:20px;">
    <div style="background:white;border-radius:16px;width:100%;max-width:520px;padding:28px;box-shadow:0 12px 30px rgba(0,0,0,.15);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <h3 style="font-size:18px;font-weight:700;color:#1a202c;">Detail Buku</h3>
            <button onclick="document.getElementById('modal-detail-buku').style.display='none'"
                    style="background:none;border:none;font-size:22px;color:#a0aec0;cursor:pointer;">×</button>
        </div>
        <div style="display:flex;gap:20px;align-items:flex-start;">
            <div id="detail-cover-wrap"
                 style="width:100px;height:135px;background:linear-gradient(135deg,#1f3c45,#2d6a5a);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fa-solid fa-book" style="color:rgba(255,255,255,.6);font-size:32px;"></i>
            </div>
            <div style="flex:1;">
                <h4 id="detail-judul" style="font-size:17px;font-weight:700;color:#1a202c;margin-bottom:4px;"></h4>
                <p id="detail-penulis" style="font-size:13px;color:#718096;margin-bottom:16px;"></p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div><p style="font-size:11px;color:#a0aec0;font-weight:600;text-transform:uppercase;margin-bottom:3px;">Penerbit</p><p id="detail-penerbit" style="font-size:14px;font-weight:600;color:#2d3748;"></p></div>
                    <div><p style="font-size:11px;color:#a0aec0;font-weight:600;text-transform:uppercase;margin-bottom:3px;">Tahun</p><p id="detail-tahun" style="font-size:14px;font-weight:600;color:#2d3748;"></p></div>
                    <div><p style="font-size:11px;color:#a0aec0;font-weight:600;text-transform:uppercase;margin-bottom:3px;">Kategori</p><p id="detail-kategori" style="font-size:14px;font-weight:600;color:#2d3748;"></p></div>
                    <div><p style="font-size:11px;color:#a0aec0;font-weight:600;text-transform:uppercase;margin-bottom:3px;">Stok</p><p id="detail-stok" style="font-size:14px;font-weight:600;color:#2d3748;"></p></div>
                </div>
            </div>
        </div>
        <div style="display:flex;justify-content:flex-end;margin-top:24px;">
            <button onclick="document.getElementById('modal-detail-buku').style.display='none'"
                    style="padding:10px 30px;background:#f7fafc;border:1px solid #e2e8f0;border-radius:10px;cursor:pointer;font-size:14px;color:#4a5568;">Tutup</button>
        </div>
    </div>
</div>

{{-- MODAL EDIT BUKU --}}
<div id="modal-edit-buku" onclick="tutupModal('modal-edit-buku', event)"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.35);z-index:999;justify-content:center;align-items:center;padding:20px;">
    <div style="background:white;border-radius:16px;width:100%;max-width:560px;padding:28px;box-shadow:0 12px 30px rgba(0,0,0,.15);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <h3 style="font-size:18px;font-weight:700;color:#1a202c;">Edit Buku</h3>
            <button onclick="document.getElementById('modal-edit-buku').style.display='none'"
                    style="background:none;border:none;font-size:22px;color:#a0aec0;cursor:pointer;">×</button>
        </div>
        <form id="form-edit-buku" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div style="display:flex;flex-direction:column;gap:14px;">
                <div><label class="form-label">Judul Buku</label><input type="text" id="edit-judul" name="judul" class="form-input" required></div>
                <div style="display:flex;gap:12px;">
                    <div style="flex:1;"><label class="form-label">Penulis</label><input type="text" id="edit-penulis" name="penulis" class="form-input" required></div>
                    <div style="flex:1;"><label class="form-label">Penerbit</label><input type="text" id="edit-penerbit" name="penerbit" class="form-input" required></div>
                </div>
                <div style="display:flex;gap:12px;">
                    <div style="flex:2;">
                        <label class="form-label">Kategori</label>
                        <select id="edit-kategori" name="kategori_id" class="form-input" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($kategoris ?? [] as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="flex:1;"><label class="form-label">Tahun Terbit</label><input type="text" id="edit-tahun" name="tahun_terbit" class="form-input" maxlength="4" required></div>
                    <div style="flex:1;"><label class="form-label">Stok Total</label><input type="number" id="edit-stok-total" name="stok_total" class="form-input" min="0" required></div>
                </div>
                <div><label class="form-label">Stok Tersedia</label><input type="number" id="edit-stok-tersedia" name="stok_tersedia" class="form-input" min="0" required></div>
                <div>
                    <label class="form-label">Ganti Cover (opsional)</label>
                    <div style="border:2px dashed #e2e8f0;border-radius:12px;padding:18px;text-align:center;cursor:pointer;"
                         onclick="document.getElementById('input-cover-edit').click()">
                        <i class="fa-solid fa-cloud-arrow-up" style="font-size:24px;color:#a0aec0;margin-bottom:6px;display:block;"></i>
                        <p style="font-size:13px;color:#718096;"><span style="color:#1fcf8e;font-weight:600;">Klik untuk upload</span> cover baru</p>
                        <p id="nama-file-edit" style="margin-top:6px;font-size:12px;color:#1fcf8e;display:none;"></p>
                    </div>
                    <input type="file" id="input-cover-edit" name="cover" accept="image/png,image/jpeg" style="display:none;"
                           onchange="tampilNamaFile(this, 'nama-file-edit')">
                </div>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:24px;">
                <button type="button" onclick="document.getElementById('modal-edit-buku').style.display='none'"
                        style="padding:11px 24px;background:#f7fafc;border:1px solid #e2e8f0;border-radius:10px;cursor:pointer;font-size:14px;color:#4a5568;">Batal</button>
                <button type="submit"
                        onclick="this.disabled=true;this.textContent='Menyimpan...';this.closest('form').submit();"
                        style="padding:11px 24px;background:#1fcf8e;color:white;border:none;border-radius:10px;cursor:pointer;font-size:14px;font-weight:600;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function tutupModal(id, event) {
    if (event.target === document.getElementById(id)) document.getElementById(id).style.display = 'none';
}
function tampilNamaFile(input, targetId) {
    const el = document.getElementById(targetId);
    if (input.files && input.files[0]) { el.textContent = '✓ ' + input.files[0].name; el.style.display = 'block'; }
}
function bukaModalDetail(judul, penulis, penerbit, tahun, kategori, stokTotal, stokTersedia, coverUrl) {
    document.getElementById('detail-judul').textContent    = judul;
    document.getElementById('detail-penulis').textContent  = penulis;
    document.getElementById('detail-penerbit').textContent = penerbit;
    document.getElementById('detail-tahun').textContent    = tahun;
    document.getElementById('detail-kategori').textContent = kategori;
    document.getElementById('detail-stok').textContent     = stokTersedia + ' / ' + stokTotal + ' tersedia';
    const coverWrap = document.getElementById('detail-cover-wrap');
    if (coverUrl) {
        coverWrap.innerHTML = '<img src="' + coverUrl + '" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">';
    } else {
        coverWrap.innerHTML = '<i class="fa-solid fa-book" style="color:rgba(255,255,255,.6);font-size:32px;"></i>';
        coverWrap.style.background = 'linear-gradient(135deg,#1f3c45,#2d6a5a)';
    }
    document.getElementById('modal-detail-buku').style.display = 'flex';
}
function bukaModalEdit(id, judul, penulis, penerbit, tahun, kategoriId, stokTotal, stokTersedia) {
    document.getElementById('edit-judul').value         = judul;
    document.getElementById('edit-penulis').value       = penulis;
    document.getElementById('edit-penerbit').value      = penerbit;
    document.getElementById('edit-tahun').value         = tahun;
    document.getElementById('edit-stok-total').value    = stokTotal;
    document.getElementById('edit-stok-tersedia').value = stokTersedia;
    const select = document.getElementById('edit-kategori');
    for (let opt of select.options) opt.selected = opt.value == kategoriId;
    document.getElementById('form-edit-buku').action = '/books/' + id;
    document.getElementById('nama-file-edit').style.display = 'none';
    document.getElementById('modal-edit-buku').style.display = 'flex';
}
function konfirmasiHapusBuku(id, judul) {
    if (confirm('Yakin ingin menghapus buku "' + judul + '"?\nTindakan ini tidak dapat dibatalkan!')) {
        const form = document.getElementById('form-hapus-buku');
        form.action = '/books/' + id;
        form.submit();
    }
}
function filterBuku() {
    const keyword = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('#tabelBuku tbody tr').forEach(row => {
        const judul   = row.getAttribute('data-judul') || '';
        const penulis = row.getAttribute('data-penulis') || '';
        row.style.display = (judul.includes(keyword) || penulis.includes(keyword)) ? '' : 'none';
    });
}
@if($errors->any())
    document.getElementById('modal-tambah-buku').style.display = 'flex';
@endif
</script>
</body>
</html>