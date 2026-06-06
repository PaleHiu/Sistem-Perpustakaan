<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/landing', function () {
    if (Auth::check()) {
        if (Auth::user()->role === 'Petugas') {
            return redirect()->route('dashboard');
        } else {
            return redirect()->route('member.dashboard');
        }
    }
    return view('landing');
})->name('landing');

Route::get('/', function () {
    return redirect()->route('landing');
});

// ============================================
// ROUTE LUPA PASSWORD SIPUS
// ============================================

Route::get('/sipus/lupa-password', function () {
    return view('Auth.forgot_password');
})->name('sipus.forgot.password');

Route::post('/sipus/lupa-password', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'email' => 'required|email|exists:users,email',
    ], [
        'email.exists' => 'Email tidak terdaftar di sistem.',
    ]);

    \DB::table('password_reset_otps')->where('email', $request->email)->delete();

    $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    \DB::table('password_reset_otps')->insert([
        'email'      => $request->email,
        'otp'        => $otp,
        'expired_at' => now()->addMinutes(10),
        'used'       => false,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    \Illuminate\Support\Facades\Mail::send([], [], function ($message) use ($request, $otp) {
        $message->to($request->email)
                ->subject('Kode OTP Reset Password - SIPUS')
                ->html('
                    <div style="font-family:Arial,sans-serif;max-width:500px;margin:0 auto;padding:30px;">
                        <div style="background:#1f3c45;padding:20px;border-radius:10px 10px 0 0;text-align:center;">
                            <h2 style="color:#1fcf8e;margin:0;letter-spacing:2px;">SIPUS</h2>
                            <p style="color:white;margin:5px 0 0;font-size:12px;">Library Management System</p>
                        </div>
                        <div style="background:white;padding:30px;border:1px solid #edf2f7;border-radius:0 0 10px 10px;">
                            <h3 style="color:#2d3748;margin-bottom:10px;">Reset Password</h3>
                            <p style="color:#718096;font-size:14px;">Gunakan kode OTP berikut untuk mereset password kamu:</p>
                            <div style="background:#f0fff4;border:2px solid #1fcf8e;border-radius:10px;padding:20px;text-align:center;margin:20px 0;">
                                <h1 style="color:#1fcf8e;font-size:42px;letter-spacing:10px;margin:0;font-family:monospace;">'.$otp.'</h1>
                            </div>
                            <p style="color:#e53e3e;font-size:13px;text-align:center;">⚠ Kode berlaku selama <strong>10 menit</strong></p>
                            <p style="color:#a0aec0;font-size:12px;margin-top:20px;">Jika kamu tidak meminta reset password, abaikan email ini.</p>
                        </div>
                    </div>
                ');
    });

    session(['reset_email' => $request->email]);

    return redirect()->route('sipus.forgot.otp')
        ->with('success', 'Kode OTP telah dikirim ke email kamu.');

})->name('sipus.forgot.password.send');

Route::get('/sipus/lupa-password/otp', function () {
    if (!session('reset_email')) {
        return redirect()->route('sipus.forgot.password');
    }
    return view('Auth.forgot_otp');
})->name('sipus.forgot.otp');

Route::post('/sipus/lupa-password/otp', function (\Illuminate\Http\Request $request) {
    $request->validate(['otp' => 'required|string|size:6']);

    $email = session('reset_email');
    if (!$email) return redirect()->route('sipus.forgot.password');

    $record = \DB::table('password_reset_otps')
                ->where('email', $email)
                ->where('otp', $request->otp)
                ->where('used', false)
                ->first();

    if (!$record) {
        return back()->withErrors(['otp' => 'Kode OTP salah.']);
    }

    if (now()->gt($record->expired_at)) {
        return back()->withErrors(['otp' => 'Kode OTP sudah kadaluarsa. Minta ulang.']);
    }

    \DB::table('password_reset_otps')
        ->where('id', $record->id)
        ->update(['used' => true]);

    session(['reset_verified' => true]);

    return redirect()->route('sipus.forgot.reset');

})->name('sipus.forgot.otp.verify');

Route::get('/sipus/lupa-password/reset', function () {
    if (!session('reset_email') || !session('reset_verified')) {
        return redirect()->route('sipus.forgot.password');
    }
    return view('Auth.forgot_reset');
})->name('sipus.forgot.reset');

Route::post('/sipus/lupa-password/reset', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'password'              => 'required|min:8|confirmed',
        'password_confirmation' => 'required',
    ], [
        'password.confirmed' => 'Konfirmasi password tidak cocok.',
        'password.min'       => 'Password minimal 8 karakter.',
    ]);

    $email = session('reset_email');
    if (!$email) return redirect()->route('sipus.forgot.password');

    \App\Models\User::where('email', $email)
        ->update(['password' => bcrypt($request->password)]);

    session()->forget(['reset_email', 'reset_verified']);

    return redirect()->route('login')
        ->with('success', 'Password berhasil direset! Silakan login.');

})->name('sipus.forgot.reset.save');

