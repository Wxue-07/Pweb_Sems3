<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Modul 2: Manajemen Anggota Perpustakaan (Budi Yanto)
     */
    public function up(): void
    {
        if (!Schema::hasTable('anggota')) {
            Schema::create('anggota', function (Blueprint $table) {
                $table->id('id_anggota');
                $table->string('nomor_anggota', 30)->unique();
                $table->string('nama_lengkap', 100);
                $table->string('jenis_anggota', 30)->default('Mahasiswa'); // Mahasiswa, Dosen, Staff
                $table->string('nim_nidn', 30)->nullable();
                $table->string('program_studi', 100)->nullable();
                $table->string('email', 100)->nullable();
                $table->string('nomor_telepon', 20)->nullable();
                $table->text('alamat')->nullable();
                $table->string('status_aktif', 30)->default('Aktif'); // Aktif, Nonaktif, Lulus / Keluar
                $table->date('tanggal_bergabung')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggota');
    }
};
