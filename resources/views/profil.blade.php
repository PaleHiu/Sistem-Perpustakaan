@extends('layouts.member_layout')

@section('title', 'Profil Member')

@section('content')

@php
    $anggota       = Auth::user()->anggota;
    $namaLengkap   = $anggota?->nama_lengkap ?? '';
    $inisial       = collect(explode(' ', $namaLengkap))->map(fn($w) => strtoupper(substr($w,0,1)))->take(2)->join('');
    $totalDipinjam = $anggota ? \App\Models\Peminjam::where('anggota_id', $anggota->id)->count() : 0;
    
    // LOGIC STATUS & PENGUNCIAN FORM
    $statusVerif   = $anggota?->status_verifikasi ?? 'Incomplete';
    $isPending     = $statusVerif === 'Pending';
    
    // Tambahkan status Rejected ke dalam kondisi ini agar form terbuka
    $isIncomplete  = in_array($statusVerif, ['Incomplete', 'Rejected']);
    
    // Form dikunci HANYA JIKA statusnya sudah Approved/Rejected (bukan Incomplete & bukan Pending)
    $isLocked      = !$isIncomplete && !$isPending; 
    
    // Cek apakah NIK sudah pernah diisi
    $hasNik        = !empty($anggota?->nik) && $statusVerif !== 'Rejected';
@endphp

{{-- NOTIFIKASI --}}
@if(session('success'))
<div style="background:#f0fff4;border:1px solid #c6f6d5;border-radius:10px;padding:12px 20px;margin-bottom:20px;color:#38a169;display:flex;align-items:center;gap:10px;">
    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
</div>
@endif
@if(session('warning'))
<div style="background:#fffbeb;border:1px solid #f6ad55;border-radius:10px;padding:12px 20px;margin-bottom:20px;color:#d97706;display:flex;align-items:center;gap:10px;">
    <i class="fa-solid fa-triangle-exclamation"></i> {{ session('warning') }}
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
@if(session('error'))
<div style="background:#fff5f5;border:1px solid #fed7d7;border-radius:10px;padding:12px 20px;margin-bottom:20px;color:#e53e3e;display:flex;align-items:center;gap:10px;">
    <i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}
</div>
@endif
@if($errors->any())
<div style="background:#fff5f5;border:1px solid #fed7d7;border-radius:10px;padding:12px 20px;margin-bottom:20px;color:#e53e3e;">
    <i class="fa-solid fa-circle-xmark"></i>
    @foreach($errors->all() as $error)<span>{{ $error }}</span>@endforeach
</div>
@endif