// ============================================
// ROUTE ADMIN (Petugas) — tanpa 'verified'
// ============================================

Route::get('/dashboard', function () {
    if (Auth::user()->role !== 'Petugas') {
        return redirect()->route('member.dashboard');
    }

    $totalBooks       = \App\Models\Buku::count();
    $totalMembers     = \App\Models\Anggota::count();
    $overdueBooks     = \App\Models\Peminjam::where('status_transaksi', 'Dipinjam')
                        ->whereNotNull('batas_pengembalian')
                        ->where('batas_pengembalian', '<', now())
                        ->count();
    $chartData        = [];
    for ($i = 6; $i >= 0; $i--) {
        $chartData[]  = \App\Models\Peminjam::whereDate('waktu_booking', now()->subDays($i))->count();
    }
    $topMembers       = \App\Models\Anggota::withCount('peminjaman')
                        ->orderBy('peminjaman_count', 'desc')->take(3)->get();
    $recentActivities = \App\Models\Peminjam::with(['anggota', 'detailPeminjaman.buku'])
                        ->latest('waktu_booking')->take(5)->get();

    return view('dashboard', compact(
        'totalBooks', 'totalMembers', 'overdueBooks',
        'chartData', 'topMembers', 'recentActivities'
    ));
})->middleware(['auth'])->name('dashboard');

Route::get('/books', function () {
    if (Auth::user()->role !== 'Petugas') return redirect()->route('member.dashboard');
    $books     = \App\Models\Buku::with('kategori')->latest()->get();
    $kategoris = \App\Models\Kategori::all();
    return view('books', [
        'books'        => $books,
        'kategoris'    => $kategoris,
        'totalTitles'  => $books->count(),
        'totalItems'   => $books->sum('stok_total'),
        'lowStock'     => $books->where('stok_tersedia', '<', 5)->count(),
        'reservations' => 0,
    ]);
})->middleware(['auth'])->name('books.index');

Route::post('/books', function (\Illuminate\Http\Request $request) {
    if (Auth::user()->role !== 'Petugas') return redirect()->route('member.dashboard');
    $request->validate([
        'judul'        => 'required|string|max:255',
        'penulis'      => 'required|string|max:255',
        'penerbit'     => 'required|string|max:255',
        'kategori_id'  => 'required|exists:kategori,id',
        'tahun_terbit' => 'required|digits:4',
        'stok_total'   => 'required|integer|min:0',
        'cover'        => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
    ]);
    $coverPath = null;
    if ($request->hasFile('cover')) {
        $coverPath = $request->file('cover')->store('covers', 'public');
    }
    \App\Models\Buku::create([
        'judul'         => $request->judul,
        'penulis'       => $request->penulis,
        'penerbit'      => $request->penerbit,
        'kategori_id'   => $request->kategori_id,
        'tahun_terbit'  => $request->tahun_terbit,
        'stok_total'    => $request->stok_total,
        'stok_tersedia' => $request->stok_total,
        'cover'         => $coverPath,
    ]);
    return redirect()->route('books.index')->with('success', 'Buku berhasil ditambahkan!');
})->middleware(['auth'])->name('books.store');

