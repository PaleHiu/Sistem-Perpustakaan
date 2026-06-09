@extends('layouts.member_layout')

@section('title', 'Booking Sukses')

@section('content')

<div style="display: flex; justify-content: center; padding: 10px 0 40px;">
<div style="background: white; border-radius: 28px; box-shadow: 0 12px 40px rgba(0,0,0,0.10); padding: 40px 44px 36px; width: 100%; max-width: 580px; display: flex; flex-direction: column; align-items: center;">

    {{-- SUCCESS ICON --}}
    <div style="margin-bottom: 18px;">
        <div style="width: 68px; height: 68px; border-radius: 50%; background: #1fcf8e; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 28px rgba(31,207,142,0.35);">
            <i class="fa-solid fa-check" style="font-size: 28px; color: white;"></i>
        </div>
    </div>

    <h1 style="font-size: 22px; font-weight: 800; color: #1fcf8e; text-align: center; margin-bottom: 6px;">Booking Berhasil!</h1>
    <p style="font-size: 13px; color: #718096; text-align: center; margin-bottom: 22px;">Tunjukkan kode OTP berikut ke petugas perpustakaan</p>

    {{-- ===================== DAFTAR BUKU ===================== --}}
    <div style="width: 100%; border: 1px solid #edf2f7; border-radius: 14px; overflow: hidden; margin-bottom: 26px;">
        @foreach($peminjaman->detailPeminjaman as $detail)
        <div style="display: flex; align-items: center; gap: 14px; padding: 14px 18px; {{ !$loop->last ? 'border-bottom: 1px solid #edf2f7;' : '' }}">

            {{-- Cover buku --}}
            @if($detail->buku?->cover)
                <div style="width: 44px; height: 56px; min-width: 44px; border-radius: 6px; overflow: hidden; flex-shrink: 0;">
                    <img src="{{ asset('storage/' . $detail->buku->cover) }}"
                        style="width:100%;height:100%;object-fit:cover;">
                </div>
            @else
                <div style="width: 44px; height: 56px; min-width: 44px; border-radius: 6px; background: linear-gradient(135deg, #1f3c45, #2d6a5a); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fa-solid fa-book" style="color: rgba(255,255,255,0.8); font-size: 16px;"></i>
                </div>
            @endif

            <div style="flex: 1; min-width: 0;">
                <p style="font-size: 13px; font-weight: 700; color: #1a202c; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    {{ $detail->buku?->judul ?? '-' }}
                </p>
                <p style="font-size: 12px; color: #718096; margin-top: 3px;">{{ $detail->buku?->penulis ?? '-' }}</p>
            </div>
            <div style="text-align: right; flex-shrink: 0;">
                <p style="font-size: 10px; font-weight: 700; letter-spacing: 0.08em; color: #a0aec0;">KEMBALI</p>
                <p style="font-size: 13px; font-weight: 700; color: #1a202c; margin-top: 2px;">
                    {{ $peminjaman->batas_pengembalian
                        ? \Carbon\Carbon::parse($peminjaman->batas_pengembalian)->format('d M Y')
                        : now()->addDays(7)->format('d M Y') }}
                </p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- OTP --}}
    <p style="font-size: 11px; font-weight: 700; letter-spacing: 0.10em; color: #a0aec0; text-align: center; margin-bottom: 10px;">KODE OTP KAMU</p>
    <div style="width: 100%; background: #e8f5f0; border: 2px solid #c8e9dc; border-radius: 14px; display: flex; align-items: center; justify-content: center; padding: 22px 20px; margin-bottom: 14px;">
        <span id="otpCode" style="font-size: 46px; font-weight: 800; color: #1fcf8e; letter-spacing: 0.18em; user-select: all; font-family: monospace;">
            {{ $peminjaman->kode_otp }}
        </span>
    </div>

    <button id="btnCopy" onclick="copyOTP()"
            style="display: inline-flex; align-items: center; gap: 7px; border: 1.5px solid #1fcf8e; color: #1fcf8e; border-radius: 24px; padding: 8px 22px; font-size: 13px; font-weight: 600; background: transparent; margin-bottom: 22px; cursor: pointer;">
        <i class="fa-regular fa-copy"></i>
        <span id="btnCopyText">Salin Kode</span>
    </button>

    {{-- TIMER --}}
    @php
        $sisaDetik = max(0, (int) now()->diffInSeconds(\Carbon\Carbon::parse($peminjaman->otp_expired_at), false));
    @endphp
    <div style="width: 100%; background: #fffbeb; border: 1.5px solid #fde68a; border-radius: 14px; padding: 18px 20px 16px; text-align: center; margin-bottom: 28px;">
        <div style="display: flex; align-items: center; justify-content: center; gap: 6px; margin-bottom: 10px;">
            <i class="fa-regular fa-clock" style="color: #d97706; font-size: 14px;"></i>
            <span style="font-size: 11px; font-weight: 700; letter-spacing: 0.10em; color: #d97706;">BERLAKU SELAMA:</span>
        </div>
        <div style="display: flex; align-items: flex-end; justify-content: center; gap: 4px;">
            <div style="display: flex; flex-direction: column; align-items: center;">
                <span id="timerJam" style="font-size: 36px; font-weight: 800; color: #d97706; line-height: 1; min-width: 56px; text-align: center;">00</span>
                <span style="font-size: 10px; font-weight: 700; letter-spacing: 0.08em; color: #d97706; opacity: 0.7; margin-top: 4px;">JAM</span>
            </div>
            <span style="font-size: 30px; font-weight: 800; color: #d97706; line-height: 1; padding-bottom: 14px; opacity: 0.6;">:</span>
            <div style="display: flex; flex-direction: column; align-items: center;">
                <span id="timerMenit" style="font-size: 36px; font-weight: 800; color: #d97706; line-height: 1; min-width: 56px; text-align: center;">00</span>
                <span style="font-size: 10px; font-weight: 700; letter-spacing: 0.08em; color: #d97706; opacity: 0.7; margin-top: 4px;">MENIT</span>
            </div>
            <span style="font-size: 30px; font-weight: 800; color: #d97706; line-height: 1; padding-bottom: 14px; opacity: 0.6;">:</span>
            <div style="display: flex; flex-direction: column; align-items: center;">
                <span id="timerDetik" style="font-size: 36px; font-weight: 800; color: #d97706; line-height: 1; min-width: 56px; text-align: center;">00</span>
                <span style="font-size: 10px; font-weight: 700; letter-spacing: 0.08em; color: #d97706; opacity: 0.7; margin-top: 4px;">DETIK</span>
            </div>
        </div>
    </div>

    {{-- STEPS --}}
    <div style="display: flex; align-items: flex-start; justify-content: center; width: 100%; margin-bottom: 28px;">
        @foreach([['1','Datang ke perpustakaan','(Tunjukkan layar ini)'],['2','Serahkan kode OTP','(Petugas validasi)'],['3','Terima buku fisik','(Selesai!)']] as $step)
        <div style="display: flex; flex-direction: column; align-items: center; gap: 8px; flex: 1; text-align: center;">
            <div style="width: 34px; height: 34px; border-radius: 50%; background: #1fcf8e; color: white; font-size: 14px; font-weight: 800; display: flex; align-items: center; justify-content: center;">{{ $step[0] }}</div>
            <p style="font-size: 12px; font-weight: 700; color: #1a202c; line-height: 1.3;">{{ $step[1] }}</p>
            <p style="font-size: 11px; color: #a0aec0; font-style: italic; margin-top: -4px;">{{ $step[2] }}</p>
        </div>
        @if(!$loop->last)
        <div style="flex-shrink: 0; width: 40px; height: 2px; background: #edf2f7; margin-top: 16px;"></div>
        @endif
        @endforeach
    </div>

    <a href="{{ route('member.peminjaman') }}"
        style="display: block; width: 100%; background: #1fcf8e; color: white; font-size: 15px; font-weight: 700; text-align: center; border-radius: 99px; padding: 16px 20px; text-decoration: none; box-shadow: 0 6px 20px rgba(31,207,142,0.30);">
        Lihat Peminjaman Saya
    </a>

