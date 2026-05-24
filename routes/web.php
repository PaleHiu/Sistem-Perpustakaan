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
// ROUTE ADMIN (Petugas)
// ============================================

Route::get('/dashboard', function () {
    if (Auth::user()->role !== 'Petugas') {
        return redirect()->route('member.dashboard');
    }

    $totalBooks = \App\Models\Buku::count();
    $totalMembers = \App\Models\Anggota::count();
    $overdueBooks = \App\Models\Peminjam::where('status_transaksi', 'Dipinjam')
                    ->whereNotNull('batas_pengembalian')
                    ->where('batas_pengembalian', '<', now())
                    ->count();

    $chartData = [];
    for ($i = 6; $i >= 0; $i--) {
        $chartData[] = \App\Models\Peminjam::whereDate('waktu_booking', now()->subDays($i))->count();
    }

    $topMembers = \App\Models\Anggota::withCount('peminjaman')
                    ->orderBy('peminjaman_count', 'desc')
                    ->take(3)->get();

    $recentActivities = \App\Models\Peminjam::with(['anggota', 'detailPeminjaman.buku'])
                        ->latest('waktu_booking')
                        ->take(5)->get();

    return view('dashboard', compact(
        'totalBooks', 'totalMembers', 'overdueBooks',
        'chartData', 'topMembers', 'recentActivities'
    ));
})->middleware(['auth', 'verified'])->name('dashboard');

// Buku
Route::get('/books', function () {
    if (Auth::user()->role !== 'Petugas') {
        return redirect()->route('member.dashboard');
    }
    $books     = \App\Models\Buku::with('kategori')->latest()->get();
    $kategoris = \App\Models\Kategori::all();
    return view('books', [
        'books'       => $books,
        'kategoris'   => $kategoris,
        'totalTitles' => $books->count(),
        'totalItems'  => $books->sum('stok_total'),
        'lowStock'    => $books->where('stok_tersedia', '<', 5)->count(),
        'reservations'=> 0,
    ]);
})->middleware(['auth', 'verified'])->name('books.index');

Route::post('/books', function (\Illuminate\Http\Request $request) {
    if (Auth::user()->role !== 'Petugas') {
        return redirect()->route('member.dashboard');
    }
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
    ]);
    return redirect()->route('books.index')->with('success', 'Buku berhasil ditambahkan!');
})->middleware(['auth', 'verified'])->name('books.store');

Route::put('/books/{id}', function (\Illuminate\Http\Request $request, $id) {
    if (Auth::user()->role !== 'Petugas') {
        return redirect()->route('member.dashboard');
    }
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
        if ($buku->cover) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($buku->cover);
        }
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
})->middleware(['auth', 'verified'])->name('books.update');

Route::delete('/books/{id}', function ($id) {
    if (Auth::user()->role !== 'Petugas') {
        return redirect()->route('member.dashboard');
    }
    $buku = \App\Models\Buku::findOrFail($id);
    if ($buku->cover) {
        \Illuminate\Support\Facades\Storage::disk('public')->delete($buku->cover);
    }
    $buku->delete();
    return redirect()->route('books.index')->with('success', 'Buku berhasil dihapus!');
})->middleware(['auth', 'verified'])->name('books.destroy');

// Anggota
Route::get('/members', function () {
    if (Auth::user()->role !== 'Petugas') {
        return redirect()->route('member.dashboard');
    }
    $members = \App\Models\Anggota::with('user')->latest()->get();
    return view('members', compact('members'));
})->middleware(['auth', 'verified'])->name('members.index');