Route::put('/books/{id}', function (\Illuminate\Http\Request $request, $id) {
    if (Auth::user()->role !== 'Petugas') return redirect()->route('member.dashboard');
    $request->validate([
        'judul'        => 'required|string|max:255',
        'penulis'      => 'required|string|max:255',
        'penerbit'     => 'required|string|max:255',
        'kategori_id'  => 'required|exists:kategori,id',
        'tahun_terbit' => 'required|digits:4',
        'stok_total'   => 'required|integer|min:0',
        'cover'        => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
    ]);
    $buku      = \App\Models\Buku::findOrFail($id);
    $coverPath = $buku->cover;
    if ($request->hasFile('cover')) {
        if ($buku->cover) \Illuminate\Support\Facades\Storage::disk('public')->delete($buku->cover);
        $coverPath = $request->file('cover')->store('covers', 'public');
    }
    $buku->update([
        'judul'         => $request->judul,
        'penulis'       => $request->penulis,
        'penerbit'      => $request->penerbit,
        'kategori_id'   => $request->kategori_id,
        'tahun_terbit'  => $request->tahun_terbit,
        'stok_total'    => $request->stok_total,
        'stok_tersedia' => $request->stok_tersedia,
        'cover'         => $coverPath,
    ]);
    return redirect()->route('books.index')->with('success', 'Buku berhasil diperbarui!');
})->middleware(['auth'])->name('books.update');

Route::delete('/books/{id}', function ($id) {
    if (Auth::user()->role !== 'Petugas') return redirect()->route('member.dashboard');
    $buku = \App\Models\Buku::findOrFail($id);
    if ($buku->cover) \Illuminate\Support\Facades\Storage::disk('public')->delete($buku->cover);
    $buku->delete();
    return redirect()->route('books.index')->with('success', 'Buku berhasil dihapus!');
})->middleware(['auth'])->name('books.destroy');

Route::get('/members', function () {
    if (Auth::user()->role !== 'Petugas') return redirect()->route('member.dashboard');
    $members = \App\Models\Anggota::with('user')->latest()->get();
    return view('members', compact('members'));
})->middleware(['auth'])->name('members.index');

Route::delete('/members/{id}', function ($id) {
    if (Auth::user()->role !== 'Petugas') return redirect()->route('member.dashboard');
    
    $anggota = \App\Models\Anggota::findOrFail($id);
    if ($anggota->user_id) {
        \App\Models\User::find($anggota->user_id)?->delete();
    }
    
    $anggota->delete();
    
    return redirect()->route('members.index')->with('success', 'Anggota berhasil dihapus!');
})->middleware(['auth'])->name('members.destroy');

Route::patch('/members/{id}/verify', function (\Illuminate\Http\Request $request, $id) {
    if (Auth::user()->role !== 'Petugas') return redirect()->route('member.dashboard');
    \App\Models\Anggota::findOrFail($id)->update([
        'status_verifikasi' => $request->status_verifikasi
    ]);
    $pesan = $request->status_verifikasi === 'Approved'
        ? 'Anggota berhasil diverifikasi!'
        : 'Anggota berhasil ditolak.';
    return redirect()->route('members.index')->with('success', $pesan);
})->middleware(['auth'])->name('members.verify');

Route::get('/borrowing', function () {
    if (Auth::user()->role !== 'Petugas') return redirect()->route('member.dashboard');
    $borrowings = \App\Models\Peminjam::with(['anggota', 'detailPeminjaman.buku'])
                    ->latest('waktu_booking')->get();
    return view('borrowing', compact('borrowings'));
})->middleware(['auth'])->name('borrowing.index');

Route::post('/borrowing/{id}/validasi', function (\Illuminate\Http\Request $request, $id) {
    if (Auth::user()->role !== 'Petugas') return redirect()->route('member.dashboard');

    $peminjaman = \App\Models\Peminjam::findOrFail($id);

    if (strtoupper($peminjaman->kode_otp) !== strtoupper($request->kode_otp)) {
        return back()->with('error_otp', 'Kode OTP salah! OTP yang dimasukkan: ' . $request->kode_otp);
    }
    if (\Carbon\Carbon::parse($peminjaman->otp_expired_at)->isPast()) {
        return back()->with('error_otp', 'Kode OTP sudah kadaluarsa!');
    }

    $totalSelesai = \App\Models\Peminjam::where('anggota_id', $peminjaman->anggota_id)
                    ->where('status_transaksi', 'Selesai')->count();
    $level        = match(true) {
        $totalSelesai >= 25 => 'Platinum',
        $totalSelesai >= 10 => 'Gold',
        default             => 'Silver',
    };
    $durasiHari   = $level === 'Platinum' ? 14 : 7;
    $petugas      = \App\Models\Petugas::where('user_id', Auth::user()->id)->first();

    $peminjaman->update([
        'status_transaksi'   => 'Dipinjam',
        'tanggal_pinjam'     => now()->toDateString(),
        'batas_pengembalian' => now()->addDays($durasiHari)->toDateString(),
        'petugas_id'         => $petugas?->id,
    ]);

    return redirect()->route('borrowing.index')
        ->with('success', 'OTP valid! Peminjaman dikonfirmasi. Durasi: ' . $durasiHari . ' hari (Level ' . $level . ')');
})->middleware(['auth'])->name('borrowing.validasi');

