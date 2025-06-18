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
(1, 'Musang King', 'Raja durian dengan daging golden-yellow, rasa manis-pahit seimbang, aroma intens dengan hint karamel', '2025-06-14 05:26:45'),
(2, 'D24 Sultan', 'Varietas premium dengan daging kuning pucat, tekstur mentega, rasa manis ringan', '2025-06-14 07:01:15'),
(3, 'Red Prawn', 'Durian dengan daging kemerahan-orange, rasa kompleks manis-pahit, tekstur lembut', '2025-06-14 07:01:15'),
(4, 'Black Thorn', 'Rival Musang King, aroma sangat kuat, daging kuning, untuk pecinta durian berpengalaman', '2025-06-14 07:01:15'),
(5, 'D13', 'Durian pemula, daging orange, rasa manis ringan, aroma tidak terlalu menyengat', '2025-06-14 07:01:15'),
(6, 'Monthong', 'Durian Thailand, daging kuning tebal, rasa manis, populer untuk ekspor', '2025-06-14 07:01:15'),
(7, 'XO', 'Durian premium dengan rasa alkohol dari fermentasi alami, untuk pecinta durian ahli', '2025-06-14 07:01:15'),
(8, 'Udang Merah', 'Durian pemula dengan daging orange-merah, rasa manis lembut, aroma ringan', '2025-06-14 07:01:15'),
(9, 'Golden Phoenix', 'Durian emas dengan daging kuning cerah, rasa manis kompleks', '2025-06-14 07:01:15'),
(10, 'Kampung Durian', 'Durian lokal tradisional, rasa autentik, harga terjangkau', '2025-06-14 07:01:15'),
(11, 'IOI', 'Durian hybrid modern, cocok untuk pemula, rasa manis seimbang', '2025-06-14 07:01:15'),
(12, 'Bawor', 'Durian lokal Jawa Tengah, daging tebal, rasa manis legit', '2025-06-14 07:01:15');

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
(1, 'Durian Premium', 'Durian kelas premium seperti Musang King, Black Thorn, Red Prawn - harga tinggi', '2025-06-14 05:26:45'),
(2, 'Durian Reguler', 'Durian kualitas baik dengan harga terjangkau seperti Monthong, D24, D13', '2025-06-14 05:26:45'),
(3, 'Durian Lokal', 'Durian varietas lokal Indonesia seperti Bawor, Petruk, Kampung', '2025-06-14 05:26:45'),
(4, 'Durian Frozen', 'Durian kupas beku ready to eat dalam kemasan higienis', '2025-06-14 05:26:45');

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
  `harga_per_kg` decimal(12,2) DEFAULT 0.00 COMMENT 'Harga per kilogram',
  `stok_tasik` int(11) DEFAULT 0,
  `stok_garut` int(11) DEFAULT 0,
  `total_kg_tasik` decimal(10,3) DEFAULT 0.000 COMMENT 'Total berat stok Tasik dalam kg',
  `total_kg_garut` decimal(10,3) DEFAULT 0.000 COMMENT 'Total berat stok Garut dalam kg',
  `total_pcs_tasik` int(11) DEFAULT 0 COMMENT 'Total pieces Tasik',
  `total_pcs_garut` int(11) DEFAULT 0 COMMENT 'Total pieces Garut',
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

