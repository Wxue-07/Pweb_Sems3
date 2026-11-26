-- ==============================================================================
-- DATABASE SISTEM MANAJEMEN PERPUSTAKAAN KAMPUS
-- KELOMPOK PPRI - PEMROGRAMAN WEB SEMESTER 3
-- ------------------------------------------------------------------------------
-- 1. Modul Katalog Buku: Winson (2531146)
-- 2. Modul Anggota Perpustakaan: Budi Yanto
-- 3. Modul Peminjaman Buku: Jeovanna
-- 4. Modul Pengembalian & Denda: Jolin
-- 5. Modul Pengadaan Buku Baru & Supplier: Rowin Journandez
-- ==============================================================================

CREATE DATABASE IF NOT EXISTS `pweb_project` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `pweb_project`;

-- ------------------------------------------------------------------------------
-- 1. Tabel: buku (Modul 1: Winson)
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `pengadaan_buku`;
DROP TABLE IF EXISTS `pengembalian_denda`;
DROP TABLE IF EXISTS `peminjaman`;
DROP TABLE IF EXISTS `supplier`;
DROP TABLE IF EXISTS `anggota`;
DROP TABLE IF EXISTS `buku`;

CREATE TABLE `buku` (
    `id_buku` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `isbn` VARCHAR(20) DEFAULT NULL,
    `judul` VARCHAR(200) NOT NULL,
    `penulis` VARCHAR(100) DEFAULT NULL,
    `penerbit` VARCHAR(100) DEFAULT NULL,
    `kategori_rak` VARCHAR(50) DEFAULT NULL,
    `sinopsis` TEXT DEFAULT NULL,
    `stok_tersedia` INT NOT NULL DEFAULT 1,
    PRIMARY KEY (`id_buku`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `buku` (`id_buku`, `isbn`, `judul`, `penulis`, `penerbit`, `kategori_rak`, `sinopsis`, `stok_tersedia`) VALUES
(1, '978-602-03-8812-1', 'Pemrograman Web Lanjut dengan PHP dan Laravel', 'Budi Raharjo', 'Informatika', 'Rak Web Dev A1', 'Panduan komprehensif membangun aplikasi web skala enterprise menggunakan arsitektur MVC pada framework Laravel.', 5),
(2, '978-602-29-1234-5', 'Sistem Basis Data Konsep dan Implementasi', 'Fathansyah', 'Informatika', 'Rak Database B2', 'Buku standar rujukan perkuliahan basis data, membahas perancangan ERD, normalisasi, dan implementasi SQL.', 4),
(3, '978-602-15-4321-0', 'Dasar Pemrograman Python untuk Sains dan Rekayasa', 'Abdul Kadir', 'Andi Offset', 'Rak Komputer C1', 'Pengenalan logika komputasi dan sintaks dasar bahasa pemrograman Python disertai contoh kasus praktikum mahasiswa.', 3),
(4, '978-979-29-7120-0', 'Algoritma dan Struktur Data Terapan', 'Rinaldi Munir', 'Informatika', 'Rak Algoritma A2', 'Membahas konsep efisiensi algoritma, array, stack, queue, tree, dan graph untuk penyelesaian problem komputasi.', 2),
(5, '978-602-44-6789-1', 'Rekayasa Perangkat Lunak Terstruktur dan Berorientasi Objek', 'Rosa A.S', 'Modula', 'Rak RPL B1', 'Kupas tuntas siklus pengembangan software menggunakan metode SDLC (Waterfall, Agile) dan permodelan UML.', 6);

-- ------------------------------------------------------------------------------
-- 2. Tabel: anggota (Modul 2: Budi Yanto)
-- ------------------------------------------------------------------------------
CREATE TABLE `anggota` (
    `id_anggota` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nomor_anggota` VARCHAR(30) NOT NULL UNIQUE,
    `nama_lengkap` VARCHAR(100) NOT NULL,
    `jenis_anggota` VARCHAR(30) NOT NULL DEFAULT 'Mahasiswa',
    `nim_nidn` VARCHAR(30) DEFAULT NULL,
    `program_studi` VARCHAR(100) DEFAULT NULL,
    `email` VARCHAR(100) DEFAULT NULL,
    `nomor_telepon` VARCHAR(20) DEFAULT NULL,
    `alamat` TEXT DEFAULT NULL,
    `status_aktif` VARCHAR(30) NOT NULL DEFAULT 'Aktif',
    `tanggal_bergabung` DATE DEFAULT NULL,
    PRIMARY KEY (`id_anggota`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `anggota` (`id_anggota`, `nomor_anggota`, `nama_lengkap`, `jenis_anggota`, `nim_nidn`, `program_studi`, `email`, `nomor_telepon`, `alamat`, `status_aktif`, `tanggal_bergabung`) VALUES
(1, 'MHS-2024-001', 'Budi Yanto', 'Mahasiswa', '2531088', 'Sistem Informasi', 'budiyanto@uib.ac.id', '081277665544', 'Komp. Baloi Blok B No. 12, Batam', 'Aktif', '2024-09-01'),
(2, 'MHS-2024-002', 'Jeovanna', 'Mahasiswa', '2531102', 'Sistem Informasi', 'jeovanna@uib.ac.id', '081388776655', 'Nagoya Hill Residence No. 8A, Batam', 'Aktif', '2024-09-01'),
(3, 'MHS-2024-003', 'Jolin', 'Mahasiswa', '2531095', 'Sistem Informasi', 'jolin@uib.ac.id', '081299887766', 'Batam Center Indah Blok C3, Batam', 'Aktif', '2024-09-01'),
(4, 'MHS-2024-004', 'Rowin Journandez', 'Mahasiswa', '2531120', 'Sistem Informasi', 'rowin@uib.ac.id', '081355443322', 'Tibon Permai Cluster Anggrek No. 21, Batam', 'Aktif', '2024-09-01'),
(5, 'DSN-2024-001', 'Dr. Hendra Pratama, M.Kom', 'Dosen', '1024088501', 'Sistem Informasi', 'hendra.pratama@uib.ac.id', '081122334455', 'Dosen Resident UIB Campus, Batam', 'Aktif', '2023-01-15'),
(6, 'MHS-2023-010', 'Siti Nurhaliza', 'Mahasiswa', '2431055', 'Teknik Informatika', 'siti.nur@uib.ac.id', '085211223344', 'Batu Aji Permai No. 44, Batam', 'Lulus / Keluar', '2023-09-01');

-- ------------------------------------------------------------------------------
-- 3. Tabel: peminjaman (Modul 3: Jeovanna)
-- ------------------------------------------------------------------------------
CREATE TABLE `peminjaman` (
    `id_peminjaman` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `kode_transaksi` VARCHAR(30) NOT NULL UNIQUE,
    `id_anggota` BIGINT UNSIGNED NOT NULL,
    `id_buku` BIGINT UNSIGNED NOT NULL,
    `tanggal_pinjam` DATE NOT NULL,
    `tanggal_jatuh_tempo` DATE NOT NULL,
    `tanggal_kembali` DATE DEFAULT NULL,
    `status_peminjaman` VARCHAR(30) NOT NULL DEFAULT 'Dipinjam',
    `catatan` TEXT DEFAULT NULL,
    PRIMARY KEY (`id_peminjaman`),
    KEY `idx_pinjam_anggota` (`id_anggota`),
    KEY `idx_pinjam_buku` (`id_buku`),
    CONSTRAINT `fk_pinjam_anggota` FOREIGN KEY (`id_anggota`) REFERENCES `anggota` (`id_anggota`) ON DELETE CASCADE,
    CONSTRAINT `fk_pinjam_buku` FOREIGN KEY (`id_buku`) REFERENCES `buku` (`id_buku`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `peminjaman` (`id_peminjaman`, `kode_transaksi`, `id_anggota`, `id_buku`, `tanggal_pinjam`, `tanggal_jatuh_tempo`, `tanggal_kembali`, `status_peminjaman`, `catatan`) VALUES
(1, 'TRX-PJ-20261101-001', 1, 1, '2026-11-01', '2026-11-08', '2026-11-08', 'Selesai', 'Peminjaman referensi mata kuliah pemrograman web'),
(2, 'TRX-PJ-20261110-002', 2, 2, '2026-11-10', '2026-11-17', NULL, 'Dipinjam', 'Peminjaman untuk keperluan tugas akhir semester'),
(3, 'TRX-PJ-20261118-003', 5, 5, '2026-11-18', '2026-11-25', NULL, 'Dipinjam', 'Peminjaman referensi dosen pengampu mata kuliah');

-- ------------------------------------------------------------------------------
-- 4. Tabel: pengembalian_denda (Modul 4: Jolin)
-- ------------------------------------------------------------------------------
CREATE TABLE `pengembalian_denda` (
    `id_pengembalian` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `kode_pengembalian` VARCHAR(30) NOT NULL UNIQUE,
    `id_peminjaman` BIGINT UNSIGNED NOT NULL,
    `tanggal_dikembalikan` DATE NOT NULL,
    `kondisi_buku` VARCHAR(30) NOT NULL DEFAULT 'Baik',
    `keterlambatan_hari` INT NOT NULL DEFAULT 0,
    `denda_keterlambatan` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `denda_kerusakan` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `total_denda` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `status_pembayaran` VARCHAR(30) NOT NULL DEFAULT 'Bebas Denda',
    `tanggal_pembayaran` DATE DEFAULT NULL,
    `catatan` TEXT DEFAULT NULL,
    PRIMARY KEY (`id_pengembalian`),
    KEY `idx_kembali_pinjam` (`id_peminjaman`),
    CONSTRAINT `fk_kembali_pinjam` FOREIGN KEY (`id_peminjaman`) REFERENCES `peminjaman` (`id_peminjaman`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `pengembalian_denda` (`id_pengembalian`, `kode_pengembalian`, `id_peminjaman`, `tanggal_dikembalikan`, `kondisi_buku`, `keterlambatan_hari`, `denda_keterlambatan`, `denda_kerusakan`, `total_denda`, `status_pembayaran`, `tanggal_pembayaran`, `catatan`) VALUES
(1, 'TRX-KB-20261108-001', 1, '2026-11-08', 'Baik', 0, 0.00, 0.00, 0.00, 'Bebas Denda', NULL, 'Dikembalikan tepat waktu dalam kondisi sangat baik');

-- ------------------------------------------------------------------------------
-- 5. Tabel: supplier (Modul 5: Rowin Journandez)
-- ------------------------------------------------------------------------------
CREATE TABLE `supplier` (
    `id_supplier` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `kode_supplier` VARCHAR(30) NOT NULL UNIQUE,
    `nama_supplier` VARCHAR(100) NOT NULL,
    `kontak_person` VARCHAR(100) DEFAULT NULL,
    `telepon` VARCHAR(20) DEFAULT NULL,
    `email` VARCHAR(100) DEFAULT NULL,
    `alamat` TEXT DEFAULT NULL,
    PRIMARY KEY (`id_supplier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `supplier` (`id_supplier`, `kode_supplier`, `nama_supplier`, `kontak_person`, `telepon`, `email`, `alamat`) VALUES
(1, 'SUP-001', 'Penerbit Informatika Bandung', 'Bpk. Dani Gunawan', '022-7208123', 'marketing@informatika.co.id', 'Jl. Buah Batu No. 45, Lengkong, Bandung'),
(2, 'SUP-002', 'Penerbit Andi Offset Yogyakarta', 'Ibu Maya Septiani', '0274-561881', 'sales@andipublisher.com', 'Jl. Beo No. 38-40, Gondokusuman, Yogyakarta'),
(3, 'SUP-003', 'Gramedia Pustaka Utama Jakarta', 'Bpk. Anton Nugroho', '021-53650110', 'wholesale@gramedia.id', 'Gedung Kompas Gramedia Blok I Lt. 5, Palmerah, Jakarta');

-- ------------------------------------------------------------------------------
-- 6. Tabel: pengadaan_buku (Modul 5: Rowin Journandez)
-- ------------------------------------------------------------------------------
CREATE TABLE `pengadaan_buku` (
    `id_pengadaan` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `kode_pengadaan` VARCHAR(30) NOT NULL UNIQUE,
    `id_supplier` BIGINT UNSIGNED NOT NULL,
    `id_buku` BIGINT UNSIGNED NOT NULL,
    `tanggal_pengadaan` DATE NOT NULL,
    `jumlah_masuk` INT NOT NULL DEFAULT 1,
    `harga_satuan` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `total_biaya` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `sumber_dana` VARCHAR(50) NOT NULL DEFAULT 'Kas Perpustakaan',
    `keterangan` TEXT DEFAULT NULL,
    PRIMARY KEY (`id_pengadaan`),
    KEY `idx_pengadaan_supplier` (`id_supplier`),
    KEY `idx_pengadaan_buku` (`id_buku`),
    CONSTRAINT `fk_pengadaan_supplier` FOREIGN KEY (`id_supplier`) REFERENCES `supplier` (`id_supplier`) ON DELETE CASCADE,
    CONSTRAINT `fk_pengadaan_buku` FOREIGN KEY (`id_buku`) REFERENCES `buku` (`id_buku`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `pengadaan_buku` (`id_pengadaan`, `kode_pengadaan`, `id_supplier`, `id_buku`, `tanggal_pengadaan`, `jumlah_masuk`, `harga_satuan`, `total_biaya`, `sumber_dana`, `keterangan`) VALUES
(1, 'PO-20261101-001', 1, 1, '2026-11-01', 5, 85000.00, 425000.00, 'Kas Perpustakaan', 'Pengadaan 5 eksemplar buku web Laravel edisi terbaru. Faktur #INV-IF-0112'),
(2, 'PO-20261105-002', 2, 3, '2026-11-05', 3, 65000.00, 195000.00, 'APBN / Yayasan Kampus', 'Pengadaan 3 eksemplar buku Python praktikum. Faktur #ANDI-2026-88');


-- ------------------------------------------------------------------------------
-- 7. Tabel: rak_buku (Modul CRUD Rak Buku Perpustakaan)
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `rak_buku`;
CREATE TABLE `rak_buku` (
    `id_rak` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `kode_rak` VARCHAR(30) NOT NULL UNIQUE,
    `nama_rak` VARCHAR(100) NOT NULL,
    `kategori_buku` VARCHAR(100) DEFAULT NULL,
    `lokasi_lantai` VARCHAR(100) NOT NULL DEFAULT 'Lantai 1',
    `kapasitas_maksimal` INT NOT NULL DEFAULT 50,
    `keterangan` TEXT DEFAULT NULL,
    PRIMARY KEY (`id_rak`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `rak_buku` (`id_rak`, `kode_rak`, `nama_rak`, `kategori_buku`, `lokasi_lantai`, `kapasitas_maksimal`, `keterangan`) VALUES
(1, 'RAK-A1', 'Rak Komputer & Web Dev', 'Teknologi Informasi', 'Lantai 1 - Sayap Barat', 50, 'Menampung buku pemrograman web, database, dan software engineering.'),
(2, 'RAK-A2', 'Rak Algoritma & Struktur Data', 'Ilmu Komputer', 'Lantai 1 - Sayap Barat', 40, 'Menampung buku dasar logika komputasi, struktur data, dan matematika informatika.'),
(3, 'RAK-B1', 'Rak Rekayasa Perangkat Lunak', 'Rekayasa Sistem', 'Lantai 1 - Sayap Timur', 45, 'Koleksi buku metodologi SDLC, perancangan sistem, UML, dan UI/UX.'),
(4, 'RAK-B2', 'Rak Basis Data & Data Science', 'Sistem Informasi', 'Lantai 2 - Sayap Barat', 60, 'Menampung buku konsep basis data, SQL, big data, dan data warehouse.'),
(5, 'RAK-C1', 'Rak Jaringan & Keamanan Siber', 'Infrastruktur IT', 'Lantai 2 - Sayap Timur', 50, 'Koleksi buku jaringan komputer, routing, server, dan cyber security.');

-- ------------------------------------------------------------------------------
-- 8. Data Default User Petugas Perpustakaan (Tabel: users)
-- ------------------------------------------------------------------------------
INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`, `updated_at`) VALUES
(1, 'Administrator Perpustakaan', 'admin@uib.ac.id', '$2y$10$.lRTVL2P1Eg4YF1oiEYoFOcuhvGxu59RtmuJJS3cNcxsbAFqsu9qO', NOW(), NOW())
ON DUPLICATE KEY UPDATE `password` = VALUES(`password`);