Route::post('/borrowing/{id}/kembalikan', function ($id) {
    if (Auth::user()->role !== 'Petugas') return redirect()->route('member.dashboard');

    $peminjaman = \App\Models\Peminjam::with('detailPeminjaman.buku')->findOrFail($id);

    if ($peminjaman->status_transaksi !== 'Dipinjam') {
        return redirect()->route('borrowing.index')->with('error_otp', 'Transaksi ini tidak dalam status Dipinjam!');
    }

    $today      = now()->toDateString();
    $batas      = $peminjaman->batas_pengembalian;
    $jumlahBuku = $peminjaman->detailPeminjaman->count();
    $totalDenda = 0;

    if ($batas && $today > $batas) {
        $hariTerlambat = (int) ceil(\Carbon\Carbon::parse($batas)->floatDiffInDays(now()));
        $totalDenda    = $hariTerlambat * 1000 * $jumlahBuku;
    }

    $peminjaman->update([
        'status_transaksi'     => 'Selesai',
        'tanggal_dikembalikan' => $today,
        'total_denda'          => $totalDenda,
    ]);

    foreach ($peminjaman->detailPeminjaman as $detail) {
        if ($detail->buku) $detail->buku->increment('stok_tersedia');
    }

    $pesan = $totalDenda > 0
        ? 'Buku berhasil dikembalikan. Denda: Rp ' . number_format($totalDenda, 0, ',', '.')
        : 'Buku berhasil dikembalikan. Tidak ada denda.';

    return redirect()->route('borrowing.index')->with('success', $pesan);
})->middleware(['auth'])->name('borrowing.kembalikan');

// ============================================
// ROUTE MEMBER
// Helper: cek apakah anggota sudah approved
// ============================================

Route::get('/member/dashboard', function () {
    if (Auth::user()->role !== 'Member') return redirect()->route('dashboard');

    $anggota = Auth::user()->anggota;

    if (!$anggota) {
        return view('dashboard_member', [
            'sedangDipinjam'   => 0,
            'totalRiwayat'     => 0,
            'totalDenda'       => 0,
            'pinjamanAktif'    => collect(),
            'aktivitasTerbaru' => collect(),
        ]);
    }

    $sedangDipinjam   = \App\Models\Peminjam::where('anggota_id', $anggota->id)->where('status_transaksi', 'Dipinjam')->count();
    $totalRiwayat     = \App\Models\Peminjam::where('anggota_id', $anggota->id)->count();
    $totalDenda       = \App\Models\Peminjam::where('anggota_id', $anggota->id)->sum('total_denda');
    $pinjamanAktif    = \App\Models\Peminjam::where('anggota_id', $anggota->id)
                        ->whereIn('status_transaksi', ['Menunggu OTP', 'Dipinjam'])
                        ->with(['detailPeminjaman.buku'])->latest('waktu_booking')->take(3)->get();
    $aktivitasTerbaru = \App\Models\Peminjam::where('anggota_id', $anggota->id)
                        ->with(['detailPeminjaman.buku'])->latest('waktu_booking')->take(5)->get();

    return view('dashboard_member', compact(
        'sedangDipinjam', 'totalRiwayat', 'totalDenda', 'pinjamanAktif', 'aktivitasTerbaru'
    ));
})->middleware(['auth', 'verified'])->name('member.dashboard');

