<!-- Sidebar (Warna Coklat Kayu: #79583F) -->
<aside class="sidebar" id="sidebar">
    <!-- Brand Header -->
    <div class="sidebar-brand">
        <div class="brand-icon">
            <i class="bi bi-book-half"></i>
        </div>
        <div class="brand-text">
            <h2>UIB'S LIBRARY</h2>
            <p>Web App Perpustakaan</p>
        </div>
    </div>

    <!-- User Profile Badge -->
    <div class="user-profile-badge">
        <div class="user-avatar">
            <i class="bi bi-person-circle"></i>
        </div>
        <div class="user-info">
            <h4>{{ Auth::check() ? Auth::user()->name : 'Petugas Perpustakaan' }}</h4>
            <span class="badge-role">{{ Auth::check() ? 'Petugas Aktif' : 'Sistem Kampus' }}</span>
        </div>
    </div>

    <div class="sidebar-section-title">Menu Utama</div>

    <!-- Navigation Menu (Hanya Menampilkan Nama Modul) -->
    <ul class="sidebar-nav">
        <!-- 1. Katalog Buku -->
        <li>
            <a href="{{ route('katalog.buku') }}" class="nav-link {{ request()->is('katalog*') || (request()->is('buku*') && !request()->is('rak*')) ? 'active' : '' }}">
                <i class="bi bi-journal-bookmark-fill"></i>
                <div>
                    <span>Katalog Buku</span>
                </div>
                @if(request()->is('katalog*') || (request()->is('buku*') && !request()->is('rak*')))
                    <span class="nav-tag-active">Aktif</span>
                @endif
            </a>
        </li>

        <!-- 2. Manajemen Rak Buku -->
        <li>
            <a href="{{ route('rak.index') }}" class="nav-link {{ request()->is('rak*') ? 'active' : '' }}">
                <i class="bi bi-bookshelf"></i>
                <div>
                    <span>Manajemen Rak Buku</span>
                </div>
                @if(request()->is('rak*'))
                    <span class="nav-tag-active">Aktif</span>
                @endif
            </a>
        </li>

        <!-- 3. Data Anggota -->
        <li>
            <a href="{{ route('anggota.index') }}" class="nav-link {{ request()->is('anggota*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i>
                <div>
                    <span>Data Anggota</span>
                </div>
                @if(request()->is('anggota*'))
                    <span class="nav-tag-active">Aktif</span>
                @endif
            </a>
        </li>

        <!-- 4. Peminjaman Buku -->
        <li>
            <a href="{{ route('peminjaman.index') }}" class="nav-link {{ request()->is('peminjaman*') ? 'active' : '' }}">
                <i class="bi bi-arrow-left-right"></i>
                <div>
                    <span>Peminjaman Buku</span>
                </div>
                @if(request()->is('peminjaman*'))
                    <span class="nav-tag-active">Aktif</span>
                @endif
            </a>
        </li>

        <!-- 5. Pengembalian Buku & Denda -->
        <li>
            <a href="{{ route('pengembalian.index') }}" class="nav-link {{ request()->is('pengembalian*') ? 'active' : '' }}">
                <i class="bi bi-cash-coin"></i>
                <div>
                    <span>Pengembalian Buku & Denda</span>
                </div>
                @if(request()->is('pengembalian*'))
                    <span class="nav-tag-active">Aktif</span>
                @endif
            </a>
        </li>

        <!-- 6. Pengadaan Buku Baru -->
        <li>
            <a href="{{ route('pengadaan.index') }}" class="nav-link {{ request()->is('pengadaan*') || request()->is('supplier*') ? 'active' : '' }}">
                <i class="bi bi-box-seam-fill"></i>
                <div>
                    <span>Pengadaan Buku Baru</span>
                </div>
                @if(request()->is('pengadaan*') || request()->is('supplier*'))
                    <span class="nav-tag-active">Aktif</span>
                @endif
            </a>
        </li>
    </ul>

    <!-- Status Autentikasi / Tombol Keluar / Masuk -->
    <div style="padding: 10px 14px; margin: 8px 12px 10px 12px; background: rgba(0, 0, 0, 0.2); border-radius: 10px; border: 1px solid rgba(233, 223, 211, 0.18);">
        @auth
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                <div style="overflow: hidden;">
                    <div style="font-size: 12px; font-weight: 700; color: #FFFDF9; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                        <i class="bi bi-person-check-fill" style="color: #81C784;"></i> {{ Auth::user()->name }}
                    </div>
                    <div style="font-size: 10px; color: #E9DFD3; opacity: 0.85;">Petugas Aktif</div>
                </div>
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" title="Keluar / Logout" style="background: rgba(211, 47, 47, 0.9); color: #FFF; border: none; padding: 4px 10px; border-radius: 6px; cursor: pointer; font-size: 11px; display: flex; align-items: center; gap: 4px;">
                        <i class="bi bi-box-arrow-right"></i> Keluar
                    </button>
                </form>
            </div>
        @else
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="font-size: 11.5px; color: #E9DFD3;">
                    <i class="bi bi-shield-lock"></i> Sesi Petugas:
                </div>
                <a href="{{ route('login') }}" style="background: var(--menu-active); color: #FFF; text-decoration: none; padding: 4px 12px; border-radius: 6px; font-size: 11px; font-weight: 700;">
                    Login Petugas
                </a>
            </div>
        @endauth
    </div>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <p><i class="bi bi-database-check"></i> Kelompok: <strong>PPRI</strong></p>
        <p style="margin-top: 3px; font-size: 10px; opacity: 0.85;">PROJECT PWEB SEMS3</p>
    </div>
</aside>
