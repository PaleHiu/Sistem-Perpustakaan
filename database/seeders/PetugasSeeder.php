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
        // Panggil fungsi tambahan yang menangani pengecekan
        $this->seedAdminIfNotExists();
    }

    /**
     * Fungsi tambahan untuk mengecek duplikasi sebelum menjalankan source code asli
     */
    private function seedAdminIfNotExists(): void
    {
        $adminEmail = 'admin@sipus.id';

        // Pengecekan: Apakah email sudah terdaftar di tabel users?
        $isUserExists = DB::table('users')->where('email', $adminEmail)->exists();

        // Jika sudah ada, tampilkan pesan dan hentikan eksekusi fungsi ini
        if ($isUserExists) {
            $this->command->info("Seeder dilewati: User dengan email {$adminEmail} sudah ada.");
            return; 
        }

        // =========================================================
        // SOURCE KODE ASLI ANDA MULAI DARI SINI (TIDAK DIUBAH SAMA SEKALI)
        // =========================================================

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

        // =========================================================
        // BATAS SOURCE KODE ASLI
        // =========================================================

        // Pesan sukses jika berhasil ditambahkan
        $this->command->info('Seeder Petugas berhasil ditambahkan ke database.');
    }
}