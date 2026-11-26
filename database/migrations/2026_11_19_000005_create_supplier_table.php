<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Modul 5: Data Supplier (Rowin Journandez)
     */
    public function up(): void
    {
        if (!Schema::hasTable('supplier')) {
            Schema::create('supplier', function (Blueprint $table) {
                $table->id('id_supplier');
                $table->string('kode_supplier', 30)->unique();
                $table->string('nama_supplier', 100);
                $table->string('kontak_person', 100)->nullable();
                $table->string('telepon', 20)->nullable();
                $table->string('email', 100)->nullable();
                $table->text('alamat')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier');
    }
};