// RUTE BARU: Menangani Proses Approve / Reject dari Admin
Route::patch('/members/{id}/verify', function (\Illuminate\Http\Request $request, $id) {
    if (Auth::user()->role !== 'Petugas') {
        return redirect()->route('member.dashboard');
    }

    // 1. Validasi input status yang dikirim (Approved / Rejected)
    $request->validate([
        'status_verifikasi' => 'required|in:Approved,Rejected',
    ]);

    // 2. Cari data anggota berdasarkan ID
    $member = \App\Models\Anggota::findOrFail($id);

    // 3. Update status verifikasi di database
    $member->update([
        'status_verifikasi' => $request->status_verifikasi
    ]);

    // 4. Set pesan sukses sesuai tindakan
    $pesan = $request->status_verifikasi === 'Approved' 
        ? 'Anggota "' . $member->nama_lengkap . '" berhasil disetujui (Approved)!' 
        : 'Dokumen anggota "' . $member->nama_lengkap . '" telah ditolak (Rejected).';

    return redirect()->route('members.index')->with('success', $pesan);
})->middleware(['auth', 'verified'])->name('members.verify');

Route::delete('/members/{id}', function ($id) {
    if (Auth::user()->role !== 'Petugas') {
        return redirect()->route('member.dashboard');
    }
    \App\Models\Anggota::findOrFail($id)->delete();
    return redirect()->route('members.index')->with('success', 'Anggota berhasil dihapus!');
})->middleware(['auth', 'verified'])->name('members.destroy');

// Borrowing
Route::get('/borrowing', function () {
    if (Auth::user()->role !== 'Petugas') {
        return redirect()->route('member.dashboard');
    }
    $borrowings = \App\Models\Peminjam::with(['anggota', 'detailPeminjaman.buku'])
                    ->latest('waktu_booking')
                    ->get();
    return view('borrowing', compact('borrowings'));
})->middleware(['auth', 'verified'])->name('borrowing.index');

Route::post('/borrowing/{id}/validasi', function (\Illuminate\Http\Request $request, $id) {
    if (Auth::user()->role !== 'Petugas') {
        return redirect()->route('member.dashboard');
    }

    $peminjaman = \App\Models\Peminjam::findOrFail($id);

    if (strtoupper($peminjaman->kode_otp) !== strtoupper($request->kode_otp)) {
        return back()->with('error_otp', 'Kode OTP salah! OTP yang dimasukkan: ' . $request->kode_otp);
    }

    if (\Carbon\Carbon::parse($peminjaman->otp_expired_at)->isPast()) {
        return back()->with('error_otp', 'Kode OTP sudah kadaluarsa!');
    }

    $petugas = \App\Models\Petugas::where('user_id', Auth::user()->id)->first();

    $peminjaman->update([
        'status_transaksi'   => 'Dipinjam',
        'tanggal_pinjam'     => now()->toDateString(),
        'batas_pengembalian' => now()->addDays(7)->toDateString(),
        'petugas_id'         => $petugas?->id,
    ]);

    return redirect()->route('borrowing.index')->with('success', 'OTP valid! Peminjaman berhasil dikonfirmasi.');

})->middleware(['auth', 'verified'])->name('borrowing.validasi');

Route::post('/borrowing/{id}/kembalikan', function ($id) {
    if (Auth::user()->role !== 'Petugas') {
        return redirect()->route('member.dashboard');
    }

    $peminjaman = \App\Models\Peminjam::with('detailPeminjaman.buku')->findOrFail($id);

    if ($peminjaman->status_transaksi !== 'Dipinjam') {
        return redirect()->route('borrowing.index')
            ->with('error_otp', 'Transaksi ini tidak dalam status Dipinjam!');
    }

    $today      = now()->toDateString();
    $batas      = $peminjaman->batas_pengembalian;
    $jumlahBuku = $peminjaman->detailPeminjaman->count();
    $totalDenda = 0;

    if ($batas && $today > $batas) {
        $hariTerlambat = (int) ceil(
            \Carbon\Carbon::parse($batas)->floatDiffInDays(now())
        );
        $totalDenda = $hariTerlambat * 1000 * $jumlahBuku;
    }

    $peminjaman->update([
        'status_transaksi'     => 'Selesai',
        'tanggal_dikembalikan' => $today,
        'total_denda'          => $totalDenda,
    ]);

    foreach ($peminjaman->detailPeminjaman as $detail) {
        if ($detail->buku) {
            $detail->buku->increment('stok_tersedia');
        }
    }

    $pesan = $totalDenda > 0
        ? 'Buku berhasil dikembalikan. Denda: Rp ' . number_format($totalDenda, 0, ',', '.')
        : 'Buku berhasil dikembalikan. Tidak ada denda.';

    return redirect()->route('borrowing.index')->with('success', $pesan);

})->middleware(['auth', 'verified'])->name('borrowing.kembalikan');


