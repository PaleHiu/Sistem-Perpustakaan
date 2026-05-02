<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Halaman Khusus Petugas/Admin
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Halaman Khusus Member
Route::get('/member/dashboard', function () {
    
    $user = Auth::user();
    $anggota = Auth::user()->anggota; 

    $sedangDipinjam = \App\Models\Peminjam::where('anggota_id', $anggota->id)
                            ->where('status_transaksi', 'Dipinjam')
                            ->count();

    $totalRiwayat = \App\Models\Peminjam::where('anggota_id', $anggota->id)
                            ->count();

    $totalDenda = \App\Models\Peminjam::where('anggota_id', $anggota->id)
                            ->sum('total_denda');

    return view('dashboard_member', compact('sedangDipinjam', 'totalRiwayat', 'totalDenda'));

})->middleware(['auth', 'verified'])->name('member.dashboard');

// Route bawaan Breeze untuk Profil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Panggil rute Auth bawaan Breeze
require __DIR__.'/auth.php';

// Halaman Manajemen Buku
Route::get('/books', function () {
    
    $totalTitles = 0;
    $totalItems = 0;
    $lowStock = 0;
    $reservations = 0;
    $books = [];

   
    // $totalTitles = \App\Models\Buku::count();
    // $totalItems = \App\Models\Buku::sum('stok');
    // $lowStock = \App\Models\Buku::where('stok', '<', 5)->count();
    // $books = \App\Models\Buku::latest()->get();
   

    return view('books', compact('totalTitles', 'totalItems', 'lowStock', 'reservations', 'books'));

})->middleware(['auth', 'verified'])->name('books.index');

// Halaman Manajemen Anggota
Route::get('/members', function () {
    
    // Siapkan array kosong sebagai default
    $members = []; 
    /*
    $members = \App\Models\Anggota::with('user')->latest()->get();
    */

    return view('members', compact('members'));

})->middleware(['auth', 'verified'])->name('members.index');

// Halaman Peminjaman (Borrowing)
Route::get('/borrowing', function () {
    
    // Variabel array kosong sebagai default (menunggu Database)
    $borrowings = []; 

    /*
    $borrowings = \App\Models\Peminjaman::with(['anggota', 'buku'])->latest()->get();
    */

    return view('borrowing', compact('borrowings'));

})->middleware(['auth', 'verified'])->name('borrowing.index');

Route::get('/member/katalog', function () {
    return view('katalog');
})->middleware(['auth', 'verified'])->name('katalog');