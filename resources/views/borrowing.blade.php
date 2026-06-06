<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman - SIPUS</title>
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

    <!-- MAIN CONTENT -->
    <main class="main-content">

        <!-- TOPBAR -->
        <header class="topbar">
            <div><p class="breadcrumb">Pages / <span>Borrowing</span></p></div>
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

        <!-- FILTER BAR -->
        <div class="card">
            <div class="filter-bar">
                <input type="text" id="searchInput" oninput="filterTabel()"
                    placeholder="Cari nama / kode OTP..." class="search-input">
                <select class="search-input" style="max-width:160px;" id="filterStatus" onchange="filterTabel()">
                    <option value="">Semua Status</option>
                    <option value="Menunggu OTP">Menunggu OTP</option>
                    <option value="Dipinjam">Dipinjam</option>
                    <option value="Selesai">Selesai</option>
                    <option value="Kadaluarsa">Kadaluarsa</option>
                </select>
            </div>
        </div>

        {{-- NOTIFIKASI --}}
        @if(session('success'))
        <div style="background:#f0fff4;border:1px solid #c6f6d5;border-radius:10px;padding:12px 20px;margin-bottom:15px;color:#38a169;display:flex;align-items:center;gap:10px;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
        @endif
        @if(session('error_otp'))
        <div style="background:#fff5f5;border:1px solid #fed7d7;border-radius:10px;padding:12px 20px;margin-bottom:15px;color:#e53e3e;display:flex;align-items:center;gap:10px;">
            <i class="fa-solid fa-circle-xmark"></i> {{ session('error_otp') }}
        </div>
        @endif

        <!-- TABEL BORROWING -->
        <div class="card table-wrapper">
            <table id="tabelBorrowing">
                <thead>
                    <tr>
                        <th>No</th><th>Nama Anggota</th><th>Kode OTP</th><th>Waktu Booking</th>
                        <th>Tgl Pinjam</th><th>Batas Kembali</th><th>Denda</th><th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($borrowings ?? [] as $index => $borrow)
                @php
                    $status     = $borrow->status_transaksi ?? 'Menunggu OTP';
                    $otpExpired = $borrow->otp_expired_at
                        ? \Carbon\Carbon::parse($borrow->otp_expired_at)->isPast() : false;
                    if ($status === 'Menunggu OTP' && $otpExpired) $status = 'Kadaluarsa';
                    $terlambat = false; $hariTerlambat = 0;
                    $dendaEstimasi = $borrow->total_denda ?? 0;
                    if ($status === 'Dipinjam' && $borrow->batas_pengembalian) {
                        $batasCarbon = \Carbon\Carbon::parse($borrow->batas_pengembalian);
                        $terlambat   = now()->gt($batasCarbon);
                        if ($terlambat) {
                            $hariTerlambat = (int) ceil($batasCarbon->floatDiffInDays(now()));
                            $dendaEstimasi = $hariTerlambat * 1000 * $borrow->detailPeminjaman->count();
                        }
                    }
                    $otpSisaDetik = 0;
                    if ($status === 'Menunggu OTP' && $borrow->otp_expired_at) {
                        $otpSisaDetik = max(0, (int) now()->diffInSeconds(\Carbon\Carbon::parse($borrow->otp_expired_at), false));
                    }
                    $firstDetail = $borrow->detailPeminjaman->first();
                    $judulBuku   = $firstDetail?->buku?->judul ?? '-';
                    $coverBuku   = $firstDetail?->buku?->cover ?? null;
                    $jumlahBuku  = $borrow->detailPeminjaman->count();
                @endphp
                <tr data-nama="{{ strtolower($borrow->anggota->nama_lengkap ?? '') }}" data-status="{{ $status }}">
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;border-radius:50%;background:#e2e8f0;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fa-solid fa-user" style="font-size:14px;color:#a0aec0;"></i>
                            </div>
                            <strong class="text-dark">{{ $borrow->anggota->nama_lengkap ?? '-' }}</strong>
                        </div>
                    </td>
                    <td>
                        @if($status === 'Menunggu OTP')
                            <span style="font-family:monospace;font-weight:700;color:#1fcf8e;font-size:14px;letter-spacing:1px;">{{ $borrow->kode_otp }}</span>
                        @else
                            <span style="color:#a0aec0;letter-spacing:2px;">••••••</span>
                        @endif
                    </td>
                    <td class="text-muted">{{ $borrow->waktu_booking ? \Carbon\Carbon::parse($borrow->waktu_booking)->format('d M, H:i') : '-' }}</td>
                    <td class="text-muted">{{ $borrow->tanggal_pinjam ? \Carbon\Carbon::parse($borrow->tanggal_pinjam)->format('d M Y') : '—' }}</td>
                    <td>
                        @if($borrow->batas_pengembalian)
                            <span style="color:{{ $terlambat ? '#e53e3e' : '#2d3748' }};font-weight:{{ $terlambat ? '700' : '400' }};">
                                {{ \Carbon\Carbon::parse($borrow->batas_pengembalian)->format('d M Y') }}
                                @if($terlambat)<br><small style="color:#e53e3e;font-size:11px;">{{ $hariTerlambat }} hari terlambat</small>@endif
                            </span>
                        @else <span style="color:#a0aec0;">—</span> @endif
                    </td>
                    <td>
                        @if($dendaEstimasi > 0)
                            <span style="color:#e53e3e;font-weight:700;">Rp {{ number_format($dendaEstimasi, 0, ',', '.') }}</span>
                        @else <span style="color:#a0aec0;">Rp 0</span> @endif
                    </td>
                    <td>
                        @if($status === 'Menunggu OTP') <span class="status pending" style="background:#fff3e0;color:#e65100;white-space:nowrap;">Menunggu OTP</span>
                        @elseif($status === 'Dipinjam' && $terlambat) <span class="status late" style="white-space:nowrap;">Terlambat</span>
                        @elseif($status === 'Dipinjam') <span class="status pending">Dipinjam</span>
                        @elseif($status === 'Selesai') <span class="status done">Selesai</span>
                        @elseif($status === 'Kadaluarsa') <span class="status" style="background:#f7fafc;color:#718096;padding:5px 12px;border-radius:20px;font-size:12px;">Kadaluarsa</span>
                        @else <span class="status late">{{ $status }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="#" class="action-icon icon-detail" title="Detail"
                            onclick="bukaModalPeminjaman(
                                '{{ $borrow->kode_otp }}',
                                '{{ addslashes($borrow->anggota->nama_lengkap ?? '-') }}',
                                'MB-{{ str_pad($borrow->anggota->id ?? 0, 8, '0', STR_PAD_LEFT) }}',
                                '{{ Auth::user()->email }}',
                                '{{ $borrow->waktu_booking ? \Carbon\Carbon::parse($borrow->waktu_booking)->format('d M Y, H:i') : '-' }}',
                                {{ $dendaEstimasi }},
                                '{{ $status }}',
                                {{ $borrow->id }},
                                {{ $otpSisaDetik }},
                                '{{ $borrow->tanggal_pinjam ? \Carbon\Carbon::parse($borrow->tanggal_pinjam)->format('d M Y') : '-' }}',
                                '{{ $borrow->batas_pengembalian ? \Carbon\Carbon::parse($borrow->batas_pengembalian)->format('d M Y') : '-' }}',
                                {{ $hariTerlambat }},
                                {{ $jumlahBuku }},
                                '{{ addslashes($judulBuku) }}',
                                '{{ $coverBuku ? asset('storage/' . $coverBuku) : '' }}'
                            )">
                            <i class="fa-solid fa-eye"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="empty-state"><i class="fa-solid fa-hand-holding-hand"></i><br>Belum ada data transaksi peminjaman.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-container">
            <span class="pagination-info">Menampilkan {{ count($borrowings ?? []) }} transaksi</span>
            <div class="pagination-links"><span class="page-item active">1</span></div>
        </div>

    </main>