INSERT INTO `produk` (`id`, `nama_produk`, `kategori_id`, `jenis_durian_id`, `harga`, `harga_per_kg`, `stok_tasik`, `stok_garut`, `total_kg_tasik`, `total_kg_garut`, `total_pcs_tasik`, `total_pcs_garut`, `satuan`, `deskripsi`, `gambar`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Durian Musang King Super', 1, 1, 150000.00, 150000.00, 25, 18, 62.500, 45.000, 25, 18, 'kg', 'Durian Musang King grade A, daging tebal golden yellow, aroma khas', NULL, 'aktif', '2025-06-14 05:26:45', '2025-06-14 06:33:02'),
(2, 'Durian D24 Sultan Premium', 1, 2, 120000.00, 120000.00, 35, 28, 87.500, 70.000, 35, 28, 'kg', 'Durian D24 Sultan grade premium, rasa manis lembut tekstur mentega', NULL, 'aktif', '2025-06-14 05:26:45', '2025-06-14 05:45:09'),
(3, 'Durian Red Prawn Jumbo', 1, 3, 135000.00, 135000.00, 20, 15, 50.000, 37.500, 20, 15, 'kg', 'Durian Red Prawn ukuran besar, daging orange kemerahan yang unik', NULL, 'aktif', '2025-06-14 05:26:45', '2025-06-14 05:45:12'),
(4, 'Durian Black Thorn Pilihan', 1, 4, 145000.00, 145000.00, 12, 8, 30.000, 20.000, 12, 8, 'kg', 'Durian Black Thorn kualitas terbaik, untuk pecinta durian sejati', NULL, 'aktif', '2025-06-14 05:26:45', '2025-06-14 05:45:19'),
(5, 'Durian Monthong Thailand', 2, 6, 85000.00, 85000.00, 45, 38, 112.500, 95.000, 45, 38, 'kg', 'Durian Monthong import Thailand, daging tebal rasa manis', NULL, 'aktif', '2025-06-14 05:26:45', '2025-06-14 05:45:15'),
(6, 'Durian D13 Beginner', 2, 5, 75000.00, 75000.00, 40, 32, 80.000, 64.000, 40, 32, 'kg', 'Durian D13 cocok untuk pemula, rasa manis tidak menyengat', NULL, 'aktif', '2025-06-14 07:03:23', '2025-06-14 07:03:23'),
(7, 'Durian Bawor Lokal', 3, 12, 65000.00, 65000.00, 30, 25, 60.000, 50.000, 30, 25, 'kg', 'Durian Bawor asli Jawa Tengah, daging tebal rasa legit', NULL, 'aktif', '2025-06-14 07:03:23', '2025-06-14 07:03:23'),
(8, 'Durian Kampung Tradisional', 3, 10, 55000.00, 55000.00, 50, 42, 75.000, 63.000, 50, 42, 'kg', 'Durian kampung varietas lokal, harga ekonomis rasa autentik', NULL, 'aktif', '2025-06-14 07:03:23', '2025-06-14 07:03:23'),
(9, 'Durian Frozen Mix Premium', 4, 1, 95000.00, 95000.00, 22, 18, 44.000, 36.000, 22, 18, 'kg', 'Campuran durian premium dalam kemasan frozen higienis', NULL, 'aktif', '2025-06-14 07:03:23', '2025-06-14 07:03:23'),
(10, 'Durian XO Special', 1, 7, 160000.00, 160000.00, 8, 5, 16.000, 10.000, 8, 5, 'kg', 'Durian XO dengan rasa fermentasi alkohol alami, eksklusif', NULL, 'aktif', '2025-06-14 07:03:23', '2025-06-14 07:03:23');

-- --------------------------------------------------------

--
-- Struktur dari tabel `stok_history`
--

CREATE TABLE `stok_history` (
  `id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `cabang_id` int(11) NOT NULL,
  `cabang` enum('tasik','garut') NOT NULL COMMENT 'Branch identifier for weight functions',
  `jenis_transaksi` enum('masuk','keluar','penyesuaian') NOT NULL,
  `jumlah` int(11) NOT NULL,
  `jumlah_sebelum` int(11) DEFAULT 0 COMMENT 'Stock pieces before transaction',
  `jumlah_sesudah` int(11) DEFAULT 0 COMMENT 'Stock pieces after transaction',
  `selisih` int(11) DEFAULT 0 COMMENT 'Difference in pieces',
  `stok_sebelum` int(11) NOT NULL,
  `stok_sesudah` int(11) NOT NULL,
  `total_kg_sebelum` decimal(10,3) DEFAULT 0.000 COMMENT 'Total kg before transaction',
  `total_kg_sesudah` decimal(10,3) DEFAULT 0.000 COMMENT 'Total kg after transaction',
  `bobot_kg` decimal(8,3) DEFAULT 0.000 COMMENT 'Weight involved in this transaction',
  `jumlah_pcs` int(11) DEFAULT 0 COMMENT 'Pieces involved in this transaction',
  `harga_per_kg_saat_itu` decimal(12,2) DEFAULT 0.00 COMMENT 'Price per kg at transaction time',
  `keterangan` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `stok_history`
--

INSERT INTO `stok_history` (`id`, `produk_id`, `cabang_id`, `cabang`, `jenis_transaksi`, `jumlah`, `jumlah_sebelum`, `jumlah_sesudah`, `selisih`, `stok_sebelum`, `stok_sesudah`, `total_kg_sebelum`, `total_kg_sesudah`, `bobot_kg`, `jumlah_pcs`, `harga_per_kg_saat_itu`, `keterangan`, `user_id`, `created_at`) VALUES
(1, 1, 1, 'tasik', 'masuk', 30, 0, 30, 30, 0, 30, 0.000, 75.000, 75.000, 30, 150000.00, 'Stok awal Durian Musang King Super cabang Tasikmalaya', 1, '2025-06-14 06:00:00'),
(2, 1, 2, 'garut', 'masuk', 25, 0, 25, 25, 0, 25, 0.000, 62.500, 62.500, 25, 150000.00, 'Stok awal Durian Musang King Super cabang Garut', 2, '2025-06-14 06:00:00'),
(3, 2, 1, 'tasik', 'masuk', 40, 0, 40, 40, 0, 40, 0.000, 100.000, 100.000, 40, 120000.00, 'Stok awal Durian D24 Sultan Premium cabang Tasikmalaya', 1, '2025-06-14 06:05:00'),
(4, 2, 2, 'garut', 'masuk', 30, 0, 30, 30, 0, 30, 0.000, 75.000, 75.000, 30, 120000.00, 'Stok awal Durian D24 Sultan Premium cabang Garut', 2, '2025-06-14 06:05:00'),
(5, 5, 1, 'tasik', 'masuk', 50, 0, 50, 50, 0, 50, 0.000, 125.000, 125.000, 50, 85000.00, 'Stok awal Durian Monthong Thailand cabang Tasikmalaya', 1, '2025-06-14 06:10:00'),
(6, 5, 2, 'garut', 'masuk', 40, 0, 40, 40, 0, 40, 0.000, 100.000, 100.000, 40, 85000.00, 'Stok awal Durian Monthong Thailand cabang Garut', 2, '2025-06-14 06:10:00'),
(7, 1, 1, 'tasik', 'keluar', 5, 30, 25, -5, 30, 25, 75.000, 62.500, 12.500, 5, 150000.00, 'Penjualan durian Musang King ke pelanggan premium', 1, '2025-06-14 08:30:00'),
(8, 2, 1, 'tasik', 'keluar', 5, 40, 35, -5, 40, 35, 100.000, 87.500, 12.500, 5, 120000.00, 'Penjualan durian D24 Sultan untuk acara keluarga', 1, '2025-06-14 09:15:00'),
(9, 2, 2, 'garut', 'keluar', 2, 30, 28, -2, 30, 28, 75.000, 70.000, 5.000, 2, 120000.00, 'Penjualan durian D24 Sultan cabang Garut', 2, '2025-06-14 10:00:00'),
(10, 5, 1, 'tasik', 'keluar', 5, 50, 45, -5, 50, 45, 125.000, 112.500, 12.500, 5, 85000.00, 'Penjualan durian Monthong untuk reseller', 1, '2025-06-14 11:30:00'),
(11, 1, 2, 'garut', 'keluar', 7, 25, 18, -7, 25, 18, 62.500, 45.000, 17.500, 7, 150000.00, 'Penjualan besar durian Musang King cabang Garut', 2, '2025-06-14 12:00:00'),
(12, 8, 1, 'tasik', 'masuk', 50, 0, 50, 50, 0, 50, 0.000, 75.000, 75.000, 50, 55000.00, 'Stok awal Durian Kampung Tradisional cabang Tasikmalaya', 1, '2025-06-14 13:00:00'),
(13, 8, 2, 'garut', 'masuk', 42, 0, 42, 42, 0, 42, 0.000, 63.000, 63.000, 42, 55000.00, 'Stok awal Durian Kampung Tradisional cabang Garut', 2, '2025-06-14 13:00:00'),
(14, 7, 1, 'tasik', 'masuk', 30, 0, 30, 30, 0, 30, 0.000, 60.000, 60.000, 30, 65000.00, 'Stok awal Durian Bawor Lokal cabang Tasikmalaya', 1, '2025-06-14 13:30:00'),
(15, 7, 2, 'garut', 'masuk', 25, 0, 25, 25, 0, 25, 0.000, 50.000, 50.000, 25, 65000.00, 'Stok awal Durian Bawor Lokal cabang Garut', 2, '2025-06-14 13:30:00');

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
-- Indeks untuk tabel `stok_history`
--
ALTER TABLE `stok_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produk_id` (`produk_id`),
  ADD KEY `cabang_id` (`cabang_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_created_at` (`created_at`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `stok_history`
--
ALTER TABLE `stok_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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

--
-- Ketidakleluasaan untuk tabel `stok_history`
--
ALTER TABLE `stok_history`
  ADD CONSTRAINT `stok_history_ibfk_1` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stok_history_ibfk_2` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stok_history_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
