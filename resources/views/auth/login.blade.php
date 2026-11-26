<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Petugas Perpustakaan - Sistem Perpustakaan Kampus</title>

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
            max-width: 440px;
            padding: 38px 32px;
        }

        .auth-brand {
            text-align: center;
            margin-bottom: 28px;
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
                    <i class="bi bi-book-half"></i>
                </div>
                <h2>PERPUSTAKAAN UIB</h2>
                <p>Masuk ke Akun Petugas & Admin Perpustakaan</p>
            </div>

            <!-- Pesan Flash -->
            @if(session('success'))
                <div class="alert-box" style="margin-bottom: 18px;">
                    <div class="alert-content">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="alert-box alert-error" style="margin-bottom: 18px;">
                    <div class="alert-content">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <div>
                            @foreach($errors->all() as $err)
                                <div>{{ $err }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Form Login -->
            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="form-group" style="margin-bottom: 18px;">
                    <label for="email"><i class="bi bi-envelope"></i> Alamat Email <span class="required">*</span></label>
                    <input type="email" name="email" id="email" class="form-control" 
                           placeholder="Masukkan alamat email Anda" value="{{ old('email') }}" required autofocus>
                </div>

                <div class="form-group" style="margin-bottom: 18px;">
                    <label for="password"><i class="bi bi-shield-lock"></i> Kata Sandi / Password <span class="required">*</span></label>
                    <div style="position: relative;">
                        <input type="password" name="password" id="password" class="form-control" 
                               placeholder="Masukkan password Anda" required style="padding-right: 40px;">
                        <button type="button" onclick="togglePassword()" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 16px;">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <div style="display: flex; align-items: center; margin-bottom: 22px; font-size: 13px;">
                    <label style="display: flex; align-items: center; gap: 7px; cursor: pointer;">
                        <input type="checkbox" name="remember" style="accent-color: var(--btn-primary); width: 16px; height: 16px;">
                        Ingat Saya
                    </label>
                </div>

                <button type="submit" class="btn-main" style="width: 100%; justify-content: center; padding: 12px; font-size: 15px;">
                    <i class="bi bi-box-arrow-in-right"></i> Masuk Sekarang
                </button>
            </form>

            <!-- Navigasi Registrasi -->
            <div class="auth-footer">
                Belum memiliki akun terdaftar? 
                <a href="{{ route('register') }}"><i class="bi bi-person-plus"></i> Daftar di sini</a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const pwd = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                pwd.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
    </script>
</body>
</html>
