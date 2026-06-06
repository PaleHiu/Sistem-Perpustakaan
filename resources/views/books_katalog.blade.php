@extends('layouts.member_layout')

@section('title', 'Katalog Buku')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

@php
    // Status verifikasi dikirim dari route, dipakai untuk kontrol tombol keranjang
    $sudahApproved = ($statusVerif ?? 'Incomplete') === 'Approved';
    $pesanVerif    = match($statusVerif ?? 'Incomplete') {
        'Pending'  => 'Akun kamu sedang diverifikasi admin. Tunggu persetujuan sebelum meminjam buku.',
        'Rejected' => 'Verifikasi akun kamu ditolak. Upload ulang dokumen di halaman profil.',
        default    => 'Lengkapi profil dan upload dokumen identitas agar bisa meminjam buku.',
    };
@endphp

{{-- NOTIF jika belum approved — banner di atas katalog --}}
@if(!$sudahApproved)
<div style="background:#fffbeb;border:1px solid #f6ad55;border-radius:12px;padding:14px 20px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;gap:15px;flex-wrap:wrap;">
    <div style="display:flex;align-items:center;gap:12px;">
        <i class="fa-solid fa-triangle-exclamation" style="color:#d97706;font-size:18px;flex-shrink:0;"></i>
        <span style="font-size:13px;color:#92400e;">{{ $pesanVerif }}</span>
    </div>
    <a href="{{ route('member.profil') }}"
       style="background:#d97706;color:white;padding:8px 18px;border-radius:20px;font-size:13px;font-weight:600;text-decoration:none;white-space:nowrap;">
        Lengkapi Profil →
    </a>
</div>
@endif

{{-- SEARCH & FILTER BAR --}}
<div style="background: white; padding: 15px 25px; border-radius: 15px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 25px; border: 1px solid #edf2f7;">
    <div style="position: relative; flex: 1; max-width: 400px;">
        <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 15px; top: 12px; color: #cbd5e0;"></i>
        <input type="text" id="searchInput" placeholder="Cari judul, penulis, kategori..."
            oninput="filterBuku()"
            style="width: 100%; padding: 10px 10px 10px 45px; border-radius: 20px; border: 1px solid #edf2f7; background: #f8fafc; outline: none;">
    </div>
    <div style="display: flex; gap: 10px;">
        <button style="background: #f8fafc; border: 1px solid #edf2f7; padding: 10px 15px; border-radius: 10px; cursor: pointer;">
            <i class="fa-solid fa-table-cells-large"></i>
        </button>
        <button style="background: #f8fafc; border: 1px solid #edf2f7; padding: 10px 15px; border-radius: 10px; cursor: pointer;">
            <i class="fa-solid fa-list"></i>
        </button>
    </div>
</div>

