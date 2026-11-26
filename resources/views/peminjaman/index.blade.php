<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Peminjaman Buku - Sistem Perpustakaan Kampus</title>

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
                    <span>Peminjaman Buku</span>
                </div>
            </div>

            <div class="top-right">
                <a href="{{ route('peminjaman.index') }}" class="btn-secondary" title="Muat Ulang Data">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </a>
                <button type="button" class="btn-main" onclick="openModal('modalAddPinjam')">
                    <i class="bi bi-plus-circle"></i> Catat Peminjaman Baru
                </button>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="content-area">
            <!-- Header Judul Modul -->
            <div class="page-header">
                <h1>Peminjaman Buku</h1>
                <p>Catat transaksi sirkulasi peminjaman buku baru, edit peminjam atau buku, atur masa pinjam, dan hapus transaksi. Persetujuan pengembalian & denda dikelola khusus pada Modul Pengembalian Buku & Denda.</p>
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

            <!-- 4 Kartu Statistik Peminjaman -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-info">
                        <p>Total Semua Peminjaman</p>
                        <h3>{{ $stats['total_transaksi'] }}</h3>
                    </div>
                    <div class="stat-icon brown">
                        <i class="bi bi-journal-arrow-up"></i>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <p>Sedang Dipinjam (Aktif)</p>
                        <h3>{{ $stats['sedang_dipinjam'] }}</h3>
                    </div>
                    <div class="stat-icon wood">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <p>Sudah Dikembalikan</p>
                        <h3>{{ $stats['sudah_selesai'] }}</h3>
                    </div>
                    <div class="stat-icon cream">
                        <i class="bi bi-check2-all"></i>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <p>Terlambat Jatuh Tempo</p>
                        <h3 style="color: {{ $stats['terlambat'] > 0 ? '#C62828' : 'var(--text-primary)' }};">
                            {{ $stats['terlambat'] }}
                        </h3>
                    </div>
                    <div class="stat-icon warning">
                        <i class="bi bi-alarm"></i>
                    </div>
                </div>
            </div>

            <!-- Form Pencarian & Filter Status Peminjaman -->
            <div class="filter-card">
                <form action="{{ route('peminjaman.index') }}" method="GET" class="filter-form">
                    <div class="search-input-group">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Cari kode transaksi, nama anggota peminjam, atau judul buku..." 
                               value="{{ request('search') }}">
                    </div>

                    <!-- Filter Status -->
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Semua Status Transaksi --</option>
                        <option value="Dipinjam" {{ request('status') == 'Dipinjam' ? 'selected' : '' }}>Sedang Dipinjam</option>
                        <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Sudah Dikembalikan</option>
                        <option value="Terlambat" {{ request('status') == 'Terlambat' ? 'selected' : '' }}>Terlambat Jatuh Tempo</option>
                    </select>

                    <button type="submit" class="btn-main" style="padding: 9px 16px;">
                        <i class="bi bi-funnel"></i> Filter
                    </button>

                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('peminjaman.index') }}" class="btn-secondary" style="padding: 9px 14px;">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Tabel Data Peminjaman Buku -->
            <div class="table-card">
                <div class="table-header-box">
                    <h3>
                        <i class="bi bi-table"></i>
                        Daftar Riwayat Transaksi Peminjaman Buku
                    </h3>
                    <span class="table-badge-count">{{ count($peminjaman) }} Transaksi Tercatat</span>
                </div>

                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th style="width: 50px; text-align: center;">No</th>
                                <th style="width: 140px;">Kode Transaksi</th>
                                <th>Anggota Peminjam</th>
                                <th>Buku Yang Dipinjam</th>
                                <th style="width: 110px;">Tgl Pinjam</th>
                                <th style="width: 120px;">Jatuh Tempo</th>
                                <th style="width: 130px; text-align: center;">Status Pinjam</th>
                                <th style="width: 140px;">Status Denda</th>
                                <th style="width: 140px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($peminjaman as $index => $item)
                                @php
                                    $isKembali = in_array($item->status_peminjaman, ['Selesai', 'Sudah Dikembalikan']);
                                    $isTelat = (!$isKembali && strtotime($item->tanggal_jatuh_tempo) < strtotime(date('Y-m-d')));
                                @endphp
                                <tr>
                                    <td style="text-align: center; color: var(--text-secondary); font-weight: 600;">
                                        {{ $index + 1 }}
                                    </td>
                                    <td>
                                        <span class="badge-isbn">{{ $item->kode_transaksi }}</span>
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: var(--text-primary); font-size: 13.5px;">
                                            {{ $item->anggota->nama_lengkap ?? 'Anggota #' . $item->id_anggota }}
                                        </div>
                                        <div style="font-size: 11.5px; color: var(--text-secondary); margin-top: 2px;">
                                            <i class="bi bi-person-vcard"></i> {{ $item->anggota->nomor_anggota ?? '-' }} &bull; {{ $item->anggota->jenis_anggota ?? 'Mahasiswa' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: var(--text-primary); font-size: 13.5px;">
                                            {{ $item->buku->judul ?? 'Buku #' . $item->id_buku }}
                                        </div>
                                        <div style="font-size: 11.5px; color: var(--text-secondary); margin-top: 2px;">
                                            <i class="bi bi-geo-alt"></i> {{ $item->buku->kategori_rak ?? 'Rak Umum' }} &bull; ISBN: {{ $item->buku->isbn ?? '-' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size: 13px; font-weight: 500;">
                                            {{ date('d/m/Y', strtotime($item->tanggal_pinjam)) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size: 13px; font-weight: 700; color: {{ $isTelat ? '#C62828' : 'var(--text-primary)' }};">
                                            {{ date('d/m/Y', strtotime($item->tanggal_jatuh_tempo)) }}
                                        </div>
                                        @if($isTelat)
                                            <div style="font-size: 10.5px; color: #C62828; font-weight: 600;">
                                                <i class="bi bi-exclamation-circle-fill"></i> Lewat Batas!
                                            </div>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        @if($isKembali)
                                            <span class="badge-status success"><i class="bi bi-check-circle-fill"></i> Dikembalikan</span>
                                            @if($item->tanggal_kembali)
                                                <div style="font-size: 10.5px; color: var(--text-secondary); margin-top: 2px;">
                                                    {{ date('d/m/Y', strtotime($item->tanggal_kembali)) }}
                                                </div>
                                            @endif
                                        @elseif($isTelat)
                                            <span class="badge-status danger"><i class="bi bi-alarm-fill"></i> Terlambat</span>
                                        @else
                                            <span class="badge-status warning"><i class="bi bi-hourglass-split"></i> Dipinjam</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->pengembalian)
                                            <div style="font-weight: 700; font-size: 12.5px; color: {{ $item->pengembalian->total_denda > 0 ? '#C62828' : '#2E7D32' }};">
                                                Rp {{ number_format($item->pengembalian->total_denda, 0, ',', '.') }}
                                            </div>
                                            <div style="margin-top: 2px;">
                                                @if($item->pengembalian->status_pembayaran === 'Lunas')
                                                    <span class="badge-status success" style="font-size: 10.5px; padding: 2px 6px;">Lunas</span>
                                                @elseif($item->pengembalian->status_pembayaran === 'Belum Lunas')
                                                    <span class="badge-status danger" style="font-size: 10.5px; padding: 2px 6px;">Belum Lunas</span>
                                                @else
                                                    <span class="badge-status info" style="font-size: 10.5px; padding: 2px 6px;">Bebas Denda</span>
                                                @endif
                                            </div>
                                        @elseif($isTelat)
                                            <span class="badge-status warning" style="font-size: 10.5px;">Estimasi Telat</span>
                                        @else
                                            <span style="color: var(--text-secondary); font-size: 12px;">-</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="action-group" style="justify-content: center;">
                                            <!-- Tombol Edit Transaksi Peminjaman -->
                                            <button type="button" class="btn-action edit" title="Edit Informasi Transaksi"
                                                    onclick="editPinjamModal({{ json_encode($item) }})">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>

                                            <!-- Tombol Detail -->
                                            <button type="button" class="btn-action view" title="Lihat Detail Transaksi"
                                                    onclick="viewPinjamDetail({{ json_encode($item) }})">
                                                <i class="bi bi-eye"></i>
                                            </button>

                                            <!-- Tombol Hapus / Batalkan -->
                                            <button type="button" class="btn-action delete" title="Hapus Transaksi Peminjaman"
                                                    onclick="confirmDeletePinjam({{ $item->id_peminjaman }}, '{{ $item->kode_transaksi }}')">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9">
                                        <div class="empty-state">
                                            <i class="bi bi-journal-x"></i>
                                            <h4>Belum ada transaksi peminjaman yang sesuai</h4>
                                            <p>Gunakan tombol "Catat Peminjaman Baru" untuk memulai transaksi sirkulasi buku.</p>
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

    <!-- MODAL 1: Tambah Transaksi Peminjaman (Insert) -->
    <div class="modal-overlay" id="modalAddPinjam">
        <div class="modal-card">
            <div class="modal-header">
                <h3><i class="bi bi-plus-circle" style="color: var(--btn-primary);"></i> Catat Peminjaman Buku Baru</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalAddPinjam')">&times;</button>
            </div>
            <form action="{{ route('peminjaman.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="add_id_anggota">Pilih Anggota Peminjam <span class="required">*</span></label>
                        <select name="id_anggota" id="add_id_anggota" class="form-select" required>
                            <option value="">-- Pilih Anggota Terdaftar --</option>
                            @foreach($anggotaList as $agt)
                                <option value="{{ $agt->id_anggota }}">
                                    {{ $agt->nama_lengkap }} ({{ $agt->nomor_anggota }} - {{ $agt->jenis_anggota }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="add_id_buku">Pilih Judul Buku <span class="required">*</span></label>
                        <select name="id_buku" id="add_id_buku" class="form-select" required>
                            <option value="">-- Pilih Buku Tersedia --</option>
                            @foreach($bukuList as $bk)
                                <option value="{{ $bk->id_buku }}">
                                    {{ $bk->judul }} (Sisa Stok: {{ $bk->stok_tersedia }} | Rak: {{ $bk->kategori_rak ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="add_tanggal_pinjam">Tanggal Peminjaman <span class="required">*</span></label>
                            <input type="date" name="tanggal_pinjam" id="add_tanggal_pinjam" class="form-control" value="{{ date('Y-m-d') }}" required onchange="calculateAddDueDate()">
                        </div>
                        <div class="form-group">
                            <label for="add_durasi">Durasi Peminjaman</label>
                            <select name="durasi_hari" id="add_durasi" class="form-select" onchange="calculateAddDueDate()">
                                <option value="7" selected>7 Hari (1 Minggu - Standar)</option>
                                <option value="14">14 Hari (2 Minggu)</option>
                                <option value="3">3 Hari (Masa Singkat)</option>
                                <option value="30">30 Hari (1 Bulan)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="add_tanggal_jatuh_tempo">Tanggal Jatuh Tempo (Otomatis)</label>
                        <input type="date" name="tanggal_jatuh_tempo" id="add_tanggal_jatuh_tempo" class="form-control" value="{{ date('Y-m-d', strtotime('+7 days')) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="add_catatan">Catatan Tambahan (Opsional)</label>
                        <textarea name="catatan" id="add_catatan" rows="2" class="form-control" placeholder="Contoh: Keperluan tugas akhir, peminjaman untuk praktikum..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalAddPinjam')">Batal</button>
                    <button type="submit" class="btn-main"><i class="bi bi-check-lg"></i> Proses Peminjaman</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: Edit Transaksi Peminjaman (Update Peminjam, Buku, Waktu) -->
    <div class="modal-overlay" id="modalEditPinjam">
        <div class="modal-card">
            <div class="modal-header">
                <h3><i class="bi bi-pencil-square" style="color: var(--menu-active);"></i> Edit Transaksi Peminjaman</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalEditPinjam')">&times;</button>
            </div>
            <form id="formEditPinjam" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <!-- Info Pembatas Pengembalian & Denda -->
                    <div style="background: #F8F4EE; border: 1px solid var(--border-beige); border-radius: 8px; padding: 12px; margin-bottom: 16px; font-size: 12px; color: var(--text-primary); line-height: 1.5;">
                        <i class="bi bi-info-circle-fill" style="color: var(--btn-primary);"></i>
                        <strong>Catatan Sistem:</strong> Anda dapat mengoreksi data peminjam, buku yang dipinjam, atau waktu transaksi. Persetujuan pengembalian buku dan kalkulasi denda <strong>hanya dapat disetujui melalui Modul Pengembalian & Denda</strong>.
                    </div>

                    <div class="form-group">
                        <label for="edit_id_anggota">Anggota Peminjam <span class="required">*</span></label>
                        <select name="id_anggota" id="edit_id_anggota" class="form-select" required>
                            @foreach($anggotaList as $agt)
                                <option value="{{ $agt->id_anggota }}">
                                    {{ $agt->nama_lengkap }} ({{ $agt->nomor_anggota }} - {{ $agt->jenis_anggota }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_id_buku">Buku Yang Dipinjam <span class="required">*</span></label>
                        <select name="id_buku" id="edit_id_buku" class="form-select" required>
                            @foreach($allBukuList as $bk)
                                <option value="{{ $bk->id_buku }}">
                                    {{ $bk->judul }} (Sisa Stok: {{ $bk->stok_tersedia }} | Rak: {{ $bk->kategori_rak ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_tanggal_pinjam">Tanggal Pinjam <span class="required">*</span></label>
                            <input type="date" name="tanggal_pinjam" id="edit_tanggal_pinjam" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_tanggal_jatuh_tempo">Batas Jatuh Tempo <span class="required">*</span></label>
                            <input type="date" name="tanggal_jatuh_tempo" id="edit_tanggal_jatuh_tempo" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_catatan">Catatan Transaksi</label>
                        <textarea name="catatan" id="edit_catatan" rows="2" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalEditPinjam')">Batal</button>
                    <button type="submit" class="btn-main"><i class="bi bi-check2-circle"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: Detail Peminjaman (Read Detail) -->
    <div class="modal-overlay" id="modalDetailPinjam">
        <div class="modal-card">
            <div class="modal-header">
                <h3><i class="bi bi-info-circle" style="color: var(--sidebar-bg);"></i> Detail Transaksi Peminjaman</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalDetailPinjam')">&times;</button>
            </div>
            <div class="modal-body">
                <div style="margin-bottom: 16px; text-align: center; padding: 12px; background: #F8F4EE; border-radius: 8px; border: 1px solid var(--border-beige);">
                    <span id="dtl_kode" class="badge-isbn" style="font-size: 14px;">-</span>
                    <div id="dtl_status" style="margin-top: 6px;">-</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label"><i class="bi bi-person"></i> Anggota Peminjam</div>
                    <div class="detail-value" id="dtl_anggota">-</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label"><i class="bi bi-book"></i> Judul Buku</div>
                    <div class="detail-value" id="dtl_buku">-</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label"><i class="bi bi-calendar-event"></i> Tanggal Pinjam</div>
                    <div class="detail-value" id="dtl_tgl_pinjam">-</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label"><i class="bi bi-calendar-check"></i> Jatuh Tempo</div>
                    <div class="detail-value" id="dtl_jatuh_tempo">-</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label"><i class="bi bi-calendar2-check"></i> Tanggal Kembali</div>
                    <div class="detail-value" id="dtl_tgl_kembali">-</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label"><i class="bi bi-receipt"></i> Status & Denda</div>
                    <div class="detail-value" id="dtl_denda_info">-</div>
                </div>

                <div class="detail-row" style="flex-direction: column; gap: 4px; border-bottom: none;">
                    <div class="detail-label"><i class="bi bi-chat-left-text"></i> Catatan Transaksi</div>
                    <div class="synopsis-box" id="dtl_catatan">-</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modalDetailPinjam')">Tutup</button>
            </div>
        </div>
    </div>

    <!-- MODAL 4: Batalkan / Hapus Transaksi (Delete) -->
    <div class="modal-overlay" id="modalDeletePinjam">
        <div class="modal-card" style="max-width: 440px;">
            <div class="modal-header">
                <h3 style="color: #D32F2F;"><i class="bi bi-exclamation-triangle-fill"></i> Hapus Transaksi</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalDeletePinjam')">&times;</button>
            </div>
            <form id="formDeletePinjam" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body" style="text-align: center; padding: 26px 20px;">
                    <i class="bi bi-trash3" style="font-size: 46px; color: #D32F2F; margin-bottom: 12px; display: inline-block;"></i>
                    <p style="font-size: 14.5px; color: var(--text-primary); margin-bottom: 8px;">
                        Apakah Anda yakin ingin menghapus transaksi peminjaman berikut?
                    </p>
                    <h4 id="deleteKodeTransaksi" style="font-size: 15px; font-weight: 700; color: #D32F2F; background: #FFEBEE; padding: 10px; border-radius: 8px; border: 1px solid #FFCDD2;"></h4>
                    <p style="font-size: 12px; color: var(--text-secondary); margin-top: 10px;">
                        Data akan terhapus dari kedua modul. Jika buku masih dalam status dipinjam, stok buku akan otomatis dikembalikan (+1).
                    </p>
                </div>
                <div class="modal-footer" style="justify-content: center;">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalDeletePinjam')">Batal</button>
                    <button type="submit" class="btn-main" style="background-color: #D32F2F; border-color: #D32F2F;">
                        <i class="bi bi-trash3-fill"></i> Ya, Hapus Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Javascript Sederhana Modal & Aksi -->
    <script>
        function openModal(id) {
            document.getElementById(id).classList.add('active');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }

        // Hitung Jatuh Tempo otomatis pada form tambah
        function calculateAddDueDate() {
            const pinjamVal = document.getElementById('add_tanggal_pinjam').value;
            const durasiVal = parseInt(document.getElementById('add_durasi').value) || 7;
            if (pinjamVal) {
                const d = new Date(pinjamVal);
                d.setDate(d.getDate() + durasiVal);
                const yyyy = d.getFullYear();
                const mm = String(d.getMonth() + 1).padStart(2, '0');
                const dd = String(d.getDate()).padStart(2, '0');
                document.getElementById('add_tanggal_jatuh_tempo').value = `${yyyy}-${mm}-${dd}`;
            }
        }

        // Buka Modal Edit Peminjaman (Update)
        function editPinjamModal(data) {
            document.getElementById('formEditPinjam').action = '/peminjaman/' + data.id_peminjaman;
            document.getElementById('edit_id_anggota').value = data.id_anggota;
            document.getElementById('edit_id_buku').value = data.id_buku;
            document.getElementById('edit_tanggal_pinjam').value = data.tanggal_pinjam;
            document.getElementById('edit_tanggal_jatuh_tempo').value = data.tanggal_jatuh_tempo;
            document.getElementById('edit_catatan').value = data.catatan || '';
            openModal('modalEditPinjam');
        }

        // Tampilkan Detail Peminjaman (Tersinkron dengan Pengembalian)
        function viewPinjamDetail(data) {
            document.getElementById('dtl_kode').innerText = data.kode_transaksi;
            document.getElementById('dtl_anggota').innerText = (data.anggota ? data.anggota.nama_lengkap + ' (' + data.anggota.nomor_anggota + ')' : 'ID #' + data.id_anggota);
            document.getElementById('dtl_buku').innerText = (data.buku ? data.buku.judul : 'ID Buku #' + data.id_buku);
            document.getElementById('dtl_tgl_pinjam').innerText = data.tanggal_pinjam;
            document.getElementById('dtl_jatuh_tempo').innerText = data.tanggal_jatuh_tempo;
            document.getElementById('dtl_tgl_kembali').innerText = data.tanggal_kembali ? data.tanggal_kembali : 'Belum dikembalikan (Menunggu di Modul Pengembalian)';
            document.getElementById('dtl_catatan').innerText = data.catatan || 'Tidak ada catatan.';
            
            let badge = '';
            if (data.status_peminjaman === 'Selesai' || data.status_peminjaman === 'Sudah Dikembalikan') {
                badge = '<span class="badge-status success"><i class="bi bi-check-circle-fill"></i> Sudah Dikembalikan</span>';
            } else {
                badge = '<span class="badge-status warning"><i class="bi bi-hourglass-split"></i> Sedang Dipinjam</span>';
            }
            document.getElementById('dtl_status').innerHTML = badge;

            if (data.pengembalian) {
                document.getElementById('dtl_denda_info').innerHTML = 
                    '<strong style="color: #79583F;">Rp ' + parseFloat(data.pengembalian.total_denda).toLocaleString('id-ID') + '</strong>' +
                    ' (' + data.pengembalian.status_pembayaran + ' - Kondisi: ' + data.pengembalian.kondisi_buku + ')';
            } else {
                document.getElementById('dtl_denda_info').innerText = 'Belum ada catatan denda (Diproses di Modul Pengembalian).';
            }

            openModal('modalDetailPinjam');
        }

        // Konfirmasi Hapus Transaksi
        function confirmDeletePinjam(id, kode) {
            document.getElementById('formDeletePinjam').action = '/peminjaman/' + id;
            document.getElementById('deleteKodeTransaksi').innerText = kode;
            openModal('modalDeletePinjam');
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