// Katalog — boleh diakses siapa saja yang sudah login
// Cek verifikasi hanya dilakukan saat tambah ke keranjang
Route::get('/member/katalog', function () {
    if (Auth::user()->role !== 'Member') return redirect()->route('dashboard');
    $books = \App\Models\Buku::with('kategori')->latest()->get();
    // Kirim status verifikasi ke view untuk keperluan notif di tombol keranjang
    $statusVerif = Auth::user()->anggota?->status_verifikasi ?? 'Incomplete';
    return view('books_katalog', compact('books', 'statusVerif'));
})->middleware(['auth', 'verified'])->name('member.katalog');

// Tambah ke keranjang — cek verifikasi di sini
Route::post('/member/keranjang/tambah', function (\Illuminate\Http\Request $request) {
    if (Auth::user()->role !== 'Member') return response()->json(['error' => 'Unauthorized'], 403);

    $anggota = Auth::user()->anggota;
    if (!$anggota) return response()->json(['error' => 'Profil anggota tidak ditemukan'], 404);

    // Blokir jika belum approved
    if ($anggota->status_verifikasi !== 'Approved') {
        $pesan = match($anggota->status_verifikasi ?? 'Incomplete') {
            'Pending'  => 'Dokumen identitas kamu sedang diverifikasi. Tunggu persetujuan admin.',
            'Rejected' => 'Dokumen identitas kamu ditolak. Upload ulang di halaman profil.',
            default    => 'Lengkapi profil dan upload dokumen identitas terlebih dahulu.',
        };
        return response()->json(['error' => $pesan, 'redirect' => route('member.profil')], 403);
    }

    $totalSelesai = \App\Models\Peminjam::where('anggota_id', $anggota->id)->where('status_transaksi', 'Selesai')->count();
    $level        = match(true) {
        $totalSelesai >= 25 => 'Platinum',
        $totalSelesai >= 10 => 'Gold',
        default             => 'Silver',
    };
    $batasPinjam  = match($level) { 'Platinum' => 6, 'Gold' => 4, default => 2 };

    $jumlahDiKeranjang = \App\Models\Keranjang::where('anggota_id', $anggota->id)->count();
    if ($jumlahDiKeranjang >= $batasPinjam) {
        return response()->json(['error' => 'Batas pinjam level ' . $level . ' adalah ' . $batasPinjam . ' buku.'], 400);
    }

    $sudahAda = \App\Models\Keranjang::where('anggota_id', $anggota->id)->where('buku_id', $request->buku_id)->exists();
    if ($sudahAda) return response()->json(['error' => 'Buku sudah ada di keranjang'], 409);

    $buku = \App\Models\Buku::findOrFail($request->buku_id);
    if ($buku->stok_tersedia <= 0) return response()->json(['error' => 'Stok buku habis'], 400);

    \App\Models\Keranjang::create(['anggota_id' => $anggota->id, 'buku_id' => $request->buku_id]);

    $jumlah = \App\Models\Keranjang::where('anggota_id', $anggota->id)->count();
    return response()->json(['success' => true, 'jumlah' => $jumlah, 'level' => $level, 'batas' => $batasPinjam]);
})->middleware(['auth', 'verified'])->name('member.keranjang.tambah');

Route::delete('/member/keranjang/{id}', function ($id) {
    if (Auth::user()->role !== 'Member') return response()->json(['error' => 'Unauthorized'], 403);
    $anggota = Auth::user()->anggota;
    \App\Models\Keranjang::where('id', $id)->where('anggota_id', $anggota->id)->delete();
    return redirect()->route('member.keranjang')->with('success', 'Buku dihapus dari keranjang');
})->middleware(['auth', 'verified'])->name('member.keranjang.hapus');

Route::post('/member/keranjang/booking', function (\Illuminate\Http\Request $request) {
    if (Auth::user()->role !== 'Member') return redirect()->route('dashboard');

    $anggota   = Auth::user()->anggota;
    $keranjang = \App\Models\Keranjang::where('anggota_id', $anggota->id)->with('buku')->get();

    if ($keranjang->isEmpty()) return redirect()->route('member.keranjang')->with('error', 'Keranjang kosong!');

    $kodeOtp    = strtoupper(\Illuminate\Support\Str::random(6));
    $peminjaman = \App\Models\Peminjam::create([
        'anggota_id'       => $anggota->id,
        'kode_otp'         => $kodeOtp,
        'waktu_booking'    => now(),
        'otp_expired_at'   => now()->addHours(24),
        'status_transaksi' => 'Menunggu OTP',
        'total_denda'      => 0,
    ]);

    foreach ($keranjang as $item) {
        \App\Models\DetailPeminjaman::create([
            'peminjaman_id' => $peminjaman->id,
            'buku_id'       => $item->buku_id,
            'jumlah'        => 1,
        ]);
        $item->buku->decrement('stok_tersedia');
    }

    \App\Models\Keranjang::where('anggota_id', $anggota->id)->delete();
    return redirect()->route('member.peminjaman.otp', ['id' => $peminjaman->id]);
})->middleware(['auth', 'verified'])->name('member.keranjang.booking');

