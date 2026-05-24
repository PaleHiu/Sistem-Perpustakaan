@extends('layouts.member_layout')

@section('title', 'Keranjang')

@section('content')

<div style="display: flex; gap: 25px; align-items: flex-start;">

    {{-- KOLOM KIRI: Buku yang Dipilih --}}
<div style="flex: 2; background: white; border-radius: 15px; padding: 25px; border: 1px solid #edf2f7;">

    <h3 style="margin: 0 0 20px; font-size: 18px; color: #2d3748;">
        Buku yang Dipilih ({{ count($keranjang) }})
    </h3>

    @forelse($keranjang as $item)
    <div style="display: flex; gap: 15px; padding: 15px 0; border-bottom: 1px solid #edf2f7; align-items: flex-start;">
        <div style="width: 70px; height: 100px; background: linear-gradient(135deg, #1f3c45, #2d6a5a); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <i class="fa-solid fa-book" style="color: rgba(255,255,255,0.6); font-size: 24px;"></i>
        </div>
        <div style="flex: 1;">
            <h4 style="margin: 0 0 5px; font-size: 15px; color: #2d3748;">{{ $item->buku->judul }}</h4>
            <p style="margin: 0 0 8px; font-size: 13px; color: #718096;">{{ $item->buku->penulis }}</p>
            <span style="display: inline-block; background: #fff3e0; color: #e65100; font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 20px;">
                STOK DI-HOLD
            </span>
            <p style="margin: 8px 0 0; font-size: 12px; color: #a0aec0;">
                <i class="fa-regular fa-clock"></i> Tersedia hingga: Besok, 09:00
            </p>
        </div>
        {{-- Tombol hapus --}}
        <form method="POST" action="{{ route('member.keranjang.hapus', $item->id) }}">
            @csrf
            @method('DELETE')
            <button type="submit" style="background: none; border: none; cursor: pointer; color: #e53e3e; font-size: 18px; padding: 5px;">
                <i class="fa-solid fa-trash"></i>
            </button>
        </form>
    </div>
    @empty
    <div style="text-align: center; padding: 40px 0;">
        <i class="fa-solid fa-cart-shopping" style="font-size: 40px; color: #e2e8f0; margin-bottom: 12px; display: block;"></i>
        <p style="color: #a0aec0;">Keranjang masih kosong</p>
        <a href="{{ route('member.katalog') }}" style="display: inline-block; margin-top: 15px; padding: 10px 24px; background: #1fcf8e; color: white; border-radius: 20px; text-decoration: none; font-size: 14px;">
            Lihat Katalog
        </a>
    </div>
    @endforelse

    @if(count($keranjang) > 0)
    <div style="margin-top: 20px;">
        <a href="{{ route('member.katalog') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border: 2px solid #1fcf8e; border-radius: 30px; color: #1fcf8e; text-decoration: none; font-size: 14px;">
            <i class="fa-solid fa-arrow-left"></i> Tambah Buku Lagi
        </a>
    </div>
    @endif
</div>

