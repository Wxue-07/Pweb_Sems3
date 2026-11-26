<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Modul CRUD Rak Buku Perpustakaan
     */
    public function up(): void
    {
        if (!Schema::hasTable('rak_buku')) {
            Schema::create('rak_buku', function (Blueprint $table) {
                $table->id('id_rak');
                $table->string('kode_rak', 30)->unique();
                $table->string('nama_rak', 100);
                $table->string('kategori_buku', 100)->nullable();
                $table->string('lokasi_lantai', 100)->default('Lantai 1');
                $table->integer('kapasitas_maksimal')->default(50);
                $table->text('keterangan')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rak_buku');
    }
};