{{-- GRID BUKU --}}
<div id="grid-buku" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 25px;">
    @forelse($books as $buku)
    <div class="card-buku"
        data-judul="{{ strtolower($buku->judul) }}"
        data-penulis="{{ strtolower($buku->penulis) }}"
        data-kategori="{{ strtolower($buku->kategori->nama_kategori ?? '') }}"
        onclick="bukaModal(
            '{{ addslashes($buku->judul) }}',
            '{{ addslashes($buku->penulis) }}',
            '{{ addslashes($buku->penerbit) }}',
            '{{ $buku->tahun_terbit }}',
            '{{ addslashes($buku->kategori->nama_kategori ?? '-') }}',
            {{ $buku->stok_tersedia }},
            {{ $buku->stok_total }},
            '{{ addslashes($buku->deskripsi ?? '-') }}',
            '{{ $buku->cover ? asset('storage/' . $buku->cover) : '' }}',
            {{ $buku->id }}
        )"
        style="background: white; border-radius: 15px; overflow: hidden; border: 1px solid #edf2f7; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;"
        onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.1)'"
        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">

        {{-- Cover --}}
        @if($buku->cover)
            <div style="height: 180px; overflow: hidden;">
                <img src="{{ asset('storage/' . $buku->cover) }}"
                    alt="{{ $buku->judul }}"
                    style="width:100%;height:100%;object-fit:cover;">
            </div>
        @else
            <div style="height: 180px; background: linear-gradient(135deg, #1f3c45, #2d6a5a); display:flex; align-items:center; justify-content:center; color:white; flex-direction:column; gap:10px; padding:15px; text-align:center;">
                <i class="fa-solid fa-book" style="font-size:30px; opacity:0.5;"></i>
                <p style="font-size:13px; font-weight:600; line-height:1.4;">{{ $buku->judul }}</p>
            </div>
        @endif

        {{-- Info --}}
        <div style="padding: 15px;">
            <h4 style="margin: 0 0 4px; font-size: 14px; color: #2d3748; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                {{ $buku->judul }}
            </h4>
            <p style="margin: 0 0 12px; font-size: 12px; color: #718096;">{{ $buku->penulis }}</p>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 11px; font-weight: 700;
                    color: {{ $buku->stok_tersedia > 0 ? '#1fcf8e' : '#e53e3e' }};
                    background: {{ $buku->stok_tersedia > 0 ? '#f0fff4' : '#fff5f5' }};
                    padding: 4px 8px; border-radius: 5px;">
                    {{ $buku->stok_tersedia > 0 ? 'Tersedia ('.$buku->stok_tersedia.')' : 'Habis' }}
                </span>
                <button style="background: {{ $buku->stok_tersedia > 0 ? '#1fcf8e' : '#cbd5e0' }}; color: white; border: none; width: 30px; height: 30px; border-radius: 50%; cursor: {{ $buku->stok_tersedia > 0 ? 'pointer' : 'not-allowed' }};">
                    <i class="fa-solid fa-plus" style="font-size: 12px;"></i>
                </button>
            </div>
        </div>
    </div>
    @empty
    <div style="grid-column:1/-1;text-align:center;padding:60px 0;color:#a0aec0;">
        <i class="fa-solid fa-book-open" style="font-size:48px;margin-bottom:15px;display:block;opacity:0.3;"></i>
        <p style="font-size:15px;">Belum ada buku di katalog</p>
    </div>
    @endforelse
</div>

{{-- MODAL POPUP --}}
<div id="modal-overlay"
    onclick="tutupModal(event)"
    style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; justify-content: center; align-items: center; padding: 20px;">

    <div id="modal-box"
        style="background: white; border-radius: 16px; width: 100%; max-width: 750px; padding: 25px; position: relative; max-height: 90vh; overflow-y: auto;">

        {{-- HEADER --}}
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <span id="modal-kategori"
                style="background: #edf2f7; color: #4a5568; font-size: 11px; font-weight: 700; padding: 5px 14px; border-radius: 20px; letter-spacing: 1px;">
            </span>
            <button onclick="document.getElementById('modal-overlay').style.display='none'; document.body.style.overflow='';"
                    style="background: none; border: none; cursor: pointer; font-size: 20px; color: #a0aec0; line-height: 1; padding: 5px;">×</button>
        </div>

        {{-- BODY --}}
        <div style="display: flex; gap: 25px; flex-wrap: wrap;">

            {{-- KIRI --}}
            <div style="width: 200px; flex-shrink: 0;">
                <div id="modal-cover-box"
                    style="height: 220px; background: linear-gradient(135deg, #1a5c4e, #2d8c6e); border-radius: 12px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: white; padding: 20px; text-align: center; margin-bottom: 15px; overflow: hidden;">
                </div>
                <div id="modal-stok-box" style="background: #f0fff4; border-radius: 10px; padding: 12px 15px; border: 1px solid #c6f6d5;">
                    <p id="modal-stok-label" style="color: #38a169; font-weight: 700; font-size: 13px; margin-bottom: 4px;">● Tersedia</p>
                    <p id="modal-stok-angka" style="font-size: 13px; color: #4a5568;"></p>
                </div>
            </div>

            {{-- KANAN --}}
            <div style="flex: 1; min-width: 200px;">
                <h2 id="modal-judul" style="font-size: 22px; font-weight: 700; color: #1a202c; margin-bottom: 6px;"></h2>
                <p id="modal-penulis" style="font-size: 14px; color: #718096; margin-bottom: 20px; display: flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-pen" style="font-size: 11px;"></i><span></span>
                </p>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                    <div>
                        <small style="font-size: 11px; color: #a0aec0; font-weight: 600; text-transform: uppercase; display: block; margin-bottom: 4px;">Penerbit</small>
                        <p id="modal-penerbit" style="font-size: 14px; font-weight: 600; color: #2d3748;"></p>
                    </div>
                    <div>
                        <small style="font-size: 11px; color: #a0aec0; font-weight: 600; text-transform: uppercase; display: block; margin-bottom: 4px;">Tahun</small>
                        <p id="modal-tahun" style="font-size: 14px; font-weight: 600; color: #2d3748;"></p>
                    </div>
                    <div>
                        <small style="font-size: 11px; color: #a0aec0; font-weight: 600; text-transform: uppercase; display: block; margin-bottom: 4px;">Kategori</small>
                        <p id="modal-kategori-val" style="font-size: 14px; font-weight: 600; color: #2d3748;"></p>
                    </div>
                    <div>
                        <small style="font-size: 11px; color: #a0aec0; font-weight: 600; text-transform: uppercase; display: block; margin-bottom: 4px;">Stok</small>
                        <p id="modal-stok-val" style="font-size: 14px; font-weight: 600; color: #2d3748;"></p>
                    </div>
                </div>
                <div>
                    <small style="font-size: 11px; color: #a0aec0; font-weight: 600; text-transform: uppercase; display: block; margin-bottom: 8px;">Deskripsi</small>
                    <p id="modal-deskripsi" style="font-size: 14px; color: #4a5568; line-height: 1.7;"></p>
                </div>
            </div>
        </div>

        {{-- FOOTER --}}
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 25px; padding-top: 20px; border-top: 1px solid #edf2f7; flex-wrap: wrap; gap: 10px;">
            <span style="font-size: 14px; color: #718096;">Katalog Buku SIPUS</span>
            <div style="display: flex; gap: 10px; align-items: center;">
                <button onclick="document.getElementById('modal-overlay').style.display='none'; document.body.style.overflow='';"
                        style="padding: 11px 24px; background: #f7fafc; border: 1px solid #edf2f7; border-radius: 30px; cursor: pointer; font-size: 14px; color: #4a5568;">
                    Tutup
                </button>
                {{-- Tombol keranjang: muncul beda tergantung status verifikasi --}}
                <button id="btn-tambah-keranjang" onclick="tambahKeKeranjang()"
                        style="padding: 11px 24px; background: #1fcf8e; color: white; border: none; border-radius: 30px; cursor: pointer; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-cart-shopping"></i> Tambah ke Keranjang
                </button>
            </div>
        </div>

    </div>