// ============================================
// ROUTE MEMBER (Area Terbuka / Tanpa KYC)
// ============================================

Route::get('/member/dashboard', function () {
    if (Auth::user()->role !== 'Member') {
        return redirect()->route('dashboard');
    }

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

    $sedangDipinjam = \App\Models\Peminjam::where('anggota_id', $anggota->id)
                        ->where('status_transaksi', 'Dipinjam')->count();

    $totalRiwayat = \App\Models\Peminjam::where('anggota_id', $anggota->id)->count();

    $totalDenda = \App\Models\Peminjam::where('anggota_id', $anggota->id)->sum('total_denda');

    $pinjamanAktif = \App\Models\Peminjam::where('anggota_id', $anggota->id)
                        ->whereIn('status_transaksi', ['Menunggu OTP', 'Dipinjam'])
                        ->with(['detailPeminjaman.buku'])
                        ->latest('waktu_booking')->take(3)->get();

    $aktivitasTerbaru = \App\Models\Peminjam::where('anggota_id', $anggota->id)
                        ->with(['detailPeminjaman.buku'])
                        ->latest('waktu_booking')->take(5)->get();

    return view('dashboard_member', compact(
        'sedangDipinjam', 'totalRiwayat', 'totalDenda',
        'pinjamanAktif', 'aktivitasTerbaru'
    ));
})->middleware(['auth', 'verified'])->name('member.dashboard');

Route::get('/member/katalog', function () {
    if (Auth::user()->role !== 'Member') {
        return redirect()->route('dashboard');
    }
    $books = \App\Models\Buku::with('kategori')->get();
    return view('books_katalog', compact('books'));
})->middleware(['auth', 'verified'])->name('member.katalog');

Route::get('/member/riwayat', function () {
    if (Auth::user()->role !== 'Member') {
        return redirect()->route('dashboard');
    }
    $anggota = Auth::user()->anggota;
    if (!$anggota) {
        return view('riwayat', ['riwayat' => collect()]);
    }
    $riwayat = \App\Models\Peminjam::where('anggota_id', $anggota->id)
                ->with(['detailPeminjaman.buku'])
                ->latest('waktu_booking')->get();
    return view('riwayat', compact('riwayat'));
})->middleware(['auth', 'verified'])->name('member.riwayat');

// Rute Profil (Tempat user melengkapi data, tidak dikunci KYC)
Route::get('/member/profil', function () {
    if (Auth::user()->role !== 'Member') {
        return redirect()->route('dashboard');
    }
    return view('profil'); 
})->middleware(['auth', 'verified'])->name('member.profil');

// Rute untuk memproses form Simpan Perubahan Profil
Route::patch('/member/profil/update', [App\Http\Controllers\ProfileController::class, 'updateProfilMember'])
    ->middleware(['auth', 'verified'])
    ->name('member.profil.update');


// ============================================
// AREA TRANSAKSI (Dikunci oleh Satpam KYC / CekKelengkapanData)
// ============================================

