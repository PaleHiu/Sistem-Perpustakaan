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

    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>LIBRARY<br>MANAGER</h2>
            <p>Admin Portal</p>
        </div>
        <nav class="sidebar-menu">
            <a href="{{ route('dashboard') }}" class="menu-item">
                <i class="fa-solid fa-table-columns"></i><span>Dashboard</span>
            </a>
            <a href="{{ route('books.index') }}" class="menu-item">
                <i class="fa-solid fa-book"></i><span>Books</span>
            </a>
            <a href="{{ route('members.index') }}" class="menu-item active">
                <i class="fa-solid fa-users"></i><span>Member</span>
            </a>
            <a href="{{ route('borrowing.index') }}" class="menu-item">
                <i class="fa-solid fa-handshake"></i><span>Borrowing</span>
            </a>
        </nav>
        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <a href="#" class="menu-item text-danger"
                    onclick="event.preventDefault(); this.closest('form').submit();">
                    <i class="fa-solid fa-right-from-bracket"></i><span>Logout</span>
                </a>
            </form>
        </div>
    </aside>

    <main class="main-content">

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

        {{-- NOTIFIKASI SUKSES --}}
        @if(session('success'))
        <div style="background: #f0fff4; border: 1px solid #c6f6d5; border-radius: 10px; padding: 12px 20px; margin-bottom: 15px; color: #38a169; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
        @endif

        <div class="card">
            <div class="filter-bar">
                <input type="text" placeholder="Cari nama / NIK..." class="search-input" id="searchInput" oninput="filterTabel()">
                <select class="search-input" style="max-width: 180px;" id="filterStatus" onchange="filterTabel()">
                    <option value="">Semua Status</option>
                    <option value="Incomplete">Incomplete</option>
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Rejected">Rejected</option>
                </select>
            </div>
        </div>

        <div class="card table-wrapper">
            <table id="tabelAnggota">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>NIK</th>
                        <th>No HP</th>
                        <th>Alamat</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members ?? [] as $index => $member)
                    <tr data-nama="{{ strtolower($member->nama_lengkap) }}"
                        data-status="{{ $member->status_verifikasi ?? 'Incomplete' }}">
                        <td>{{ $index + 1 }}</td>
                        <td><strong class="text-dark">{{ $member->nama_lengkap }}</strong></td>
                        <td class="text-muted">{{ $member->user->email ?? '-' }}</td>
                        <td class="text-muted">{{ $member->nik ?? '-' }}</td>
                        <td class="text-muted">{{ $member->no_hp ?? '-' }}</td>
                        <td class="text-muted">{{ Str::limit($member->alamat ?? '-', 25) }}</td>
                        <td>
                            @php $status = $member->status_verifikasi ?? 'Incomplete'; @endphp
                            @if($status === 'Approved')
                                <span class="status done">Approved</span>
                            @elseif($status === 'Rejected')
                                <span class="status late">Rejected</span>
                            @elseif($status === 'Pending')
                                <span class="status pending">Pending</span>
                            @else
                                <span class="status" style="background:#f7fafc; color:#718096; padding: 5px 12px; border-radius: 20px; font-size: 12px;">Incomplete</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="#" class="action-icon icon-detail" title="Detail"
                               onclick="bukaModalAnggota(
                                   '{{ addslashes($member->nama_lengkap) }}',
                                   'MB-{{ str_pad($member->id, 8, '0', STR_PAD_LEFT) }}',
                                   '{{ addslashes($member->user->email ?? '-') }}',
                                   '{{ $member->no_hp ?? '-' }}',
                                   '{{ addslashes($member->alamat ?? '-') }}',
                                   '{{ $member->status_verifikasi ?? 'Incomplete' }}',
                                   {{ $member->id }},
                                   '{{ $member->dokumen_identitas ? asset('storage/' . $member->dokumen_identitas) : '' }}'
                               )">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="#" class="action-icon" title="Hapus"
                               style="color: #e53e3e; margin-left: 8px;"
                               onclick="konfirmasiHapus({{ $member->id }}, '{{ addslashes($member->nama_lengkap) }}')">
                                <i class="fa-solid fa-trash"></i>
                            </a>
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

        <div class="pagination-container">
            <span class="pagination-info">Menampilkan {{ count($members ?? []) }} data</span>
            <div class="pagination-links">
                <span class="page-item active">1</span>
            </div>
        </div>

    </main>
</div>

{{-- Form Hapus Tersembunyi --}}
<form id="form-hapus" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

{{-- Form Verifikasi Tersembunyi --}}
<form id="form-verifikasi" method="POST" style="display: none;">
    @csrf
    @method('PATCH') <input type="hidden" name="status_verifikasi" id="input-status-verifikasi">