</div>

{{-- MODAL DETAIL PEMINJAMAN --}}
<div id="modal-peminjaman" onclick="tutupModalPeminjaman(event)"
        style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.35);z-index:999;justify-content:center;align-items:center;padding:20px;overflow-y:auto;">
    <div style="background:white;border-radius:16px;width:100%;max-width:600px;padding:24px;box-shadow:0 12px 30px rgba(0,0,0,0.15);margin:auto;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px;">
            <div>
                <p id="modal-status-label" style="font-size:11px;font-weight:700;color:#1fcf8e;letter-spacing:0.08em;margin-bottom:5px;"></p>
                <h3 id="modal-trx-title" style="font-size:18px;font-weight:700;color:#1a202c;"></h3>
            </div>
            <button onclick="tutupModal()" style="background:none;border:none;font-size:22px;color:#a0aec0;cursor:pointer;">×</button>
        </div>
        <div style="background:#f8fafc;border-radius:12px;padding:16px;margin-bottom:20px;display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div><p style="font-size:11px;color:#a0aec0;font-weight:600;letter-spacing:0.05em;margin-bottom:4px;">ANGGOTA</p><p id="modal-anggota" style="font-size:14px;font-weight:700;color:#1a202c;"></p><span id="modal-anggota-id" style="background:#edf2f7;color:#718096;font-size:11px;padding:2px 8px;border-radius:4px;"></span></div>
            <div><p style="font-size:11px;color:#a0aec0;font-weight:600;letter-spacing:0.05em;margin-bottom:4px;">PETUGAS</p><p id="modal-petugas" style="font-size:14px;font-weight:700;color:#1a202c;"></p></div>
            <div><p style="font-size:11px;color:#a0aec0;font-weight:600;letter-spacing:0.05em;margin-bottom:4px;">TGL PINJAM</p><p id="modal-tgl-pinjam" style="font-size:14px;color:#2d3748;"></p></div>
            <div><p style="font-size:11px;color:#a0aec0;font-weight:600;letter-spacing:0.05em;margin-bottom:4px;">BATAS KEMBALI</p><p id="modal-batas" style="font-size:14px;font-weight:700;"></p></div>
        </div>
        <div id="info-buku-section" style="background:#f0fff4;border-radius:12px;padding:14px;margin-bottom:20px;display:flex;align-items:center;gap:12px;">
            <div id="modal-cover-borrowing" style="width:44px;height:56px;background:linear-gradient(135deg,#1f3c45,#2d6a5a);border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;overflow:hidden;">
                <i class="fa-solid fa-book" style="color:rgba(255,255,255,0.7);font-size:16px;"></i>
            </div>
            <div>
                <p id="modal-judul-buku" style="font-size:14px;font-weight:700;color:#1a202c;margin-bottom:3px;"></p>
                <p id="modal-jumlah-buku" style="font-size:12px;color:#718096;"></p>
            </div>
        </div>
        <div id="denda-section" style="display:none;background:#fff5f5;border:1px solid #fed7d7;border-radius:12px;padding:16px;margin-bottom:20px;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
                <i class="fa-solid fa-triangle-exclamation" style="color:#e53e3e;font-size:16px;"></i>
                <p style="font-size:13px;font-weight:700;color:#e53e3e;">Buku Terlambat Dikembalikan!</p>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div><p style="font-size:11px;color:#a0aec0;margin-bottom:3px;">HARI TERLAMBAT</p><p id="modal-hari-terlambat" style="font-size:18px;font-weight:800;color:#e53e3e;"></p></div>
                <div><p style="font-size:11px;color:#a0aec0;margin-bottom:3px;">TOTAL DENDA</p><p id="modal-total-denda" style="font-size:18px;font-weight:800;color:#e53e3e;"></p></div>
            </div>
            <p style="font-size:11px;color:#718096;margin-top:8px;">Perhitungan: Rp 1.000 × <span id="modal-hari-x"></span> hari × <span id="modal-buku-x"></span> buku</p>
        </div>
        <div id="otp-section" style="display:none;">
            <div style="background:#f0fff4;border:2px dashed #1fcf8e;border-radius:12px;padding:20px;text-align:center;margin-bottom:20px;">
                <p style="font-size:11px;font-weight:700;color:#1fcf8e;letter-spacing:0.1em;margin-bottom:14px;">INPUT KODE OTP ANGGOTA</p>
                <input type="text" id="input-otp-langsung" maxlength="6" placeholder="Ketik 6 karakter OTP"
                    style="width:220px;height:56px;text-align:center;border-radius:10px;border:2px solid #c6f6d5;font-size:26px;font-weight:800;color:#1a202c;outline:none;background:white;letter-spacing:8px;text-transform:uppercase;font-family:monospace;"
                    oninput="this.value=this.value.toUpperCase().replace(/[^A-Z0-9]/g,'')">
                <p style="font-size:12px;color:#718096;display:flex;align-items:center;justify-content:center;gap:5px;margin-top:12px;">
                    <i class="fa-regular fa-clock"></i> OTP kadaluarsa dalam <strong style="color:#e53e3e;" id="otp-timer">--:--</strong>
                </p>
            </div>
            <div style="background:#f8fafc;border-radius:12px;padding:16px;margin-bottom:20px;">
                <p style="font-size:13px;font-weight:700;color:#2d3748;margin-bottom:12px;">KONDISI FISIK BUKU</p>
                <div style="display:flex;gap:20px;flex-wrap:wrap;">
                    <label style="display:flex;align-items:center;gap:8px;font-size:14px;cursor:pointer;"><input type="radio" name="kondisi_fisik" value="Sangat Baik" checked style="accent-color:#1fcf8e;"> Sangat Baik</label>
                    <label style="display:flex;align-items:center;gap:8px;font-size:14px;cursor:pointer;"><input type="radio" name="kondisi_fisik" value="Rusak Ringan" style="accent-color:#1fcf8e;"> Rusak Ringan</label>
                    <label style="display:flex;align-items:center;gap:8px;font-size:14px;cursor:pointer;"><input type="radio" name="kondisi_fisik" value="Hilang" style="accent-color:#1fcf8e;"> Hilang</label>
                </div>
            </div>
            <form id="form-validasi-otp" method="POST">
                @csrf
                <input type="hidden" id="input-otp-hidden" name="kode_otp">
                <button type="button" onclick="konfirmasiOTP()"
                        style="width:100%;padding:15px;background:#1fcf8e;color:white;border:none;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer;margin-bottom:8px;">
                    <i class="fa-solid fa-check"></i> Konfirmasi Peminjaman
                </button>
            </form>
            <p style="text-align:center;font-size:11px;color:#a0aec0;">*Sistem akan mencatat tanggal pinjam hari ini</p>
        </div>
        <div id="kembalikan-section" style="display:none;">
            <form id="form-kembalikan" method="POST">
                @csrf
                <button type="button" onclick="konfirmasiKembalikan()"
                        style="width:100%;padding:15px;background:#3182ce;color:white;border:none;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer;margin-bottom:8px;display:flex;align-items:center;justify-content:center;gap:10px;">
                    <i class="fa-solid fa-rotate-left"></i> Proses Pengembalian Buku
                </button>
            </form>
            <p style="text-align:center;font-size:11px;color:#a0aec0;">*Stok buku akan bertambah setelah pengembalian dikonfirmasi</p>
        </div>
        <div id="btn-tutup-saja" style="display:none;text-align:right;">
            <button onclick="tutupModal()" style="padding:11px 30px;background:#f7fafc;border:1px solid #e2e8f0;border-radius:10px;cursor:pointer;font-size:14px;color:#4a5568;">Tutup</button>
        </div>
    </div>
