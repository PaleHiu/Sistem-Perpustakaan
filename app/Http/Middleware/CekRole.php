<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CekRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Jika belum login, arahkan ke landing page
        if (!Auth::check()) {
            return redirect('/landing');
        }

        // 2. Jika Role tidak sesuai dengan yang diminta oleh Route
        if (Auth::user()->role !== $role) {
            // Evaluasi siapa pelakunya, lalu arahkan ke "rumah" masing-masing
            if (Auth::user()->role === 'Petugas') {
                return redirect()->route('dashboard')->with('warning', 'Akses ditolak! Anda mencoba memasuki area khusus Member.');
            } else {
                return redirect()->route('member.dashboard')->with('warning', 'Akses ditolak! Halaman ini dikhususkan untuk Petugas/Admin.');
            }
        }

        // 3. Jika aman dan sesuai, persilakan masuk
        return $next($request);
    }
}