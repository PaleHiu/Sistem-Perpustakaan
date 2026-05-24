@extends('layouts.member_layout')

@section('title', 'Peminjaman Saya')

@section('content')

{{-- SEARCH & FILTER --}}
<div style="background: white; padding: 15px 20px; border-radius: 15px; display: flex; align-items: center; gap: 15px; margin-bottom: 25px; border: 1px solid #edf2f7; flex-wrap: wrap;">
    <div style="position: relative; flex: 1; min-width: 220px;">
        <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #cbd5e0; font-size: 13px;"></i>
        <input type="text" id="searchPeminjaman" oninput="filterPeminjaman()"
               placeholder="Cari judul buku..."
               style="width: 100%; padding: 10px 10px 10px 40px; border-radius: 20px; border: 1px solid #edf2f7; background: #f8fafc; font-size: 13px; outline: none;">
    </div>
    <select id="filterStatus" onchange="filterPeminjaman()"
            style="padding: 10px 16px; border-radius: 20px; border: 1px solid #edf2f7; background: #f8fafc; font-size: 13px; color: #4a5568; outline: none; cursor: pointer;">
        <option value="">Semua Status</option>
        <option value="Menunggu OTP">Menunggu OTP</option>
        <option value="Dipinjam">Dipinjam</option>
        <option value="Selesai">Selesai</option>
        <option value="Batal">Batal</option>
    </select>
    <span style="font-size: 13px; color: #718096; white-space: nowrap;">
        Total: <strong>{{ count($peminjaman ?? []) }} Transaksi</strong>
    </span>
</div>

{{-- LIST PEMINJAMAN --}}
<div id="list-peminjaman">
@forelse($peminjaman ?? [] as $trx)
@php
    $status     = $trx->status_transaksi;
    $otpExpired = $trx->otp_expired_at
        ? \Carbon\Carbon::parse($trx->otp_expired_at)->isPast()
        : false;
    if ($status === 'Menunggu OTP' && $otpExpired) $status = 'Kadaluarsa';

    $terlambat = $status === 'Dipinjam'
        && $trx->batas_pengembalian
        && \Carbon\Carbon::parse($trx->batas_pengembalian)->isPast();
    if ($terlambat) $status = 'Terlambat';

    $firstBuku  = $trx->detailPeminjaman->first()?->buku;
    $judulBuku  = $firstBuku?->judul ?? 'Buku tidak diketahui';
    $jumlahBuku = $trx->detailPeminjaman->count();

    $borderLeft = match($status) {
        'Dipinjam'     => 'border-left: 4px solid #4299e1;',
        'Terlambat'    => 'border-left: 4px solid #e53e3e;',
        'Selesai'      => '',
        'Menunggu OTP' => 'border-left: 4px solid #e65100;',
        default        => '',
    };

    $badgeBg    = match($status) {
        'Menunggu OTP' => 'background:#fff3e0; color:#e65100;',
        'Dipinjam'     => 'background:#ebf8ff; color:#3182ce;',
        'Selesai'      => 'background:#f0fff4; color:#38a169;',
        'Terlambat'    => 'background:#fff5f5; color:#e53e3e;',
        default        => 'background:#f7fafc; color:#718096;',
    };

    // Hitung sisa waktu OTP
    $otpSisaDetik = 0;
    if ($status === 'Menunggu OTP' && $trx->otp_expired_at) {
        $otpSisaDetik = max(0, (int) now()->diffInSeconds(\Carbon\Carbon::parse($trx->otp_expired_at), false));
    }
@endphp

