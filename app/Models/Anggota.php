<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    use HasFactory;

    // Modul 2: Budi Yanto (Manajemen Anggota)
    protected $table = 'anggota';
    protected $primaryKey = 'id_anggota';
    public $timestamps = false;

    protected $fillable = [
        'nomor_anggota',
        'nama_lengkap',
        'jenis_anggota',
        'nim_nidn',
        'program_studi',
        'email',
        'nomor_telepon',
        'alamat',
        'status_aktif',
        'tanggal_bergabung',
    ];

    // Relasi ke tabel transaksi peminjaman
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'id_anggota', 'id_anggota');
    }
}
