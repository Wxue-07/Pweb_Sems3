<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manajemen Rak Buku - Sistem Perpustakaan Kampus</title>

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
                    <span>Manajemen Rak Buku</span>
                </div>
            </div>

            <div class="top-right">
                <a href="{{ route('rak.index') }}" class="btn-secondary" title="Muat Ulang Data">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </a>
                <button type="button" class="btn-main" onclick="openModal('modalAddRak')">
                    <i class="bi bi-plus-lg"></i> Tambah Rak Baru
                </button>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="content-area">
            <!-- Header Judul Modul -->
            <div class="page-header">
                <h1>Manajemen Rak Buku</h1>
                <p>Kelola data tata letak rak fisik perpustakaan kampus, kategori klasifikasi buku, dan kapasitas tampung eksemplar secara teratur.</p>
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

            <!-- 4 Kartu Statistik Rak Buku -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-info">
                        <p>Total Rak Buku</p>
                        <h3>{{ $stats['total_rak'] }}</h3>
                    </div>
                    <div class="stat-icon brown">
                        <i class="bi bi-grid-3x3-gap-fill"></i>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <p>Total Kapasitas Fisik</p>
                        <h3>{{ $stats['total_kapasitas'] }} <span style="font-size: 13px; font-weight: normal; color: var(--text-secondary);">buku</span></h3>
                    </div>
                    <div class="stat-icon wood">
                        <i class="bi bi-archive-fill"></i>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <p>Total Kategori Rak</p>
                        <h3>{{ $stats['total_kategori'] ?? 0 }}</h3>
                    </div>
                    <div class="stat-icon cream">
                        <i class="bi bi-tag-fill"></i>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <p>Judul Buku di Perpustakaan</p>
                        <h3>{{ $stats['total_buku'] }}</h3>
                    </div>
                    <div class="stat-icon warning">
                        <i class="bi bi-journals"></i>
                    </div>
                </div>
            </div>

            <!-- Form Filter & Pencarian Rak -->
            <div class="filter-card">
                <form action="{{ route('rak.index') }}" method="GET" class="filter-form">
                    <div class="search-input-group">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Cari kode rak, nama rak, atau kategori buku..." 
                               value="{{ request('search') }}">
                    </div>

                    <!-- Filter Kategori -->
                    <select name="kategori" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Semua Kategori Rak --</option>
                        @foreach($kategoriList as $k)
                            <option value="{{ $k }}" {{ request('kategori') == $k ? 'selected' : '' }}>
                                {{ $k }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn-main" style="padding: 9px 16px;">
                        <i class="bi bi-funnel"></i> Filter
                    </button>

                    @if(request()->hasAny(['search', 'kategori']))
                        <a href="{{ route('rak.index') }}" class="btn-secondary" style="padding: 9px 14px;">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Tabel Data Rak Buku -->
            <div class="table-card">
                <div class="table-header-box">
                    <h3>
                        <i class="bi bi-bookshelf"></i>
                        Daftar Rak Buku Perpustakaan Kampus
                    </h3>
                    <span class="table-badge-count">{{ count($rakList) }} Rak Terdaftar</span>
                </div>

                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th style="width: 50px; text-align: center;">No</th>
                                <th style="width: 130px;">Kode Rak</th>
                                <th>Nama Rak</th>
                                <th>Kategori Klasifikasi</th>
                                <th style="width: 140px; text-align: center;">Kapasitas Maksimal</th>
                                <th>Keterangan</th>
                                <th style="width: 130px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rakList as $index => $item)
                                <tr>
                                    <td style="text-align: center; color: var(--text-secondary); font-weight: 600;">
                                        {{ $index + 1 }}
                                    </td>
                                    <td>
                                        <span class="badge-isbn">{{ $item->kode_rak }}</span>
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: var(--text-primary); font-size: 14px;">
                                            {{ $item->nama_rak }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge-rak">
                                            <i class="bi bi-tag-fill"></i> {{ $item->kategori_buku ?? 'Umum' }}
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="badge-status success" style="font-size: 12.5px; font-weight: 700;">
                                            <i class="bi bi-box-seam"></i> {{ $item->kapasitas_maksimal }} Buku
                                        </span>
                                    </td>
                                    <td>
                                        <div style="font-size: 12px; color: var(--text-secondary); max-width: 250px;">
                                            {{ $item->keterangan ?? 'Tidak ada keterangan khusus.' }}
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="action-group" style="justify-content: center;">
                                            <!-- Tombol Detail -->
                                            <button type="button" class="btn-action view" title="Lihat Detail Rak & Buku"
                                                    onclick="viewRakDetail({{ json_encode($item) }})">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <!-- Tombol Edit -->
                                            <button type="button" class="btn-action edit" title="Edit Informasi Rak"
                                                    onclick="editRakModal({{ json_encode($item) }})">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <!-- Tombol Hapus -->
                                            <button type="button" class="btn-action delete" title="Hapus Rak Buku"
                                                    onclick="confirmDeleteRak({{ $item->id_rak }}, '{{ addslashes($item->nama_rak) }}', '{{ $item->kode_rak }}')">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="empty-state">
                                            <i class="bi bi-grid-3x3"></i>
                                            <h4>Belum ada data rak buku</h4>
                                            <p>Gunakan tombol "Tambah Rak Baru" di atas untuk mendaftarkan rak buku perpustakaan.</p>
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

    <!-- MODAL 1: Tambah Rak Buku Baru (Insert) -->
    <div class="modal-overlay" id="modalAddRak">
        <div class="modal-card">
            <div class="modal-header">
                <h3><i class="bi bi-plus-circle" style="color: var(--btn-primary);"></i> Tambah Rak Buku Baru</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalAddRak')">&times;</button>
            </div>
            <form action="{{ route('rak.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="add_kode_rak">Kode Rak (Opsional)</label>
                            <input type="text" name="kode_rak" id="add_kode_rak" class="form-control" placeholder="Contoh: RAK-A1, RAK-B2 (Otomatis jika kosong)">
                        </div>
                        <div class="form-group">
                            <label for="add_kapasitas">Kapasitas Maksimal <span class="required">*</span></label>
                            <input type="number" name="kapasitas_maksimal" id="add_kapasitas" class="form-control" min="1" value="50" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="add_nama_rak">Nama Rak Buku <span class="required">*</span></label>
                        <input type="text" name="nama_rak" id="add_nama_rak" class="form-control" placeholder="Contoh: Rak Web Development & PHP" required>
                    </div>

                    <div class="form-group">
                        <label for="add_kategori">Kategori Buku / Klasifikasi <span class="required">*</span></label>
                        <input type="text" name="kategori_buku" id="add_kategori" class="form-control" placeholder="Contoh: Pemrograman & Komputer" required>
                    </div>

                    <div class="form-group">
                        <label for="add_keterangan">Keterangan / Catatan Fisik</label>
                        <textarea name="keterangan" id="add_keterangan" rows="3" class="form-control" placeholder="Contoh: Baris ketiga sebelah pintu masuk ruang referensi..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalAddRak')">Batal</button>
                    <button type="submit" class="btn-main"><i class="bi bi-save"></i> Simpan Rak Baru</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: Edit Rak Buku (Update) -->
    <div class="modal-overlay" id="modalEditRak">
        <div class="modal-card">
            <div class="modal-header">
                <h3><i class="bi bi-pencil-square" style="color: var(--menu-active);"></i> Edit Data Rak Buku</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalEditRak')">&times;</button>
            </div>
            <form id="formEditRak" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_kode_rak">Kode Rak <span class="required">*</span></label>
                            <input type="text" name="kode_rak" id="edit_kode_rak" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_kapasitas">Kapasitas Maksimal <span class="required">*</span></label>
                            <input type="number" name="kapasitas_maksimal" id="edit_kapasitas" class="form-control" min="1" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_nama_rak">Nama Rak Buku <span class="required">*</span></label>
                        <input type="text" name="nama_rak" id="edit_nama_rak" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_kategori">Kategori Buku <span class="required">*</span></label>
                        <input type="text" name="kategori_buku" id="edit_kategori" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_keterangan">Keterangan / Catatan</label>
                        <textarea name="keterangan" id="edit_keterangan" rows="3" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalEditRak')">Batal</button>
                    <button type="submit" class="btn-main"><i class="bi bi-check2-circle"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: Detail Rak (Read Detail) -->
    <div class="modal-overlay" id="modalDetailRak">
        <div class="modal-card">
            <div class="modal-header">
                <h3><i class="bi bi-info-circle" style="color: var(--sidebar-bg);"></i> Informasi Detail Rak Buku</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalDetailRak')">&times;</button>
            </div>
            <div class="modal-body">
                <div style="margin-bottom: 16px; text-align: center; padding: 14px; background: #F8F4EE; border-radius: 10px; border: 1px solid var(--border-beige);">
                    <h2 id="detail_nama_rak" style="font-size: 18px; font-weight: 800; color: var(--text-primary); margin-bottom: 4px;">-</h2>
                    <span id="detail_kode_badge" class="badge-isbn">-</span>
                </div>

                <div class="detail-row">
                    <div class="detail-label"><i class="bi bi-tag"></i> Kategori Buku</div>
                    <div class="detail-value" id="detail_kategori_rak">-</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label"><i class="bi bi-box-seam"></i> Kapasitas Maksimal</div>
                    <div class="detail-value" id="detail_kapasitas_rak" style="font-weight: 700; color: #79583F;">-</div>
                </div>

                <div class="detail-row" style="flex-direction: column; gap: 4px; border-bottom: none;">
                    <div class="detail-label"><i class="bi bi-card-text"></i> Keterangan Rak</div>
                    <div class="synopsis-box" id="detail_keterangan_rak">-</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modalDetailRak')">Tutup</button>
            </div>
        </div>
    </div>

    <!-- MODAL 4: Konfirmasi Hapus Rak (Delete) -->
    <div class="modal-overlay" id="modalDeleteRak">
        <div class="modal-card" style="max-width: 440px;">
            <div class="modal-header">
                <h3 style="color: #D32F2F;"><i class="bi bi-exclamation-triangle-fill"></i> Hapus Rak Buku</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalDeleteRak')">&times;</button>
            </div>
            <form id="formDeleteRak" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body" style="text-align: center; padding: 26px 20px;">
                    <i class="bi bi-trash3" style="font-size: 46px; color: #D32F2F; margin-bottom: 12px; display: inline-block;"></i>
                    <p style="font-size: 14.5px; color: var(--text-primary); margin-bottom: 8px;">
                        Apakah Anda yakin ingin menghapus rak buku berikut?
                    </p>
                    <h4 id="deleteRakNama" style="font-size: 15px; font-weight: 700; color: #D32F2F; background: #FFEBEE; padding: 10px; border-radius: 8px; border: 1px solid #FFCDD2;"></h4>
                    <p style="font-size: 12px; color: var(--text-secondary); margin-top: 10px;">
                        Rak yang masih memiliki kaitan penempatan buku tidak dapat dihapus.
                    </p>
                </div>
                <div class="modal-footer" style="justify-content: center;">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalDeleteRak')">Batal</button>
                    <button type="submit" class="btn-main" style="background-color: #D32F2F; border-color: #D32F2F;">
                        <i class="bi bi-trash3-fill"></i> Ya, Hapus Rak
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Javascript Sederhana Modal -->
    <script>
        function openModal(id) {
            document.getElementById(id).classList.add('active');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }

        // Tampilkan Detail Rak
        function viewRakDetail(data) {
            document.getElementById('detail_nama_rak').innerText = data.nama_rak;
            document.getElementById('detail_kode_badge').innerText = data.kode_rak;
            document.getElementById('detail_kategori_rak').innerText = data.kategori_buku || 'Umum';
            document.getElementById('detail_kapasitas_rak').innerText = data.kapasitas_maksimal + ' Buku';
            document.getElementById('detail_keterangan_rak').innerText = data.keterangan || 'Tidak ada catatan tambahan.';
            openModal('modalDetailRak');
        }

        // Buka Modal Edit Rak
        function editRakModal(data) {
            document.getElementById('formEditRak').action = '/rak/' + data.id_rak;
            document.getElementById('edit_kode_rak').value = data.kode_rak;
            document.getElementById('edit_nama_rak').value = data.nama_rak;
            document.getElementById('edit_kategori').value = data.kategori_buku || '';
            document.getElementById('edit_kapasitas').value = data.kapasitas_maksimal;
            document.getElementById('edit_keterangan').value = data.keterangan || '';
            openModal('modalEditRak');
        }

        // Konfirmasi Hapus Rak
        function confirmDeleteRak(id, nama, kode) {
            document.getElementById('formDeleteRak').action = '/rak/' + id;
            document.getElementById('deleteRakNama').innerText = nama + ' (' + kode + ')';
            openModal('modalDeleteRak');
        }

        // Toggle Sidebar Mobile
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
