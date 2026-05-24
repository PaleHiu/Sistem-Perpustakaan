<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Keranjang extends Model
{
    protected $table    = 'keranjang';
    protected $fillable = ['anggota_id', 'buku_id'];

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'buku_id');
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'anggota_id');
    }
}