{{-- WRAPPER DUA KOLOM --}}
<div style="display:flex;gap:25px;align-items:flex-start;">

    {{-- KOLOM KIRI --}}
    <div style="width:260px;flex-shrink:0;">
        <div style="background:white;border-radius:15px;padding:25px 20px;border:1px solid #edf2f7;text-align:center;">
            
            {{-- FORM KHUSUS UPLOAD FOTO PROFIL--}}
            <form id="form-avatar" method="POST" action="{{ route('member.profil.avatar') }}" enctype="multipart/form-data" style="display:none;">
                @csrf
                <input type="file" id="input-foto-profil-independent" name="foto_profil" accept="image/jpg,image/jpeg,image/png" onchange="document.getElementById('form-avatar').submit()">
            </form>

            {{-- WADAH FOTO PROFIL INTERAKTIF --}}
            <label for="input-foto-profil-independent"
                   onmouseenter="document.getElementById('avatar-overlay').style.opacity='1'"
                   onmouseleave="document.getElementById('avatar-overlay').style.opacity='0'"
                   style="position:relative; width:90px; height:90px; margin:0 auto 15px; display:block; cursor:pointer; border-radius:50%; overflow:hidden; box-shadow:0 4px 6px rgba(0,0,0,0.1); border:3px solid white; transition:transform 0.2s;">

                {{-- Gambar Asli / Inisial --}}
                @if($anggota?->foto_profil)
                    <img src="{{ asset('storage/' . $anggota->foto_profil) }}" style="width:100%; height:100%; object-fit:cover; display:block;">
                @else
                    <div style="width:100%; height:100%; background:#1fcf8e; display:flex; align-items:center; justify-content:center; font-size:32px; font-weight:700; color:white;">
                        {{ $inisial ?: 'U' }}
                    </div>
                @endif

                {{-- Overlay Gelap & Ikon Kamera --}}
                <div id="avatar-overlay" style="position:absolute; inset:0; background:rgba(0,0,0,0.55); display:flex; flex-direction:column; align-items:center; justify-content:center; opacity:0; transition:opacity 0.2s;">
                    <i class="fa-solid fa-camera" style="color:white; font-size:22px; margin-bottom:4px;"></i>
                    <span style="color:white; font-size:10px; font-weight:600; letter-spacing:0.5px;">UBAH FOTO</span>
                </div>
            </label>

            <h3 style="font-size:17px;font-weight:700;color:#2d3748;margin-bottom:4px;">
                {{ $namaLengkap ?: 'Nama belum diisi' }}
            </h3>
            <p style="font-size:13px;color:#718096;margin-bottom:8px;">{{ Auth::user()->email }}</p>
            <p style="font-size:12px;color:#718096;margin-bottom:12px;">NIK: {{ $anggota?->nik ?? 'Belum diisi' }}</p>

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

            <div style="display:flex;justify-content:space-between;background:#f8fafc;border-radius:10px;padding:12px 15px;margin-bottom:12px;">
                <span style="font-size:13px;color:#718096;">Total Pinjaman</span>
                <strong style="font-size:13px;color:#2d3748;">{{ $totalDipinjam }}</strong>
            </div>
            <div style="display:flex;justify-content:space-between;background:#f8fafc;border-radius:10px;padding:12px 15px;margin-bottom:20px;">
                <span style="font-size:13px;color:#718096;">Member Sejak</span>
                <strong style="font-size:13px;color:#2d3748;">{{ Auth::user()->created_at->format('M Y') }}</strong>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="width:100%;padding:12px;border:2px solid #e53e3e;background:white;color:#e53e3e;border-radius:30px;cursor:pointer;font-size:14px;font-weight:500;display:flex;align-items:center;justify-content:center;gap:8px;">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Log Out
                </button>
            </form>
        </div>
    </div>

    {{-- KOLOM KANAN --}}
    <div style="flex:1;background:white;border-radius:15px;padding:25px;border:1px solid #edf2f7;">

        <div style="display:flex;border-bottom:1px solid #edf2f7;margin-bottom:25px;">
            <button onclick="switchTab('info')" id="tab-info" style="padding:12px 20px;font-size:14px;font-weight:600;border:none;background:none;cursor:pointer;border-bottom:2px solid #1fcf8e;color:#1fcf8e;margin-bottom:-1px;">Informasi Pribadi</button>
            <button onclick="switchTab('keamanan')" id="tab-keamanan" style="padding:12px 20px;font-size:14px;border:none;background:none;cursor:pointer;border-bottom:2px solid transparent;color:#718096;">Keamanan</button>
        </div>

        {{-- TAB: INFORMASI PRIBADI --}}
        <div id="content-info">
            <form method="POST" action="{{ route('member.profil.update') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="password_konfirmasi" id="hidden_password_konfirmasi">
                <input type="file" id="input-foto-profil" name="foto_profil" accept="image/jpg,image/jpeg,image/png" style="display:none;" class="locked-input" {{ $isIncomplete ? '' : 'disabled' }} onchange="this.form.submit()">

                <div style="margin-bottom:20px;">
                    <label style="font-size:13px;color:#4a5568;font-weight:500;display:block;margin-bottom:8px;">Nama Lengkap <span style="color:#e53e3e;">*</span></label>
                    <input type="text" name="nama_lengkap" class="locked-input"
                           value="{{ old('nama_lengkap', $anggota?->nama_lengkap ?? '') }}"
                           {{ $isIncomplete ? 'required' : 'disabled' }}
                           style="width:100%;padding:12px 15px;border:1px solid #edf2f7;border-radius:10px;font-size:14px;color:#2d3748;outline:none;background:{{ $isIncomplete ? '#fafafa' : '#e2e8f0' }};cursor:{{ $isIncomplete ? 'text' : 'not-allowed' }};">
                </div>

                <div style="display:flex;gap:15px;margin-bottom:20px;">
                    <div style="flex:1;">
                        <label style="font-size:13px;color:#4a5568;font-weight:500;display:block;margin-bottom:8px;">NIK</label>
                        <div style="position:relative;">
                            <input type="text" name="nik" maxlength="16" class="{{ $hasNik ? '' : 'locked-input' }}"
                                   value="{{ old('nik', $anggota?->nik ?? '') }}"
                                   placeholder="16 digit NIK" {{ ($hasNik || !$isIncomplete) ? 'disabled' : '' }}
                                   style="width:100%;padding:12px 15px;border:1px solid #edf2f7;border-radius:10px;font-size:14px;color:#2d3748;background:{{ ($hasNik || !$isIncomplete) ? '#e2e8f0' : '#fafafa' }};cursor:{{ ($hasNik || !$isIncomplete) ? 'not-allowed' : 'text' }};outline:none;">
                            @if($hasNik)
                                <i class="fa-solid fa-lock" style="position:absolute; right:15px; top:15px; color:#a0aec0; font-size:12px;" title="NIK tidak dapat diubah"></i>
                            @endif
                        </div>
                        @if($hasNik)
                            <p style="font-size:11px;color:#e53e3e;margin-top:6px;">* NIK telah tervalidasi dan tidak dapat diubah.</p>
                        @endif
                    </div>
                    <div style="flex:1;">
                        <label style="font-size:13px;color:#4a5568;font-weight:500;display:block;margin-bottom:8px;">No. HP</label>
                        <input type="text" name="no_hp" maxlength="15" class="locked-input"
                               value="{{ old('no_hp', $anggota?->no_hp ?? '') }}"
                               placeholder="08xxxxxxxxxx" {{ $isIncomplete ? '' : 'disabled' }}
                               style="width:100%;padding:12px 15px;border:1px solid #edf2f7;border-radius:10px;font-size:14px;color:#2d3748;background:{{ $isIncomplete ? '#fafafa' : '#e2e8f0' }};cursor:{{ $isIncomplete ? 'text' : 'not-allowed' }};outline:none;">
                    </div>
                </div>

                <div style="margin-bottom:25px;">
                    <label style="font-size:13px;color:#4a5568;font-weight:500;display:block;margin-bottom:8px;">Alamat Domisili</label>
                    <textarea name="alamat" rows="3" class="locked-input"
                              placeholder="Masukkan alamat lengkap..." {{ $isIncomplete ? '' : 'disabled' }}
                              style="width:100%;padding:12px 15px;border:1px solid #edf2f7;border-radius:10px;font-size:14px;color:#2d3748;background:{{ $isIncomplete ? '#fafafa' : '#e2e8f0' }};cursor:{{ $isIncomplete ? 'text' : 'not-allowed' }};outline:none;resize:none;font-family:inherit;">{{ old('alamat', $anggota?->alamat ?? '') }}</textarea>
                </div>

                <hr style="border:none;border-top:1px solid #edf2f7;margin-bottom:25px;">

                <h4 style="font-size:15px;font-weight:700;color:#2d3748;margin-bottom:16px;">Dokumen Identitas</h4>

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

                <div style="display:flex;gap:24px;align-items:flex-start;flex-wrap:wrap;margin-bottom:25px;background:#f8fafc;padding:20px;border-radius:12px;border:1px solid #edf2f7;">
                    
                    {{-- KOLOM KIRI (Preview Gambar) --}}
                    <div style="flex-shrink:0;width:180px;">
                        @if($anggota?->dokumen_identitas)
                            <a href="{{ url('/private/dokumen/' . basename($anggota->dokumen_identitas)) }}" target="_blank" 
                               style="display:block;border-radius:10px;overflow:hidden;box-shadow:0 4px 6px rgba(0,0,0,0.05);border:2px solid white;transition:transform 0.2s;">
                                <img src="{{ url('/private/dokumen/' . basename($anggota->dokumen_identitas)) }}" alt="Dokumen" 
                                     style="width:100%;height:115px;object-fit:cover;display:block;">
                            </a>
                        @else
                            <div style="width:100%;height:115px;border-radius:10px;border:2px dashed #cbd5e0;background:white;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;">
                                <i class="fa-regular fa-id-card" style="font-size:26px;color:#cbd5e0;"></i>
                                <p style="font-size:11px;color:#a0aec0;font-weight:500;">Belum ada</p>
                            </div>
                        @endif
                    </div>

                    {{-- KOLOM KANAN (Form Upload) --}}
                    <div style="flex:1;min-width:250px;">
                        <label style="font-size:16px;color:#4a5568;font-weight:600;display:block;margin-bottom:8px;">
                            {{ $anggota?->dokumen_identitas ? 'Ganti Dokumen' : 'Unggah Dokumen' }}
                        </label>
                        <input type="file" name="dokumen_identitas" class="locked-input" accept=".jpg,.jpeg,.png,.pdf" {{ $isIncomplete ? '' : 'disabled' }}
                               style="width:100%;padding:12px;border:1px dashed #cbd5e0;border-radius:10px;font-size:13px;color:#4a5568;background:{{ $isIncomplete ? 'white' : '#e2e8f0' }};cursor:{{ $isIncomplete ? 'pointer' : 'not-allowed' }};transition:all 0.2s;">
                        <p style="font-size:14px;color:#718096;margin-top:8px;line-height:1.5;">
                            Format: <strong>JPG, PNG, PDF</strong>. Maksimal <strong>2MB</strong>.<br>
                            Pastikan tulisan identitas terbaca dengan jelas untuk mempercepat verifikasi.
                        </p>
                    </div>
                </div>

                {{-- AREA TOMBOL DINAMIS --}}
                <div style="display:flex;justify-content:flex-end;padding-top:20px;border-top:1px solid #edf2f7;gap:10px;">
                    @if($isPending)
                        <button type="button" disabled style="padding:13px 35px;background:#a0aec0;color:white;border:none;border-radius:30px;font-size:15px;font-weight:600;cursor:not-allowed;">
                            Menunggu Verifikasi Admin
                        </button>
                    @elseif($isIncomplete)
                        {{-- User Baru / Incomplete: Langsung tombol Simpan Profil --}}
                        <button type="submit" style="padding:12px 30px;background:#1fcf8e;color:white;border:none;border-radius:30px;font-size:14px;font-weight:600;cursor:pointer;">
                            Simpan Profil
                        </button>
                    @else
                        {{-- User Lama: Tombol Edit -> Munculkan Modal --}}
                        <button type="button" id="btn-trigger-edit" onclick="openPasswordModal()"
                                style="padding:12px 30px;background:#3182ce;color:white;border:none;border-radius:30px;font-size:14px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:8px;transition:0.3s;">
                            <i class="fa-solid fa-lock-open"></i> Edit Profil
                        </button>
                        <button type="submit" id="btn-save-profile" style="display:none; padding:12px 30px;background:#1fcf8e;color:white;border:none;border-radius:30px;font-size:14px;font-weight:600;cursor:pointer;">
                            Simpan Perubahan
                        </button>
                    @endif
                </div>
            </form>
        </div>
        {{-- AKHIR TAB INFO --}}

        {{-- TAB: KEAMANAN --}}
        <div id="content-keamanan" style="display:none;">
            <h4 style="font-size:15px;font-weight:700;color:#2d3748;margin-bottom:5px;">Ubah Password</h4>
            <p style="font-size:13px;color:#718096;margin-bottom:20px;">Pastikan password baru minimal 8 karakter.</p>

            <form method="POST" action="{{ route('member.profil.password') }}">
                @csrf
                <div style="margin-bottom:20px;">
                    <label style="font-size:13px;color:#4a5568;font-weight:500;display:block;margin-bottom:8px;">Password Saat Ini <span style="color:#e53e3e;">*</span></label>
                    <input type="password" name="password_lama" required placeholder="Masukkan password saat ini" style="width:100%;padding:12px 15px;border:1px solid #edf2f7;border-radius:10px;font-size:14px;color:#2d3748;background:#fafafa;outline:none;">
                </div>
                <div style="margin-bottom:20px;">
                    <label style="font-size:13px;color:#4a5568;font-weight:500;display:block;margin-bottom:8px;">Password Baru <span style="color:#e53e3e;">*</span></label>
                    <input type="password" name="password_baru" required minlength="8" placeholder="Minimal 8 karakter" style="width:100%;padding:12px 15px;border:1px solid #edf2f7;border-radius:10px;font-size:14px;color:#2d3748;background:#fafafa;outline:none;">
                </div>
                <div style="margin-bottom:30px;">
                    <label style="font-size:13px;color:#4a5568;font-weight:500;display:block;margin-bottom:8px;">Konfirmasi Password Baru <span style="color:#e53e3e;">*</span></label>
                    <input type="password" name="password_baru_confirmation" required placeholder="Ulangi password baru" style="width:100%;padding:12px 15px;border:1px solid #edf2f7;border-radius:10px;font-size:14px;color:#2d3748;background:#fafafa;outline:none;">
                </div>
                <div style="display:flex;justify-content:flex-end;">
                    <button type="submit" style="padding:13px 35px;background:#3182ce;color:white;border:none;border-radius:30px;font-size:15px;font-weight:600;cursor:pointer;">
                        Ubah Password
                    </button>
                </div>
            </form>

            <hr style="border:none;border-top:1px solid #edf2f7;margin:35px 0 25px 0;">

            <h4 style="font-size:15px;font-weight:700;color:#e53e3e;margin-bottom:5px;">Hapus Akun Permanen</h4>
            <p style="font-size:13px;color:#718096;margin-bottom:20px;">Peringatan: Sekali Anda menghapus akun, semua data profil dan riwayat peminjaman akan hilang dan tidak dapat dipulihkan kembali.</p>

            <form method="POST" action="{{ route('member.profil.hapus') }}" onsubmit="return confirm('Peringatan Terakhir!\n\nApakah Anda YAKIN ingin menghapus akun ini selamanya? Data tidak dapat dipulihkan!');">
                @csrf
                @method('DELETE')
                <div style="margin-bottom:20px;">
                    <label style="font-size:13px;color:#4a5568;font-weight:500;display:block;margin-bottom:8px;">Konfirmasi Password <span style="color:#e53e3e;">*</span></label>
                    <input type="password" name="password_hapus" required placeholder="Masukkan password Anda untuk konfirmasi" style="width:100%;padding:12px 15px;border:1px solid #fc8181;border-radius:10px;font-size:14px;color:#2d3748;background:#fff5f5;outline:none;">
                </div>
                <div style="display:flex;justify-content:flex-end;">
                    <button type="submit" style="padding:13px 35px;background:#e53e3e;color:white;border:none;border-radius:30px;font-size:15px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:8px;">
                        <i class="fa-solid fa-trash-can"></i> Hapus Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL POP-UP PASSWORD --}}
