@extends('layouts.member_layout')

@section('title', 'Dashboard Member')

@section('content')

{{-- ALERT DENDA --}}
@php
    $adaTerlambat = ($pinjamanAktif ?? collect())->filter(function($trx) {
        return $trx->status_transaksi === 'Dipinjam'
            && $trx->batas_pengembalian
            && \Carbon\Carbon::parse($trx->batas_pengembalian)->isPast();
    })->count() > 0;

    $adaDendaBelumBayar = ($pinjamanAktif ?? collect())->filter(function($trx) {
        return ($trx->total_denda ?? 0) > 0
            && $trx->status_transaksi !== 'Selesai';
    })->sum('total_denda');
@endphp

@if($adaTerlambat)
<div style="background: #fff5f5; border: 1px solid #fed7d7; border-radius: 12px; padding: 15px 20px; margin-bottom: 25px; display: flex; align-items: center; gap: 15px;">
    <i class="fa-solid fa-triangle-exclamation" style="color: #e53e3e;"></i>
    <span style="color: #e53e3e;">Kamu memiliki buku yang <b>terlambat dikembalikan</b>. Segera kembalikan ke perpustakaan.</span>
</div>
@elseif($adaDendaBelumBayar > 0)
<div style="background: #fffbeb; border: 1px solid #f6ad55; border-radius: 12px; padding: 15px 20px; margin-bottom: 25px; display: flex; align-items: center; gap: 15px;">
    <i class="fa-solid fa-triangle-exclamation" style="color: #d97706;"></i>
    <span>Kamu memiliki denda belum lunas — <b>Rp {{ number_format($adaDendaBelumBayar, 0, ',', '.') }}</b>.</span>
</div>
@endif

