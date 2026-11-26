<?php

use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PengadaanController;
use App\Http\Controllers\PengembalianController;
use App\Http\Controllers\RakController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Manajemen Perpustakaan Kampus
| Pemrograman Web - Semester 3
|--------------------------------------------------------------------------
*/

// ==========================================================
// HALAMAN UTAMA: TAMPILAN PERTAMA KALI ADALAH LOGIN PAGE
// ==========================================================
Route::get('/', [AuthController::class, 'showLogin'])->name('home');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================================
// MODUL-MODUL PERPUSTAKAAN KAMPUS
// ==========================================================

// 1. Modul Manajemen Katalog Buku
Route::get('/katalog-buku', [BukuController::class, 'index'])->name('katalog.buku');
Route::resource('buku', BukuController::class);

// 2. Modul Manajemen Rak Buku
Route::resource('rak', RakController::class);

// 3. Modul Manajemen Anggota Perpustakaan
Route::resource('anggota', AnggotaController::class);

// 4. Modul Transaksi Peminjaman Buku
Route::resource('peminjaman', PeminjamanController::class);
Route::put('/peminjaman/{id}/perpanjang', [PeminjamanController::class, 'perpanjang'])->name('peminjaman.perpanjang');

// 5. Modul Transaksi Pengembalian dan Denda
Route::resource('pengembalian', PengembalianController::class);
Route::put('/pengembalian/{id}/bayar-denda', [PengembalianController::class, 'bayarDenda'])->name('pengembalian.bayar-denda');

// 6. Modul Manajemen Pengadaan Buku Baru & Supplier
Route::resource('pengadaan', PengadaanController::class);
Route::post('/supplier', [PengadaanController::class, 'storeSupplier'])->name('supplier.store');
Route::put('/supplier/{id}', [PengadaanController::class, 'updateSupplier'])->name('supplier.update');
Route::delete('/supplier/{id}', [PengadaanController::class, 'destroySupplier'])->name('supplier.destroy');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