</div>
</div>

<script>
let totalSeconds = {{ $sisaDetik }};

function pad(n) { return String(n).padStart(2, '0'); }

function tick() {
    if (totalSeconds <= 0) {
        document.getElementById('timerJam').textContent   = '00';
        document.getElementById('timerMenit').textContent = '00';
        document.getElementById('timerDetik').textContent = '00';
        return;
    }
    totalSeconds--;
    const h = Math.floor(totalSeconds / 3600);
    const m = Math.floor((totalSeconds % 3600) / 60);
    const s = Math.floor(totalSeconds % 60);
    document.getElementById('timerJam').textContent   = pad(h);
    document.getElementById('timerMenit').textContent = pad(m);
    document.getElementById('timerDetik').textContent = pad(s);
    setTimeout(tick, 1000);
}
tick();

function copyOTP() {
    const code = document.getElementById('otpCode').textContent.trim();
    navigator.clipboard.writeText(code).then(() => {
        const btn  = document.getElementById('btnCopy');
        const text = document.getElementById('btnCopyText');
        text.textContent     = 'Tersalin!';
        btn.style.background = '#1fcf8e';
        btn.style.color      = 'white';
        setTimeout(() => {
            text.textContent     = 'Salin Kode';
            btn.style.background = 'transparent';
            btn.style.color      = '#1fcf8e';
        }, 2000);
    });
}
</script>

@endsection