<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjam extends Model
{
    protected $table = 'peminjaman'; // Sesuai nama tabel di ERD

    protected $fillable = [
        'anggota_id', 'petugas_id', 'kode_otp', 'status_transaksi', 'total_denda'
    ];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'anggota_id');
    }
}