</div>

<script>
function tutupModal() {
    document.getElementById('modal-peminjaman').style.display = 'none';
    document.body.style.overflow = '';
    if (window.otpInterval) clearInterval(window.otpInterval);
}
function tutupModalPeminjaman(event) {
    if (event.target === document.getElementById('modal-peminjaman')) tutupModal();
}
function bukaModalPeminjaman(kodeOtp, anggota, anggotaId, petugas, tanggal,
                              denda, status, borrowId, otpSisaDetik,
                              tglPinjam, batasKembali, hariTerlambat, jumlahBuku, judulBuku, coverUrl) {
    document.getElementById('modal-trx-title').textContent   = 'Detail Peminjaman #' + kodeOtp;
    document.getElementById('modal-status-label').textContent = 'STATUS: ' + status.toUpperCase();
    document.getElementById('modal-anggota').textContent      = anggota;
    document.getElementById('modal-anggota-id').textContent   = anggotaId;
    document.getElementById('modal-petugas').textContent      = petugas;
    document.getElementById('modal-tgl-pinjam').textContent   = tglPinjam !== '-' ? tglPinjam : 'Belum dipinjam';
    document.getElementById('modal-judul-buku').textContent   = judulBuku;
    document.getElementById('modal-jumlah-buku').textContent  = jumlahBuku + ' judul buku';
    const batasEl = document.getElementById('modal-batas');
    batasEl.textContent = batasKembali !== '-' ? batasKembali : '-';
    batasEl.style.color = hariTerlambat > 0 ? '#e53e3e' : '#2d3748';
    const coverEl = document.getElementById('modal-cover-borrowing');
    if (coverUrl && coverUrl !== '') {
        coverEl.innerHTML = '<img src="' + coverUrl + '" style="width:100%;height:100%;object-fit:cover;">';
        coverEl.style.background = 'none';
    } else {
        coverEl.style.background = 'linear-gradient(135deg,#1f3c45,#2d6a5a)';
        coverEl.innerHTML = '<i class="fa-solid fa-book" style="color:rgba(255,255,255,0.7);font-size:16px;"></i>';
    }
    document.getElementById('form-validasi-otp').action = '/borrowing/' + borrowId + '/validasi';
    document.getElementById('form-kembalikan').action   = '/borrowing/' + borrowId + '/kembalikan';
    document.getElementById('otp-section').style.display        = 'none';
    document.getElementById('kembalikan-section').style.display = 'none';
    document.getElementById('btn-tutup-saja').style.display     = 'none';
    document.getElementById('denda-section').style.display      = 'none';
    if (window.otpInterval) clearInterval(window.otpInterval);
    if (status === 'Menunggu OTP') {
        document.getElementById('otp-section').style.display = 'block';
        document.getElementById('input-otp-langsung').value  = '';
        mulaiTimerOTP(otpSisaDetik);
        setTimeout(() => document.getElementById('input-otp-langsung').focus(), 300);
    } else if (status === 'Dipinjam' || status === 'Terlambat') {
        document.getElementById('kembalikan-section').style.display = 'block';
        if (hariTerlambat > 0) {
            document.getElementById('denda-section').style.display     = 'block';
            document.getElementById('modal-hari-terlambat').textContent = hariTerlambat + ' hari';
            document.getElementById('modal-total-denda').textContent    = 'Rp ' + Number(denda).toLocaleString('id-ID');
            document.getElementById('modal-hari-x').textContent         = hariTerlambat;
            document.getElementById('modal-buku-x').textContent         = jumlahBuku;
        }
    } else {
        document.getElementById('btn-tutup-saja').style.display = 'block';
        if (denda > 0) {
            document.getElementById('denda-section').style.display     = 'block';
            document.getElementById('modal-hari-terlambat').textContent = '-';
            document.getElementById('modal-total-denda').textContent    = 'Rp ' + Number(denda).toLocaleString('id-ID');
            document.getElementById('modal-hari-x').textContent = '-';
            document.getElementById('modal-buku-x').textContent = jumlahBuku;
        }
    }
    document.getElementById('modal-peminjaman').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function mulaiTimerOTP(detik) {
    if (window.otpInterval) clearInterval(window.otpInterval);
    const timerEl = document.getElementById('otp-timer');
    function tick() {
        if (detik <= 0) { timerEl.textContent = 'KADALUARSA'; timerEl.style.color = '#a0aec0'; clearInterval(window.otpInterval); return; }
        const h = Math.floor(detik / 3600), m = Math.floor((detik % 3600) / 60), s = Math.floor(detik % 60);
        timerEl.textContent = h > 0
            ? String(h).padStart(2,'0')+':'+String(m).padStart(2,'0')+':'+String(s).padStart(2,'0')
            : String(m).padStart(2,'0')+':'+String(s).padStart(2,'0');
        detik--;
    }
    tick();
    window.otpInterval = setInterval(tick, 1000);
}
function konfirmasiOTP() {
    const otp = document.getElementById('input-otp-langsung').value.trim().toUpperCase();
    if (otp.length < 6) { alert('Masukkan kode OTP lengkap (6 karakter)!'); return; }
    document.getElementById('input-otp-hidden').value = otp;
    document.getElementById('form-validasi-otp').submit();
}
function konfirmasiKembalikan() {
    if (confirm('Konfirmasi pengembalian buku ini?\n\nPastikan buku sudah diterima secara fisik.')) {
        document.getElementById('form-kembalikan').submit();
    }
}
function filterTabel() {
    const keyword = document.getElementById('searchInput').value.toLowerCase();
    const status  = document.getElementById('filterStatus').value;
    document.querySelectorAll('#tabelBorrowing tbody tr').forEach(row => {
        const nama      = row.getAttribute('data-nama') || '';
        const rowStatus = row.getAttribute('data-status') || '';
        row.style.display = nama.includes(keyword) && (status === '' || rowStatus === status) ? '' : 'none';
    });
}
</script>
</body>
</html>