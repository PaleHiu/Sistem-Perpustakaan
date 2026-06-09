<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjam extends Model
{
    protected $table = 'peminjaman'; // Sesuai nama tabel di ERD

    protected $fillable = [
        'anggota_id', 'petugas_id', 'kode_otp', 'waktu_booking', 'otp_expired_at', 'tanggal_pinjam', 'batas_pengembalian', 'tanggal_dikembalikan', 'status_transaksi', 'total_denda'
    ];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'anggota_id');
    }

    public function detailPeminjaman()
    {
        return $this->hasMany(DetailPeminjaman::class, 'peminjaman_id');
    }
}