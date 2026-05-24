@extends('layouts.member_layout')

@section('title', 'Katalog Buku')

@section('content')

{{-- META CSRF untuk AJAX — dipindah ke atas sebelum JavaScript --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- SEARCH & FILTER BAR --}}
<div style="background: white; padding: 15px 25px; border-radius: 15px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 25px; border: 1px solid #edf2f7;">
    <div style="position: relative; flex: 1; max-width: 400px;">
        <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 15px; top: 12px; color: #cbd5e0;"></i>
        <input type="text" placeholder="Cari judul, penulis, kategori..."
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
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 25px;">
    @foreach($books as $buku)
    {{-- PERBAIKAN: tambahkan {{ $buku->id }} sebagai parameter terakhir bukaModal() --}}
    <div onclick="bukaModal(
            '{{ addslashes($buku->judul) }}',
            '{{ addslashes($buku->penulis) }}',
            '{{ addslashes($buku->penerbit) }}',
            '{{ $buku->tahun_terbit }}',
            '{{ addslashes($buku->kategori->nama_kategori ?? '-') }}',
            {{ $buku->stok_tersedia }},
            {{ $buku->stok_total }},
            '{{ addslashes($buku->deskripsi ?? '-') }}',
            {{ $buku->id }}
        )"
        style="background: white; border-radius: 15px; overflow: hidden; border: 1px solid #edf2f7; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;"
        onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.1)'"
        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">

        {{-- Cover --}}
        <div style="height: 180px; background: linear-gradient(135deg, #1f3c45, #2d6a5a); display: flex; flex-direction: column; align-items: center; justify-content: center; color: white; padding: 15px; text-align: center;">
            <i class="fa-solid fa-book" style="font-size: 30px; opacity: 0.5; margin-bottom: 10px;"></i>
            <p style="font-size: 13px; font-weight: 600; line-height: 1.4;">{{ $buku->judul }}</p>
            <p style="font-size: 11px; opacity: 0.7; margin-top: 5px;">{{ strtoupper($buku->penulis) }}</p>
        </div>

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
    @endforeach
</div>

