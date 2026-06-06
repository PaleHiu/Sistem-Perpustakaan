<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CekUserMasihAda
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            // Cek langsung ke database, bypass cache
            $userMasihAda = User::withoutGlobalScopes()->find(Auth::id());

            if (!$userMasihAda) {
                // Paksa logout dan hapus session
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('error', 'Akun kamu tidak ditemukan. Silakan hubungi admin.');
            }
        }

        return $next($request);
    }
}