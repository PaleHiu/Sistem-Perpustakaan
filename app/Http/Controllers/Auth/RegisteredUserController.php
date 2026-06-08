<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Anggota;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // Menggunakan UI Custom kamu
        return view('auth.index');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            
            // 1. Tambahkan 'ends_with:@gmail.com' pada array email
            'email'      => [
                'required', 
                'string', 
                'lowercase', 
                'email', 
                'max:255', 
                'ends_with:@gmail.com', 
                'unique:'.User::class
            ],
            'password'   => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            // 2. Tambahkan pesan error kustom (bahasa Indonesia)
            'email.ends_with' => 'Keamanan sistem: Pendaftaran hanya diizinkan menggunakan alamat @gmail.com.'
        ]);

        // 1. Simpan akun login
        $user = User::create([
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'Member',
        ]);

        // 2. Simpan nama ke profil anggota
        Anggota::create([
            'user_id'           => $user->id,
            'nama_lengkap'      => $request->first_name . ' ' . $request->last_name,
            'status_verifikasi' => 'Incomplete', // Menjaga konsistensi status awal
        ]);

        event(new Registered($user));

        Auth::login($user);

        // 3. Arahkan ke halaman Dashboard Member
        return redirect()->route('member.dashboard'); 
    }
}