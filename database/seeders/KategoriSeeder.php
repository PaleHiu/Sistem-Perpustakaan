<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Kategori::insert([
        ['nama_kategori' => 'Komputer'],
        ['nama_kategori' => 'Matematika'],
        ['nama_kategori' => 'Bahasa'],
        ['nama_kategori' => 'Sains'],
        ['nama_kategori' => 'Sejarah'],
        ['nama_kategori' => 'Umum'],
    ]);
    }
}