{{-- KOLOM KANAN: Ringkasan --}}
<div style="flex: 1; background: white; border-radius: 15px; padding: 25px; border: 1px solid #edf2f7;">
    <h3 style="margin: 0 0 20px; font-size: 18px; color: #2d3748;">Ringkasan Peminjaman</h3>

    <div style="display: flex; flex-direction: column; gap: 14px; margin-bottom: 20px;">
        <div style="display: flex; justify-content: space-between;">
            <span style="font-size: 14px; color: #718096;">Jumlah Buku</span>
            <strong style="font-size: 14px; color: #2d3748;">{{ count($keranjang) }} Buku</strong>
        </div>
        <div style="display: flex; justify-content: space-between;">
            <span style="font-size: 14px; color: #718096;">Durasi Pinjam</span>
            <strong style="font-size: 14px; color: #2d3748;">7 hari</strong>
        </div>
        <div style="display: flex; justify-content: space-between;">
            <span style="font-size: 14px; color: #718096;">Tanggal Mulai</span>
            <strong style="font-size: 14px; color: #2d3748;">{{ now()->format('d M Y') }}</strong>
        </div>
        <div style="display: flex; justify-content: space-between;">
            <span style="font-size: 14px; color: #718096;">Batas Pengembalian</span>
            <strong style="font-size: 14px; color: #2d3748;">{{ now()->addDays(7)->format('d M Y') }}</strong>
        </div>
    </div>

    <div style="background: #fffbeb; border: 1px solid #f6ad55; border-radius: 10px; padding: 12px 15px; margin-bottom: 20px; display: flex; gap: 10px;">
        <i class="fa-solid fa-triangle-exclamation" style="color: #d97706; flex-shrink: 0; margin-top: 2px;"></i>
        <p style="margin: 0; font-size: 12px; color: #92400e; line-height: 1.5;">
            <strong>Penting:</strong> Stok buku di-hold selama proses. Selesaikan booking dalam 24 jam.
        </p>
    </div>

    @if(count($keranjang) > 0)
    <form method="POST" action="{{ route('member.keranjang.booking') }}">
        @csrf
        <button type="submit" style="width: 100%; padding: 14px; background: #1fcf8e; color: white; border: none; border-radius: 30px; font-size: 15px; font-weight: 600; cursor: pointer; margin-bottom: 12px;">
            Lanjutkan Booking <i class="fa-solid fa-arrow-right"></i>
        </button>
    </form>
    @endif

    <a href="{{ route('member.katalog') }}" style="display: flex; align-items: center; justify-content: center; gap: 8px; padding: 13px; border: 2px solid #1fcf8e; border-radius: 30px; color: #1fcf8e; text-decoration: none; font-size: 15px;">
        <i class="fa-solid fa-arrow-left"></i> Tambah Buku Lagi
    </a>

    <p style="text-align: center; margin-top: 20px; font-size: 11px; color: #cbd5e0; text-transform: uppercase; letter-spacing: 1px;">
        Powered by SIPUS Core
    </p>