Route::middleware([
    'auth', 
    'verified', 
    \App\Http\Middleware\CekKelengkapanData::class
])->group(function () {

    // 1. Tampilan Keranjang
    Route::get('/member/keranjang', function () {
        if (Auth::user()->role !== 'Member') return redirect()->route('dashboard');
        $anggota   = Auth::user()->anggota;
        $keranjang = $anggota
            ? \App\Models\Keranjang::where('anggota_id', $anggota->id)->with('buku')->get()
            : collect();
        return view('keranjang', compact('keranjang'));
    })->name('member.keranjang');

    // 2. Tambah Buku ke Keranjang
    Route::post('/member/keranjang/tambah', function (\Illuminate\Http\Request $request) {
        if (Auth::user()->role !== 'Member') return response()->json(['error' => 'Unauthorized'], 403);
        $anggota = Auth::user()->anggota;
        if (!$anggota) return response()->json(['error' => 'Profil anggota tidak ditemukan'], 404);
        
        $sudahAda = \App\Models\Keranjang::where('anggota_id', $anggota->id)
                    ->where('buku_id', $request->buku_id)->exists();
        if ($sudahAda) return response()->json(['error' => 'Buku sudah ada di keranjang'], 409);
        
        $buku = \App\Models\Buku::findOrFail($request->buku_id);
        if ($buku->stok_tersedia <= 0) return response()->json(['error' => 'Stok buku habis'], 400);
        
        \App\Models\Keranjang::create([
            'anggota_id' => $anggota->id,
            'buku_id'    => $request->buku_id,
        ]);
        
        $jumlah = \App\Models\Keranjang::where('anggota_id', $anggota->id)->count();
        return response()->json(['success' => true, 'jumlah' => $jumlah]);
    })->name('member.keranjang.tambah');

    // 3. Hapus Buku dari Keranjang
    Route::delete('/member/keranjang/{id}', function ($id) {
        if (Auth::user()->role !== 'Member') return response()->json(['error' => 'Unauthorized'], 403);
        $anggota = Auth::user()->anggota;
        \App\Models\Keranjang::where('id', $id)->where('anggota_id', $anggota->id)->delete();
        return redirect()->route('member.keranjang')->with('success', 'Buku dihapus dari keranjang');
    })->name('member.keranjang.hapus');

    // 4. Proses Booking / Checkout
    Route::post('/member/keranjang/booking', function (\Illuminate\Http\Request $request) {
        if (Auth::user()->role !== 'Member') return redirect()->route('dashboard');
        
        $anggota   = Auth::user()->anggota;
        $keranjang = \App\Models\Keranjang::where('anggota_id', $anggota->id)->with('buku')->get();

        if ($keranjang->isEmpty()) {
            return redirect()->route('member.keranjang')->with('error', 'Keranjang kosong!');
        }

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

    })->name('member.keranjang.booking');

    // 5. Tampilan Peminjaman Saya (Tadinya error karena belum tergabung)
    Route::get('/member/peminjaman', function () {
        if (Auth::user()->role !== 'Member') return redirect()->route('dashboard');
        
        $anggota    = Auth::user()->anggota;
        $peminjaman = $anggota
            ? \App\Models\Peminjam::where('anggota_id', $anggota->id)
                ->with(['detailPeminjaman.buku'])
                ->latest('waktu_booking')->get()
            : collect();
        return view('peminjaman', compact('peminjaman')); // Perhatikan view ini disesuaikan ke 'peminjaman_saya'
    })->name('member.peminjaman');

    // 6. Tampilan Sukses OTP
    Route::get('/member/peminjaman/{id}/otp', function ($id) {
        if (Auth::user()->role !== 'Member') return redirect()->route('dashboard');
        
        $anggota    = Auth::user()->anggota;
        $peminjaman = \App\Models\Peminjam::where('id', $id)
                        ->where('anggota_id', $anggota->id)
                        ->with(['detailPeminjaman.buku'])
                        ->firstOrFail();
        return view('booking_sukses', compact('peminjaman'));
    })->name('member.peminjaman.otp');

});

// ============================================
// ROUTE PROFIL BAWAAN BREEZE
// ============================================

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';