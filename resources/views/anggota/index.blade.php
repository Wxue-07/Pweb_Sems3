<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data Anggota - Sistem Perpustakaan Kampus</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Tema Warna Coklat Putih Perpustakaan PPRI -->
    <link rel="stylesheet" href="{{ asset('css/style-perpustakaan.css') }}">
</head>
<body>

    <!-- Sidebar Komponen Bersama -->
    @include('partials.sidebar')

    <!-- Main Content Wrapper -->
    <div class="main-wrapper">
        <!-- Top Navbar -->
        <header class="top-navbar">
            <div class="top-left">
                <button class="toggle-btn" id="sidebarToggle" aria-label="Toggle menu">
                    <i class="bi bi-list"></i>
                </button>
                <div class="breadcrumb">
                    <i class="bi bi-house-door"></i>
                    <i class="bi bi-chevron-right" style="font-size: 10px;"></i>
                    <span>Data Anggota</span>
                </div>
            </div>

            <div class="top-right">
                <a href="{{ route('anggota.index') }}" class="btn-secondary" title="Muat Ulang Data">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </a>
                <button type="button" class="btn-main" onclick="openModal('modalAddAnggota')">
                    <i class="bi bi-person-plus-fill"></i> Tambah Anggota Baru
                </button>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="content-area">
            <!-- Header Judul Modul -->
            <div class="page-header">
                <h1>Data Anggota</h1>
                <p>Kelola data pendaftaran mahasiswa dan dosen perpustakaan kampus, pembaruan informasi kontak, status keanggotaan, serta riwayat anggota secara terstruktur.</p>
            </div>

            <!-- Pesan Notifikasi Sukses / Gagal -->
            @if(session('success'))
                <div class="alert-box" id="flashAlert">
                    <div class="alert-content">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button class="alert-close" onclick="document.getElementById('flashAlert').remove()">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert-box alert-error" id="flashAlertError">
                    <div class="alert-content">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button class="alert-close" onclick="document.getElementById('flashAlertError').remove()">&times;</button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert-box alert-error" id="flashAlertValidation">
                    <div class="alert-content">
                        <i class="bi bi-exclamation-octagon-fill"></i>
                        <div>
                            @foreach($errors->all() as $err)
                                <div>&bull; {{ $err }}</div>
                            @endforeach
                        </div>
                    </div>
                    <button class="alert-close" onclick="document.getElementById('flashAlertValidation').remove()">&times;</button>
                </div>
            @endif

            <!-- 4 Kartu Statistik Anggota -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-info">
                        <p>Total Semua Anggota</p>
                        <h3>{{ $stats['total_anggota'] }}</h3>
                    </div>
                    <div class="stat-icon brown">
                        <i class="bi bi-people"></i>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <p>Anggota Berstatus Aktif</p>
                        <h3>{{ $stats['total_aktif'] }}</h3>
                    </div>
                    <div class="stat-icon wood">
                        <i class="bi bi-person-check"></i>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <p>Anggota Mahasiswa</p>
                        <h3>{{ $stats['total_mahasiswa'] }}</h3>
                    </div>
                    <div class="stat-icon cream">
                        <i class="bi bi-mortarboard"></i>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <p>Anggota Dosen & Staff</p>
                        <h3>{{ $stats['total_dosen_staff'] }}</h3>
                    </div>
                    <div class="stat-icon warning">
                        <i class="bi bi-person-badge"></i>
                    </div>
                </div>
            </div>

            <!-- Form Pencarian & Filter Anggota -->
            <div class="filter-card">
                <form action="{{ route('anggota.index') }}" method="GET" class="filter-form">
                    <div class="search-input-group">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Cari nama anggota, nomor anggota, NIM/NIDN, email, atau program studi..." 
                               value="{{ request('search') }}">
                    </div>

                    <!-- Filter Jenis Anggota -->
                    <select name="jenis_anggota" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Semua Jenis Anggota --</option>
                        <option value="Mahasiswa" {{ request('jenis_anggota') == 'Mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                        <option value="Dosen" {{ request('jenis_anggota') == 'Dosen' ? 'selected' : '' }}>Dosen</option>
                        <option value="Staff" {{ request('jenis_anggota') == 'Staff' ? 'selected' : '' }}>Staff</option>
                    </select>

                    <!-- Filter Status Aktif -->
                    <select name="status_aktif" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Semua Status --</option>
                        <option value="Aktif" {{ request('status_aktif') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Nonaktif" {{ request('status_aktif') == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        <option value="Lulus / Keluar" {{ request('status_aktif') == 'Lulus / Keluar' ? 'selected' : '' }}>Lulus / Keluar</option>
                    </select>

                    <button type="submit" class="btn-main" style="padding: 9px 16px;">
                        <i class="bi bi-funnel"></i> Filter
                    </button>

                    @if(request()->hasAny(['search', 'jenis_anggota', 'status_aktif']))
                        <a href="{{ route('anggota.index') }}" class="btn-secondary" style="padding: 9px 14px;">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Tabel Data Anggota Perpustakaan -->
            <div class="table-card">
                <div class="table-header-box">
                    <h3>
                        <i class="bi bi-person-lines-fill"></i>
                        Daftar Anggota Perpustakaan Kampus
                    </h3>
                    <span class="table-badge-count">{{ count($anggota) }} Anggota Terdata</span>
                </div>

                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th style="width: 50px; text-align: center;">No</th>
                                <th style="width: 140px;">Nomor Anggota</th>
                                <th>Nama Lengkap & Jenis</th>
                                <th>NIM / NIDN & Prodi</th>
                                <th>Informasi Kontak</th>
                                <th style="width: 130px; text-align: center;">Status</th>
                                <th style="width: 130px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($anggota as $index => $item)
                                <tr>
                                    <td style="text-align: center; color: var(--text-secondary); font-weight: 600;">
                                        {{ $index + 1 }}
                                    </td>
                                    <td>
                                        <span class="badge-isbn">{{ $item->nomor_anggota }}</span>
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: var(--text-primary); font-size: 14px;">
                                            {{ $item->nama_lengkap }}
                                        </div>
                                        <div style="margin-top: 3px;">
                                            @if($item->jenis_anggota === 'Dosen')
                                                <span class="badge-status info"><i class="bi bi-person-badge"></i> Dosen</span>
                                            @elseif($item->jenis_anggota === 'Staff')
                                                <span class="badge-status warning"><i class="bi bi-person-gear"></i> Staff</span>
                                            @else
                                                <span class="badge-status neutral"><i class="bi bi-mortarboard"></i> Mahasiswa</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: var(--text-primary);">
                                            <i class="bi bi-card-text" style="color: var(--text-secondary); margin-right: 4px;"></i>{{ $item->nim_nidn ?? '-' }}
                                        </div>
                                        <div style="font-size: 12px; color: var(--text-secondary); margin-top: 2px;">
                                            <i class="bi bi-mortarboard" style="color: var(--text-secondary); margin-right: 4px;"></i>{{ $item->program_studi ?? 'Umum' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size: 12.5px; color: var(--text-primary);">
                                            <i class="bi bi-envelope" style="color: var(--text-secondary); margin-right: 4px;"></i>{{ $item->email ?? '-' }}
                                        </div>
                                        <div style="font-size: 12px; color: var(--text-secondary); margin-top: 2px;">
                                            <i class="bi bi-telephone" style="color: var(--text-secondary); margin-right: 4px;"></i>{{ $item->nomor_telepon ?? '-' }}
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        @if($item->status_aktif === 'Aktif')
                                            <span class="badge-status success"><i class="bi bi-check-circle"></i> Aktif</span>
                                        @elseif($item->status_aktif === 'Nonaktif')
                                            <span class="badge-status danger"><i class="bi bi-dash-circle"></i> Nonaktif</span>
                                        @else
                                            <span class="badge-status warning"><i class="bi bi-box-arrow-right"></i> {{ $item->status_aktif }}</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="action-group" style="justify-content: center;">
                                            <!-- Tombol Detail -->
                                            <button type="button" class="btn-action view" title="Lihat Profil Anggota"
                                                    onclick="viewAnggotaDetail({{ json_encode($item) }})">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <!-- Tombol Edit -->
                                            <button type="button" class="btn-action edit" title="Edit Data Anggota"
                                                    onclick="editAnggotaModal({{ json_encode($item) }})">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <!-- Tombol Hapus -->
                                            <button type="button" class="btn-action delete" title="Hapus Anggota"
                                                    onclick="confirmDeleteAnggota({{ $item->id_anggota }}, '{{ addslashes($item->nama_lengkap) }}')">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="empty-state">
                                            <i class="bi bi-person-x"></i>
                                            <h4>Tidak ada data anggota yang sesuai</h4>
                                            <p>Coba gunakan kata kunci pencarian lain atau tambahkan data anggota baru.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL 1: Tambah Anggota Baru (Insert) -->
    <div class="modal-overlay" id="modalAddAnggota">
        <div class="modal-card">
            <div class="modal-header">
                <h3><i class="bi bi-person-plus" style="color: var(--btn-primary);"></i> Tambah Anggota Perpustakaan</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalAddAnggota')">&times;</button>
            </div>
            <form action="{{ route('anggota.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="add_nama">Nama Lengkap <span class="required">*</span></label>
                            <input type="text" name="nama_lengkap" id="add_nama" class="form-control" placeholder="Contoh: Budi Santoso" required>
                        </div>
                        <div class="form-group">
                            <label for="add_jenis">Jenis Anggota <span class="required">*</span></label>
                            <select name="jenis_anggota" id="add_jenis" class="form-select" required>
                                <option value="Mahasiswa">Mahasiswa</option>
                                <option value="Dosen">Dosen</option>
                                <option value="Staff">Staff Kampus</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="add_nim">NIM / NIDN</label>
                            <input type="text" name="nim_nidn" id="add_nim" class="form-control" placeholder="Contoh: 2531088 atau 0412345678">
                        </div>
                        <div class="form-group">
                            <label for="add_prodi">Program Studi / Fakultas</label>
                            <input type="text" name="program_studi" id="add_prodi" class="form-control" placeholder="Contoh: Sistem Informasi">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="add_email">Alamat Email</label>
                            <input type="email" name="email" id="add_email" class="form-control" placeholder="Contoh: budi@uib.ac.id">
                        </div>
                        <div class="form-group">
                            <label for="add_telepon">Nomor Handphone / WhatsApp</label>
                            <input type="text" name="nomor_telepon" id="add_telepon" class="form-control" placeholder="Contoh: 081234567890">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="add_status">Status Keaktifan</label>
                        <select name="status_aktif" id="add_status" class="form-select">
                            <option value="Aktif" selected>Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                            <option value="Lulus / Keluar">Lulus / Keluar</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="add_alamat">Alamat Lengkap</label>
                        <textarea name="alamat" id="add_alamat" rows="3" class="form-control" placeholder="Tuliskan alamat domisili anggota..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalAddAnggota')">Batal</button>
                    <button type="submit" class="btn-main"><i class="bi bi-save"></i> Daftarkan Anggota</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: Edit Anggota (Update) -->
    <div class="modal-overlay" id="modalEditAnggota">
        <div class="modal-card">
            <div class="modal-header">
                <h3><i class="bi bi-pencil-square" style="color: var(--menu-active);"></i> Edit Informasi Anggota</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalEditAnggota')">&times;</button>
            </div>
            <form id="formEditAnggota" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_nama">Nama Lengkap <span class="required">*</span></label>
                            <input type="text" name="nama_lengkap" id="edit_nama" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_jenis">Jenis Anggota <span class="required">*</span></label>
                            <select name="jenis_anggota" id="edit_jenis" class="form-select" required>
                                <option value="Mahasiswa">Mahasiswa</option>
                                <option value="Dosen">Dosen</option>
                                <option value="Staff">Staff Kampus</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_nim">NIM / NIDN</label>
                            <input type="text" name="nim_nidn" id="edit_nim" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="edit_prodi">Program Studi / Fakultas</label>
                            <input type="text" name="program_studi" id="edit_prodi" class="form-control">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_email">Alamat Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="edit_telepon">Nomor Handphone / WhatsApp</label>
                            <input type="text" name="nomor_telepon" id="edit_telepon" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_status">Status Keaktifan <span class="required">*</span></label>
                        <select name="status_aktif" id="edit_status" class="form-select" required>
                            <option value="Aktif">Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                            <option value="Lulus / Keluar">Lulus / Keluar</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_alamat">Alamat Lengkap</label>
                        <textarea name="alamat" id="edit_alamat" rows="3" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalEditAnggota')">Batal</button>
                    <button type="submit" class="btn-main"><i class="bi bi-check2-circle"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: Detail Anggota (Read Detail) -->
    <div class="modal-overlay" id="modalDetailAnggota">
        <div class="modal-card">
            <div class="modal-header">
                <h3><i class="bi bi-person-bounding-box" style="color: var(--sidebar-bg);"></i> Profil Lengkap Anggota</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalDetailAnggota')">&times;</button>
            </div>
            <div class="modal-body">
                <div style="margin-bottom: 18px; text-align: center; padding: 14px; background: #F8F4EE; border-radius: 10px; border: 1px solid var(--border-beige);">
                    <h2 id="detail_nama" style="font-size: 18px; font-weight: 800; color: var(--text-primary); margin-bottom: 4px;">-</h2>
                    <span id="detail_nomor_badge" class="badge-isbn">-</span>
                </div>

                <div class="detail-row">
                    <div class="detail-label"><i class="bi bi-tag"></i> Jenis Anggota</div>
                    <div class="detail-value" id="detail_jenis">-</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label"><i class="bi bi-card-text"></i> NIM / NIDN</div>
                    <div class="detail-value" id="detail_nim">-</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label"><i class="bi bi-mortarboard"></i> Program Studi</div>
                    <div class="detail-value" id="detail_prodi">-</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label"><i class="bi bi-envelope"></i> Email</div>
                    <div class="detail-value" id="detail_email">-</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label"><i class="bi bi-telephone"></i> No. Telepon / WA</div>
                    <div class="detail-value" id="detail_telepon">-</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label"><i class="bi bi-check2-circle"></i> Status Keaktifan</div>
                    <div class="detail-value" id="detail_status">-</div>
                </div>

                <div class="detail-row" style="flex-direction: column; gap: 4px; border-bottom: none;">
                    <div class="detail-label"><i class="bi bi-geo-alt"></i> Alamat Domisili</div>
                    <div class="synopsis-box" id="detail_alamat">-</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modalDetailAnggota')">Tutup</button>
            </div>
        </div>
    </div>

    <!-- MODAL 4: Konfirmasi Hapus Anggota (Delete) -->
    <div class="modal-overlay" id="modalDeleteAnggota">
        <div class="modal-card" style="max-width: 440px;">
            <div class="modal-header">
                <h3 style="color: #D32F2F;"><i class="bi bi-exclamation-triangle-fill"></i> Hapus / Nonaktifkan Anggota</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalDeleteAnggota')">&times;</button>
            </div>
            <form id="formDeleteAnggota" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body" style="text-align: center; padding: 26px 20px;">
                    <i class="bi bi-person-x" style="font-size: 46px; color: #D32F2F; margin-bottom: 12px; display: inline-block;"></i>
                    <p style="font-size: 14.5px; color: var(--text-primary); margin-bottom: 8px;">
                        Apakah Anda yakin ingin menghapus data anggota berikut dari sistem?
                    </p>
                    <h4 id="deleteAnggotaNama" style="font-size: 15px; font-weight: 700; color: #D32F2F; background: #FFEBEE; padding: 10px; border-radius: 8px; border: 1px solid #FFCDD2;"></h4>
                    <p style="font-size: 12px; color: var(--text-secondary); margin-top: 10px;">
                        Data yang masih memiliki peminjaman buku aktif tidak dapat dihapus.
                    </p>
                </div>
                <div class="modal-footer" style="justify-content: center;">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalDeleteAnggota')">Batal</button>
                    <button type="submit" class="btn-main" style="background-color: #D32F2F; border-color: #D32F2F;">
                        <i class="bi bi-trash3-fill"></i> Ya, Hapus Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Javascript Sederhana untuk Modal & Sidebar -->
    <script>
        // Buka Modal
        function openModal(id) {
            document.getElementById(id).classList.add('active');
        }

        // Tutup Modal
        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }

        // Tampilkan Detail Anggota
        function viewAnggotaDetail(data) {
            document.getElementById('detail_nama').innerText = data.nama_lengkap;
            document.getElementById('detail_nomor_badge').innerText = data.nomor_anggota;
            document.getElementById('detail_jenis').innerText = data.jenis_anggota;
            document.getElementById('detail_nim').innerText = data.nim_nidn || '-';
            document.getElementById('detail_prodi').innerText = data.program_studi || '-';
            document.getElementById('detail_email').innerText = data.email || '-';
            document.getElementById('detail_telepon').innerText = data.nomor_telepon || '-';
            document.getElementById('detail_status').innerText = data.status_aktif;
            document.getElementById('detail_alamat').innerText = data.alamat || 'Alamat belum ditambahkan.';
            openModal('modalDetailAnggota');
        }

        // Form Edit Anggota
        function editAnggotaModal(data) {
            document.getElementById('formEditAnggota').action = '/anggota/' + data.id_anggota;
            document.getElementById('edit_nama').value = data.nama_lengkap;
            document.getElementById('edit_jenis').value = data.jenis_anggota;
            document.getElementById('edit_nim').value = data.nim_nidn || '';
            document.getElementById('edit_prodi').value = data.program_studi || '';
            document.getElementById('edit_email').value = data.email || '';
            document.getElementById('edit_telepon').value = data.nomor_telepon || '';
            document.getElementById('edit_status').value = data.status_aktif;
            document.getElementById('edit_alamat').value = data.alamat || '';
            openModal('modalEditAnggota');
        }

        // Konfirmasi Hapus Anggota
        function confirmDeleteAnggota(id, nama) {
            document.getElementById('formDeleteAnggota').action = '/anggota/' + id;
            document.getElementById('deleteAnggotaNama').innerText = nama;
            openModal('modalDeleteAnggota');
        }

        // Responsive sidebar toggle di HP
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('mobile-open');
            });
        }
    </script>
</body>
</html>
