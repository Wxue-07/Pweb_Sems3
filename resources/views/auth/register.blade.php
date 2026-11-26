<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Akun Baru - Sistem Perpustakaan Kampus</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Tema Warna Coklat Putih Perpustakaan PPRI -->
    <link rel="stylesheet" href="{{ asset('css/style-perpustakaan.css') }}">

    <style>
        .auth-container {
            min-height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #F7F3ED 0%, #E9DFD3 100%);
            padding: 24px 16px;
        }

        .auth-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-beige);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(62, 48, 38, 0.08);
            width: 100%;
            max-width: 480px;
            padding: 36px 32px;
        }

        .auth-brand {
            text-align: center;
            margin-bottom: 24px;
        }

        .auth-brand .brand-icon-big {
            width: 58px;
            height: 58px;
            background-color: var(--sidebar-bg);
            color: #FFFDF9;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 12px;
            box-shadow: 0 6px 14px rgba(121, 88, 63, 0.25);
        }

        .auth-brand h2 {
            font-size: 20px;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: 0.5px;
        }

        .auth-brand p {
            font-size: 13px;
            color: var(--text-secondary);
            margin-top: 4px;
        }

        .auth-footer {
            text-align: center;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid var(--border-beige);
            font-size: 13px;
            color: var(--text-secondary);
        }

        .auth-footer a {
            color: var(--btn-primary);
            text-decoration: none;
            font-weight: 700;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="auth-container">
        <div class="auth-card">
            <!-- Header Brand -->
            <div class="auth-brand">
                <div class="brand-icon-big">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <h2>PERPUSTAKAAN UIB</h2>
                <p>Pendaftaran Akun Baru Petugas / Admin</p>
            </div>

            <!-- Pesan Notifikasi Error -->
            @if($errors->any())
                <div class="alert-box alert-error" style="margin-bottom: 18px;">
                    <div class="alert-content">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <div>
                            @foreach($errors->all() as $err)
                                <div>&bull; {{ $err }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Form Registrasi -->
            <form action="{{ route('register.post') }}" method="POST">
                @csrf
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="reg_name"><i class="bi bi-person"></i> Nama Lengkap <span class="required">*</span></label>
                    <input type="text" name="name" id="reg_name" class="form-control" 
                           placeholder="Contoh: Nama Petugas Perpustakaan" value="{{ old('name') }}" required autofocus>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="reg_email"><i class="bi bi-envelope"></i> Alamat Email <span class="required">*</span></label>
                    <input type="email" name="email" id="reg_email" class="form-control" 
                           placeholder="nama@uib.ac.id" value="{{ old('email') }}" required>
                </div>

                <div class="form-row" style="margin-bottom: 15px;">
                    <div class="form-group">
                        <label for="reg_password"><i class="bi bi-lock"></i> Kata Sandi <span class="required">*</span></label>
                        <input type="password" name="password" id="reg_password" class="form-control" 
                               placeholder="Min. 6 karakter" required>
                    </div>
                    <div class="form-group">
                        <label for="reg_password_confirmation"><i class="bi bi-lock-fill"></i> Konfirmasi <span class="required">*</span></label>
                        <input type="password" name="password_confirmation" id="reg_password_confirmation" class="form-control" 
                               placeholder="Ulangi kata sandi" required>
                    </div>
                </div>

                <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 20px;">
                    <i class="bi bi-shield-check" style="color: #2E7D32;"></i> Data pengguna akan langsung disimpan dengan aman ke dalam tabel <code>users</code> database.
                </div>

                <button type="submit" class="btn-main" style="width: 100%; justify-content: center; padding: 12px; font-size: 15px;">
                    <i class="bi bi-person-check-fill"></i> Daftar & Masuk ke Sistem
                </button>
            </form>

            <!-- Navigasi ke Login -->
            <div class="auth-footer">
                Sudah memiliki akun terdaftar? 
                <a href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right"></i> Masuk di sini</a>
            </div>
        </div>
    </div>

</body>
</html>