</div>

{{-- MODAL NOTIF VERIFIKASI (muncul jika belum approved) --}}
<div id="modal-verif"
    style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;justify-content:center;align-items:center;padding:20px;">
    <div style="background:white;border-radius:16px;max-width:420px;width:100%;padding:30px;text-align:center;">
        <div style="width:60px;height:60px;background:#fffbeb;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 15px;">
            <i class="fa-solid fa-triangle-exclamation" style="font-size:24px;color:#d97706;"></i>
        </div>
        <h3 style="font-size:17px;font-weight:700;color:#2d3748;margin-bottom:10px;">Akun Belum Terverifikasi</h3>
        <p id="pesan-verif" style="font-size:13px;color:#718096;margin-bottom:25px;line-height:1.6;"></p>
        <div style="display:flex;gap:10px;justify-content:center;">
            <button onclick="document.getElementById('modal-verif').style.display='none';"
                    style="padding:11px 24px;background:#f7fafc;border:1px solid #edf2f7;border-radius:30px;cursor:pointer;font-size:14px;color:#4a5568;">
                Nanti Saja
            </button>
            <a href="{{ route('member.profil') }}"
               style="padding:11px 24px;background:#1fcf8e;color:white;border:none;border-radius:30px;font-size:14px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                <i class="fa-solid fa-user"></i> Lengkapi Profil
            </a>
        </div>
    </div>
</div>

{{-- JAVASCRIPT --}}
<script>
let selectedBukuId  = null;
const sudahApproved = {{ $sudahApproved ? 'true' : 'false' }};
const pesanVerif    = "{{ $pesanVerif }}";