// Keranjang — blokir jika belum approved
Route::get('/member/keranjang', function () {
    if (Auth::user()->role !== 'Member') return redirect()->route('dashboard');

    $anggota = Auth::user()->anggota;
    if (!$anggota || $anggota->status_verifikasi !== 'Approved') {
        $pesan = match($anggota?->status_verifikasi ?? 'Incomplete') {
            'Pending'  => 'Dokumen identitas kamu sedang diverifikasi petugas.',
            'Rejected' => 'Dokumen identitas kamu ditolak. Upload ulang di profil.',
            default    => 'Lengkapi profil dan upload dokumen identitas terlebih dahulu.',
        };
        return redirect()->route('member.profil')->with('warning', $pesan);
    }

    $keranjang = \App\Models\Keranjang::where('anggota_id', $anggota->id)->with('buku')->get();
    return view('keranjang', compact('keranjang'));
})->middleware(['auth', 'verified'])->name('member.keranjang');

// Peminjaman — blokir jika belum approved
Route::get('/member/peminjaman', function () {
    if (Auth::user()->role !== 'Member') return redirect()->route('dashboard');

    $anggota = Auth::user()->anggota;
    if (!$anggota || $anggota->status_verifikasi !== 'Approved') {
        $pesan = match($anggota?->status_verifikasi ?? 'Incomplete') {
            'Pending'  => 'Akun kamu sedang diverifikasi. Tunggu persetujuan admin.',
            'Rejected' => 'Verifikasi akun kamu ditolak. Upload ulang dokumen di profil.',
            default    => 'Lengkapi profil dan upload dokumen identitas terlebih dahulu.',
        };
        return redirect()->route('member.profil')->with('warning', $pesan);
    }

    $peminjaman = \App\Models\Peminjam::where('anggota_id', $anggota->id)
                    ->with(['detailPeminjaman.buku'])->latest('waktu_booking')->get();
    return view('peminjaman', compact('peminjaman'));
})->middleware(['auth', 'verified'])->name('member.peminjaman');

Route::delete('/member/peminjaman/{id}/batal', function ($id) {
    if (Auth::user()->role !== 'Member') return redirect()->route('dashboard');

    $anggota    = Auth::user()->anggota;
    $peminjaman = \App\Models\Peminjam::where('id', $id)
                    ->where('anggota_id', $anggota->id)
                    ->where('status_transaksi', 'Menunggu OTP')->firstOrFail();

    foreach ($peminjaman->detailPeminjaman as $detail) {
        if ($detail->buku) $detail->buku->increment('stok_tersedia');
    }

    $peminjaman->update(['status_transaksi' => 'Batal']);
    return redirect()->route('member.peminjaman')->with('success', 'Booking berhasil dibatalkan.');
})->middleware(['auth', 'verified'])->name('member.peminjaman.batal');

// Riwayat — blokir jika belum approved
Route::get('/member/riwayat', function () {
    if (Auth::user()->role !== 'Member') return redirect()->route('dashboard');

    $anggota = Auth::user()->anggota;
    if (!$anggota || $anggota->status_verifikasi !== 'Approved') {
        $pesan = match($anggota?->status_verifikasi ?? 'Incomplete') {
            'Pending'  => 'Akun kamu sedang diverifikasi. Tunggu persetujuan admin.',
            'Rejected' => 'Verifikasi akun kamu ditolak. Upload ulang dokumen di profil.',
            default    => 'Lengkapi profil dan upload dokumen identitas terlebih dahulu.',
        };
        return redirect()->route('member.profil')->with('warning', $pesan);
    }

    $riwayat = \App\Models\Peminjam::where('anggota_id', $anggota->id)
                ->with(['detailPeminjaman.buku'])->latest('waktu_booking')->get();
    return view('riwayat', compact('riwayat'));
})->middleware(['auth', 'verified'])->name('member.riwayat');

