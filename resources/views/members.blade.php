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

    <main class="main-content">

        <!-- TOPBAR -->
        <header class="topbar">
            <div><p class="breadcrumb">Pages / <span>Members</span></p></div>
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

        @if(session('success'))
        <div style="background:#f0fff4;border:1px solid #c6f6d5;border-radius:10px;padding:12px 20px;margin-bottom:15px;color:#38a169;display:flex;align-items:center;gap:10px;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
        @endif

        <div class="card">
            <div class="filter-bar">
                <input type="text" placeholder="Cari nama / NIK..." class="search-input" id="searchInput" oninput="filterTabel()">
                <select class="search-input" style="max-width:180px;" id="filterStatus" onchange="filterTabel()">
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
                        <th>No</th><th>Foto</th><th>Nama Lengkap</th><th>Email</th>
                        <th>NIK</th><th>No HP</th><th>Alamat</th><th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members ?? [] as $index => $member)
                    <tr data-nama="{{ strtolower($member->nama_lengkap) }}"
                        data-status="{{ $member->status_verifikasi ?? 'Incomplete' }}">
                        <td>{{ $index + 1 }}</td>
                        <td>
                            @if($member->foto_profil)
                                <img src="{{ asset('storage/' . $member->foto_profil) }}" style="width:38px;height:38px;border-radius:50%;object-fit:cover;">
                            @else
                                <div style="width:38px;height:38px;border-radius:50%;background:#e2e8f0;display:flex;align-items:center;justify-content:center;">
                                    <i class="fa-solid fa-user" style="color:#a0aec0;font-size:14px;"></i>
                                </div>
                            @endif
                        </td>
                        <td><strong class="text-dark">{{ $member->nama_lengkap }}</strong></td>
                        <td class="text-muted">{{ $member->user->email ?? '-' }}</td>
                        <td class="text-muted">{{ $member->nik ?? '-' }}</td>
                        <td class="text-muted">{{ $member->no_hp ?? '-' }}</td>
                        <td class="text-muted">{{ Str::limit($member->alamat ?? '-', 25) }}</td>
                        <td>
                            @php $status = $member->status_verifikasi ?? 'Incomplete'; @endphp
                            @if($status === 'Approved') <span class="status done">Approved</span>
                            @elseif($status === 'Rejected') <span class="status late">Rejected</span>
                            @elseif($status === 'Pending') <span class="status pending">Pending</span>
                            @else <span class="status" style="background:#f7fafc;color:#718096;padding:5px 12px;border-radius:20px;font-size:12px;">Incomplete</span>
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
                                '{{ $member->dokumen_identitas ? asset('storage/' . $member->dokumen_identitas) : '' }}',
                                '{{ $member->foto_profil ? asset('storage/' . $member->foto_profil) : '' }}'
                            )"><i class="fa-solid fa-eye"></i></a>
                            <a href="#" class="action-icon" title="Hapus" style="color:#e53e3e;margin-left:8px;"
                                onclick="konfirmasiHapus({{ $member->id }}, '{{ addslashes($member->nama_lengkap) }}')">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="empty-state"><i class="fa-solid fa-user-xmark"></i><br>Belum ada data anggota yang terdaftar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-container">
            <span class="pagination-info">Menampilkan {{ count($members ?? []) }} data</span>
            <div class="pagination-links"><span class="page-item active">1</span></div>
        </div>

    </main>
</div>

<form id="form-hapus" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>
<form id="form-verifikasi" method="POST" style="display:none;">
    @csrf
    @method('PATCH')
    <input type="hidden" name="status_verifikasi" id="input-status-verifikasi">
</form>

