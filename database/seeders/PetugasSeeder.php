<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PetugasSeeder extends Seeder
{
public function run(): void
{
    // 1. Buat User untuk Login Admin
    $userId = DB::table('users')->insertGetId([
        'email'      => 'admin@sipus.id',
        'password'   => Hash::make('superadmin'),
        'role'       => 'Petugas',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // 2. Buat Profilnya di tabel Petugas
    DB::table('petugas')->insert([
        'user_id'      => $userId,
        'nama_petugas' => 'Administrator Utama',
        'created_at'   => now(),
        'updated_at'   => now(),
    ]);
}
}