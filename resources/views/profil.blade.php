@extends('layouts.member_layout')

@section('title', 'Profil Member')

@section('content')

@php
    $anggota       = Auth::user()->anggota;
    $namaLengkap   = $anggota?->nama_lengkap ?? '';
    $inisial       = collect(explode(' ', $namaLengkap))->map(fn($w) => strtoupper(substr($w,0,1)))->take(2)->join('');
    $totalDipinjam = $anggota ? \App\Models\Peminjam::where('anggota_id', $anggota->id)->count() : 0;
    $statusVerif   = $anggota?->status_verifikasi ?? 'Incomplete';
@endphp

{{-- NOTIFIKASI --}}
@if(session('success'))
<div style="background:#f0fff4;border:1px solid #c6f6d5;border-radius:10px;padding:12px 20px;margin-bottom:20px;color:#38a169;display:flex;align-items:center;gap:10px;">
    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
</div>
@endif
@if(session('success_password'))
<div style="background:#f0fff4;border:1px solid #c6f6d5;border-radius:10px;padding:12px 20px;margin-bottom:20px;color:#38a169;display:flex;align-items:center;gap:10px;">
    <i class="fa-solid fa-circle-check"></i> {{ session('success_password') }}
</div>
@endif
@if(session('error_password'))
<div style="background:#fff5f5;border:1px solid #fed7d7;border-radius:10px;padding:12px 20px;margin-bottom:20px;color:#e53e3e;display:flex;align-items:center;gap:10px;">
    <i class="fa-solid fa-circle-xmark"></i> {{ session('error_password') }}
</div>
@endif
@if($errors->any())
<div style="background:#fff5f5;border:1px solid #fed7d7;border-radius:10px;padding:12px 20px;margin-bottom:20px;color:#e53e3e;">
    <i class="fa-solid fa-circle-xmark"></i>
    @foreach($errors->all() as $error)<span>{{ $error }}</span>@endforeach
</div>
@endif

