-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 14 Jun 2025 pada 09.12
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `durian_sibuyunk`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `cabang`
--

CREATE TABLE `cabang` (
  `id` int(11) NOT NULL,
  `nama_cabang` varchar(50) NOT NULL,
  `kode_cabang` varchar(10) NOT NULL,
  `alamat` text DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `cabang`
--

INSERT INTO `cabang` (`id`, `nama_cabang`, `kode_cabang`, `alamat`, `telepon`, `created_at`) VALUES
(1, 'Tasikmalaya', 'TSK', 'Jl. Raya Tasikmalaya No. 123', '0265-123456', '2025-06-14 05:26:45'),
(2, 'Garut', 'GRT', 'Jl. Raya Garut No. 456', '0262-789012', '2025-06-14 05:26:45');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jenis_durian`
--

CREATE TABLE `jenis_durian` (
  `id` int(11) NOT NULL,
  `nama_jenis` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `jenis_durian`
--

INSERT INTO `jenis_durian` (`id`, `nama_jenis`, `deskripsi`, `created_at`) VALUES
(1, 'Monthong bali', '', '2025-06-14 05:26:45'),
(6, 'Monthong Palu', NULL, '2025-06-14 07:01:15'),
(7, 'Monthong Jatim', NULL, '2025-06-14 07:01:15'),
(8, 'Bawor', NULL, '2025-06-14 07:01:15'),
(9, 'Musangking ', NULL, '2025-06-14 07:01:15'),
(10, 'Kanyao', NULL, '2025-06-14 07:01:15'),
(11, 'Masmuar', NULL, '2025-06-14 07:01:15'),
(12, 'Duri Hitam', NULL, '2025-06-14 07:01:15'),
(13, 'Petruk', NULL, '2025-06-14 07:01:15'),
(14, 'Super Tembaga', NULL, '2025-06-14 07:01:15'),
(15, 'Lokal', NULL, '2025-06-14 07:01:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id`, `nama_kategori`, `deskripsi`, `created_at`) VALUES
(1, 'Durian Segar', 'Durian segar berbagai jenis', '2025-06-14 05:26:45'),
(2, 'Olahan Durian', 'Produk olahan dari durian', '2025-06-14 05:26:45'),
(3, 'Minuman Durian', 'Minuman berbahan dasar durian', '2025-06-14 05:26:45'),
(4, 'Frozen Durian', 'Durian beku siap konsumsi', '2025-06-14 05:26:45');

-- --------------------------------------------------------

--
-- Struktur dari tabel `produk`
--

CREATE TABLE `produk` (
  `id` int(11) NOT NULL,
  `nama_produk` varchar(200) NOT NULL,
  `kategori_id` int(11) DEFAULT NULL,
  `jenis_durian_id` int(11) DEFAULT NULL,
  `harga` decimal(12,2) NOT NULL,
  `stok_tasik` int(11) DEFAULT 0,
  `stok_garut` int(11) DEFAULT 0,
  `satuan` varchar(20) DEFAULT 'pcs',
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `produk`
--

INSERT INTO `produk` (`id`, `nama_produk`, `kategori_id`, `jenis_durian_id`, `harga`, `stok_tasik`, `stok_garut`, `satuan`, `deskripsi`, `gambar`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Durian Monthong Premium', 1, 1, 85000.00, 0, 15, 'kg', 'Durian monthong premium pilihan terbaik', NULL, 'aktif', '2025-06-14 05:26:45', '2025-06-14 06:33:02'),
(2, 'Durian Musang King', 1, NULL, 120000.00, 10, 8, 'kg', 'Durian musang king premium dengan daging tebal', NULL, 'nonaktif', '2025-06-14 05:26:45', '2025-06-14 05:45:09'),
(3, 'Es Krim Durian', 2, NULL, 15000.00, 50, 40, 'cup', 'Es krim durian lezat dan segar', NULL, 'nonaktif', '2025-06-14 05:26:45', '2025-06-14 05:45:12'),
(4, 'Pancake Durian', 2, NULL, 25000.00, 30, 25, 'box', 'Pancake lembut dengan isian durian', NULL, 'nonaktif', '2025-06-14 05:26:45', '2025-06-14 05:45:19'),
(5, 'Jus Durian Fresh', 3, NULL, 18000.00, 25, 20, 'gelas', 'Jus durian segar tanpa campuran', NULL, 'nonaktif', '2025-06-14 05:26:45', '2025-06-14 05:45:15'),
(6, 'Eskrim Durian', 2, NULL, 5000.00, 10, 0, 'cup', '', NULL, 'aktif', '2025-06-14 07:03:23', '2025-06-14 07:03:23');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `cabang_id` int(11) NOT NULL,
  `role` enum('admin') DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `cabang_id`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin_tasik', '$2y$10$QZeVFCjLrcHWG8MshJ57Fe/ZHI8B0Qx98J8Hda/WfTrEerIbpphUa', 'Admin Tasikmalaya', 1, 'admin', '2025-06-14 05:26:45', '2025-06-14 05:26:45'),
(2, 'admin_garut', '$2y$10$ZxL3Gcb8Hhc5vJZO.tWo/e6BDhWggOMQ.TwaknrN8cV/hu76Zxn4i', 'Admin Garut', 2, 'admin', '2025-06-14 05:26:45', '2025-06-14 05:26:45');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `cabang`
--
ALTER TABLE `cabang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_cabang` (`kode_cabang`);

--
-- Indeks untuk tabel `jenis_durian`
--
ALTER TABLE `jenis_durian`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kategori_id` (`kategori_id`),
  ADD KEY `jenis_durian_id` (`jenis_durian_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `cabang`
--
ALTER TABLE `cabang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `jenis_durian`
--
ALTER TABLE `jenis_durian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `produk_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `produk_ibfk_2` FOREIGN KEY (`jenis_durian_id`) REFERENCES `jenis_durian` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
