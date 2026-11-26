<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    // Modul 3: Jeovanna (Transaksi Peminjaman)
    protected $table = 'peminjaman';
    protected $primaryKey = 'id_peminjaman';
    public $timestamps = false;

    protected $fillable = [
        'kode_transaksi',
        'id_anggota',
        'id_buku',
        'tanggal_pinjam',
        'tanggal_jatuh_tempo',
        'tanggal_kembali',
        'status_peminjaman',
        'catatan',
    ];

    // Relasi ke Anggota Perpustakaan
    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'id_anggota', 'id_anggota');
    }

    // Relasi ke Katalog Buku
    public function buku()
    {
        return $this->belongsTo(Buku::class, 'id_buku', 'id_buku');
    }

    // Relasi ke Catatan Pengembalian dan Denda
    public function pengembalian()
    {
        return $this->hasOne(PengembalianDenda::class, 'id_peminjaman', 'id_peminjaman');
    }
}
