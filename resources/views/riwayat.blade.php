@extends('layouts.member_layout')

@section('title', 'Riwayat Peminjaman')

@section('content')

<style>
    .scroll-buku::-webkit-scrollbar { width: 5px; }
    .scroll-buku::-webkit-scrollbar-track { background: transparent; }
    .scroll-buku::-webkit-scrollbar-thumb { background: #c8e9dc; border-radius: 10px; }
</style>

<div style="margin-bottom: 20px;">
    <h2 style="font-size: 20px; color: #2d3748; margin-bottom: 5px;">Riwayat Peminjaman</h2>
    <p style="font-size: 13px; color: #718096;">Daftar lengkap transaksi peminjaman buku yang telah Anda lakukan.</p>
</div>

{{-- FILTER TABS --}}
<div style="background: white; border-radius: 12px; padding: 12px 20px; margin-bottom: 20px; display: flex; gap: 5px; border: 1px solid #edf2f7; overflow-x: auto; flex-wrap: wrap;">
    @foreach(['Semua','Dipinjam','Selesai','Terlambat','Kadaluarsa'] as $tab)
    <button onclick="filterRiwayat('{{ $tab }}')"
            id="tab-{{ $tab }}"
            style="font-size: 13px; cursor: pointer; padding: 6px 16px; border-radius: 20px; border: 1.5px solid {{ $tab === 'Semua' ? '#1fcf8e' : '#edf2f7' }}; background: {{ $tab === 'Semua' ? '#f0fff4' : 'white' }}; color: {{ $tab === 'Semua' ? '#1fcf8e' : '#718096' }}; font-weight: {{ $tab === 'Semua' ? '600' : '400' }}; white-space: nowrap; transition: all 0.2s;">
        {{ $tab }}
    </button>
    @endforeach
</div>

{{-- LIST RIWAYAT --}}
<div id="list-riwayat">
    @forelse($riwayat ?? [] as $trx)
    @php
        $status = $trx->status_transaksi;
        $otpExpired = $trx->otp_expired_at
            ? \Carbon\Carbon::parse($trx->otp_expired_at)->isPast()
            : false;
        if ($status === 'Menunggu OTP' && $otpExpired) $status = 'Kadaluarsa';

        $terlambat = false;
        if ($status === 'Dipinjam' && $trx->batas_pengembalian) {
            $terlambat = \Carbon\Carbon::parse($trx->batas_pengembalian)->isPast();
            if ($terlambat) $status = 'Terlambat';
        }

        $badgeStyle = match($status) {
            'Selesai'      => 'background:#f0fff4; color:#38a169;',
            'Dipinjam'     => 'background:#ebf8ff; color:#3182ce;',
            'Terlambat'    => 'background:#fff5f5; color:#e53e3e;',
            'Menunggu OTP' => 'background:#fff3e0; color:#e65100;',
            'Kadaluarsa'   => 'background:#f7fafc; color:#718096;',
            default        => 'background:#f7fafc; color:#718096;',
        };

        $firstDetail = $trx->detailPeminjaman->first();
        $judulBuku   = $firstDetail?->buku?->judul ?? 'Buku tidak diketahui';
        $penulisBuku = $firstDetail?->buku?->penulis ?? '-';
        $coverBuku   = $firstDetail?->buku?->cover ?? null; 
        $jumlahBuku  = $trx->detailPeminjaman->count();

        $tanggalPinjam  = $trx->tanggal_pinjam
            ? \Carbon\Carbon::parse($trx->tanggal_pinjam)->translatedFormat('d M Y')
            : '-';
        $tanggalKembali = $trx->batas_pengembalian
            ? \Carbon\Carbon::parse($trx->batas_pengembalian)->translatedFormat('d M Y')
            : '-';

    $tanggalKembali = $trx->batas_pengembalian
            ? \Carbon\Carbon::parse($trx->batas_pengembalian)->translatedFormat('d M Y')
            : '-';


    // --- TAMBAHAN BARU: Siapkan Data Buku Format JSON ---
            $bookDetails = [];
            foreach($trx->detailPeminjaman as $detail) {
                $bookDetails[] = [
                    'judul'    => $detail->buku?->judul ?? 'Buku tidak diketahui',
                    'penulis'  => $detail->buku?->penulis ?? '-',
                    'kategori' => $detail->buku?->kategori?->nama_kategori ?? 'Umum',
                    'cover'    => $detail->buku?->cover ? asset('storage/' . $detail->buku->cover) : '',
                    'libId'    => 'LIB-' . str_pad($detail->buku?->id ?? 0, 4, '0', STR_PAD_LEFT)
                ];
            }
            // Ganti baris ini agar kebal dari error tanda kutip!
            $booksBase64 = base64_encode(json_encode($bookDetails));
            // ----------------------------------------------------
        @endphp

    <div class="card-riwayat" data-status="{{ $status }}"
        style="background: white; border-radius: 15px; padding: 18px 20px; margin-bottom: 15px; border: 1px solid #edf2f7;">

        {{-- INFO BUKU (CARD) --}}
        <div style="display: flex; align-items: center; gap: 15px;">

            {{-- Cover buku --}}
            @if($coverBuku)
                <div style="width: 60px; height: 80px; border-radius: 8px; overflow: hidden; flex-shrink: 0;">
                    <img src="{{ asset('storage/' . $coverBuku) }}"
                        alt="{{ $judulBuku }}"
                        style="width: 100%; height: 100%; object-fit: cover;">
                </div>
            @else
                <div style="width: 60px; height: 80px; background: linear-gradient(135deg, #1f3c45, #2d6a5a); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fa-solid fa-book" style="color: rgba(255,255,255,0.6); font-size: 20px;"></i>
                </div>
            @endif

            <div style="flex: 1; min-width: 0;">
                <h4 style="margin: 0 0 3px; font-size: 15px; color: #2d3748; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    {{ $judulBuku }}
                    @if($jumlahBuku > 1)
                        <span style="font-size: 12px; color: #718096; font-weight: 400;">+ {{ $jumlahBuku - 1 }} lainnya</span>
                    @endif
                </h4>
                <p style="margin: 0 0 3px; font-size: 13px; color: #718096;">{{ $penulisBuku }}</p>
                <small style="color: #a0aec0; font-size: 12px;">{{ $tanggalPinjam }} – {{ $tanggalKembali }}</small>
            </div>

            {{-- BADGE STATUS --}}
            <span style="font-size: 11px; font-weight: 700; padding: 5px 14px; border-radius: 20px; white-space: nowrap; {{ $badgeStyle }}">
                {{ strtoupper($status) }}
            </span>

{{-- TOMBOL DETAIL --}}
                        <button onclick="bukaModalRiwayat(
                        '{{ $booksBase64 }}', 
                        'TRX-{{ str_pad($trx->id, 4, '0', STR_PAD_LEFT) }}',
                        '{{ addslashes($trx->anggota?->nama_lengkap ?? Auth::user()->anggota?->nama_lengkap ?? '-') }}',
                        '{{ $trx->waktu_booking ? \Carbon\Carbon::parse($trx->waktu_booking)->format('d M Y, H:i') : '-' }}',
                        '{{ $tanggalKembali }}',
                        '{{ $trx->tanggal_dikembalikan ? \Carbon\Carbon::parse($trx->tanggal_dikembalikan)->format('d M Y, H:i') : '-' }}',
                        '{{ $status === 'Selesai' ? 'Baik' : '-' }}',
                        '{{ $trx->total_denda ?? 0 }}',
                        '{{ $status }}',
                        '{{ $trx->kode_otp }}',
                        '{{ $trx->tanggal_dikembalikan ? \Carbon\Carbon::parse($trx->tanggal_dikembalikan)->format('d M Y, H:i') : '-' }}'
                    )"
                    style="border: 1.5px solid #1fcf8e; background: transparent; color: #1fcf8e; padding: 8px 20px; border-radius: 20px; cursor: pointer; font-size: 13px; white-space: nowrap; flex-shrink: 0;">
                Detail
            </button>
        </div>

        {{-- INFO TAMBAHAN (hanya tampil kalau Selesai) --}}
        @if($status === 'Selesai')
        <div style="display: flex; justify-content: space-between; margin-top: 14px; padding-top: 12px; border-top: 1px solid #f7fafc; flex-wrap: wrap; gap: 10px;">
            <div>
                <span style="font-size: 11px; color: #a0aec0; display: block; letter-spacing: 0.5px; text-transform: uppercase;">Transaction ID</span>
                <strong style="font-size: 13px; color: #2d3748;">TRX-{{ str_pad($trx->id, 4, '0', STR_PAD_LEFT) }}</strong>
            </div>
            <div>
                <span style="font-size: 11px; color: #a0aec0; display: block; letter-spacing: 0.5px; text-transform: uppercase;">Denda</span>
                <strong style="font-size: 13px; color: #2d3748;">Rp {{ number_format($trx->total_denda ?? 0, 0, ',', '.') }}</strong>
            </div>
            <div>
                <span style="font-size: 11px; color: #a0aec0; display: block; letter-spacing: 0.5px; text-transform: uppercase;">OTP</span>
                <strong style="font-size: 13px; color: #2d3748;">{{ substr($trx->kode_otp, 0, 2) }}****</strong>
            </div>
        </div>
        @endif

    </div>
    @empty
    <div style="background: white; border-radius: 15px; padding: 40px; text-align: center; border: 1px solid #edf2f7;">
        <i class="fa-regular fa-clock" style="font-size: 40px; color: #e2e8f0; margin-bottom: 12px; display: block;"></i>
        <p style="color: #a0aec0; font-size: 14px;">Belum ada riwayat peminjaman</p>
    </div>
    @endforelse