<div class="card-peminjaman"
     data-status="{{ $status }}"
     data-judul="{{ strtolower($judulBuku) }}"
     style="background: white; border-radius: 15px; padding: 20px 25px; margin-bottom: 20px; border: 1px solid #edf2f7; {{ $borderLeft }}">

    {{-- HEADER CARD --}}
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 18px;">
        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <span style="font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 20px; letter-spacing: 0.5px; {{ $badgeBg }}">
                {{ strtoupper($status) }}
            </span>
            <strong style="font-size: 15px; color: #2d3748;">
                TRX-{{ str_pad($trx->id, 4, '0', STR_PAD_LEFT) }}
            </strong>
            <span style="font-size: 13px; color: #a0aec0;">
                • {{ \Carbon\Carbon::parse($trx->waktu_booking)->format('d M Y, H:i') }}
            </span>
        </div>
        <div style="text-align: right;">
            @if($trx->batas_pengembalian)
                <p style="font-size: 11px; color: {{ $terlambat ? '#e53e3e' : '#a0aec0' }}; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 3px; font-weight: 600;">
                    Batas Kembali
                </p>
                <strong style="font-size: 15px; color: {{ $terlambat ? '#e53e3e' : '#2d3748' }};">
                    {{ \Carbon\Carbon::parse($trx->batas_pengembalian)->format('d M Y') }}
                </strong>
            @endif
        </div>
    </div>

    {{-- BODY CARD --}}
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            {{-- Cover buku --}}
            <div style="display: flex;">
                @foreach($trx->detailPeminjaman->take(2) as $detail)
                <div style="width: 50px; height: 70px; background: linear-gradient(135deg, #1f3c45, #2d6a5a); border-radius: 6px; display: flex; align-items: center; justify-content: center; border: 2px solid white; {{ !$loop->first ? 'margin-left: -15px;' : '' }}">
                    <i class="fa-solid fa-book" style="color: rgba(255,255,255,0.6); font-size: 16px;"></i>
                </div>
                @endforeach
            </div>
            <div>
                <p style="font-size: 14px; font-weight: 600; color: #2d3748; margin-bottom: 3px;">
                    {{ $judulBuku }}
                    @if($jumlahBuku > 1)
                        <span style="font-size: 12px; color: #718096; font-weight: 400;">+ {{ $jumlahBuku - 1 }} lainnya</span>
                    @endif
                </p>
                <p style="font-size: 12px; color: #718096;">{{ $jumlahBuku }} Judul Buku</p>
            </div>
        </div>

        {{-- Info kanan --}}
        @if($status === 'Menunggu OTP')
        <div style="text-align: right;">
            <p style="font-size: 11px; color: #e65100; letter-spacing: 0.5px; text-transform: uppercase; font-weight: 600; margin-bottom: 3px;">OTP Berlaku</p>
            <strong id="timer-{{ $trx->id }}" style="font-size: 22px; color: #e65100; font-family: monospace;">--:--:--</strong>
            <script>
                (function() {
                    let sisa = {{ $otpSisaDetik }};
                    const el = document.getElementById('timer-{{ $trx->id }}');
                    function tick() {
                        if (sisa <= 0) { el.textContent = 'KADALUARSA'; el.style.color = '#a0aec0'; return; }
                        sisa--;
                        const h = Math.floor(sisa/3600);
                        const m = Math.floor((sisa%3600)/60);
                        const s = sisa % 60;
                        el.textContent = String(Math.floor(h)).padStart(2,'0')+':'+String(Math.floor(m)).padStart(2,'0')+':'+String(Math.floor(s)).padStart(2,'0');
                        setTimeout(tick, 1000);
                    }
                    tick();
                })();
            </script>
        </div>
        @elseif($status === 'Terlambat' || ($trx->total_denda ?? 0) > 0)
        <div style="text-align: right;">
            <p style="font-size: 11px; color: #e53e3e; letter-spacing: 0.5px; text-transform: uppercase; font-weight: 600; margin-bottom: 3px;">Total Denda</p>
            <strong style="font-size: 22px; color: #e53e3e;">Rp {{ number_format($trx->total_denda ?? 0, 0, ',', '.') }}</strong>
        </div>
        @endif
    </div>

    {{-- FOOTER CARD --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 18px; padding-top: 15px; border-top: 1px solid #f7fafc;">
        <span style="font-size: 13px; color: {{ match($status) {
            'Menunggu OTP' => '#e65100',
            'Dipinjam'     => '#4299e1',
            'Terlambat'    => '#e53e3e',
            'Selesai'      => '#38a169',
            default        => '#718096'
        } }};">
            <i class="fa-solid fa-circle" style="font-size: 8px; margin-right: 5px;"></i>
            {{ match($status) {
                'Menunggu OTP' => 'OTP aktif — segera ambil buku',
                'Dipinjam'     => 'Sedang dipinjam',
                'Terlambat'    => 'Buku terlambat dikembalikan',
                'Selesai'      => 'Transaksi selesai',
                'Kadaluarsa'   => 'OTP sudah kadaluarsa',
                default        => $status
            } }}
        </span>

        @if($status === 'Menunggu OTP')
            <a href="{{ route('member.peminjaman.otp', ['id' => $trx->id]) }}"
               style="font-size: 13px; color: #1fcf8e; text-decoration: none; font-weight: 500;">
                Lihat OTP →
            </a>
        @elseif($status !== 'Selesai')
            <span style="font-size: 13px; color: #a0aec0;">{{ strtoupper($status) }}</span>
        @else
            <a href="{{ route('member.riwayat') }}"
               style="font-size: 13px; color: #1fcf8e; text-decoration: none; font-weight: 500;">
                Lihat Riwayat →
            </a>
        @endif
    </div>

</div>
@empty
<div style="background: white; border-radius: 15px; padding: 40px; text-align: center; border: 1px solid #edf2f7;">
    <i class="fa-regular fa-folder-open" style="font-size: 40px; color: #e2e8f0; margin-bottom: 12px; display: block;"></i>
    <p style="color: #a0aec0; font-size: 14px;">Belum ada transaksi peminjaman</p>
    <a href="{{ route('member.katalog') }}" style="display: inline-block; margin-top: 15px; padding: 10px 24px; background: #1fcf8e; color: white; border-radius: 20px; text-decoration: none; font-size: 14px;">
        Lihat Katalog
    </a>
</div>
@endforelse
</div>

{{-- PAGINATION --}}
<div style="background: white; border-radius: 15px; padding: 15px 25px; border: 1px solid #edf2f7; display: flex; justify-content: space-between; align-items: center; margin-top: 10px;">
    <span style="font-size: 13px; color: #718096;">
        Menampilkan <strong>{{ count($peminjaman ?? []) }}</strong> transaksi
    </span>
    <div style="display: flex; gap: 8px;">
        <button style="width: 34px; height: 34px; border-radius: 8px; border: 1px solid #edf2f7; background: white; cursor: pointer; color: #718096;">‹</button>
        <button style="width: 34px; height: 34px; border-radius: 8px; border: none; background: #1fcf8e; color: white; font-weight: 600; cursor: pointer;">1</button>
        <button style="width: 34px; height: 34px; border-radius: 8px; border: 1px solid #edf2f7; background: white; cursor: pointer; color: #718096;">›</button>
    </div>
</div>

<script>
function filterPeminjaman() {
    const keyword = document.getElementById('searchPeminjaman').value.toLowerCase();
    const status  = document.getElementById('filterStatus').value;
    document.querySelectorAll('.card-peminjaman').forEach(card => {
        const judul     = card.getAttribute('data-judul') || '';
        const cardStatus = card.getAttribute('data-status') || '';
        const cocok     = judul.includes(keyword) && (status === '' || cardStatus === status);
        card.style.display = cocok ? '' : 'none';
    });
}
</script>

@endsection