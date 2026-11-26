<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;

    protected $table = 'buku';
    protected $primaryKey = 'id_buku';
    public $timestamps = false;

    protected $fillable = [
        'isbn',
        'judul',
        'penulis',
        'penerbit',
        'kategori_rak',
        'sinopsis',
        'stok_tersedia',
    ];

    // Relasi ke riwayat peminjaman
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'id_buku', 'id_buku');
    }

    // Relasi ke transaksi pengadaan buku
    public function pengadaan()
    {
        return $this->hasMany(Pengadaan::class, 'id_buku', 'id_buku');
    }
}
