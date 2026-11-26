<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rak extends Model
{
    use HasFactory;

    // Modul CRUD Rak Buku Perpustakaan
    protected $table = 'rak_buku';
    protected $primaryKey = 'id_rak';
    public $timestamps = false;

    protected $fillable = [
        'kode_rak',
        'nama_rak',
        'kategori_buku',
        'kapasitas_maksimal',
        'keterangan',
    ];

    /**
     * Hitung perkiraan buku yang diletakkan pada rak ini
     */
    public function getJumlahBukuAttribute()
    {
        return Buku::where('kategori_rak', 'like', "%{$this->kode_rak}%")
            ->orWhere('kategori_rak', 'like', "%{$this->nama_rak}%")
            ->count();
    }
}
