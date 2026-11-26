<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pengembalian Buku & Denda - Sistem Perpustakaan Kampus</title>

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
                    <span>Pengembalian Buku & Denda</span>
                </div>
            </div>

            <div class="top-right">
                <a href="{{ route('pengembalian.index') }}" class="btn-secondary" title="Muat Ulang Data">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </a>
                <button type="button" class="btn-main" onclick="openModal('modalAddPengembalian')">
                    <i class="bi bi-box-arrow-in-down-left"></i> Catat Pengembalian Buku
                </button>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="content-area">
            <!-- Header Judul Modul -->
            <div class="page-header">
                <h1>Pengembalian Buku & Denda</h1>
                <p>Pusat persetujuan (Approve) pengembalian buku dan kalkulasi denda perpustakaan. Seluruh data transaksi dari Modul Peminjaman tersinkron otomatis di sini.</p>
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

            <!-- 4 Kartu Statistik Pengembalian & Denda -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-info">
                        <p>Total Transaksi</p>
                        <h3>{{ $stats['total_transaksi'] }}</h3>
                    </div>
                    <div class="stat-icon brown">
                        <i class="bi bi-journal-text"></i>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <p>Menunggu Pengembalian</p>
                        <h3 style="color: {{ $stats['menunggu_kembali'] > 0 ? '#C62828' : 'var(--text-primary)' }};">
                            {{ $stats['menunggu_kembali'] }}
                        </h3>
                    </div>
                    <div class="stat-icon wood">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <p>Sudah Dikembalikan</p>
                        <h3>{{ $stats['sudah_kembali'] }}</h3>
                    </div>
                    <div class="stat-icon cream">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <p>Denda Belum Lunas</p>
                        <h3 style="color: {{ $stats['denda_tertunggak'] > 0 ? '#C62828' : '#2E7D32' }};">
                            Rp {{ number_format($stats['denda_tertunggak'], 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="stat-icon warning">
                        <i class="bi bi-receipt"></i>
                    </div>
                </div>
            </div>

            <!-- Form Filter Status Transaksi & Pembayaran Denda -->
            <div class="filter-card">
                <form action="{{ route('pengembalian.index') }}" method="GET" class="filter-form">
                    <div class="search-input-group">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Cari kode transaksi, kode kembali, nama anggota, atau judul buku..." 
                               value="{{ request('search') }}">
                    </div>

                    <!-- Filter Status -->
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Semua Status Transaksi --</option>
                        <option value="Dipinjam" {{ request('status') == 'Dipinjam' ? 'selected' : '' }}>Menunggu Pengembalian (Dipinjam)</option>
                        <option value="Dikembalikan" {{ request('status') == 'Dikembalikan' ? 'selected' : '' }}>Sudah Selesai Dikembalikan</option>
                        <option value="Belum_Lunas" {{ request('status') == 'Belum_Lunas' ? 'selected' : '' }}>Memiliki Denda Belum Lunas</option>
                        <option value="Lunas" {{ request('status') == 'Lunas' ? 'selected' : '' }}>Denda Lunas</option>
                        <option value="Bebas_Denda" {{ request('status') == 'Bebas_Denda' ? 'selected' : '' }}>Bebas Denda</option>
                    </select>

                    <button type="submit" class="btn-main" style="padding: 9px 16px;">
                        <i class="bi bi-funnel"></i> Filter
                    </button>

                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('pengembalian.index') }}" class="btn-secondary" style="padding: 9px 14px;">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Tabel Riwayat Pengembalian & Denda (Sinkron dengan Peminjaman) -->
            <div class="table-card">
                <div class="table-header-box">
                    <h3>
                        <i class="bi bi-journal-arrow-down"></i>
                        Daftar Transaksi Peminjaman, Pengembalian & Denda
                    </h3>
                    <span class="table-badge-count">{{ count($transaksiList) }} Transaksi Tersinkron</span>
                </div>

                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th style="width: 50px; text-align: center;">No</th>
                                <th style="width: 140px;">Kode Transaksi</th>
                                <th>Anggota Peminjam</th>
                                <th>Buku Yang Dipinjam</th>
                                <th style="width: 115px;">Jatuh Tempo</th>
                                <th style="width: 130px; text-align: center;">Status Kembali</th>
                                <th>Kondisi & Rincian Denda</th>
                                <th style="width: 120px; text-align: center;">Status Bayar</th>
                                <th style="width: 160px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transaksiList as $index => $item)
                                @php
                                    $isKembali = in_array($item->status_peminjaman, ['Selesai', 'Sudah Dikembalikan']);
                                    $isTelat = (!$isKembali && strtotime($item->tanggal_jatuh_tempo) < strtotime(date('Y-m-d')));
                                    $hariTelatEst = 0;
                                    if ($isTelat) {
                                        $hariTelatEst = (int) ceil((strtotime(date('Y-m-d')) - strtotime($item->tanggal_jatuh_tempo)) / 86400);
                                    }
                                @endphp
                                <tr>
                                    <td style="text-align: center; color: var(--text-secondary); font-weight: 600;">
                                        {{ $index + 1 }}
                                    </td>
                                    <td>
                                        <span class="badge-isbn">{{ $item->kode_transaksi }}</span>
                                        @if($item->pengembalian)
                                            <div style="margin-top: 4px;">
                                                <span class="badge-status info" style="font-size: 10.5px; font-weight: 600;">
                                                    <i class="bi bi-receipt"></i> {{ $item->pengembalian->kode_pengembalian }}
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: var(--text-primary); font-size: 13.5px;">
                                            {{ $item->anggota->nama_lengkap ?? 'Anggota #' . $item->id_anggota }}
                                        </div>
                                        <div style="font-size: 11.5px; color: var(--text-secondary); margin-top: 2px;">
                                            {{ $item->anggota->nomor_anggota ?? '-' }} &bull; {{ $item->anggota->jenis_anggota ?? 'Mahasiswa' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: var(--text-primary); font-size: 13.5px;">
                                            {{ $item->buku->judul ?? 'Buku #' . $item->id_buku }}
                                        </div>
                                        <div style="font-size: 11.5px; color: var(--text-secondary); margin-top: 2px;">
                                            ISBN: {{ $item->buku->isbn ?? '-' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size: 13px; font-weight: 600; color: {{ $isTelat ? '#C62828' : 'var(--text-primary)' }};">
                                            {{ date('d/m/Y', strtotime($item->tanggal_jatuh_tempo)) }}
                                        </div>
                                        @if($isTelat)
                                            <div style="font-size: 10.5px; color: #C62828; font-weight: 600;">
                                                Telat {{ $hariTelatEst }} hari
                                            </div>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        @if($isKembali)
                                            <span class="badge-status success"><i class="bi bi-check-circle-fill"></i> Dikembalikan</span>
                                            @if($item->tanggal_kembali)
                                                <div style="font-size: 11px; color: var(--text-secondary); margin-top: 2px;">
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
                                            <div style="font-weight: 700; color: {{ $item->pengembalian->total_denda > 0 ? '#C62828' : '#2E7D32' }};">
                                                Rp {{ number_format($item->pengembalian->total_denda, 0, ',', '.') }}
                                            </div>
                                            <div style="font-size: 11px; color: var(--text-secondary); margin-top: 2px;">
                                                Kondisi: <strong>{{ $item->pengembalian->kondisi_buku }}</strong> 
                                                @if($item->pengembalian->keterlambatan_hari > 0)
                                                    &bull; Telat {{ $item->pengembalian->keterlambatan_hari }} hari
                                                @endif
                                            </div>
                                        @elseif($isTelat)
                                            <div style="font-size: 12px; color: #C62828; font-weight: 600;">
                                                Estimasi Denda: Rp {{ number_format($hariTelatEst * 1000, 0, ',', '.') }}
                                            </div>
                                            <div style="font-size: 10.5px; color: var(--text-secondary);">
                                                (Dihitung saat pengembalian disetujui)
                                            </div>
                                        @else
                                            <span style="font-size: 12px; color: var(--text-secondary);">Rp 0 (Tepat Waktu)</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        @if($item->pengembalian)
                                            @if($item->pengembalian->status_pembayaran === 'Lunas')
                                                <span class="badge-status success"><i class="bi bi-check-circle-fill"></i> Lunas</span>
                                            @elseif($item->pengembalian->status_pembayaran === 'Belum Lunas')
                                                <span class="badge-status danger"><i class="bi bi-exclamation-circle-fill"></i> Belum Lunas</span>
                                            @else
                                                <span class="badge-status info"><i class="bi bi-shield-check"></i> Bebas Denda</span>
                                            @endif
                                        @else
                                            <span style="font-size: 12px; color: var(--text-secondary);">-</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="action-group" style="justify-content: center;">
                                            @if(!$isKembali)
                                                <!-- Tombol APPROVE PENGEMBALIAN BUKU & DENDA (Hanya bisa dilakukan di modul ini) -->
                                                <button type="button" class="btn-action edit" 
                                                        style="background: var(--btn-primary); color: #FFFDF9; width: auto; padding: 5px 12px; gap: 5px; font-weight: 700;" 
                                                        title="Approve Pengembalian Buku & Hitung Denda"
                                                        onclick="approvePinjamModal({{ json_encode($item) }})">
                                                    <i class="bi bi-check2-circle"></i> Approve
                                                </button>
                                            @else
                                                <!-- Jika Ada Denda Yang Belum Lunas -->
                                                @if($item->pengembalian && $item->pengembalian->status_pembayaran === 'Belum Lunas')
                                                    <button type="button" class="btn-action edit" title="Lunasi Denda Anggota"
                                                            onclick="bayarDendaModal({{ json_encode($item->pengembalian) }})">
                                                        <i class="bi bi-wallet2"></i>
                                                    </button>
                                                @endif

                                                <!-- Tombol Batalkan / Reset Persetujuan Pengembalian (Sync balik ke status 'Dipinjam') -->
                                                <button type="button" class="btn-action delete" title="Batalkan Persetujuan Pengembalian (Kembalikan ke Dipinjam)"
                                                        onclick="confirmBatalPengembalian({{ $item->id_peminjaman }}, '{{ $item->kode_transaksi }}')">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </button>
                                            @endif

                                            <!-- Tombol Detail -->
                                            <button type="button" class="btn-action view" title="Lihat Detail Transaksi & Denda"
                                                    onclick="viewPengembalianDetail({{ json_encode($item) }})">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9">
                                        <div class="empty-state">
                                            <i class="bi bi-receipt-cutoff"></i>
                                            <h4>Belum ada transaksi peminjaman yang tercatat</h4>
                                            <p>Data transaksi peminjaman akan otomatis muncul di sini dan siap untuk disetujui pengembaliannya.</p>
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

    <!-- MODAL 1: Catat & Approve Pengembalian Buku (Insert / Approve) -->
    <div class="modal-overlay" id="modalAddPengembalian">
        <div class="modal-card">
            <div class="modal-header">
                <h3><i class="bi bi-check2-circle" style="color: var(--btn-primary);"></i> Approve Pengembalian Buku & Denda</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalAddPengembalian')">&times;</button>
            </div>
            <form action="{{ route('pengembalian.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="select_pinjam">Pilih Transaksi Peminjaman Yang Akan Dikembalikan <span class="required">*</span></label>
                        <select name="id_peminjaman" id="select_pinjam" class="form-select" required onchange="updateKalkulasiPreview()">
                            <option value="">-- Pilih Transaksi Peminjaman Aktif --</option>
                            @foreach($peminjamanAktif as $pj)
                                <option value="{{ $pj->id_peminjaman }}" 
                                        data-tempo="{{ $pj->tanggal_jatuh_tempo }}"
                                        data-buku="{{ $pj->buku->judul ?? '-' }}"
                                        data-anggota="{{ $pj->anggota->nama_lengkap ?? '-' }}">
                                    {{ $pj->kode_transaksi }} - {{ $pj->anggota->nama_lengkap ?? '-' }} | Buku: {{ $pj->buku->judul ?? '-' }} (Jatuh Tempo: {{ $pj->tanggal_jatuh_tempo }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="kembali_tgl">Tanggal Dikembalikan <span class="required">*</span></label>
                            <input type="date" name="tanggal_dikembalikan" id="kembali_tgl" class="form-control" value="{{ date('Y-m-d') }}" required onchange="updateKalkulasiPreview()">
                        </div>
                        <div class="form-group">
                            <label for="kembali_kondisi">Kondisi Fisik Buku <span class="required">*</span></label>
                            <select name="kondisi_buku" id="kembali_kondisi" class="form-select" required onchange="updateKalkulasiPreview()">
                                <option value="Baik" selected>Kondisi Baik (Tidak ada denda fisik)</option>
                                <option value="Rusak Ringan">Rusak Ringan (Denda Rp 15.000)</option>
                                <option value="Rusak Berat">Rusak Berat (Denda Rp 35.000)</option>
                                <option value="Hilang">Buku Hilang (Denda Penggantian Rp 75.000)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Kotak Preview Kalkulasi Otomatis Denda -->
                    <div style="background: #F8F4EE; padding: 14px; border-radius: 8px; margin-bottom: 16px; border: 1px solid var(--border-beige);">
                        <div style="font-size: 13px; font-weight: 700; color: var(--text-primary); margin-bottom: 6px;">
                            <i class="bi bi-calculator"></i> Kalkulasi Denda Otomatis Sistem:
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 12.5px; color: var(--text-secondary); margin-bottom: 3px;">
                            <span>Keterlambatan:</span>
                            <span id="prev_telat">0 hari (Rp 0)</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 12.5px; color: var(--text-secondary); margin-bottom: 3px;">
                            <span>Denda Kerusakan / Hilang:</span>
                            <span id="prev_rusak">Rp 0</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 14px; font-weight: 800; color: #79583F; border-top: 1px dashed var(--border-beige); padding-top: 6px; margin-top: 4px;">
                            <span>Total Denda:</span>
                            <span id="prev_total">Rp 0</span>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 14px;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13.5px; font-weight: 600;">
                            <input type="checkbox" name="status_bayar_langsung" value="lunas" style="width: 17px; height: 17px; accent-color: var(--btn-primary);">
                            Denda Langsung Dibayar Lunas oleh Anggota Sekarang
                        </label>
                    </div>

                    <div class="form-group">
                        <label for="kembali_catatan">Catatan / Keterangan Pengembalian</label>
                        <textarea name="catatan" id="kembali_catatan" rows="2" class="form-control" placeholder="Contoh: Kondisi buku utuh, denda dibayar tunai..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalAddPengembalian')">Batal</button>
                    <button type="submit" class="btn-main"><i class="bi bi-check2-all"></i> Approve Pengembalian</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: Lunasi Denda (Update) -->
    <div class="modal-overlay" id="modalBayarDenda">
        <div class="modal-card" style="max-width: 460px;">
            <div class="modal-header">
                <h3><i class="bi bi-wallet2" style="color: var(--menu-active);"></i> Konfirmasi Pembayaran Denda</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalBayarDenda')">&times;</button>
            </div>
            <form id="formBayarDenda" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div style="background: #F8F4EE; padding: 14px; border-radius: 8px; margin-bottom: 16px; border: 1px solid var(--border-beige); text-align: center;">
                        <div style="font-size: 12.5px; color: var(--text-secondary);">Kode Pengembalian:</div>
                        <h4 id="bayar_kode" style="font-size: 16px; font-weight: 800; color: var(--text-primary); margin-bottom: 8px;">-</h4>
                        <div style="font-size: 12.5px; color: var(--text-secondary);">Tagihan Denda Yang Harus Dibayar:</div>
                        <h2 id="bayar_total" style="font-size: 24px; font-weight: 800; color: #C62828; margin-top: 4px;">Rp 0</h2>
                    </div>

                    <div class="form-group">
                        <label for="catatan_bayar">Catatan Pembayaran</label>
                        <input type="text" name="catatan_bayar" id="catatan_bayar" class="form-control" placeholder="Contoh: Dibayar tunai di meja sirkulasi" value="Dibayar tunai">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalBayarDenda')">Batal</button>
                    <button type="submit" class="btn-main"><i class="bi bi-cash-coin"></i> Tandai Sudah Lunas</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: Detail Pengembalian (Read Detail) -->
    <div class="modal-overlay" id="modalDetailPengembalian">
        <div class="modal-card">
            <div class="modal-header">
                <h3><i class="bi bi-info-circle" style="color: var(--sidebar-bg);"></i> Rincian Pengembalian & Denda</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalDetailPengembalian')">&times;</button>
            </div>
            <div class="modal-body">
                <div style="margin-bottom: 16px; text-align: center; padding: 12px; background: #F8F4EE; border-radius: 8px; border: 1px solid var(--border-beige);">
                    <span id="dtl_kembali_kode" class="badge-isbn" style="font-size: 14px;">-</span>
                    <div id="dtl_kembali_status" style="margin-top: 6px;">-</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label"><i class="bi bi-person"></i> Anggota Peminjam</div>
                    <div class="detail-value" id="dtl_kembali_anggota">-</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label"><i class="bi bi-book"></i> Judul Buku</div>
                    <div class="detail-value" id="dtl_kembali_buku">-</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label"><i class="bi bi-calendar-event"></i> Tanggal Dikembalikan</div>
                    <div class="detail-value" id="dtl_kembali_tgl">-</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label"><i class="bi bi-patch-check"></i> Kondisi Buku</div>
                    <div class="detail-value" id="dtl_kembali_kondisi">-</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label"><i class="bi bi-alarm"></i> Denda Keterlambatan</div>
                    <div class="detail-value" id="dtl_kembali_denda_telat">-</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label"><i class="bi bi-shield-slash"></i> Denda Kerusakan/Hilang</div>
                    <div class="detail-value" id="dtl_kembali_denda_rusak">-</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label"><i class="bi bi-cash-stack"></i> Total Denda</div>
                    <div class="detail-value" id="dtl_kembali_total_denda" style="font-weight: 800; color: #79583F;">-</div>
                </div>

                <div class="detail-row" style="flex-direction: column; gap: 4px; border-bottom: none;">
                    <div class="detail-label"><i class="bi bi-chat-left-text"></i> Catatan Transaksi</div>
                    <div class="synopsis-box" id="dtl_kembali_catatan">-</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modalDetailPengembalian')">Tutup</button>
            </div>
        </div>
    </div>

    <!-- MODAL 4: Batalkan Persetujuan Pengembalian (Delete / Reset) -->
    <div class="modal-overlay" id="modalDeletePengembalian">
        <div class="modal-card" style="max-width: 440px;">
            <div class="modal-header">
                <h3 style="color: #D32F2F;"><i class="bi bi-exclamation-triangle-fill"></i> Batalkan Pengembalian</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalDeletePengembalian')">&times;</button>
            </div>
            <form id="formDeletePengembalian" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body" style="text-align: center; padding: 26px 20px;">
                    <i class="bi bi-arrow-counterclockwise" style="font-size: 46px; color: #D32F2F; margin-bottom: 12px; display: inline-block;"></i>
                    <p style="font-size: 14.5px; color: var(--text-primary); margin-bottom: 8px;">
                        Apakah Anda yakin ingin membatalkan persetujuan pengembalian transaksi ini?
                    </p>
                    <h4 id="deleteKodeKembali" style="font-size: 15px; font-weight: 700; color: #D32F2F; background: #FFEBEE; padding: 10px; border-radius: 8px; border: 1px solid #FFCDD2;"></h4>
                    <p style="font-size: 12px; color: var(--text-secondary); margin-top: 10px;">
                        Status transaksi akan otomatis dikembalikan menjadi <strong>"Dipinjam"</strong> di kedua modul, catatan denda dibatalkan, dan stok buku fisik akan disesuaikan kembali.
                    </p>
                </div>
                <div class="modal-footer" style="justify-content: center;">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalDeletePengembalian')">Batal</button>
                    <button type="submit" class="btn-main" style="background-color: #D32F2F; border-color: #D32F2F;">
                        <i class="bi bi-arrow-counterclockwise"></i> Ya, Batalkan Pengembalian
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Javascript Kalkulasi Realtime & Modal -->
    <script>
        function openModal(id) {
            document.getElementById(id).classList.add('active');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }

        // Buka Modal Approve Pengembalian khusus dari baris transaksi
        function approvePinjamModal(data) {
            const selectPinjam = document.getElementById('select_pinjam');
            
            // Cek apakah opsi sudah ada di select, jika belum tambahkan opsi transaksi ini
            let found = false;
            for (let i = 0; i < selectPinjam.options.length; i++) {
                if (selectPinjam.options[i].value == data.id_peminjaman) {
                    selectPinjam.selectedIndex = i;
                    found = true;
                    break;
                }
            }
            if (!found) {
                const opt = document.createElement('option');
                opt.value = data.id_peminjaman;
                opt.dataset.tempo = data.tanggal_jatuh_tempo;
                opt.dataset.buku = data.buku ? data.buku.judul : '-';
                opt.dataset.anggota = data.anggota ? data.anggota.nama_lengkap : '-';
                opt.innerText = data.kode_transaksi + ' - ' + (data.anggota ? data.anggota.nama_lengkap : '-') + ' | Buku: ' + (data.buku ? data.buku.judul : '-');
                selectPinjam.appendChild(opt);
                selectPinjam.value = data.id_peminjaman;
            }

            document.getElementById('kembali_tgl').value = new Date().toISOString().split('T')[0];
            document.getElementById('kembali_kondisi').value = 'Baik';
            updateKalkulasiPreview();
            openModal('modalAddPengembalian');
        }

        // Kalkulasi Otomatis Preview Denda di Modal Tambah
        function updateKalkulasiPreview() {
            const selectPinjam = document.getElementById('select_pinjam');
            const selectedOpt = selectPinjam.options[selectPinjam.selectedIndex];
            const tglKembaliVal = document.getElementById('kembali_tgl').value;
            const kondisiVal = document.getElementById('kembali_kondisi').value;

            if (!selectedOpt || !selectedOpt.dataset.tempo || !tglKembaliVal) {
                document.getElementById('prev_telat').innerText = '0 hari (Rp 0)';
                document.getElementById('prev_rusak').innerText = 'Rp 0';
                document.getElementById('prev_total').innerText = 'Rp 0';
                return;
            }

            const jatuhTempo = new Date(selectedOpt.dataset.tempo);
            const kembali = new Date(tglKembaliVal);
            
            let hariTelat = 0;
            let dendaTelat = 0;
            if (kembali > jatuhTempo) {
                const diffTime = Math.abs(kembali - jatuhTempo);
                hariTelat = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                dendaTelat = hariTelat * 1000;
            }

            let dendaRusak = 0;
            if (kondisiVal === 'Rusak Ringan') dendaRusak = 15000;
            else if (kondisiVal === 'Rusak Berat') dendaRusak = 35000;
            else if (kondisiVal === 'Hilang') dendaRusak = 75000;

            const total = dendaTelat + dendaRusak;

            document.getElementById('prev_telat').innerText = hariTelat + ' hari (Rp ' + dendaTelat.toLocaleString('id-ID') + ')';
            document.getElementById('prev_rusak').innerText = 'Rp ' + dendaRusak.toLocaleString('id-ID');
            document.getElementById('prev_total').innerText = 'Rp ' + total.toLocaleString('id-ID');
        }

        // Buka Modal Pelunasan Denda
        function bayarDendaModal(data) {
            document.getElementById('formBayarDenda').action = '/pengembalian/' + data.id_pengembalian + '/bayar-denda';
            document.getElementById('bayar_kode').innerText = data.kode_pengembalian;
            document.getElementById('bayar_total').innerText = 'Rp ' + parseFloat(data.total_denda).toLocaleString('id-ID');
            openModal('modalBayarDenda');
        }

        // Tampilkan Detail Pengembalian
        function viewPengembalianDetail(data) {
            const hasReturn = data.pengembalian != null;
            document.getElementById('dtl_kembali_kode').innerText = hasReturn ? data.pengembalian.kode_pengembalian : data.kode_transaksi;
            document.getElementById('dtl_kembali_anggota').innerText = (data.anggota ? data.anggota.nama_lengkap : 'Anggota');
            document.getElementById('dtl_kembali_buku').innerText = (data.buku ? data.buku.judul : 'Buku');
            document.getElementById('dtl_kembali_tgl').innerText = data.tanggal_kembali ? data.tanggal_kembali : 'Belum Dikembalikan';
            document.getElementById('dtl_kembali_kondisi').innerText = hasReturn ? data.pengembalian.kondisi_buku : 'Belum Dicek';
            document.getElementById('dtl_kembali_denda_telat').innerText = hasReturn ? (data.pengembalian.keterlambatan_hari + ' hari (Rp ' + parseFloat(data.pengembalian.denda_keterlambatan).toLocaleString('id-ID') + ')') : '-';
            document.getElementById('dtl_kembali_denda_rusak').innerText = hasReturn ? ('Rp ' + parseFloat(data.pengembalian.denda_kerusakan).toLocaleString('id-ID')) : '-';
            document.getElementById('dtl_kembali_total_denda').innerText = hasReturn ? ('Rp ' + parseFloat(data.pengembalian.total_denda).toLocaleString('id-ID')) : 'Rp 0';
            document.getElementById('dtl_kembali_catatan').innerText = (hasReturn && data.pengembalian.catatan) ? data.pengembalian.catatan : (data.catatan || 'Tidak ada catatan.');

            let badge = '';
            if (hasReturn) {
                if (data.pengembalian.status_pembayaran === 'Lunas') {
                    badge = '<span class="badge-status success"><i class="bi bi-check-circle-fill"></i> Denda Lunas</span>';
                } else if (data.pengembalian.status_pembayaran === 'Belum Lunas') {
                    badge = '<span class="badge-status danger"><i class="bi bi-exclamation-circle-fill"></i> Denda Belum Lunas</span>';
                } else {
                    badge = '<span class="badge-status info"><i class="bi bi-shield-check"></i> Bebas Denda</span>';
                }
            } else {
                badge = '<span class="badge-status warning"><i class="bi bi-hourglass-split"></i> Belum Dikembalikan</span>';
            }
            document.getElementById('dtl_kembali_status').innerHTML = badge;

            openModal('modalDetailPengembalian');
        }

        // Konfirmasi Pembatalan Persetujuan Pengembalian
        function confirmBatalPengembalian(idPeminjaman, kodeTransaksi) {
            document.getElementById('formDeletePengembalian').action = '/pengembalian/' + idPeminjaman;
            document.getElementById('deleteKodeKembali').innerText = kodeTransaksi;
            openModal('modalDeletePengembalian');
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