Route::get('/member/profil', function () {
    if (Auth::user()->role !== 'Member') return redirect()->route('dashboard');
    return view('profil');
})->middleware(['auth', 'verified'])->name('member.profil');

Route::post('/member/profil/update', function (\Illuminate\Http\Request $request) {
    if (Auth::user()->role !== 'Member') return redirect()->route('dashboard');

    $request->validate([
        'nama_lengkap'      => 'required|string|max:255',
        'no_hp'             => 'nullable|string|max:15',
        'alamat'            => 'nullable|string',
        'nik'               => 'nullable|string|max:16|unique:anggota,nik,' . Auth::user()->anggota?->id,
        'foto_profil'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'dokumen_identitas' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
    ]);

    $fotoProfil = Auth::user()->anggota?->foto_profil;
    if ($request->hasFile('foto_profil')) {
        if ($fotoProfil) \Illuminate\Support\Facades\Storage::disk('public')->delete($fotoProfil);
        $fotoProfil = $request->file('foto_profil')->store('foto_profil', 'public');
    }

    $dokumenIdentitas = Auth::user()->anggota?->dokumen_identitas;
    $ubahStatusVerif  = false;
    if ($request->hasFile('dokumen_identitas')) {
        if ($dokumenIdentitas) \Illuminate\Support\Facades\Storage::disk('public')->delete($dokumenIdentitas);
        $dokumenIdentitas = $request->file('dokumen_identitas')->store('dokumen_identitas', 'public');
        $ubahStatusVerif  = true;
    }

    $anggota    = Auth::user()->anggota;
    $statusBaru = $ubahStatusVerif ? 'Pending' : ($anggota?->status_verifikasi ?? 'Incomplete');

    if ($anggota) {
        $anggota->update([
            'nama_lengkap'      => $request->nama_lengkap,
            'no_hp'             => $request->no_hp,
            'alamat'            => $request->alamat,
            'nik'               => $request->nik,
            'foto_profil'       => $fotoProfil,
            'dokumen_identitas' => $dokumenIdentitas,
            'status_verifikasi' => $statusBaru,
        ]);
    } else {
        \App\Models\Anggota::create([
            'user_id'           => Auth::user()->id,
            'nama_lengkap'      => $request->nama_lengkap,
            'no_hp'             => $request->no_hp,
            'alamat'            => $request->alamat,
            'nik'               => $request->nik,
            'foto_profil'       => $fotoProfil,
            'dokumen_identitas' => $dokumenIdentitas,
            'status_verifikasi' => 'Pending',
        ]);
    }

    return redirect()->route('member.profil')->with('success', 'Profil berhasil diperbarui!');
})->middleware(['auth', 'verified'])->name('member.profil.update');

Route::post('/member/profil/password', function (\Illuminate\Http\Request $request) {
    if (Auth::user()->role !== 'Member') return redirect()->route('dashboard');

    $request->validate([
        'password_lama'              => 'required',
        'password_baru'              => 'required|min:8|confirmed',
        'password_baru_confirmation' => 'required',
    ]);

    if (!\Illuminate\Support\Facades\Hash::check($request->password_lama, Auth::user()->password)) {
        return back()->with('error_password', 'Password lama tidak sesuai!')->withInput();
    }

    Auth::user()->update(['password' => \Illuminate\Support\Facades\Hash::make($request->password_baru)]);
    return redirect()->route('member.profil')->with('success_password', 'Password berhasil diubah!');
})->middleware(['auth', 'verified'])->name('member.profil.password');

Route::get('/member/peminjaman/{id}/otp', function ($id) {
    if (Auth::user()->role !== 'Member') return redirect()->route('dashboard');
    $anggota    = Auth::user()->anggota;
    $peminjaman = \App\Models\Peminjam::where('id', $id)
                    ->where('anggota_id', $anggota->id)
                    ->with(['detailPeminjaman.buku'])->firstOrFail();
    return view('booking_sukses', compact('peminjaman'));
})->middleware(['auth', 'verified'])->name('member.peminjaman.otp');

// ============================================
// ROUTE PROFIL BAWAAN BREEZE
// ============================================

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';