<div style="display: grid; grid-template-columns: 1fr 320px; gap: 25px;">

    {{-- KOLOM KIRI --}}
    <div style="display: flex; flex-direction: column; gap: 25px;">

        {{-- PINJAMAN AKTIF --}}
        <div style="background: white; border-radius: 15px; padding: 20px; border: 1px solid #edf2f7;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="margin: 0; font-size: 16px;">Pinjaman Aktif</h3>
                <a href="{{ route('member.peminjaman') }}" style="color: #1fcf8e; font-size: 13px; text-decoration: none;">Lihat Semua →</a>
            </div>

            @forelse($pinjamanAktif ?? [] as $trx)
            @php
                $firstBuku  = $trx->detailPeminjaman->first()?->buku;
                $judul      = $firstBuku?->judul ?? 'Buku tidak diketahui';
                $penulis    = $firstBuku?->penulis ?? '-';
                $tglPinjam  = $trx->tanggal_pinjam
                    ? \Carbon\Carbon::parse($trx->tanggal_pinjam)->format('d M Y')
                    : '-';
                $tglKembali = $trx->batas_pengembalian
                    ? \Carbon\Carbon::parse($trx->batas_pengembalian)->format('d M Y')
                    : '-';

                $terlambat = $trx->status_transaksi === 'Dipinjam'
                    && $trx->batas_pengembalian
                    && \Carbon\Carbon::parse($trx->batas_pengembalian)->isPast();

                $hariSisa = $trx->batas_pengembalian
                    ? (int) now()->diffInDays(\Carbon\Carbon::parse($trx->batas_pengembalian), false)
                    : null;
            @endphp
            <div style="display: flex; align-items: center; gap: 15px; padding: 15px 0; border-bottom: 1px solid #f7fafc;">
                @if($firstBuku?->cover)
                    <div style="width: 55px; height: 75px; border-radius: 6px; overflow: hidden; flex-shrink: 0;">
                        <img src="{{ asset('storage/' . $firstBuku->cover) }}"
                            style="width:100%;height:100%;object-fit:cover;">
                    </div>
                @else
                    <div style="width: 55px; height: 75px; background: linear-gradient(135deg, #1f3c45, #2d6a5a); border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fa-solid fa-book" style="color: rgba(255,255,255,0.6); font-size: 20px;"></i>
                    </div>
                @endif
                <div style="flex: 1;">
                    <h4 style="margin: 0 0 3px; font-size: 14px; color: #2d3748;">{{ $judul }}</h4>
                    <p style="margin: 0 0 5px; font-size: 12px; color: #718096;">{{ $penulis }}</p>
                    <small style="color: #a0aec0;">
                        {{ $trx->status_transaksi === 'Menunggu OTP' ? 'OTP: '.$trx->kode_otp : 'Dipinjam: '.$tglPinjam.' - '.$tglKembali }}
                    </small>
                    <div style="height: 4px; background: {{ $terlambat ? '#fed7d7' : '#c6f6d5' }}; border-radius: 4px; margin-top: 8px;"></div>
                </div>
                @if($trx->status_transaksi === 'Menunggu OTP')
                    <span style="background: #fff3e0; color: #e65100; font-size: 11px; padding: 4px 8px; border-radius: 6px; font-weight: bold; white-space: nowrap;">MENUNGGU OTP</span>
                @elseif($terlambat)
                    <span style="background: #fff5f5; color: #e53e3e; font-size: 11px; padding: 4px 8px; border-radius: 6px; font-weight: bold; white-space: nowrap;">TERLAMBAT</span>
                @elseif($hariSisa !== null)
                    <span style="background: #f0fff4; color: #38a169; font-size: 11px; padding: 4px 8px; border-radius: 6px; font-weight: bold; white-space: nowrap;">{{ $hariSisa }} HARI LAGI</span>
                @endif
            </div>
            @empty
            <div style="text-align: center; padding: 30px 0;">
                <i class="fa-regular fa-folder-open" style="font-size: 40px; color: #e2e8f0; margin-bottom: 12px; display: block;"></i>
                <p style="color: #a0aec0; font-size: 14px;">Belum ada pinjaman aktif</p>
            </div>
            @endforelse

            <a href="{{ route('member.katalog') }}"
                style="display: block; text-align: center; margin-top: 15px; padding: 12px; border: 2px solid #1fcf8e; border-radius: 10px; color: #1fcf8e; text-decoration: none; font-weight: 500;">
                + Pinjam Buku Baru
            </a>
        </div>

        {{-- AKTIVITAS TERBARU --}}
        <div style="background: white; border-radius: 15px; padding: 20px; border: 1px solid #edf2f7;">
            <h3 style="margin: 0 0 20px; font-size: 16px;">Aktivitas Terbaru</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid #edf2f7;">
                        <th style="text-align: left; padding: 8px 0; font-size: 11px; color: #a0aec0; text-transform: uppercase; letter-spacing: 1px;">Buku</th>
                        <th style="text-align: left; padding: 8px 0; font-size: 11px; color: #a0aec0; text-transform: uppercase; letter-spacing: 1px;">Tanggal</th>
                        <th style="text-align: right; padding: 8px 0; font-size: 11px; color: #a0aec0; text-transform: uppercase; letter-spacing: 1px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($aktivitasTerbaru ?? [] as $trx)
                    @php
                        $judulAktivitas = $trx->detailPeminjaman->first()?->buku?->judul ?? '-';
                        $tglAktivitas   = $trx->waktu_booking
                            ? \Carbon\Carbon::parse($trx->waktu_booking)->format('d M Y')
                            : '-';
                        $stAktivitas    = $trx->status_transaksi;
                        $terlambatAkt   = $stAktivitas === 'Dipinjam'
                            && $trx->batas_pengembalian
                            && \Carbon\Carbon::parse($trx->batas_pengembalian)->isPast();
                        if ($terlambatAkt) $stAktivitas = 'Terlambat';
                    @endphp
                    <tr style="border-bottom: 1px solid #f7fafc;">
                        <td style="padding: 12px 0; font-size: 14px; color: #2d3748;">{{ $judulAktivitas }}</td>
                        <td style="padding: 12px 0; font-size: 13px; color: #718096;">{{ $tglAktivitas }}</td>
                        <td style="padding: 12px 0; text-align: right;">
                            @if($stAktivitas === 'Selesai')
                                <span style="background: #f0fff4; color: #38a169; font-size: 11px; padding: 3px 10px; border-radius: 20px;">SELESAI</span>
                            @elseif($stAktivitas === 'Dipinjam')
                                <span style="background: #ebf8ff; color: #3182ce; font-size: 11px; padding: 3px 10px; border-radius: 20px;">DIPINJAM</span>
                            @elseif($stAktivitas === 'Terlambat')
                                <span style="background: #fff5f5; color: #e53e3e; font-size: 11px; padding: 3px 10px; border-radius: 20px;">TERLAMBAT</span>
                            @elseif($stAktivitas === 'Menunggu OTP')
                                <span style="background: #fff3e0; color: #e65100; font-size: 11px; padding: 3px 10px; border-radius: 20px;">MENUNGGU OTP</span>
                            @else
                                <span style="background: #f7fafc; color: #718096; font-size: 11px; padding: 3px 10px; border-radius: 20px;">{{ strtoupper($stAktivitas) }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="padding: 30px 0; text-align: center; color: #a0aec0; font-size: 14px;">
                            <i class="fa-regular fa-clock" style="font-size: 30px; display: block; margin-bottom: 8px; color: #e2e8f0;"></i>
                            Belum ada aktivitas
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    {{-- KOLOM KANAN --}}
    <div style="display: flex; flex-direction: column; gap: 20px;">

        <div style="background: white; border-radius: 15px; padding: 20px; border: 1px solid #edf2f7; border-bottom: 3px solid #1fcf8e;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <p style="margin: 0 0 8px; font-size: 13px; color: #718096;">Sedang Dipinjam</p>
                    <h2 style="margin: 0; font-size: 36px; color: #2d3748;">{{ $sedangDipinjam ?? 0 }}</h2>
                    <small style="color: #a0aec0;">Buku</small>
                </div>
                <span style="background: #f0fff4; color: #38a169; font-size: 11px; padding: 4px 10px; border-radius: 20px;">Aktif</span>
            </div>
        </div>

        <div style="background: white; border-radius: 15px; padding: 20px; border: 1px solid #edf2f7; border-bottom: 3px solid #4299e1;">
            <div>
                <p style="margin: 0 0 8px; font-size: 13px; color: #718096;">Total Riwayat</p>
                <h2 style="margin: 0; font-size: 36px; color: #2d3748;">{{ $totalRiwayat ?? 0 }}</h2>
                <small style="color: #a0aec0;">Buku</small>
            </div>
        </div>

        <div style="background: white; border-radius: 15px; padding: 20px; border: 1px solid #edf2f7; border-bottom: 3px solid #e53e3e;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <p style="margin: 0 0 8px; font-size: 13px; color: #718096;">Total Denda</p>
                    <h2 style="margin: 0; font-size: 28px; color: #e53e3e;">
                        Rp {{ number_format($totalDenda ?? 0, 0, ',', '.') }}
                    </h2>
                </div>
                @if(($totalDenda ?? 0) > 0)
                <span style="background: #fff5f5; color: #e53e3e; font-size: 11px; padding: 4px 10px; border-radius: 20px;">WAJIB BAYAR</span>
                @endif
            </div>
        </div>

        <div style="background: #1f3c45; border-radius: 15px; padding: 20px; color: white;">
            <h4 style="margin: 0 0 10px; font-size: 12px; letter-spacing: 1px; color: #1fcf8e;">INFORMASI MEMBER</h4>
            <p style="margin: 0 0 5px; font-size: 12px; opacity: 0.7;">Terdaftar sejak</p>
            <p style="margin: 0 0 15px; font-size: 14px; font-weight: 600;">
                {{ Auth::user()->created_at->format('d M Y') }}
            </p>
            <p style="margin: 0 0 5px; font-size: 12px; opacity: 0.7;">Status Akun</p>
            <p style="margin: 0 0 15px; font-size: 14px; font-weight: 600; color: #1fcf8e;">
                {{ Auth::user()->anggota->status_verifikasi ?? 'Incomplete' }}
            </p>
            <a href="{{ route('member.profil') }}"
                style="display: block; text-align: center; background: #1fcf8e; color: white; border: none; padding: 12px; border-radius: 10px; font-weight: 600; cursor: pointer; text-decoration: none; font-size: 14px;">
                Lihat Profil
            </a>
        </div>

    </div>

</div>

@endsection