<div style="display:flex;gap:25px;align-items:flex-start;">

    {{-- KOLOM KIRI --}}
    <div style="width:260px;flex-shrink:0;display:flex;flex-direction:column;gap:20px;">

        {{-- KARTU PROFIL --}}
        <div style="background:white;border-radius:15px;padding:25px 20px;border:1px solid #edf2f7;text-align:center;">

            {{-- Foto profil + tombol kamera --}}
            <div style="position:relative;width:80px;margin:0 auto 15px;">
                @if($anggota?->foto_profil)
                    <img src="{{ asset('storage/' . $anggota->foto_profil) }}"
                        style="width:80px;height:80px;border-radius:50%;object-fit:cover;">
                @else
                    <div style="width:80px;height:80px;background:#1fcf8e;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:26px;font-weight:700;color:white;">
                        {{ $inisial ?: 'U' }}
                    </div>
                @endif
                <label for="input-foto-profil"
                    style="position:absolute;bottom:0;right:0;width:26px;height:26px;background:#1fcf8e;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;border:2px solid white;">
                    <i class="fa-solid fa-camera" style="font-size:11px;color:white;"></i>
                </label>
            </div>

            <h3 style="font-size:17px;font-weight:700;color:#2d3748;margin-bottom:4px;">
                {{ $namaLengkap ?: 'Nama belum diisi' }}
            </h3>
            <p style="font-size:13px;color:#718096;margin-bottom:8px;">{{ Auth::user()->email }}</p>
            <p style="font-size:12px;color:#718096;margin-bottom:12px;">NIK: {{ $anggota?->nik ?? 'Belum diisi' }}</p>

            {{-- Badge status verifikasi --}}
            @php
                $badgeColor = match($statusVerif) {
                    'Approved' => ['bg'=>'#f0fff4','color'=>'#38a169','icon'=>'fa-circle-check'],
                    'Pending'  => ['bg'=>'#fffbeb','color'=>'#d97706','icon'=>'fa-clock'],
                    'Rejected' => ['bg'=>'#fff5f5','color'=>'#e53e3e','icon'=>'fa-circle-xmark'],
                    default    => ['bg'=>'#f7fafc','color'=>'#718096','icon'=>'fa-circle-exclamation'],
                };
            @endphp
            <span style="display:inline-flex;align-items:center;gap:5px;background:{{ $badgeColor['bg'] }};color:{{ $badgeColor['color'] }};font-size:12px;font-weight:600;padding:5px 14px;border-radius:20px;margin-bottom:20px;">
                <i class="fa-solid {{ $badgeColor['icon'] }}" style="font-size:12px;"></i>
                {{ $statusVerif }}
            </span>

            {{-- Stats --}}
            <div style="display:flex;justify-content:space-between;background:#f8fafc;border-radius:10px;padding:12px 15px;margin-bottom:12px;">
                <span style="font-size:13px;color:#718096;">Total Pinjaman</span>
                <strong style="font-size:13px;color:#2d3748;">{{ $totalDipinjam }}</strong>
            </div>
            <div style="display:flex;justify-content:space-between;background:#f8fafc;border-radius:10px;padding:12px 15px;margin-bottom:20px;">
                <span style="font-size:13px;color:#718096;">Member Sejak</span>
                <strong style="font-size:13px;color:#2d3748;">{{ Auth::user()->created_at->format('M Y') }}</strong>
            </div>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="width:100%;padding:12px;border:2px solid #e53e3e;background:white;color:#e53e3e;border-radius:30px;cursor:pointer;font-size:14px;font-weight:500;display:flex;align-items:center;justify-content:center;gap:8px;">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Log Out
                </button>
            </form>
        </div>

        {{-- STATUS KEANGGOTAAN --}}
        <div style="background:white;border-radius:15px;padding:20px;border:1px solid #edf2f7;">
            <h4 style="font-size:15px;font-weight:700;color:#2d3748;margin-bottom:12px;">Status Keanggotaan</h4>
            <div style="background:#edf2f7;border-radius:10px;height:8px;margin-bottom:10px;overflow:hidden;">
                <div style="width:60%;height:100%;background:#1fcf8e;border-radius:10px;"></div>
            </div>
            <p style="font-size:12px;color:#718096;">250 poin lagi untuk menjadi <strong style="color:#d97706;">Gold Member</strong></p>
        </div>

    </div>

    {{-- KOLOM KANAN --}}
    <div style="flex:1;background:white;border-radius:15px;padding:25px;border:1px solid #edf2f7;">

        {{-- TABS --}}
        <div style="display:flex;border-bottom:1px solid #edf2f7;margin-bottom:25px;">
            <button onclick="switchTab('info')" id="tab-info"
                    style="padding:12px 20px;font-size:14px;font-weight:600;border:none;background:none;cursor:pointer;border-bottom:2px solid #1fcf8e;color:#1fcf8e;margin-bottom:-1px;">
                Informasi Pribadi
            </button>
            <button onclick="switchTab('keamanan')" id="tab-keamanan"
                    style="padding:12px 20px;font-size:14px;border:none;background:none;cursor:pointer;border-bottom:2px solid transparent;color:#718096;">
                Keamanan
            </button>
        </div>

        {{-- TAB: INFORMASI PRIBADI --}}
        <div id="content-info">

            {{-- Form gabung: foto profil + data diri + dokumen identitas --}}
            <form method="POST" action="{{ route('member.profil.update') }}" enctype="multipart/form-data">
                @csrf

                {{-- Input foto profil tersembunyi — submit otomatis saat foto dipilih --}}
                <input type="file" id="input-foto-profil" name="foto_profil"
                    accept="image/jpg,image/jpeg,image/png"
                    style="display:none;"
                    onchange="this.form.submit()">

                {{-- Nama Lengkap --}}
                <div style="margin-bottom:20px;">
                    <label style="font-size:13px;color:#4a5568;font-weight:500;display:block;margin-bottom:8px;">Nama Lengkap <span style="color:#e53e3e;">*</span></label>
                    <input type="text" name="nama_lengkap"
                        value="{{ old('nama_lengkap', $anggota?->nama_lengkap ?? '') }}"
                        required
                        style="width:100%;padding:12px 15px;border:1px solid #edf2f7;border-radius:10px;font-size:14px;color:#2d3748;outline:none;background:#fafafa;">
                </div>

                {{-- NIK & No HP --}}
                <div style="display:flex;gap:15px;margin-bottom:20px;">
                    <div style="flex:1;">
                        <label style="font-size:13px;color:#4a5568;font-weight:500;display:block;margin-bottom:8px;">NIK</label>
                        <input type="text" name="nik" maxlength="16"
                            value="{{ old('nik', $anggota?->nik ?? '') }}"
                            placeholder="16 digit NIK"
                            style="width:100%;padding:12px 15px;border:1px solid #edf2f7;border-radius:10px;font-size:14px;color:#2d3748;background:#fafafa;outline:none;">
                    </div>
                    <div style="flex:1;">
                        <label style="font-size:13px;color:#4a5568;font-weight:500;display:block;margin-bottom:8px;">No. HP</label>
                        <input type="text" name="no_hp" maxlength="15"
                            value="{{ old('no_hp', $anggota?->no_hp ?? '') }}"
                            placeholder="08xxxxxxxxxx"
                            style="width:100%;padding:12px 15px;border:1px solid #edf2f7;border-radius:10px;font-size:14px;color:#2d3748;background:#fafafa;outline:none;">
                    </div>
                </div>

                {{-- Alamat --}}
                <div style="margin-bottom:25px;">
                    <label style="font-size:13px;color:#4a5568;font-weight:500;display:block;margin-bottom:8px;">Alamat Domisili</label>
                    <textarea name="alamat" rows="3"
                            placeholder="Masukkan alamat lengkap..."
                            style="width:100%;padding:12px 15px;border:1px solid #edf2f7;border-radius:10px;font-size:14px;color:#2d3748;background:#fafafa;outline:none;resize:none;font-family:inherit;">{{ old('alamat', $anggota?->alamat ?? '') }}</textarea>
                </div>

                <hr style="border:none;border-top:1px solid #edf2f7;margin-bottom:25px;">

                {{-- DOKUMEN IDENTITAS --}}
                <h4 style="font-size:15px;font-weight:700;color:#2d3748;margin-bottom:16px;">Dokumen Identitas</h4>

                {{-- Info status verifikasi --}}
                @if($statusVerif === 'Pending')
                <div style="background:#fffbeb;border:1px solid #f6ad55;border-radius:10px;padding:14px 16px;margin-bottom:16px;display:flex;gap:10px;align-items:flex-start;">
                    <i class="fa-solid fa-clock" style="color:#d97706;flex-shrink:0;margin-top:2px;"></i>
                    <p style="font-size:13px;color:#92400e;margin:0;">Dokumen sedang <strong>menunggu verifikasi</strong> oleh petugas. Proses 1-2 hari kerja.</p>
                </div>
                @elseif($statusVerif === 'Approved')
                <div style="background:#f0fff4;border:1px solid #68d391;border-radius:10px;padding:14px 16px;margin-bottom:16px;display:flex;gap:10px;align-items:flex-start;">
                    <i class="fa-solid fa-circle-check" style="color:#38a169;flex-shrink:0;margin-top:2px;"></i>
                    <p style="font-size:13px;color:#22543d;margin:0;">Dokumen identitas <strong>telah diverifikasi</strong>. Anda dapat menikmati semua layanan perpustakaan.</p>
                </div>
                @elseif($statusVerif === 'Rejected')
                <div style="background:#fff5f5;border:1px solid #fc8181;border-radius:10px;padding:14px 16px;margin-bottom:16px;display:flex;gap:10px;align-items:flex-start;">
                    <i class="fa-solid fa-circle-xmark" style="color:#e53e3e;flex-shrink:0;margin-top:2px;"></i>
                    <p style="font-size:13px;color:#742a2a;margin:0;">Dokumen <strong>ditolak</strong>. Mohon unggah ulang foto identitas yang lebih jelas.</p>
                </div>
                @else
                <div style="background:#edf2f7;border:1px solid #cbd5e0;border-radius:10px;padding:14px 16px;margin-bottom:16px;display:flex;gap:10px;align-items:flex-start;">
                    <i class="fa-solid fa-circle-info" style="color:#4a5568;flex-shrink:0;margin-top:2px;"></i>
                    <p style="font-size:13px;color:#2d3748;margin:0;">Unggah dokumen identitas (KTP/KTM) untuk verifikasi akun.</p>
                </div>
                @endif

                {{-- Preview + Upload dokumen --}}
                <div style="display:flex;gap:20px;align-items:flex-start;flex-wrap:wrap;margin-bottom:25px;">

                    {{-- Preview dokumen --}}
                    <div style="flex:1;min-width:200px;max-width:280px;">
                        @if($anggota?->dokumen_identitas)
                            <a href="{{ asset('storage/' . $anggota->dokumen_identitas) }}" target="_blank">
                                <img src="{{ asset('storage/' . $anggota->dokumen_identitas) }}"
                                    alt="Dokumen Identitas"
                                    style="width:100%;border-radius:10px;border:2px solid #1fcf8e;object-fit:cover;">
                            </a>
                        @else
                            <div style="width:100%;height:130px;border-radius:10px;border:2px dashed #cbd5e0;background:#f8fafc;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;">
                                <i class="fa-regular fa-image" style="font-size:28px;color:#cbd5e0;"></i>
                                <p style="font-size:12px;color:#a0aec0;">Belum ada dokumen</p>
                            </div>
                        @endif
                    </div>

                    {{-- Input upload --}}
                    <div style="flex:1;min-width:200px;">
                        <label style="font-size:13px;color:#4a5568;font-weight:500;display:block;margin-bottom:8px;">
                            {{ $anggota?->dokumen_identitas ? 'Ganti Dokumen' : 'Unggah Dokumen' }}
                        </label>
                        <input type="file" name="dokumen_identitas"
                            accept=".jpg,.jpeg,.png,.pdf"
                            style="width:100%;padding:10px;border:1px dashed #cbd5e0;border-radius:8px;font-size:13px;background:#fafafa;cursor:pointer;">
                        <p style="font-size:11px;color:#a0aec0;margin-top:6px;">Format: JPG, PNG, PDF. Maks 2MB.</p>
                    </div>
                </div>

                {{-- Tombol simpan --}}
                <div style="display:flex;justify-content:flex-end;padding-top:20px;border-top:1px solid #edf2f7;">
                    <button type="submit"
                            style="padding:13px 35px;background:#1fcf8e;color:white;border:none;border-radius:30px;font-size:15px;font-weight:600;cursor:pointer;">
                        Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>

        {{-- TAB: KEAMANAN --}}
        <div id="content-keamanan" style="display:none;">
            <h4 style="font-size:15px;font-weight:700;color:#2d3748;margin-bottom:5px;">Ubah Password</h4>
            <p style="font-size:13px;color:#718096;margin-bottom:20px;">Pastikan password baru minimal 8 karakter.</p>

            <form method="POST" action="{{ route('member.profil.password') }}">
                @csrf

                <div style="margin-bottom:20px;">
                    <label style="font-size:13px;color:#4a5568;font-weight:500;display:block;margin-bottom:8px;">Password Saat Ini <span style="color:#e53e3e;">*</span></label>
                    <input type="password" name="password_lama" required
                        placeholder="Masukkan password saat ini"
                        style="width:100%;padding:12px 15px;border:1px solid #edf2f7;border-radius:10px;font-size:14px;color:#2d3748;background:#fafafa;outline:none;">
                </div>

                <div style="margin-bottom:20px;">
                    <label style="font-size:13px;color:#4a5568;font-weight:500;display:block;margin-bottom:8px;">Password Baru <span style="color:#e53e3e;">*</span></label>
                    <input type="password" name="password_baru" required minlength="8"
                        placeholder="Minimal 8 karakter"
                        style="width:100%;padding:12px 15px;border:1px solid #edf2f7;border-radius:10px;font-size:14px;color:#2d3748;background:#fafafa;outline:none;">
                </div>

                <div style="margin-bottom:30px;">
                    <label style="font-size:13px;color:#4a5568;font-weight:500;display:block;margin-bottom:8px;">Konfirmasi Password Baru <span style="color:#e53e3e;">*</span></label>
                    <input type="password" name="password_baru_confirmation" required
                        placeholder="Ulangi password baru"
                        style="width:100%;padding:12px 15px;border:1px solid #edf2f7;border-radius:10px;font-size:14px;color:#2d3748;background:#fafafa;outline:none;">
                </div>

                <div style="display:flex;justify-content:flex-end;">
                    <button type="submit"
                            style="padding:13px 35px;background:#3182ce;color:white;border:none;border-radius:30px;font-size:15px;font-weight:600;cursor:pointer;">
                        Ubah Password
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

{{-- JAVASCRIPT --}}
<script>
function switchTab(tab) {
    ['info', 'keamanan'].forEach(t => {
        document.getElementById('tab-' + t).style.borderBottomColor = 'transparent';
        document.getElementById('tab-' + t).style.color             = '#718096';
        document.getElementById('tab-' + t).style.fontWeight        = '400';
        document.getElementById('content-' + t).style.display       = 'none';
    });
    document.getElementById('tab-' + tab).style.borderBottomColor = '#1fcf8e';
    document.getElementById('tab-' + tab).style.color             = '#1fcf8e';
    document.getElementById('tab-' + tab).style.fontWeight        = '600';
    document.getElementById('content-' + tab).style.display       = 'block';
}

@if(session('error_password') || $errors->has('password_baru') || $errors->has('password_lama'))
    switchTab('keamanan');
@endif
</script>

@endsection