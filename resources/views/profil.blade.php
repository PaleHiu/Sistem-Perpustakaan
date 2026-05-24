@extends('layouts.member_layout')

@section('title', 'Profil Member')

@section('content')

{{-- Tangkap pesan Peringatan dari Satpam KYC --}}
@if(session('warning'))
    <div style="background-color: #fff3cd; color: #856404; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ffeeba;">
        <strong>Perhatian!</strong> {{ session('warning') }}
    </div>
@endif

{{-- Tangkap pesan Sukses setelah update data --}}
@if(session('success'))
    <div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
        <strong>Berhasil!</strong> {{ session('success') }}
    </div>
@endif

<div style="display: flex; gap: 25px; align-items: flex-start;">

    {{-- ===== KOLOM KIRI (TETAP SAMA) ===== --}}
    <div style="width: 260px; flex-shrink: 0; display: flex; flex-direction: column; gap: 20px;">
        <div style="background: white; border-radius: 15px; padding: 25px 20px; border: 1px solid #edf2f7; text-align: center;">
            <div style="position: relative; display: inline-block; margin-bottom: 15px;">
                <div style="width: 80px; height: 80px; background: #1fcf8e; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 26px; font-weight: 700; color: white; margin: 0 auto;">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}{{ strtoupper(substr(explode(' ', Auth::user()->name ?? 'U ')[1] ?? '', 0, 1)) }}
                </div>
            </div>
            <h3 style="font-size: 17px; font-weight: 700; color: #2d3748; margin-bottom: 4px;">
                {{ Auth::user()->anggota->nama_lengkap ?? Auth::user()->name }}
            </h3>
            <p style="font-size: 13px; color: #718096; margin-bottom: 12px;">
                NIK: {{ Auth::user()->anggota->nik ?? 'Belum diisi' }}
            </p>

            <span style="display: inline-flex; align-items: center; gap: 5px; background: #f0fff4; color: #38a169; font-size: 12px; font-weight: 600; padding: 5px 14px; border-radius: 20px; margin-bottom: 20px;">
                <i class="fa-solid fa-circle-check" style="font-size: 12px;"></i> Akun {{ Auth::user()->anggota->status_verifikasi ?? 'Incomplete' }}
            </span>

            <div style="display: flex; justify-content: space-between; background: #f8fafc; border-radius: 10px; padding: 12px 15px; margin-bottom: 20px;">
                <span style="font-size: 13px; color: #718096;">Total Dipinjam</span>
                <strong style="font-size: 13px; color: #2d3748;">12</strong>
            </div>
            <div style="display: flex; justify-content: space-between; background: #f8fafc; border-radius: 10px; padding: 12px 15px; margin-bottom: 20px;">
                <span style="font-size: 13px; color: #718096;">Member Sejak</span>
                <strong style="font-size: 13px; color: #2d3748;">{{ Auth::user()->created_at->format('M Y') }}</strong>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="width: 100%; padding: 12px; border: 2px solid #e53e3e; background: white; color: #e53e3e; border-radius: 30px; cursor: pointer; font-size: 14px; font-weight: 500; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Log Out
                </button>
            </form>
        </div>

        <div style="background: white; border-radius: 15px; padding: 20px; border: 1px solid #edf2f7;">
            <h4 style="font-size: 16px; font-weight: 700; color: #2d3748; margin-bottom: 15px; line-height: 1.4;">Status<br>Keanggotaan</h4>
            <div style="background: #edf2f7; border-radius: 10px; height: 8px; margin-bottom: 10px; overflow: hidden;">
                <div style="width: 60%; height: 100%; background: #1fcf8e; border-radius: 10px;"></div>
            </div>
            <p style="font-size: 12px; color: #718096;">
                250 poin lagi untuk menjadi <strong style="color: #d97706;">Gold Member</strong>
            </p>
        </div>
    </div>

    {{-- ===== KOLOM KANAN ===== --}}
    <div style="flex: 1; background: white; border-radius: 15px; padding: 25px; border: 1px solid #edf2f7;">

        <div style="display: flex; gap: 0; border-bottom: 1px solid #edf2f7; margin-bottom: 25px;">
            <span style="padding: 12px 20px; font-size: 14px; font-weight: 600; color: #1fcf8e; border-bottom: 2px solid #1fcf8e; cursor: pointer; margin-bottom: -1px;">
                Informasi & Dokumen
            </span>
        </div>

        {{-- FORM UTAMA (Mencakup teks dan gambar) --}}
        <form method="POST" action="{{ route('member.profil.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            {{-- Nama Lengkap --}}
            <div style="margin-bottom: 20px;">
                <label style="font-size: 13px; color: #4a5568; font-weight: 500; display: block; margin-bottom: 8px;">Nama Lengkap</label>
                <div style="position: relative;">
                    <input type="text" name="nama_lengkap" value="{{ Auth::user()->anggota->nama_lengkap ?? Auth::user()->name }}"
                        style="width: 100%; padding: 12px 45px 12px 15px; border: 1px solid #edf2f7; border-radius: 10px; font-size: 14px; color: #2d3748; outline: none; background: #fafafa;" required>
                    <i class="fa-solid fa-pen" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #a0aec0; font-size: 13px;"></i>
                </div>
            </div>

            {{-- NIK & No HP --}}
            <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                <div style="flex: 1;">
                    <label style="font-size: 13px; color: #4a5568; font-weight: 500; display: block; margin-bottom: 8px;">NIK</label>
                    <div style="position: relative;">
                        <input type="text" name="nik" value="{{ Auth::user()->anggota->nik ?? '' }}" placeholder="Masukkan 16 digit NIK"
                            style="width: 100%; padding: 12px 45px 12px 15px; border: 1px solid #edf2f7; border-radius: 10px; font-size: 14px; color: #2d3748; background: #fafafa; outline: none;" required>
                        <i class="fa-solid fa-pen" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #a0aec0; font-size: 13px;"></i>
                    </div>
                </div>
                <div style="flex: 1;">
                    <label style="font-size: 13px; color: #4a5568; font-weight: 500; display: block; margin-bottom: 8px;">No. HP</label>
                    <div style="position: relative;">
                        <input type="text" name="no_hp" value="{{ Auth::user()->anggota->no_hp ?? '' }}" placeholder="Contoh: 0812xxxx"
                            style="width: 100%; padding: 12px 45px 12px 15px; border: 1px solid #edf2f7; border-radius: 10px; font-size: 14px; color: #2d3748; background: #fafafa; outline: none;" required>
                        <i class="fa-solid fa-pen" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #a0aec0; font-size: 13px;"></i>
                    </div>
                </div>
            </div>

            {{-- Alamat Domisili --}}
            <div style="margin-bottom: 20px;">
                <label style="font-size: 13px; color: #4a5568; font-weight: 500; display: block; margin-bottom: 8px;">Alamat Domisili</label>
                <div style="position: relative;">
                    <textarea name="alamat" placeholder="Masukkan alamat domisili lengkap"
                        style="width: 100%; padding: 12px 45px 12px 15px; border: 1px solid #edf2f7; border-radius: 10px; font-size: 14px; color: #2d3748; background: #fafafa; outline: none; resize: none; min-height: 80px; font-family: inherit;" required>{{ Auth::user()->anggota->alamat ?? '' }}</textarea>
                    <i class="fa-solid fa-pen" style="position: absolute; right: 15px; top: 15px; color: #a0aec0; font-size: 13px;"></i>
                </div>
            </div>

            <hr style="border: none; border-top: 1px solid #edf2f7; margin: 30px 0;">

            {{-- DOKUMEN IDENTITAS --}}
            <h4 style="font-size: 16px; font-weight: 700; color: #2d3748; margin-bottom: 20px;">Pratinjau & Unggah Dokumen</h4>
            <div style="display: flex; gap: 20px; align-items: flex-start; flex-wrap: wrap; margin-bottom: 30px;">

                {{-- Preview Gambar Dinamis --}}
                <div style="flex: 1; min-width: 200px; max-width: 320px;">
                    @if(Auth::user()->anggota->dokumen_identitas)
                        <img src="{{ asset('storage/' . Auth::user()->anggota->dokumen_identitas) }}" alt="Dokumen Identitas"
                            style="width: 100%; border-radius: 10px; border: 2px solid #1fcf8e; object-fit: cover;">
                    @else
                        <img src="https://via.placeholder.com/320x200?text=Belum+Ada+Dokumen" alt="Dokumen Identitas"
                            style="width: 100%; border-radius: 10px; border: 2px dashed #cbd5e0; object-fit: cover;">
                    @endif
                </div>

                {{-- Info Status & Input File --}}
                <div style="flex: 1; min-width: 220px;">
                    @php
                        $status = Auth::user()->anggota->status_verifikasi ?? 'Incomplete';
                    @endphp

                    @if($status == 'Pending')
                        <div style="background: #fffbeb; border: 1px solid #f6ad55; border-radius: 10px; padding: 14px 16px; margin-bottom: 16px; display: flex; gap: 10px; align-items: flex-start;">
                            <i class="fa-solid fa-clock" style="color: #d97706; flex-shrink: 0; margin-top: 2px;"></i>
                            <p style="font-size: 13px; color: #92400e; margin: 0;">Dokumen Anda sedang <strong>menunggu verifikasi</strong> oleh petugas. Proses ini memakan waktu 1-2 hari kerja.</p>
                        </div>
                    @elseif($status == 'Approved')
                        <div style="background: #f0fff4; border: 1px solid #68d391; border-radius: 10px; padding: 14px 16px; margin-bottom: 16px; display: flex; gap: 10px; align-items: flex-start;">
                            <i class="fa-solid fa-circle-check" style="color: #38a169; flex-shrink: 0; margin-top: 2px;"></i>
                            <p style="font-size: 13px; color: #22543d; margin: 0;">Dokumen identitas Anda telah <strong>berhasil diverifikasi</strong>. Anda dapat menikmati semua layanan perpustakaan.</p>
                        </div>
                    @elseif($status == 'Rejected')
                        <div style="background: #fff5f5; border: 1px solid #fc8181; border-radius: 10px; padding: 14px 16px; margin-bottom: 16px; display: flex; gap: 10px; align-items: flex-start;">
                            <i class="fa-solid fa-circle-xmark" style="color: #e53e3e; flex-shrink: 0; margin-top: 2px;"></i>
                            <p style="font-size: 13px; color: #742a2a; margin: 0;">Dokumen Anda <strong>ditolak</strong>. Mohon unggah ulang foto identitas yang lebih jelas.</p>
                        </div>
                    @else
                        <div style="background: #edf2f7; border: 1px solid #cbd5e0; border-radius: 10px; padding: 14px 16px; margin-bottom: 16px; display: flex; gap: 10px; align-items: flex-start;">
                            <i class="fa-solid fa-circle-info" style="color: #4a5568; flex-shrink: 0; margin-top: 2px;"></i>
                            <p style="font-size: 13px; color: #2d3748; margin: 0;">Silakan unggah dokumen identitas Anda (KTP/KTM) untuk keperluan verifikasi akun.</p>
                        </div>
                    @endif
                    
                    {{-- Input File Pengganti Tombol Lama --}}
                    <label style="font-size: 13px; color: #4a5568; font-weight: 500; display: block; margin-bottom: 8px;">Unggah / Ganti Dokumen Baru</label>
                    <input type="file" name="dokumen_identitas" accept=".jpg,.jpeg,.png,.pdf" 
                           style="width: 100%; padding: 10px; border: 1px dashed #cbd5e0; border-radius: 8px; font-size: 13px; background: #fafafa; cursor: pointer;">
                </div>
            </div>

            {{-- Tombol Simpan Ditaruh Paling Bawah agar Mengirim Teks & File Sekaligus --}}
            <div style="display: flex; justify-content: flex-end; padding-top: 20px; border-top: 1px solid #edf2f7;">
                <button type="submit" style="padding: 13px 35px; background: #1fcf8e; color: white; border: none; border-radius: 30px; font-size: 15px; font-weight: 600; cursor: pointer;">
                    Simpan Perubahan & Dokumen
                </button>
            </div>

        </form>

    </div>
</div>

@endsection