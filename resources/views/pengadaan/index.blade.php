<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pengadaan Buku Baru - Sistem Perpustakaan Kampus</title>

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
                    <span>Pengadaan Buku Baru</span>
                </div>
            </div>

            <div class="top-right">
                <a href="{{ route('pengadaan.index', ['tab' => $tab]) }}" class="btn-secondary" title="Muat Ulang Data">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </a>
                <button type="button" class="btn-secondary" onclick="openModal('modalAddSupplier')" style="background-color: #F3ECE3; color: var(--text-primary); border-color: var(--border-beige);">
                    <i class="bi bi-truck"></i> + Supplier Baru
                </button>
                <button type="button" class="btn-main" onclick="openModal('modalAddPengadaan')">
                    <i class="bi bi-box-arrow-in-down"></i> Catat Stok Buku Masuk
                </button>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="content-area">
            <!-- Header Judul Modul -->
            <div class="page-header">
                <h1>Pengadaan Buku Baru</h1>
                <p>Kelola data mitra supplier penerbit dan transaksi masuknya stok buku baru ke perpustakaan kampus, revisi penerimaan fisik buku, serta arsip anggaran pengadaan.</p>
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

            <!-- 4 Kartu Statistik Pengadaan -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-info">
                        <p>Total Transaksi Pengadaan</p>
                        <h3>{{ $stats['total_pengadaan'] }}</h3>
                    </div>
                    <div class="stat-icon brown">
                        <i class="bi bi-cart-check"></i>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <p>Total Stok Buku Masuk</p>
                        <h3>{{ $stats['total_buku_masuk'] }} <span style="font-size: 13px; font-weight: normal; color: var(--text-secondary);">eks</span></h3>
                    </div>
                    <div class="stat-icon wood">
                        <i class="bi bi-stack"></i>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <p>Supplier Terdaftar</p>
                        <h3>{{ $stats['total_supplier'] }}</h3>
                    </div>
                    <div class="stat-icon cream">
                        <i class="bi bi-truck"></i>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <p>Total Anggaran Pengadaan</p>
                        <h3 style="font-size: 19px;">Rp {{ number_format($stats['total_anggaran'], 0, ',', '.') }}</h3>
                    </div>
                    <div class="stat-icon warning">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
            </div>

            <!-- Tab Navigasi: Pengadaan Stok vs Data Supplier -->
            <div class="tab-nav">
                <a href="{{ route('pengadaan.index', ['tab' => 'pengadaan']) }}" 
                   class="tab-btn {{ $tab === 'pengadaan' ? 'active' : '' }}">
                    <i class="bi bi-boxes"></i> Riwayat Pengadaan Stok Buku ({{ count($pengadaan) }})
                </a>
                <a href="{{ route('pengadaan.index', ['tab' => 'supplier']) }}" 
                   class="tab-btn {{ $tab === 'supplier' ? 'active' : '' }}">
                    <i class="bi bi-building"></i> Data Master Supplier Mitra ({{ count($suppliers) }})
                </a>
            </div>

            <!-- ========================================== -->
            <!-- TAB 1: RIWAYAT TRANSAKSI PENGADAAN BUKU -->
            <!-- ========================================== -->
            @if($tab === 'pengadaan')
                <!-- Form Pencarian Pengadaan -->
                <div class="filter-card">
                    <form action="{{ route('pengadaan.index') }}" method="GET" class="filter-form">
                        <input type="hidden" name="tab" value="pengadaan">
                        <div class="search-input-group">
                            <i class="bi bi-search"></i>
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Cari nomor PO pengadaan, nama supplier, atau judul buku..." 
                                   value="{{ request('search') }}">
                        </div>

                        <button type="submit" class="btn-main" style="padding: 9px 16px;">
                            <i class="bi bi-funnel"></i> Cari
                        </button>

                        @if(request()->filled('search'))
                            <a href="{{ route('pengadaan.index', ['tab' => 'pengadaan']) }}" class="btn-secondary" style="padding: 9px 14px;">
                                <i class="bi bi-x-circle"></i> Reset
                            </a>
                        @endif
                    </form>
                </div>

                <!-- Tabel Data Pengadaan Buku -->
                <div class="table-card">
                    <div class="table-header-box">
                        <h3>
                            <i class="bi bi-card-checklist"></i>
                            Daftar Transaksi Masuknya Stok Buku Perpustakaan
                        </h3>
                        <span class="table-badge-count">{{ count($pengadaan) }} Transaksi</span>
                    </div>

                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px; text-align: center;">No</th>
                                    <th style="width: 140px;">No. Pengadaan</th>
                                    <th>Judul Buku Diterima</th>
                                    <th>Mitra Supplier</th>
                                    <th style="width: 110px;">Tgl Masuk</th>
                                    <th style="width: 100px; text-align: center;">Jml Masuk</th>
                                    <th>Total Biaya & Sumber</th>
                                    <th style="width: 130px; text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pengadaan as $index => $item)
                                    <tr>
                                        <td style="text-align: center; color: var(--text-secondary); font-weight: 600;">
                                            {{ $index + 1 }}
                                        </td>
                                        <td>
                                            <span class="badge-isbn">{{ $item->kode_pengadaan }}</span>
                                        </td>
                                        <td>
                                            <div style="font-weight: 700; color: var(--text-primary); font-size: 13.5px;">
                                                {{ $item->buku->judul ?? 'Buku #' . $item->id_buku }}
                                            </div>
                                            <div style="font-size: 11.5px; color: var(--text-secondary); margin-top: 2px;">
                                                ISBN: {{ $item->buku->isbn ?? '-' }} &bull; Sisa Stok: {{ $item->buku->stok_tersedia ?? '-' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; color: var(--text-primary); font-size: 13px;">
                                                <i class="bi bi-truck" style="color: var(--text-secondary); margin-right: 4px;"></i>{{ $item->supplier->nama_supplier ?? '-' }}
                                            </div>
                                            <div style="font-size: 11.5px; color: var(--text-secondary); margin-top: 2px;">
                                                CP: {{ $item->supplier->kontak_person ?? '-' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-size: 13px; font-weight: 500;">
                                                {{ date('d/m/Y', strtotime($item->tanggal_pengadaan)) }}
                                            </div>
                                        </td>
                                        <td style="text-align: center;">
                                            <span class="badge-status success" style="font-size: 13px; font-weight: 700;">
                                                +{{ $item->jumlah_masuk }} eks
                                            </span>
                                        </td>
                                        <td>
                                            <div style="font-weight: 700; color: #79583F;">
                                                Rp {{ number_format($item->total_biaya, 0, ',', '.') }}
                                            </div>
                                            <div style="font-size: 11px; color: var(--text-secondary); margin-top: 2px;">
                                                <span class="badge-status neutral" style="padding: 1px 6px; font-size: 10px;">{{ $item->sumber_dana }}</span>
                                            </div>
                                        </td>
                                        <td style="text-align: center;">
                                            <div class="action-group" style="justify-content: center;">
                                                <!-- Edit / Revisi Stok -->
                                                <button type="button" class="btn-action edit" title="Revisi Jumlah Stok / Data Pengadaan"
                                                        onclick="editPengadaanModal({{ json_encode($item) }})">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>

                                                <!-- Detail Pengadaan -->
                                                <button type="button" class="btn-action view" title="Lihat Detail Transaksi"
                                                        onclick="viewPengadaanDetail({{ json_encode($item) }})">
                                                    <i class="bi bi-eye"></i>
                                                </button>

                                                <!-- Batal / Hapus Pengadaan -->
                                                <button type="button" class="btn-action delete" title="Batalkan Transaksi Pengadaan"
                                                        onclick="confirmDeletePengadaan({{ $item->id_pengadaan }}, '{{ $item->kode_pengadaan }}', {{ $item->jumlah_masuk }})">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8">
                                            <div class="empty-state">
                                                <i class="bi bi-box2"></i>
                                                <h4>Belum ada transaksi pengadaan buku</h4>
                                                <p>Gunakan tombol "Catat Stok Buku Masuk" untuk menambah stok koleksi buku baru.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            <!-- ========================================== -->
            <!-- TAB 2: DATA MASTER SUPPLIER -->
            <!-- ========================================== -->
            @else
                <!-- Form Pencarian Supplier -->
                <div class="filter-card">
                    <form action="{{ route('pengadaan.index') }}" method="GET" class="filter-form">
                        <input type="hidden" name="tab" value="supplier">
                        <div class="search-input-group">
                            <i class="bi bi-search"></i>
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Cari kode supplier, nama vendor, kontak person, atau email..." 
                                   value="{{ request('search') }}">
                        </div>

                        <button type="submit" class="btn-main" style="padding: 9px 16px;">
                            <i class="bi bi-funnel"></i> Cari
                        </button>

                        @if(request()->filled('search'))
                            <a href="{{ route('pengadaan.index', ['tab' => 'supplier']) }}" class="btn-secondary" style="padding: 9px 14px;">
                                <i class="bi bi-x-circle"></i> Reset
                            </a>
                        @endif
                    </form>
                </div>

                <!-- Tabel Data Supplier -->
                <div class="table-card">
                    <div class="table-header-box">
                        <h3>
                            <i class="bi bi-truck"></i>
                            Daftar Supplier / Penerbit Rekanan Perpustakaan
                        </h3>
                        <span class="table-badge-count">{{ count($suppliers) }} Mitra Rekanan</span>
                    </div>

                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px; text-align: center;">No</th>
                                    <th style="width: 130px;">Kode Supplier</th>
                                    <th>Nama Supplier / Penerbit</th>
                                    <th>Kontak Person</th>
                                    <th>Telepon & Email</th>
                                    <th>Alamat Kantor</th>
                                    <th style="width: 120px; text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($suppliers as $index => $supp)
                                    <tr>
                                        <td style="text-align: center; color: var(--text-secondary); font-weight: 600;">
                                            {{ $index + 1 }}
                                        </td>
                                        <td>
                                            <span class="badge-isbn">{{ $supp->kode_supplier }}</span>
                                        </td>
                                        <td>
                                            <div style="font-weight: 700; color: var(--text-primary); font-size: 13.5px;">
                                                {{ $supp->nama_supplier }}
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-size: 13px; color: var(--text-primary);">
                                                <i class="bi bi-person" style="color: var(--text-secondary); margin-right: 4px;"></i>{{ $supp->kontak_person ?? '-' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-size: 12.5px; color: var(--text-primary);">
                                                <i class="bi bi-telephone" style="color: var(--text-secondary); margin-right: 4px;"></i>{{ $supp->telepon ?? '-' }}
                                            </div>
                                            <div style="font-size: 12px; color: var(--text-secondary); margin-top: 2px;">
                                                <i class="bi bi-envelope" style="color: var(--text-secondary); margin-right: 4px;"></i>{{ $supp->email ?? '-' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-size: 12px; color: var(--text-secondary); max-width: 250px;">
                                                {{ $supp->alamat ?? 'Alamat belum dicatat' }}
                                            </div>
                                        </td>
                                        <td style="text-align: center;">
                                            <div class="action-group" style="justify-content: center;">
                                                <!-- Edit Supplier -->
                                                <button type="button" class="btn-action edit" title="Edit Data Supplier"
                                                        onclick="editSupplierModal({{ json_encode($supp) }})">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>

                                                <!-- Hapus Supplier -->
                                                <button type="button" class="btn-action delete" title="Hapus Data Supplier"
                                                        onclick="confirmDeleteSupplier({{ $supp->id_supplier }}, '{{ addslashes($supp->nama_supplier) }}')">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="empty-state">
                                                <i class="bi bi-truck"></i>
                                                <h4>Belum ada supplier yang terdaftar</h4>
                                                <p>Gunakan tombol "+ Supplier Baru" di kanan atas untuk mendaftarkan mitra vendor.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </main>
    </div>

    <!-- ========================================================== -->
    <!-- MODAL-MODAL AKSI (INSERT, UPDATE, READ, DELETE) -->
    <!-- ========================================================== -->

    <!-- MODAL 1: Tambah Pengadaan Buku Baru (Insert) -->
    <div class="modal-overlay" id="modalAddPengadaan">
        <div class="modal-card">
            <div class="modal-header">
                <h3><i class="bi bi-box-arrow-in-down" style="color: var(--btn-primary);"></i> Catat Pengadaan Stok Buku Baru</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalAddPengadaan')">&times;</button>
            </div>
            <form action="{{ route('pengadaan.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="po_supplier">Pilih Mitra Supplier / Penerbit <span class="required">*</span></label>
                        <select name="id_supplier" id="po_supplier" class="form-select" required>
                            <option value="">-- Pilih Supplier Rekanan --</option>
                            @foreach($suppliers as $sp)
                                <option value="{{ $sp->id_supplier }}">{{ $sp->nama_supplier }} ({{ $sp->kode_supplier }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="po_buku">Pilih Judul Buku Yang Ditambah Stoknya <span class="required">*</span></label>
                        <select name="id_buku" id="po_buku" class="form-select" required>
                            <option value="">-- Pilih Buku Dari Katalog --</option>
                            @foreach($bukuList as $bk)
                                <option value="{{ $bk->id_buku }}">
                                    {{ $bk->judul }} (Saat ini tersedia: {{ $bk->stok_tersedia }} eks | Rak: {{ $bk->kategori_rak ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="po_tanggal">Tanggal Pengadaan / Masuk <span class="required">*</span></label>
                            <input type="date" name="tanggal_pengadaan" id="po_tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="po_jumlah">Jumlah Eksemplar Masuk <span class="required">*</span></label>
                            <input type="number" name="jumlah_masuk" id="po_jumlah" class="form-control" min="1" value="5" required oninput="hitungTotalBiayaAdd()">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="po_harga">Harga Beli Satuan (Rp) <span class="required">*</span></label>
                            <input type="number" name="harga_satuan" id="po_harga" class="form-control" min="0" value="75000" required oninput="hitungTotalBiayaAdd()">
                        </div>
                        <div class="form-group">
                            <label for="po_sumber">Sumber Anggaran / Dana</label>
                            <select name="sumber_dana" id="po_sumber" class="form-select">
                                <option value="Kas Perpustakaan" selected>Kas Perpustakaan</option>
                                <option value="APBN / Yayasan Kampus">APBN / Yayasan Kampus</option>
                                <option value="Hibah / Donasi Alumni">Hibah / Donasi Alumni</option>
                            </select>
                        </div>
                    </div>

                    <div style="background: #F8F4EE; padding: 10px 14px; border-radius: 8px; margin-bottom: 14px; border: 1px solid var(--border-beige); display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 13px; font-weight: 600; color: var(--text-secondary);">Perkiraan Total Anggaran:</span>
                        <span id="po_total_preview" style="font-size: 16px; font-weight: 800; color: #79583F;">Rp 375.000</span>
                    </div>

                    <div class="form-group">
                        <label for="po_keterangan">Keterangan / Nomor Faktur Pembelian</label>
                        <textarea name="keterangan" id="po_keterangan" rows="2" class="form-control" placeholder="Contoh: Pembelian buku edisi revisi terbaru, faktur no: INV-2026-xxx..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalAddPengadaan')">Batal</button>
                    <button type="submit" class="btn-main"><i class="bi bi-save"></i> Simpan Stok Masuk</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: Tambah Supplier Baru (Insert Supplier) -->
    <div class="modal-overlay" id="modalAddSupplier">
        <div class="modal-card">
            <div class="modal-header">
                <h3><i class="bi bi-truck" style="color: var(--btn-primary);"></i> Tambah Supplier / Penerbit Baru</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalAddSupplier')">&times;</button>
            </div>
            <form action="{{ route('supplier.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="supp_nama">Nama Supplier / Penerbit <span class="required">*</span></label>
                        <input type="text" name="nama_supplier" id="supp_nama" class="form-control" placeholder="Contoh: Penerbit Andi Offset, Gramedia Pustaka..." required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="supp_cp">Nama Kontak Person (PIC)</label>
                            <input type="text" name="kontak_person" id="supp_cp" class="form-control" placeholder="Contoh: Ibu Rina (Marketing)">
                        </div>
                        <div class="form-group">
                            <label for="supp_telp">Nomor Telepon / WA</label>
                            <input type="text" name="telepon" id="supp_telp" class="form-control" placeholder="Contoh: 081298765432">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="supp_email">Email Supplier</label>
                        <input type="email" name="email" id="supp_email" class="form-control" placeholder="Contoh: marketing@penerbit.co.id">
                    </div>

                    <div class="form-group">
                        <label for="supp_alamat">Alamat Kantor / Gudang</label>
                        <textarea name="alamat" id="supp_alamat" rows="3" class="form-control" placeholder="Tuliskan alamat lengkap supplier..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalAddSupplier')">Batal</button>
                    <button type="submit" class="btn-main"><i class="bi bi-check2-circle"></i> Simpan Supplier</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: Edit Pengadaan / Revisi Jumlah Stok (Update) -->
    <div class="modal-overlay" id="modalEditPengadaan">
        <div class="modal-card">
            <div class="modal-header">
                <h3><i class="bi bi-pencil-square" style="color: var(--menu-active);"></i> Revisi Transaksi Pengadaan</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalEditPengadaan')">&times;</button>
            </div>
            <form id="formEditPengadaan" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div style="background: #F8F4EE; padding: 10px 14px; border-radius: 8px; margin-bottom: 14px; border: 1px solid var(--border-beige);">
                        <div style="font-size: 12px; color: var(--text-secondary);">Kode Pengadaan: <strong id="edit_po_kode">-</strong></div>
                        <div style="font-size: 13.5px; font-weight: 700; color: var(--text-primary); margin-top: 2px;" id="edit_po_buku">-</div>
                    </div>

                    <div class="form-group">
                        <label for="edit_po_supplier">Supplier Rekanan <span class="required">*</span></label>
                        <select name="id_supplier" id="edit_po_supplier" class="form-select" required>
                            @foreach($suppliers as $sp)
                                <option value="{{ $sp->id_supplier }}">{{ $sp->nama_supplier }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_po_tanggal">Tanggal Masuk <span class="required">*</span></label>
                            <input type="date" name="tanggal_pengadaan" id="edit_po_tanggal" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_po_jumlah">Revisi Jumlah Masuk <span class="required">*</span></label>
                            <input type="number" name="jumlah_masuk" id="edit_po_jumlah" class="form-control" min="1" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_po_harga">Harga Satuan (Rp) <span class="required">*</span></label>
                            <input type="number" name="harga_satuan" id="edit_po_harga" class="form-control" min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_po_sumber">Sumber Dana</label>
                            <select name="sumber_dana" id="edit_po_sumber" class="form-select">
                                <option value="Kas Perpustakaan">Kas Perpustakaan</option>
                                <option value="APBN / Yayasan Kampus">APBN / Yayasan Kampus</option>
                                <option value="Hibah / Donasi Alumni">Hibah / Donasi Alumni</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_po_keterangan">Keterangan Tambahan</label>
                        <textarea name="keterangan" id="edit_po_keterangan" rows="2" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalEditPengadaan')">Batal</button>
                    <button type="submit" class="btn-main"><i class="bi bi-check2-circle"></i> Simpan Revisi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 4: Edit Data Supplier (Update Supplier) -->
    <div class="modal-overlay" id="modalEditSupplier">
        <div class="modal-card">
            <div class="modal-header">
                <h3><i class="bi bi-pencil-square" style="color: var(--menu-active);"></i> Edit Data Supplier</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalEditSupplier')">&times;</button>
            </div>
            <form id="formEditSupplier" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_supp_nama">Nama Supplier <span class="required">*</span></label>
                        <input type="text" name="nama_supplier" id="edit_supp_nama" class="form-control" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_supp_cp">Kontak Person (PIC)</label>
                            <input type="text" name="kontak_person" id="edit_supp_cp" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="edit_supp_telp">Telepon</label>
                            <input type="text" name="telepon" id="edit_supp_telp" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_supp_email">Email</label>
                        <input type="email" name="email" id="edit_supp_email" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="edit_supp_alamat">Alamat Lengkap</label>
                        <textarea name="alamat" id="edit_supp_alamat" rows="3" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalEditSupplier')">Batal</button>
                    <button type="submit" class="btn-main"><i class="bi bi-check2-circle"></i> Perbarui Supplier</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 5: Detail Pengadaan (Read Detail) -->
    <div class="modal-overlay" id="modalDetailPengadaan">
        <div class="modal-card">
            <div class="modal-header">
                <h3><i class="bi bi-info-circle" style="color: var(--sidebar-bg);"></i> Rincian Transaksi Pengadaan</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalDetailPengadaan')">&times;</button>
            </div>
            <div class="modal-body">
                <div style="margin-bottom: 16px; text-align: center; padding: 12px; background: #F8F4EE; border-radius: 8px; border: 1px solid var(--border-beige);">
                    <span id="dtl_po_kode" class="badge-isbn" style="font-size: 14px;">-</span>
                    <h3 id="dtl_po_buku" style="font-size: 16px; font-weight: 800; color: var(--text-primary); margin-top: 6px;">-</h3>
                </div>

                <div class="detail-row">
                    <div class="detail-label"><i class="bi bi-truck"></i> Supplier Mitra</div>
                    <div class="detail-value" id="dtl_po_supplier">-</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label"><i class="bi bi-calendar-event"></i> Tanggal Pengadaan</div>
                    <div class="detail-value" id="dtl_po_tanggal">-</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label"><i class="bi bi-stack"></i> Jumlah Masuk</div>
                    <div class="detail-value" id="dtl_po_jumlah">-</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label"><i class="bi bi-tag"></i> Harga Satuan</div>
                    <div class="detail-value" id="dtl_po_harga">-</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label"><i class="bi bi-cash-stack"></i> Total Biaya</div>
                    <div class="detail-value" id="dtl_po_total" style="font-weight: 800; color: #79583F;">-</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label"><i class="bi bi-wallet2"></i> Sumber Dana</div>
                    <div class="detail-value" id="dtl_po_sumber">-</div>
                </div>

                <div class="detail-row" style="flex-direction: column; gap: 4px; border-bottom: none;">
                    <div class="detail-label"><i class="bi bi-chat-left-text"></i> Keterangan Transaksi</div>
                    <div class="synopsis-box" id="dtl_po_keterangan">-</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modalDetailPengadaan')">Tutup</button>
            </div>
        </div>
    </div>

    <!-- MODAL 6: Hapus / Batalkan Pengadaan (Delete) -->
    <div class="modal-overlay" id="modalDeletePengadaan">
        <div class="modal-card" style="max-width: 440px;">
            <div class="modal-header">
                <h3 style="color: #D32F2F;"><i class="bi bi-exclamation-triangle-fill"></i> Batalkan Pengadaan</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalDeletePengadaan')">&times;</button>
            </div>
            <form id="formDeletePengadaan" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body" style="text-align: center; padding: 26px 20px;">
                    <i class="bi bi-trash3" style="font-size: 46px; color: #D32F2F; margin-bottom: 12px; display: inline-block;"></i>
                    <p style="font-size: 14.5px; color: var(--text-primary); margin-bottom: 8px;">
                        Apakah Anda yakin ingin membatalkan transaksi pengadaan berikut?
                    </p>
                    <h4 id="deleteKodePengadaan" style="font-size: 15px; font-weight: 700; color: #D32F2F; background: #FFEBEE; padding: 10px; border-radius: 8px; border: 1px solid #FFCDD2;"></h4>
                    <p style="font-size: 12px; color: var(--text-secondary); margin-top: 10px;">
                        Stok buku fisik di katalog akan otomatis dikurangi kembali sebanyak jumlah yang tercatat.
                    </p>
                </div>
                <div class="modal-footer" style="justify-content: center;">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalDeletePengadaan')">Batal</button>
                    <button type="submit" class="btn-main" style="background-color: #D32F2F; border-color: #D32F2F;">
                        <i class="bi bi-trash3-fill"></i> Ya, Batalkan Pengadaan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 7: Hapus Supplier (Delete Supplier) -->
    <div class="modal-overlay" id="modalDeleteSupplier">
        <div class="modal-card" style="max-width: 440px;">
            <div class="modal-header">
                <h3 style="color: #D32F2F;"><i class="bi bi-exclamation-triangle-fill"></i> Hapus Data Supplier</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalDeleteSupplier')">&times;</button>
            </div>
            <form id="formDeleteSupplier" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body" style="text-align: center; padding: 26px 20px;">
                    <i class="bi bi-truck-flatbed" style="font-size: 46px; color: #D32F2F; margin-bottom: 12px; display: inline-block;"></i>
                    <p style="font-size: 14.5px; color: var(--text-primary); margin-bottom: 8px;">
                        Apakah Anda yakin ingin menghapus mitra supplier berikut?
                    </p>
                    <h4 id="deleteNamaSupplier" style="font-size: 15px; font-weight: 700; color: #D32F2F; background: #FFEBEE; padding: 10px; border-radius: 8px; border: 1px solid #FFCDD2;"></h4>
                    <p style="font-size: 12px; color: var(--text-secondary); margin-top: 10px;">
                        Supplier yang memiliki transaksi pengadaan tidak dapat dihapus demi integritas data.
                    </p>
                </div>
                <div class="modal-footer" style="justify-content: center;">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalDeleteSupplier')">Batal</button>
                    <button type="submit" class="btn-main" style="background-color: #D32F2F; border-color: #D32F2F;">
                        <i class="bi bi-trash3-fill"></i> Ya, Hapus Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Javascript Modal & Perhitungan -->
    <script>
        function openModal(id) {
            document.getElementById(id).classList.add('active');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }

        // Hitung perkiraan biaya realtime di modal tambah
        function hitungTotalBiayaAdd() {
            const jml = parseInt(document.getElementById('po_jumlah').value) || 0;
            const hrg = parseFloat(document.getElementById('po_harga').value) || 0;
            const total = jml * hrg;
            document.getElementById('po_total_preview').innerText = 'Rp ' + total.toLocaleString('id-ID');
        }

        // Buka Modal Edit Pengadaan
        function editPengadaanModal(data) {
            document.getElementById('formEditPengadaan').action = '/pengadaan/' + data.id_pengadaan;
            document.getElementById('edit_po_kode').innerText = data.kode_pengadaan;
            document.getElementById('edit_po_buku').innerText = (data.buku ? data.buku.judul : 'Buku #' + data.id_buku);
            document.getElementById('edit_po_supplier').value = data.id_supplier;
            document.getElementById('edit_po_tanggal').value = data.tanggal_pengadaan;
            document.getElementById('edit_po_jumlah').value = data.jumlah_masuk;
            document.getElementById('edit_po_harga').value = parseInt(data.harga_satuan);
            document.getElementById('edit_po_sumber').value = data.sumber_dana;
            document.getElementById('edit_po_keterangan').value = data.keterangan || '';
            openModal('modalEditPengadaan');
        }

        // Tampilkan Detail Pengadaan
        function viewPengadaanDetail(data) {
            document.getElementById('dtl_po_kode').innerText = data.kode_pengadaan;
            document.getElementById('dtl_po_buku').innerText = (data.buku ? data.buku.judul : 'Buku #' + data.id_buku);
            document.getElementById('dtl_po_supplier').innerText = (data.supplier ? data.supplier.nama_supplier : 'Supplier #' + data.id_supplier);
            document.getElementById('dtl_po_tanggal').innerText = data.tanggal_pengadaan;
            document.getElementById('dtl_po_jumlah').innerText = data.jumlah_masuk + ' eksemplar';
            document.getElementById('dtl_po_harga').innerText = 'Rp ' + parseFloat(data.harga_satuan).toLocaleString('id-ID');
            document.getElementById('dtl_po_total').innerText = 'Rp ' + parseFloat(data.total_biaya).toLocaleString('id-ID');
            document.getElementById('dtl_po_sumber').innerText = data.sumber_dana;
            document.getElementById('dtl_po_keterangan').innerText = data.keterangan || 'Tidak ada keterangan.';
            openModal('modalDetailPengadaan');
        }

        // Konfirmasi Hapus Pengadaan
        function confirmDeletePengadaan(id, kode, jml) {
            document.getElementById('formDeletePengadaan').action = '/pengadaan/' + id;
            document.getElementById('deleteKodePengadaan').innerText = kode + ' (' + jml + ' eks)';
            openModal('modalDeletePengadaan');
        }

        // Buka Modal Edit Supplier
        function editSupplierModal(data) {
            document.getElementById('formEditSupplier').action = '/supplier/' + data.id_supplier;
            document.getElementById('edit_supp_nama').value = data.nama_supplier;
            document.getElementById('edit_supp_cp').value = data.kontak_person || '';
            document.getElementById('edit_supp_telp').value = data.telepon || '';
            document.getElementById('edit_supp_email').value = data.email || '';
            document.getElementById('edit_supp_alamat').value = data.alamat || '';
            openModal('modalEditSupplier');
        }

        // Konfirmasi Hapus Supplier
        function confirmDeleteSupplier(id, nama) {
            document.getElementById('formDeleteSupplier').action = '/supplier/' + id;
            document.getElementById('deleteNamaSupplier').innerText = nama;
            openModal('modalDeleteSupplier');
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