</form>

{{-- ===================== MODAL DETAIL ANGGOTA ===================== --}}
<div id="modal-anggota"
     onclick="tutupModalAnggota(event)"
     style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.35); z-index: 999; justify-content: center; align-items: center; padding: 20px;">

    <div style="background: white; border-radius: 16px; width: 100%; max-width: 600px; padding: 24px; box-shadow: 0 12px 30px rgba(0,0,0,0.15);">

        {{-- HEADER --}}
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-size: 18px; font-weight: 700; color: #1a202c;">Detail Anggota</h3>
            <button onclick="document.getElementById('modal-anggota').style.display='none'; document.body.style.overflow='';"
                    style="background: none; border: none; font-size: 22px; color: #a0aec0; cursor: pointer;">×</button>
        </div>

        {{-- BODY --}}
        <div style="display: flex; gap: 20px; align-items: flex-start;">

            {{-- KIRI --}}
            <div style="width: 130px; flex-shrink: 0; text-align: center;">
                <div style="width: 90px; height: 90px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px;">
                    <i class="fa-solid fa-user" style="font-size: 32px; color: #a0aec0;"></i>
                </div>
                <span id="modal-badge" style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; margin-bottom: 6px;"></span>
                <p id="modal-badge-label" style="font-size: 11px; color: #718096;"></p>
            </div>

            {{-- KANAN --}}
            <div style="flex: 1; min-width: 0;">
                <h4 id="modal-nama" style="font-size: 16px; font-weight: 700; color: #1a202c; margin-bottom: 2px;"></h4>
                <small id="modal-id" style="color: #718096; font-size: 12px;"></small>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 14px;">
                    <div>
                        <p style="font-size: 11px; color: #a0aec0; font-weight: 600; text-transform: uppercase; margin-bottom: 3px;">No HP</p>
                        <p id="modal-hp" style="font-size: 13px; color: #2d3748;"></p>
                    </div>
                    <div>
                        <p style="font-size: 11px; color: #a0aec0; font-weight: 600; text-transform: uppercase; margin-bottom: 3px;">Email</p>
                        <p id="modal-email" style="font-size: 13px; color: #2d3748; word-break: break-all;"></p>
                    </div>
                    <div>
                        <p style="font-size: 11px; color: #a0aec0; font-weight: 600; text-transform: uppercase; margin-bottom: 3px;">Status</p>
                        <p id="modal-status" style="font-size: 13px; color: #2d3748;"></p>
                    </div>
                </div>

                <div style="margin-top: 12px;">
                    <p style="font-size: 11px; color: #a0aec0; font-weight: 600; text-transform: uppercase; margin-bottom: 3px;">Alamat</p>
                    <p id="modal-alamat" style="font-size: 13px; color: #2d3748; line-height: 1.5;"></p>
                </div>

                <div style="margin-top: 12px;">
                    <p style="font-size: 11px; color: #a0aec0; font-weight: 600; text-transform: uppercase; margin-bottom: 8px;">Dokumen Identitas</p>
                    <div id="modal-dokumen-container" style="background: #f7fafc; border-radius: 10px; border: 1px dashed #e2e8f0; display: flex; align-items: center; justify-content: center; color: #a0aec0; font-size: 13px; overflow: hidden; width: 100%;">
                    </div>
                </div>
            </div>
        </div>

        {{-- FOOTER --}}
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding-top: 16px; border-top: 1px solid #edf2f7;">
            
            {{-- Tombol Aksi Verifikasi Kiri (Hanya muncul jika Status = Pending) --}}
            <div id="modal-action-verifikasi"></div>

            {{-- Tombol Utama Kanan --}}
            <div style="display: flex; gap: 10px;">
                <button id="modal-btn-hapus"
                        style="padding: 10px 24px; background: white; border: 1.5px solid #e53e3e; color: #e53e3e; border-radius: 10px; cursor: pointer; font-size: 14px;">
                    <i class="fa-solid fa-trash" style="font-size: 12px;"></i> Hapus Anggota
                </button>
                <button onclick="document.getElementById('modal-anggota').style.display='none'; document.body.style.overflow='';"
                        class="button" style="padding: 10px 24px; background: #cbd5e0; color: #2d3748; border: none; border-radius: 10px; cursor: pointer; font-size: 14px; font-weight: 600; min-width: auto; height: auto; line-height: normal;">
                    Tutup
                </button>
            </div>
        </div>

    </div>
</div>