</div>

    <!-- {{-- ===== KOLOM KIRI: Buku yang Dipilih ===== --}}
    <div style="flex: 2; background: white; border-radius: 15px; padding: 25px; border: 1px solid #edf2f7;">
        
        <h3 style="margin: 0 0 20px; font-size: 18px; color: #2d3748;">
            Buku yang Dipilih ({{ count($keranjang) }})
        </h3>

        {{-- ITEM BUKU 1 --}}
        <div style="display: flex; gap: 15px; padding: 15px 0; border-bottom: 1px solid #edf2f7; align-items: flex-start;">
            <img src="https://via.placeholder.com/70x100" 
                style="width: 70px; height: 100px; object-fit: cover; border-radius: 8px;" 
                alt="Cover Buku">
            <div style="flex: 1;">
                <h4 style="margin: 0 0 5px; font-size: 15px; color: #2d3748;">
                    Algoritma &amp; Struktur Data Lanjut
                </h4>
                <p style="margin: 0 0 8px; font-size: 13px; color: #718096;">Prof. Sastro</p>
                <span style="display: inline-block; background: #fff3e0; color: #e65100; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">
                    STOK DI-HOLD
                </span>
                <p style="margin: 8px 0 0; font-size: 12px; color: #a0aec0;">
                    <i class="fa-regular fa-clock"></i> Tersedia hingga: Besok, 09:00
                </p>
            </div>
            <button style="background: none; border: none; cursor: pointer; color: #e53e3e; font-size: 18px; padding: 5px;">
                <i class="fa-solid fa-trash"></i>
            </button>
        </div>

        {{-- ITEM BUKU 2 --}}
        <div style="display: flex; gap: 15px; padding: 15px 0; border-bottom: 1px solid #edf2f7; align-items: flex-start;">
            <img src="https://via.placeholder.com/70x100" 
                style="width: 70px; height: 100px; object-fit: cover; border-radius: 8px;" 
                alt="Cover Buku">
            <div style="flex: 1;">
                <h4 style="margin: 0 0 5px; font-size: 15px; color: #2d3748;">
                    Desain UI/UX: Fondasi Kreativitas
                </h4>
                <p style="margin: 0 0 8px; font-size: 13px; color: #718096;">Maya Putri</p>
                <span style="display: inline-block; background: #fff3e0; color: #e65100; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">
                    STOK DI-HOLD
                </span>
                <p style="margin: 8px 0 0; font-size: 12px; color: #a0aec0;">
                    <i class="fa-regular fa-clock"></i> Tersedia hingga: Besok, 09:00
                </p>
            </div>
            <button style="background: none; border: none; cursor: pointer; color: #e53e3e; font-size: 18px; padding: 5px;">
                <i class="fa-solid fa-trash"></i>
            </button>
        </div>

        {{-- TOMBOL TAMBAH BUKU --}}
        <div style="margin-top: 20px;">
            <a href="{{ route('member.katalog') }}" 
                style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border: 2px solid #1fcf8e; border-radius: 30px; color: #1fcf8e; text-decoration: none; font-size: 14px; font-weight: 500;">
                <i class="fa-solid fa-arrow-left"></i> Tambah Buku Lagi
            </a>
        </div>

    </div>

    {{-- ===== KOLOM KANAN: Ringkasan Peminjaman ===== --}}
    <div style="flex: 1; background: white; border-radius: 15px; padding: 25px; border: 1px solid #edf2f7;">

        <h3 style="margin: 0 0 20px; font-size: 18px; color: #2d3748;">Ringkasan Peminjaman</h3>

        {{-- DETAIL RINGKASAN --}}
        <div style="display: flex; flex-direction: column; gap: 14px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 14px; color: #718096;">Jumlah Buku</span>
                <strong style="font-size: 14px; color: #2d3748;">2 Buku</strong>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 14px; color: #718096;">Durasi Pinjam</span>
                <strong style="font-size: 14px; color: #2d3748;">7 hari</strong>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 14px; color: #718096;">Tanggal Mulai</span>
                <strong style="font-size: 14px; color: #2d3748;">30 Apr 2026</strong>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 14px; color: #718096;">Batas Pengembalian</span>
                <strong style="font-size: 14px; color: #2d3748;">07 Mei 2026</strong>
            </div>
        </div>

        {{-- ALERT WARNING --}}
        <div style="background: #fffbeb; border: 1px solid #f6ad55; border-radius: 10px; padding: 12px 15px; margin-bottom: 20px; display: flex; gap: 10px; align-items: flex-start;">
            <i class="fa-solid fa-triangle-exclamation" style="color: #d97706; margin-top: 2px; flex-shrink: 0;"></i>
            <p style="margin: 0; font-size: 12px; color: #92400e; line-height: 1.5;">
                <strong>Penting:</strong> Stok buku di-hold selama proses. Selesaikan booking dalam 24 jam untuk menjamin ketersediaan.
            </p>
        </div>

        {{-- TOMBOL LANJUTKAN BOOKING --}}
        <button style="width: 100%; padding: 14px; background: #1fcf8e; color: white; border: none; border-radius: 30px; font-size: 15px; font-weight: 600; cursor: pointer; margin-bottom: 12px; display: flex; align-items: center; justify-content: center; gap: 8px;">
            Lanjutkan Booking <i class="fa-solid fa-arrow-right"></i>
        </button>

        {{-- TOMBOL TAMBAH BUKU LAGI --}}
        <a href="{{ route('member.katalog') }}" 
            style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 13px; border: 2px solid #1fcf8e; border-radius: 30px; color: #1fcf8e; text-decoration: none; font-size: 15px; font-weight: 500; box-sizing: border-box;">
            <i class="fa-solid fa-arrow-left"></i> Tambah Buku Lagi
        </a>

        {{-- POWERED BY --}}
        <p style="text-align: center; margin-top: 20px; font-size: 11px; color: #cbd5e0; letter-spacing: 1px; text-transform: uppercase;">
            Powered by SIPUS Core
        </p>

    </div> -->

</div>

@endsection