{{-- MODAL DETAIL ANGGOTA --}}
<div id="modal-anggota" onclick="tutupModalAnggota(event)"
    style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.35);z-index:999;justify-content:center;align-items:center;padding:20px;">
    <div style="background:white;border-radius:16px;width:100%;max-width:600px;padding:24px;box-shadow:0 12px 30px rgba(0,0,0,0.15);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <h3 style="font-size:18px;font-weight:700;color:#1a202c;">Detail Anggota</h3>
            <button onclick="document.getElementById('modal-anggota').style.display='none';document.body.style.overflow='';"
                    style="background:none;border:none;font-size:22px;color:#a0aec0;cursor:pointer;">×</button>
        </div>
        <div style="display:flex;gap:20px;align-items:flex-start;">
            <div style="width:130px;flex-shrink:0;text-align:center;">
                <div id="modal-foto-profil" style="width:90px;height:90px;border-radius:50%;background:#e2e8f0;display:flex;align-items:center;justify-content:center;margin:0 auto 10px;overflow:hidden;">
                    <i class="fa-solid fa-user" style="font-size:32px;color:#a0aec0;"></i>
                </div>
                <span id="modal-badge" style="display:inline-block;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;margin-bottom:6px;"></span>
                <p id="modal-badge-label" style="font-size:11px;color:#718096;"></p>
            </div>
            <div style="flex:1;min-width:0;">
                <h4 id="modal-nama" style="font-size:16px;font-weight:700;color:#1a202c;margin-bottom:2px;"></h4>
                <small id="modal-id" style="color:#718096;font-size:12px;"></small>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:14px;">
                    <div><p style="font-size:11px;color:#a0aec0;font-weight:600;text-transform:uppercase;margin-bottom:3px;">No HP</p><p id="modal-hp" style="font-size:13px;color:#2d3748;"></p></div>
                    <div><p style="font-size:11px;color:#a0aec0;font-weight:600;text-transform:uppercase;margin-bottom:3px;">Email</p><p id="modal-email" style="font-size:13px;color:#2d3748;word-break:break-all;"></p></div>
                    <div><p style="font-size:11px;color:#a0aec0;font-weight:600;text-transform:uppercase;margin-bottom:3px;">Status</p><p id="modal-status" style="font-size:13px;color:#2d3748;"></p></div>
                </div>
                <div style="margin-top:12px;"><p style="font-size:11px;color:#a0aec0;font-weight:600;text-transform:uppercase;margin-bottom:3px;">Alamat</p><p id="modal-alamat" style="font-size:13px;color:#2d3748;line-height:1.5;"></p></div>
                <div style="margin-top:12px;">
                    <p style="font-size:11px;color:#a0aec0;font-weight:600;text-transform:uppercase;margin-bottom:8px;">Dokumen Identitas</p>
                    <div id="modal-dokumen-container" style="background:#f7fafc;border-radius:10px;border:1px dashed #e2e8f0;display:flex;align-items:center;justify-content:center;color:#a0aec0;font-size:13px;overflow:hidden;width:100%;"></div>
                </div>
            </div>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:20px;padding-top:16px;border-top:1px solid #edf2f7;">
            <div id="modal-action-verifikasi"></div>
            <div style="display:flex;gap:10px;">
                <button id="modal-btn-hapus" style="padding:10px 24px;background:white;border:1.5px solid #e53e3e;color:#e53e3e;border-radius:10px;cursor:pointer;font-size:14px;">
                    <i class="fa-solid fa-trash" style="font-size:12px;"></i> Hapus Anggota
                </button>
                <button onclick="document.getElementById('modal-anggota').style.display='none';document.body.style.overflow='';"
                        style="padding:10px 24px;background:#cbd5e0;color:#2d3748;border:none;border-radius:10px;cursor:pointer;font-size:14px;font-weight:600;">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function bukaModalAnggota(nama, id, email, hp, alamat, status, memberId, dokumenUrl, fotoUrl) {
    document.getElementById('modal-nama').textContent   = nama;
    document.getElementById('modal-id').textContent     = 'ID: ' + id;
    document.getElementById('modal-email').textContent  = email;
    document.getElementById('modal-hp').textContent     = hp;
    document.getElementById('modal-alamat').textContent = alamat;
    document.getElementById('modal-status').textContent = status;
    const badge = document.getElementById('modal-badge');
    const label = document.getElementById('modal-badge-label');
    const statusMap = {
        'Approved'  : {bg:'#f0fff4',color:'#38a169',text:'Approved',  label:'Akun Terverifikasi'},
        'Rejected'  : {bg:'#fff5f5',color:'#e53e3e',text:'Rejected',  label:'Akun Ditolak'},
        'Pending'   : {bg:'#fffbeb',color:'#d97706',text:'Pending',   label:'Menunggu Verifikasi'},
        'Incomplete': {bg:'#f7fafc',color:'#718096',text:'Incomplete',label:'Profil Belum Lengkap'},
    };
    const s = statusMap[status] || statusMap['Incomplete'];
    badge.textContent = s.text; badge.style.background = s.bg; badge.style.color = s.color;
    label.textContent = s.label;
    const fotoEl = document.getElementById('modal-foto-profil');
    if (fotoUrl && fotoUrl !== '') {
        fotoEl.innerHTML = '<img src="' + fotoUrl + '" style="width:100%;height:100%;object-fit:cover;">';
    } else {
        fotoEl.innerHTML = '<i class="fa-solid fa-user" style="font-size:32px;color:#a0aec0;"></i>';
        fotoEl.style.background = '#e2e8f0';
    }
    const docContainer = document.getElementById('modal-dokumen-container');
    if (dokumenUrl) {
        docContainer.style.height = '180px'; docContainer.style.border = 'none';
        docContainer.innerHTML = `<a href="${dokumenUrl}" target="_blank"><img src="${dokumenUrl}" style="width:100%;height:100%;object-fit:cover;border-radius:10px;"></a>`;
    } else {
        docContainer.style.height = '80px'; docContainer.style.border = '1px dashed #e2e8f0';
        docContainer.innerHTML = `<i class="fa-regular fa-image" style="font-size:18px;margin-right:5px;"></i> Belum tersedia`;
    }
    const containerVerifikasi = document.getElementById('modal-action-verifikasi');
    if (status === 'Pending') {
        containerVerifikasi.innerHTML = `
            <button onclick="verifikasiAnggota(${memberId}, 'Approved')" style="padding:10px 18px;background:#2bb673;color:white;border:none;border-radius:10px;cursor:pointer;font-size:14px;font-weight:600;margin-right:8px;"><i class="fa-solid fa-check"></i> Setujui</button>
            <button onclick="verifikasiAnggota(${memberId}, 'Rejected')" style="padding:10px 18px;background:white;border:1.5px solid #e53e3e;color:#e53e3e;border-radius:10px;cursor:pointer;font-size:14px;font-weight:600;"><i class="fa-solid fa-xmark"></i> Tolak</button>`;
    } else { containerVerifikasi.innerHTML = ''; }
    document.getElementById('modal-btn-hapus').onclick = function() { konfirmasiHapus(memberId, nama); };
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
    if (confirm(`Apakah Anda yakin ingin ${status === 'Approved' ? 'menyetujui' : 'menolak'} berkas ini?`)) {
        const form = document.getElementById('form-verifikasi');
        form.action = '/members/' + id + '/verify';
        document.getElementById('input-status-verifikasi').value = status;
        form.submit();
    }
}
function filterTabel() {
    const keyword = document.getElementById('searchInput').value.toLowerCase();
    const status  = document.getElementById('filterStatus').value.toLowerCase();
    document.querySelectorAll('#tabelAnggota tbody tr').forEach(row => {
        const nama      = row.getAttribute('data-nama') || '';
        const rowStatus = row.getAttribute('data-status') || '';
        row.style.display = nama.includes(keyword) && (status === '' || rowStatus.toLowerCase() === status) ? '' : 'none';
    });
}
</script>
</body>
</html>