function bukaModal(judul, penulis, penerbit, tahun, kategori,
                   stokTersedia, stokTotal, deskripsi, coverUrl, bukuId) {
    selectedBukuId = bukuId;

    document.getElementById('modal-judul').textContent                         = judul;
    document.getElementById('modal-penulis').querySelector('span').textContent = penulis;
    document.getElementById('modal-penerbit').textContent                      = penerbit;
    document.getElementById('modal-tahun').textContent                         = tahun;
    document.getElementById('modal-kategori').textContent                      = kategori.toUpperCase();
    document.getElementById('modal-kategori-val').textContent                  = kategori;
    document.getElementById('modal-stok-val').textContent                      = stokTersedia;
    document.getElementById('modal-deskripsi').textContent                     = deskripsi;
    document.getElementById('modal-stok-angka').textContent                    = stokTersedia + ' / ' + stokTotal + ' tersedia';

    // Cover
    const coverBox = document.getElementById('modal-cover-box');
    if (coverUrl && coverUrl !== '') {
        coverBox.innerHTML        = '<img src="' + coverUrl + '" style="width:100%;height:100%;object-fit:cover;border-radius:12px;">';
        coverBox.style.background = 'none';
        coverBox.style.padding    = '0';
    } else {
        coverBox.style.background = 'linear-gradient(135deg, #1a5c4e, #2d8c6e)';
        coverBox.style.padding    = '20px';
        coverBox.innerHTML = `
            <i class="fa-solid fa-book" style="font-size:28px;opacity:0.4;margin-bottom:15px;"></i>
            <h3 style="font-size:15px;font-weight:700;line-height:1.4;margin-bottom:10px;">${judul}</h3>
            <div style="width:30px;height:2px;background:rgba(255,255,255,0.4);margin-bottom:10px;"></div>
            <p style="font-size:11px;opacity:0.8;letter-spacing:1px;text-transform:uppercase;">${penulis}</p>
        `;
    }

    // Stok box
    const stokBox   = document.getElementById('modal-stok-box');
    const stokLabel = document.getElementById('modal-stok-label');
    if (stokTersedia > 0) {
        stokBox.style.background  = '#f0fff4';
        stokBox.style.borderColor = '#c6f6d5';
        stokLabel.style.color     = '#38a169';
        stokLabel.textContent     = '● Tersedia';
    } else {
        stokBox.style.background  = '#fff5f5';
        stokBox.style.borderColor = '#fed7d7';
        stokLabel.style.color     = '#e53e3e';
        stokLabel.textContent     = '● Tidak Tersedia';
    }

    // Ubah tampilan tombol keranjang jika belum approved
    const btn = document.getElementById('btn-tambah-keranjang');
    if (!sudahApproved) {
        btn.style.background = '#d97706';
        btn.innerHTML        = '<i class="fa-solid fa-lock"></i> Verifikasi Dulu';
    } else {
        btn.style.background = '#1fcf8e';
        btn.innerHTML        = '<i class="fa-solid fa-cart-shopping"></i> Tambah ke Keranjang';
    }

    document.getElementById('modal-overlay').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function tambahKeKeranjang() {
    if (!selectedBukuId) return;

    // Jika belum approved, tampilkan modal notif — JANGAN langsung fetch
    if (!sudahApproved) {
        document.getElementById('pesan-verif').textContent = pesanVerif;
        document.getElementById('modal-overlay').style.display = 'none';
        document.getElementById('modal-verif').style.display   = 'flex';
        return;
    }

    const btn    = document.getElementById('btn-tambah-keranjang');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menambahkan...';

    fetch('{{ route('member.keranjang.tambah') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ buku_id: selectedBukuId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            btn.innerHTML        = '<i class="fa-solid fa-check"></i> Ditambahkan!';
            btn.style.background = '#38a169';
            setTimeout(() => {
                document.getElementById('modal-overlay').style.display = 'none';
                document.body.style.overflow = '';
                window.location.href = '{{ route('member.keranjang') }}';
            }, 1000);
        } else {
            alert(data.error || 'Gagal menambahkan ke keranjang');
            btn.disabled  = false;
            btn.innerHTML = '<i class="fa-solid fa-cart-shopping"></i> Tambah ke Keranjang';
        }
    })
    .catch(() => {
        alert('Terjadi kesalahan, coba lagi');
        btn.disabled  = false;
        btn.innerHTML = '<i class="fa-solid fa-cart-shopping"></i> Tambah ke Keranjang';
    });
}

function tutupModal(event) {
    if (event.target === document.getElementById('modal-overlay')) {
        document.getElementById('modal-overlay').style.display = 'none';
        document.body.style.overflow = '';
    }
}

// Filter pencarian buku
function filterBuku() {
    const keyword = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('.card-buku').forEach(card => {
        const judul    = card.getAttribute('data-judul') || '';
        const penulis  = card.getAttribute('data-penulis') || '';
        const kategori = card.getAttribute('data-kategori') || '';
        card.style.display = (judul + penulis + kategori).includes(keyword) ? '' : 'none';
    });
}
</script>

@endsection