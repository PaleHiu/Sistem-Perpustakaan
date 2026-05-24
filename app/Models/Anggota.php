<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    use HasFactory;

    protected $table = 'anggota';

    protected $fillable = [
        'user_id', 
        'nama_lengkap', 
        'nik', 
        'no_hp', 
        'alamat', 
        'status_verifikasi',
        'dokumen_identitas',
    ];

public function user()
{
    // Menghubungkan Anggota ke tabel Users berdasarkan kolom user_id
    return $this->belongsTo(User::class, 'user_id');
}

public function peminjaman()
{
    return $this->hasMany(\App\Models\Peminjam::class, 'anggota_id');
}
}