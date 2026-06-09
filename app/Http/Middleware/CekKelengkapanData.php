<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CekKelengkapanData
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Pastikan user sudah login terlebih dahulu
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 2. Ambil data anggota dari user yang sedang login
        $anggota = Auth::user()->anggota;

        // 3. Cek apakah relasi anggota tidak ditemukan, atau NIK, No HP, atau Alamat masih kosong
        if (!$anggota || empty($anggota->nik) || empty($anggota->no_hp) || empty($anggota->alamat)) {
            
            // 4. Jika kosong atau tidak ada, tendang (redirect) ke halaman profil dan bawa pesan error
            return redirect()->route('member.profil')->with('warning', 'Akses ditolak! Silakan lengkapi NIK, No. HP, dan Alamat Anda terlebih dahulu untuk mulai meminjam buku.');
        }

        // 5. Jika data lengkap, persilakan masuk ke halaman yang dituju
        return $next($request);
    }
}