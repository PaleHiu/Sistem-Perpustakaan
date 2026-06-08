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
        // Jika belum login, atau rolenya tidak sesuai dengan yang diminta route
        if (!Auth::check() || Auth::user()->role !== $role) {
            // Lempar ke halaman landing (atau bisa dikustomisasi ke halaman 403 Forbidden)
            return redirect('/landing'); 
        }

        return $next($request);
    }
}