<div id="passwordModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:999; justify-content:center; align-items:center; backdrop-filter:blur(3px);">
    <div style="background:white; padding:30px; border-radius:15px; width:100%; max-width:400px; box-shadow:0 10px 25px rgba(0,0,0,0.2);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
            <h4 style="font-size:18px; font-weight:700; color:#2d3748; margin:0;">Validasi Keamanan</h4>
            <i class="fa-solid fa-lock" style="color:#a0aec0;"></i>
        </div>
        <p style="font-size:13px; color:#718096; margin-bottom:20px; line-height:1.5;">Untuk melindungi data Anda, masukkan password akun Anda untuk membuka akses edit profil.</p>
        
        <input type="password" id="modal_password_input" placeholder="Masukkan password Anda" 
               style="width:100%; padding:12px 15px; border:1px solid #edf2f7; border-radius:10px; margin-bottom:10px; font-size:14px; outline:none; background:#fafafa;">
        
        <div id="modal_error_msg" style="color:#e53e3e; font-size:12px; margin-bottom:15px; display:none;">
            <i class="fa-solid fa-circle-xmark"></i> Password salah! Silakan coba lagi.
        </div>
        
        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
            <button type="button" onclick="closePasswordModal()" style="padding:10px 20px; background:#edf2f7; color:#4a5568; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer; border:none;">Batal</button>
            <button type="button" id="btn-verify-pwd" onclick="verifyPasswordAndUnlock()" style="padding:10px 20px; background:#3182ce; color:white; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer; border:none; display:flex; align-items:center; gap:6px;">Verifikasi</button>
        </div>
    </div>
