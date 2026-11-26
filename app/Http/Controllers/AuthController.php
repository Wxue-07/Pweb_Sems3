<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Halaman Login Petugas / Admin Perpustakaan
     */
    public function showLogin()
    {
        // Jika sudah login, langsung arahkan ke katalog buku
        if (Auth::check()) {
            return redirect()->route('katalog.buku');
        }

        return view('auth.login');
    }

    /**
     * Proses Login ke Database
     */
    public function login(Request $request)
    {
        // 1. Validasi input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $remember = $request->filled('remember');

        // 2. Cek kredensial ke database (tabel users)
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $userName = Auth::user()->name;

            return redirect()->intended(route('katalog.buku'))
                ->with('success', "Selamat datang kembali, {$userName}! Anda berhasil login ke sistem.");
        }

        // 3. Jika login gagal
        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan tidak sesuai dengan database.',
        ])->onlyInput('email');
    }

    /**
     * Halaman Registrasi Petugas / Anggota Baru
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('katalog.buku');
        }

        return view('auth.register');
    }

    /**
     * Proses Registrasi Pengguna Baru ke Database
     */
    public function register(Request $request)
    {
        // 1. Validasi input registrasi
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar di sistem perpustakaan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // 2. Simpan user baru ke database
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // 3. Langsung login setelah registrasi
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('katalog.buku')
            ->with('success', "Registrasi berhasil! Selamat datang di Sistem Perpustakaan Kampus, {$user->name}.");
    }

    /**
     * Proses Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil keluar (logout) dari sistem perpustakaan.');
    }
}