<script>
function bukaModalAnggota(nama, id, email, hp, alamat, status, memberId, dokumenUrl) {
    document.getElementById('modal-nama').textContent  = nama;
    document.getElementById('modal-id').textContent    = 'ID: ' + id;
    document.getElementById('modal-email').textContent = email;
    document.getElementById('modal-hp').textContent    = hp;
    document.getElementById('modal-alamat').textContent = alamat;
    document.getElementById('modal-status').textContent = status;

    const badge = document.getElementById('modal-badge');
    const label = document.getElementById('modal-badge-label');
    const statusMap = {
        'Approved'  : { bg: '#f0fff4', color: '#38a169', text: 'Approved',   label: 'Akun Terverifikasi' },
        'Rejected'  : { bg: '#fff5f5', color: '#e53e3e', text: 'Rejected',   label: 'Akun Ditolak' },
        'Pending'   : { bg: '#fffbeb', color: '#d97706', text: 'Pending',    label: 'Menunggu Verifikasi' },
        'Incomplete': { bg: '#f7fafc', color: '#718096', text: 'Incomplete', label: 'Profil Belum Lengkap' },
    };
    const s = statusMap[status] || statusMap['Incomplete'];
    badge.textContent      = s.text;
    badge.style.background = s.bg;
    badge.style.color      = s.color;
    label.textContent      = s.label;

    // Set Preview Gambar Dokumen
    const docContainer = document.getElementById('modal-dokumen-container');
    if (dokumenUrl) {
        docContainer.style.height = '180px';
        docContainer.style.border = 'none';
        docContainer.innerHTML = `<a href="${dokumenUrl}" target="_blank" title="Klik untuk memperbesar gambar">
            <img src="${dokumenUrl}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
        </a>`;
    } else {
        docContainer.style.height = '80px';
        docContainer.style.border = '1px dashed #e2e8f0';
        docContainer.innerHTML = `<i class="fa-regular fa-image" style="font-size: 18px; margin-right: 5px;"></i> Belum tersedia`;
    }

    // Suntik Tombol Aksi Verifikasi jika status Pending
    const containerVerifikasi = document.getElementById('modal-action-verifikasi');
    if (status === 'Pending') {
        containerVerifikasi.innerHTML = `
            <button onclick="verifikasiAnggota(${memberId}, 'Approved')" style="padding: 10px 18px; background: #2bb673; color: white; border: none; border-radius: 10px; cursor: pointer; font-size: 14px; font-weight: 600; margin-right: 8px;">
                <i class="fa-solid fa-check"></i> Setujui
            </button>
            <button onclick="verifikasiAnggota(${memberId}, 'Rejected')" style="padding: 10px 18px; background: white; border: 1.5px solid #e53e3e; color: #e53e3e; border-radius: 10px; cursor: pointer; font-size: 14px; font-weight: 600;">
                <i class="fa-solid fa-xmark"></i> Tolak
            </button>
        `;
    } else {
        containerVerifikasi.innerHTML = '';
    }

    document.getElementById('modal-btn-hapus').onclick = function() {
        konfirmasiHapus(memberId, nama);
    };

    document.getElementById('modal-anggota').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function tutupModalAnggota(event) {
    if (event.target === document.getElementById('modal-anggota')) {
        document.getElementById('modal-anggota').style.display = 'none';
        document.body.style.overflow = '';
    }
}

function konfirmasiHapus(id, nama) {
    if (confirm('Yakin ingin menghapus anggota "' + nama + '"?\nTindakan ini tidak dapat dibatalkan!')) {
        const form = document.getElementById('form-hapus');
        form.action = '/members/' + id;
        form.submit();
    }
}

function verifikasiAnggota(id, status) {
    let kataKerja = status === 'Approved' ? 'menyetujui' : 'menolak';
    if (confirm(`Apakah Anda yakin ingin ${kataKerja} berkas pendaftaran anggota ini?`)) {
        const form = document.getElementById('form-verifikasi');
        form.action = '/members/' + id + '/verify'; // URL ini akan dicocokkan oleh Route::patch kita
        document.getElementById('input-status-verifikasi').value = status;
        form.submit();
    }
}

// Pencarian dan Filter Status Asli
function filterTabel() {
    const keyword = document.getElementById('searchInput').value.toLowerCase();
    const status  = document.getElementById('filterStatus').value.toLowerCase();
    const rows    = document.querySelectorAll('#tabelAnggota tbody tr');

    rows.forEach(row => {
        const nama       = row.getAttribute('data-nama') || '';
        const rowStatus  = row.getAttribute('data-status') || '';
        const cocoknama  = nama.includes(keyword);
        const cocokstatus = status === '' || rowStatus.toLowerCase() === status;
        row.style.display = cocoknama && cocokstatus ? '' : 'none';
    });
}
</script>

</body>
</html>