</div>

{{-- PAGINATION --}}
<div style="display: flex; justify-content: center; gap: 8px; margin-top: 25px; align-items: center;">
    <button style="width: 34px; height: 34px; border-radius: 8px; border: 1px solid #edf2f7; background: white; cursor: pointer; color: #718096;">‹</button>
    <button style="width: 34px; height: 34px; border-radius: 8px; border: none; background: #1fcf8e; color: white; font-weight: 600; cursor: pointer;">1</button>
    <button style="width: 34px; height: 34px; border-radius: 8px; border: 1px solid #edf2f7; background: white; cursor: pointer; color: #4a5568;">2</button>
    <button style="width: 34px; height: 34px; border-radius: 8px; border: 1px solid #edf2f7; background: white; cursor: pointer; color: #4a5568;">3</button>
    <button style="width: 34px; height: 34px; border-radius: 8px; border: 1px solid #edf2f7; background: white; cursor: pointer; color: #718096;">›</button>
</div>

{{-- MODAL DETAIL RIWAYAT --}}
<div id="modal-riwayat"
    onclick="tutupModalRiwayat(event)"
    style="display: none; position: fixed; inset: 0; background: rgba(15,28,28,0.52); backdrop-filter: blur(3px); z-index: 999; justify-content: center; align-items: center; padding: 20px;">

    <div style="background: white; border-radius: 24px; width: 100%; max-width: 640px; max-height: 90vh; overflow-y: auto; padding: 28px 30px 24px; display: flex; flex-direction: column; gap: 20px;">

        {{-- HEADER MODAL --}}
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <h2 style="font-size: 16px; font-weight: 800; color: #1a202c;">Detail Riwayat Peminjaman</h2>
                <span id="modal-status-badge" style="font-size: 10px; font-weight: 700; padding: 4px 12px; border-radius: 20px;"></span>
            </div>
            <button onclick="document.getElementById('modal-riwayat').style.display='none'; document.body.style.overflow='';"
                    style="width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #718096; background: #f7fafc; border: none; font-size: 18px;">×</button>
        </div>

        {{-- BOOK INFO CARD (MODAL) --}}
        <div id="modal-books-container" class="scroll-buku" style="display: flex; flex-direction: column; gap: 12px; max-height: 230px; overflow-y: auto; padding-right: 5px;">
            </div>

        {{-- DETAIL GRID --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px 24px; border-top: 1px solid #edf2f7; padding-top: 20px;">
            <div>
                <p style="font-size: 11.5px; color: #a0aec0; font-weight: 500; margin-bottom: 4px;">ID Transaksi</p>
                <p id="modal-trx-id" style="font-size: 15px; font-weight: 800; color: #1a202c;"></p>
            </div>
            <div>
                <p style="font-size: 11.5px; color: #a0aec0; font-weight: 500; margin-bottom: 4px;">Nama Peminjam</p>
                <p id="modal-peminjam" style="font-size: 15px; font-weight: 800; color: #1a202c;"></p>
            </div>
            <div>
                <p style="font-size: 11.5px; color: #a0aec0; font-weight: 500; margin-bottom: 4px;">Waktu Booking</p>
                <p id="modal-booking" style="font-size: 14px; color: #718096;"></p>
            </div>
            <div>
                <p style="font-size: 11.5px; color: #a0aec0; font-weight: 500; margin-bottom: 4px;">Batas Pengembalian</p>
                <p id="modal-batas" style="font-size: 14px; color: #718096;"></p>
            </div>
            <div style="grid-column: 1 / -1;">
                <p style="font-size: 11.5px; color: #a0aec0; font-weight: 500; margin-bottom: 4px;">Tanggal Kembali</p>
                <p id="modal-kembali" style="font-size: 14px; color: #718096;"></p>
            </div>
        </div>

        {{-- KONDISI & DENDA --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; border-top: 1px solid #edf2f7; padding-top: 18px;">
            <div style="display: flex; align-items: center; gap: 9px; border: 1.5px solid #edf2f7; border-radius: 14px; padding: 12px 16px;">
                <i class="fa-regular fa-circle-check" style="color: #a0aec0; font-size: 14px; flex-shrink: 0;"></i>
                <span style="flex: 1; font-size: 13px; color: #718096; font-weight: 500;">Kondisi Buku</span>
                <span id="modal-kondisi" style="font-size: 13px; font-weight: 700;"></span>
            </div>
            <div style="display: flex; align-items: center; gap: 9px; border: 1.5px solid #edf2f7; border-radius: 14px; padding: 12px 16px;">
                <i class="fa-regular fa-credit-card" style="color: #a0aec0; font-size: 14px; flex-shrink: 0;"></i>
                <span style="flex: 1; font-size: 13px; color: #718096; font-weight: 500;">Denda</span>
                <span id="modal-denda" style="font-size: 13px; font-weight: 700;"></span>
            </div>
        </div>

{{-- OTP SECTION --}}
        <div id="modal-otp-container" style="background: #f0fff4; border-radius: 14px; border: 1.5px dashed #1fcf8e; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
            <div>
                <p id="modal-otp-title" style="font-size: 11.5px; color: #2d3748; font-weight: 700; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">OTP Pengembalian</p>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <p id="modal-otp" style="font-size: 26px; font-weight: 800; color: #1fcf8e; letter-spacing: 0.15em; margin: 0; font-family: monospace;"></p>
                    
                    {{-- Tombol Copy --}}
                    <button id="btn-copy-otp" onclick="copyOtpRiwayat()" style="background: white; border: 1px solid #c8e9dc; border-radius: 6px; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #1fcf8e; display: none;" title="Salin Kode">
                        <i class="fa-regular fa-copy"></i>
                    </button>
                </div>
                <p id="modal-otp-hint" style="font-size: 11px; color: #718096; margin-top: 6px; margin-bottom: 0; display: none;">Berikan kode OTP ini ke petugas perpustakaan.</p>
            </div>
            <div style="text-align: right;">
                <p style="font-size: 11px; color: #a0aec0; font-weight: 600; letter-spacing: 0.04em; margin-bottom: 4px;">Verifikasi System</p>
                <p id="modal-verify-date" style="font-size: 12px; color: #718096; margin: 0;"></p>
            </div>
        </div>

        {{-- FOOTER --}}
        <div style="display: flex; justify-content: flex-end; padding-top: 4px;">
            <button onclick="document.getElementById('modal-riwayat').style.display='none'; document.body.style.overflow='';"
                    style="font-size: 14px; font-weight: 700; color: #1a202c; border: 2px solid #edf2f7; border-radius: 99px; padding: 10px 36px; background: white; cursor: pointer;">
                Tutup
            </button>
        </div>

    </div>
</div>

{{-- JAVASCRIPT --}}
<script>
// Filter tab riwayat
function filterRiwayat(tab) {
    ['Semua','Dipinjam','Selesai','Terlambat','Kadaluarsa'].forEach(t => {
        const btn = document.getElementById('tab-' + t);
        if (t === tab) {
            btn.style.borderColor = '#1fcf8e';
            btn.style.background  = '#f0fff4';
            btn.style.color       = '#1fcf8e';
            btn.style.fontWeight  = '600';
        } else {
            btn.style.borderColor = '#edf2f7';
            btn.style.background  = 'white';
            btn.style.color       = '#718096';
            btn.style.fontWeight  = '400';
        }
    });
    document.querySelectorAll('.card-riwayat').forEach(card => {
        const status = card.getAttribute('data-status');
        card.style.display = (tab === 'Semua' || status === tab) ? '' : 'none';
    });
}

// Fungsi Buka Modal Baru
function bukaModalRiwayat(booksBase64, trxId, peminjam, booking, batas, kembali, kondisi, denda, status, otp, verifyDate) {

    // 1. Render Daftar Buku (Dekode dari Base64)
    const books = JSON.parse(atob(booksBase64));
    const container = document.getElementById('modal-books-container');
    container.innerHTML = ''; // Bersihkan kontainer

    books.forEach(book => {
        let coverHtml = book.cover 
            ? `<img src="${book.cover}" style="width:100%;height:100%;object-fit:cover;">`
            : `<i class="fa-solid fa-book" style="color:rgba(255,255,255,0.75);font-size:22px;"></i>`;
        
        let coverStyle = book.cover ? 'background:none; box-shadow:none;' : 'background:linear-gradient(145deg, #3aaea0, #2e7d7d); box-shadow:0 4px 14px rgba(46,125,125,0.3);';

        const bookHtml = `
        <div style="background: #e8f5f0; border-radius: 14px; border: 1px solid #c8e9dc; padding: 18px; display: flex; align-items: flex-start; gap: 16px; flex-shrink: 0;">
            <div style="width: 70px; height: 90px; border-radius: 8px; display: flex; flex-direction: column; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden; ${coverStyle}">
                ${coverHtml}
            </div>
            <div style="flex: 1; min-width: 0;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 7px;">
                    <span style="font-size: 9.5px; font-weight: 800; letter-spacing: 0.1em; color: #1fcf8e; background: white; border: 1px solid #c8e9dc; border-radius: 4px; padding: 2px 8px;">${book.kategori.toUpperCase()}</span>
                    <span style="font-size: 11.5px; color: #a0aec0; font-weight: 500;">ID: ${book.libId}</span>
                </div>
                <p style="font-size: 15px; font-weight: 800; color: #1a202c; line-height: 1.3; margin-bottom: 4px;">${book.judul}</p>
                <p style="font-size: 12.5px; color: #718096; margin-bottom: 8px;">${book.penulis}</p>
                <p style="display: flex; align-items: center; gap: 5px; font-size: 12px; color: #718096;">
                    <i class="fa-regular fa-file-lines" style="font-size: 12px;"></i> Jumlah dipinjam: 1 eksemplar
                </p>
            </div>
        </div>
        `;
        container.innerHTML += bookHtml;
    });

    // 2. Render Detail Transaksi
    document.getElementById('modal-trx-id').textContent      = trxId;
    document.getElementById('modal-peminjam').textContent    = peminjam;
    document.getElementById('modal-booking').textContent     = booking;
    document.getElementById('modal-batas').textContent       = batas;
    document.getElementById('modal-kembali').textContent     = kembali !== '-' ? kembali : 'Belum dikembalikan';

    // === LOGIKA OTP PINTAR ===
    const otpContainer = document.getElementById('modal-otp-container');
    const otpTitle     = document.getElementById('modal-otp-title');
    const otpText      = document.getElementById('modal-otp');
    const otpHint      = document.getElementById('modal-otp-hint');
    const btnCopy      = document.getElementById('btn-copy-otp');

    if (status === 'Dipinjam' || status === 'Terlambat') {
        // Mode Aktif (Siap Dikembalikan)
        otpTitle.textContent = 'OTP PENGEMBALIAN';
        otpText.textContent  = otp; // Tampilkan utuh
        otpText.style.color  = '#1fcf8e';
        otpContainer.style.background  = '#f0fff4';
        otpContainer.style.borderColor = '#1fcf8e';
        otpContainer.style.borderStyle = 'dashed';
        otpHint.style.display = 'block';
        btnCopy.style.display = 'flex';
    } else {
        // Mode Non-aktif (Sudah Selesai / Batal)
        otpTitle.textContent = 'KODE OTP TRANSAKSI';
        otpText.textContent  = otp.substring(0, 2) + '****'; // Sensor kodenya
        otpText.style.color  = '#a0aec0';
        otpContainer.style.background  = '#f7fafc';
        otpContainer.style.borderColor = '#edf2f7';
        otpContainer.style.borderStyle = 'solid';
        otpHint.style.display = 'none';
        btnCopy.style.display = 'none';
    }
    // =========================

    document.getElementById('modal-verify-date').textContent = verifyDate !== '-' ? 'Digunakan pada: ' + verifyDate : 'Belum digunakan';

    // Kondisi buku & Denda
    const kondisiEl = document.getElementById('modal-kondisi');
    kondisiEl.textContent = kondisi !== '-' ? kondisi : 'Belum dicatat';
    kondisiEl.style.color = kondisi === 'Baik' ? '#1fcf8e' : '#e53e3e';

    const dendaEl  = document.getElementById('modal-denda');
    const dendaNum = parseInt(denda) || 0;
    dendaEl.textContent = 'Rp ' + dendaNum.toLocaleString('id-ID');
    dendaEl.style.color = dendaNum > 0 ? '#e53e3e' : '#1fcf8e';

    // Badge status
    const badge = document.getElementById('modal-status-badge');
    const statusMap = {
        'Selesai'      : { bg: '#f0fff4', color: '#38a169' },
        'Dipinjam'     : { bg: '#ebf8ff', color: '#3182ce' },
        'Terlambat'    : { bg: '#fff5f5', color: '#e53e3e' },
        'Menunggu OTP' : { bg: '#fff3e0', color: '#e65100' },
        'Kadaluarsa'   : { bg: '#f7fafc', color: '#718096' },
    };
    const s = statusMap[status] || statusMap['Kadaluarsa'];
    badge.textContent      = status.toUpperCase();
    badge.style.background = s.bg;
    badge.style.color      = s.color;

    // Tampilkan Modal
    document.getElementById('modal-riwayat').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function tutupModalRiwayat(event) {
    if (event.target === document.getElementById('modal-riwayat')) {
        document.getElementById('modal-riwayat').style.display = 'none';
        document.body.style.overflow = '';
    }
}

function tutupModalRiwayat(event) {
        if (event.target === document.getElementById('modal-riwayat')) {
            document.getElementById('modal-riwayat').style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    // ==========================================
    // FUNGSI COPY OTP (PASTE DI SINI)
    // ==========================================
    function copyOtpRiwayat() {
        // 1. Ambil teks OTP
        const otpCode = document.getElementById('modal-otp').innerText.trim();

        // 2. Buat elemen input sementara (tidak terlihat) untuk menampung teks
        const tempInput = document.createElement("input");
        tempInput.value = otpCode;
        document.body.appendChild(tempInput);

        // 3. Sorot (select) dan jalankan perintah copy bawaan browser
        tempInput.select();
        tempInput.setSelectionRange(0, 99999); // Untuk kompatibilitas HP/Mobile
        document.execCommand("copy");

        // 4. Hapus kembali elemen sementaranya agar bersih
        document.body.removeChild(tempInput);

        // 5. Berikan efek visual tombol berhasil ditekan (Warna solid & Centang)
        const btn = document.getElementById('btn-copy-otp');
        btn.innerHTML = '<i class="fa-solid fa-check"></i>';
        btn.style.background = '#1fcf8e';
        btn.style.color = 'white';

        // 6. Kembalikan ke wujud awal setelah 2 detik
        setTimeout(() => {
            btn.innerHTML = '<i class="fa-regular fa-copy"></i>';
            btn.style.background = 'white';
            btn.style.color = '#1fcf8e';
        }, 2000);
    }
</script>
</script>



@endsection