</div>

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

function openPasswordModal() {
    document.getElementById('passwordModal').style.display = 'flex';
    document.getElementById('modal_password_input').value = '';
    document.getElementById('modal_error_msg').style.display = 'none';
    setTimeout(() => document.getElementById('modal_password_input').focus(), 100);
}

function closePasswordModal() {
    document.getElementById('passwordModal').style.display = 'none';
}

function verifyPasswordAndUnlock() {
    const pwdInput = document.getElementById('modal_password_input').value;
    const btn = document.getElementById('btn-verify-pwd');
    
    if(!pwdInput) {
        document.getElementById('modal_error_msg').style.display = 'block';
        document.getElementById('modal_error_msg').innerHTML = '<i class="fa-solid fa-circle-xmark"></i> Password tidak boleh kosong!';
        return;
    }

    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mengecek...';
    btn.disabled = true;

    fetch('{{ route("member.profil.verify-password") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ password: pwdInput })
    })
    .then(response => response.json())
    .then(data => {
        btn.innerHTML = 'Verifikasi';
        btn.disabled = false;
        
        if (data.valid) {
            closePasswordModal();
            unlockForm(pwdInput);
        } else {
            document.getElementById('modal_error_msg').style.display = 'block';
            document.getElementById('modal_error_msg').innerHTML = '<i class="fa-solid fa-circle-xmark"></i> Password salah! Silakan coba lagi.';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        btn.innerHTML = 'Verifikasi';
        btn.disabled = false;
        alert('Terjadi kesalahan jaringan.');
    });
}

