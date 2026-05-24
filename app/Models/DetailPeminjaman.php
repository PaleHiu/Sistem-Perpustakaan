<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPeminjaman extends Model
{
    protected $table    = 'detail_peminjaman';
    protected $fillable = [
        'peminjaman_id',
        'buku_id',
        'jumlah',
    ];

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'buku_id');
    }

    public function peminjaman()
    {
        return $this->belongsTo(Peminjam::class, 'peminjaman_id');
    }
}