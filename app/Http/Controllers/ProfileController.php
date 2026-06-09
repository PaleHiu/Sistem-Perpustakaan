<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information (Bawaan Breeze untuk Email/Password).
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * ========================================================
     * FUNGSI BARU: Update Profil Member (Kamar Anggota)
     * ========================================================
     */
public function updateProfilMember(\Illuminate\Http\Request $request)
{
    $request->validate([
        'nama_lengkap'      => 'required|string|max:255',
        'nik'               => 'required|string|max:16',
        'no_hp'             => 'required|string|max:15',
        'alamat'            => 'required|string',
        'dokumen_identitas' => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:2048', // Maksimal 2MB
    ]);

    $anggota = \Illuminate\Support\Facades\Auth::user()->anggota;

    $dataUpdate = [
        'nama_lengkap' => $request->nama_lengkap,
        'nik'          => $request->nik,
        'no_hp'        => $request->no_hp,
        'alamat'       => $request->alamat,
    ];

    // Jika user mengunggah file baru
    if ($request->hasFile('dokumen_identitas')) {
        $path = $request->file('dokumen_identitas')->store('dokumen_member', 'public');
        $dataUpdate['dokumen_identitas'] = $path;
        $dataUpdate['status_verifikasi'] = 'Pending'; // Otomatis jadi pending untuk dicek Admin
    }

    $anggota->update($dataUpdate);

    return redirect()->back()->with('success', 'Profil dan dokumen berhasil disimpan! Menunggu verifikasi Admin.');
}
}