function unlockForm(validatedPassword) {
    document.getElementById('hidden_password_konfirmasi').value = validatedPassword;
    
    document.querySelectorAll('.locked-input').forEach(el => {
        el.disabled = false;
        el.style.backgroundColor = '#fafafa';
        el.style.cursor = el.type === 'file' ? 'pointer' : 'text';
    });
    
    const cameraIcon = document.getElementById('camera-icon-wrapper');
    if (cameraIcon) cameraIcon.style.display = 'flex';

    document.getElementById('btn-trigger-edit').style.display = 'none';
    document.getElementById('btn-save-profile').style.display = 'block';
}

document.getElementById("modal_password_input").addEventListener("keypress", function(event) {
  if (event.key === "Enter") {
    event.preventDefault();
    verifyPasswordAndUnlock();
  }
});

function handleAvatarClick(event) {
    const fileInput = document.getElementById('input-foto-profil');
    
    // Jika input masih dikunci (form belum di-unlock), cegah file explorer terbuka
    if (fileInput.disabled) {
        event.preventDefault();
        // Buka modal password untuk memverifikasi keamanan terlebih dahulu
        openPasswordModal();
    }
    // Jika form sudah di-unlock, file explorer akan otomatis terbuka berkat tag <label>
}
</script>

@endsection