{{-- ===================== MODAL POPUP ===================== --}}
<div id="modal-overlay"
    onclick="tutupModal(event)"
    style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; justify-content: center; align-items: center; padding: 20px;">

    <div id="modal-box"
        style="background: white; border-radius: 16px; width: 100%; max-width: 750px; padding: 25px; position: relative; max-height: 90vh; overflow-y: auto;">

        {{-- HEADER MODAL --}}
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <span id="modal-kategori"
                style="background: #edf2f7; color: #4a5568; font-size: 11px; font-weight: 700; padding: 5px 14px; border-radius: 20px; letter-spacing: 1px;">
            </span>
            <button onclick="document.getElementById('modal-overlay').style.display='none'"
                    style="background: none; border: none; cursor: pointer; font-size: 20px; color: #a0aec0; line-height: 1; padding: 5px;">
                ×
            </button>
        </div>

        {{-- BODY MODAL --}}
        <div style="display: flex; gap: 25px; flex-wrap: wrap;">

            {{-- KIRI: Cover + Stok --}}
            <div style="width: 200px; flex-shrink: 0;">
                <div style="height: 220px; background: linear-gradient(135deg, #1a5c4e, #2d8c6e); border-radius: 12px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: white; padding: 20px; text-align: center; margin-bottom: 15px;">
                    <i class="fa-solid fa-book" style="font-size: 28px; opacity: 0.4; margin-bottom: 15px;"></i>
                    <h3 id="modal-judul-cover" style="font-size: 15px; font-weight: 700; line-height: 1.4; margin-bottom: 10px;"></h3>
                    <div style="width: 30px; height: 2px; background: rgba(255,255,255,0.4); margin-bottom: 10px;"></div>
                    <p id="modal-penulis-cover" style="font-size: 11px; opacity: 0.8; letter-spacing: 1px; text-transform: uppercase;"></p>
                </div>

                {{-- Stok Box --}}
                <div id="modal-stok-box" style="background: #f0fff4; border-radius: 10px; padding: 12px 15px; border: 1px solid #c6f6d5;">
                    <p id="modal-stok-label" style="color: #38a169; font-weight: 700; font-size: 13px; margin-bottom: 4px;">● Tersedia</p>
                    <p id="modal-stok-angka" style="font-size: 13px; color: #4a5568;"></p>
                </div>
            </div>

            {{-- KANAN: Detail --}}
            <div style="flex: 1; min-width: 200px;">
                <h2 id="modal-judul" style="font-size: 22px; font-weight: 700; color: #1a202c; margin-bottom: 6px;"></h2>
                <p id="modal-penulis" style="font-size: 14px; color: #718096; margin-bottom: 20px; display: flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-pen" style="font-size: 11px;"></i>
                    <span></span>
                </p>

                {{-- Grid Info --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                    <div>
                        <small style="font-size: 11px; color: #a0aec0; letter-spacing: 1px; font-weight: 600; text-transform: uppercase; display: block; margin-bottom: 4px;">Penerbit</small>
                        <p id="modal-penerbit" style="font-size: 14px; font-weight: 600; color: #2d3748;"></p>
                    </div>
                    <div>
                        <small style="font-size: 11px; color: #a0aec0; letter-spacing: 1px; font-weight: 600; text-transform: uppercase; display: block; margin-bottom: 4px;">Tahun</small>
                        <p id="modal-tahun" style="font-size: 14px; font-weight: 600; color: #2d3748;"></p>
                    </div>
                    <div>
                        <small style="font-size: 11px; color: #a0aec0; letter-spacing: 1px; font-weight: 600; text-transform: uppercase; display: block; margin-bottom: 4px;">Kategori</small>
                        <p id="modal-kategori-val" style="font-size: 14px; font-weight: 600; color: #2d3748;"></p>
                    </div>
                    <div>
                        <small style="font-size: 11px; color: #a0aec0; letter-spacing: 1px; font-weight: 600; text-transform: uppercase; display: block; margin-bottom: 4px;">Stok</small>
                        <p id="modal-stok-val" style="font-size: 14px; font-weight: 600; color: #2d3748;"></p>
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div>
                    <small style="font-size: 11px; color: #a0aec0; letter-spacing: 1px; font-weight: 600; text-transform: uppercase; display: block; margin-bottom: 8px;">Deskripsi</small>
                    <p id="modal-deskripsi" style="font-size: 14px; color: #4a5568; line-height: 1.7;"></p>
                </div>
            </div>
        </div>

        {{-- FOOTER MODAL --}}
        {{-- PERBAIKAN: hapus tombol duplikat, lengkapi tag <a> --}}
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 25px; padding-top: 20px; border-top: 1px solid #edf2f7; flex-wrap: wrap; gap: 10px;">
            <a href="{{ route('member.katalog') }}"
               style="font-size: 14px; color: #718096; text-decoration: none;">
                ← Lihat halaman penuh
            </a>
            <div style="display: flex; gap: 10px; align-items: center;">
                <button onclick="document.getElementById('modal-overlay').style.display='none'"
                        style="padding: 11px 24px; background: #f7fafc; border: 1px solid #edf2f7; border-radius: 30px; cursor: pointer; font-size: 14px; color: #4a5568;">
                    Tutup
                </button>
                {{-- PERBAIKAN: hanya satu tombol Tambah ke Keranjang --}}
                <button id="btn-tambah-keranjang" onclick="tambahKeKeranjang()"
                        style="padding: 11px 24px; background: #1fcf8e; color: white; border: none; border-radius: 30px; cursor: pointer; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-cart-shopping"></i> Tambah ke Keranjang
                </button>
            </div>
        </div>

    </div>
</div>


{{-- JAVASCRIPT MODAL --}}
<script>
let selectedBukuId = null;

// PERBAIKAN: tambahkan parameter bukuId di akhir function signature
function bukaModal(judul, penulis, penerbit, tahun, kategori, stokTersedia, stokTotal, deskripsi, bukuId) {
    selectedBukuId = bukuId; // simpan id buku yang dipilih

    // Isi semua data ke elemen modal
    document.getElementById('modal-judul').textContent          = judul;
    document.getElementById('modal-judul-cover').textContent    = judul;
    document.getElementById('modal-penulis-cover').textContent  = penulis;
    document.getElementById('modal-penulis').querySelector('span').textContent = penulis;
    document.getElementById('modal-penerbit').textContent       = penerbit;
    document.getElementById('modal-tahun').textContent          = tahun;
    document.getElementById('modal-kategori').textContent       = kategori.toUpperCase();
    document.getElementById('modal-kategori-val').textContent   = kategori;
    document.getElementById('modal-stok-val').textContent       = stokTersedia;
    document.getElementById('modal-deskripsi').textContent      = deskripsi;
    document.getElementById('modal-stok-angka').textContent     = stokTersedia + ' / ' + stokTotal + ' tersedia';

    // Ubah warna stok box sesuai ketersediaan
    const stokBox   = document.getElementById('modal-stok-box');
    const stokLabel = document.getElementById('modal-stok-label');
    if (stokTersedia > 0) {
        stokBox.style.background   = '#f0fff4';
        stokBox.style.borderColor  = '#c6f6d5';
        stokLabel.style.color      = '#38a169';
        stokLabel.textContent      = '● Tersedia';
    } else {
        stokBox.style.background   = '#fff5f5';
        stokBox.style.borderColor  = '#fed7d7';
        stokLabel.style.color      = '#e53e3e';
        stokLabel.textContent      = '● Tidak Tersedia';
    }

    // Tampilkan overlay
    const overlay = document.getElementById('modal-overlay');
    overlay.style.display = 'flex';
    document.body.style.overflow = 'hidden'; // cegah scroll background
}

function tambahKeKeranjang() {
    if (!selectedBukuId) return;

    const btn = document.getElementById('btn-tambah-keranjang');
    btn.disabled  = true;
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
            btn.innerHTML = '<i class="fa-solid fa-check"></i> Ditambahkan!';
            btn.style.background = '#38a169';
            setTimeout(() => {
                document.getElementById('modal-overlay').style.display = 'none';
                document.body.style.overflow = '';
                // Redirect ke keranjang
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
    // Tutup hanya jika klik di luar modal box
    if (event.target === document.getElementById('modal-overlay')) {
        document.getElementById('modal-overlay').style.display = 'none';
        document.body.style.overflow = '';
    }
}
</script>

@endsection