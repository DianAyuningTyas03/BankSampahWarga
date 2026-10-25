-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 25, 2026 at 03:53 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bank_sampah_warga`
--

-- --------------------------------------------------------

--
-- Table structure for table `jenis_sampah`
--

CREATE TABLE `jenis_sampah` (
  `id` int NOT NULL,
  `nama_sampah` varchar(100) NOT NULL,
  `harga_per_kg` decimal(12,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `jenis_sampah`
--

INSERT INTO `jenis_sampah` (`id`, `nama_sampah`, `harga_per_kg`) VALUES
(1, 'Kardus', '5000.00'),
(2, 'Botol Plastik', '2000.00'),
(3, 'Besi', '8000.00'),
(4, 'Kaca', '2500.00');

-- --------------------------------------------------------

--
-- Table structure for table `setoran`
--

CREATE TABLE `setoran` (
  `id` int NOT NULL,
  `warga_id` int NOT NULL,
  `petugas_id` int DEFAULT NULL,
  `jenis_sampah_id` int NOT NULL,
  `berat` decimal(10,2) NOT NULL,
  `harga_per_kg` decimal(12,2) NOT NULL,
  `total_harga` decimal(12,2) NOT NULL,
  `tanggal` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('menunggu','diterima','ditolak') DEFAULT 'menunggu',
  `foto_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `setoran`
--

INSERT INTO `setoran` (`id`, `warga_id`, `petugas_id`, `jenis_sampah_id`, `berat`, `harga_per_kg`, `total_harga`, `tanggal`, `status`, `foto_path`) VALUES
(1, 3, 2, 4, '4.00', '2500.00', '10000.00', '2026-10-25 09:03:29', 'menunggu', NULL),
(2, 3, 2, 1, '15.00', '5000.00', '75000.00', '2026-10-25 09:41:45', 'menunggu', NULL),
(3, 3, 2, 4, '1.00', '2500.00', '2500.00', '2026-10-25 11:53:55', 'menunggu', NULL),
(4, 3, 2, 4, '3.00', '2500.00', '7500.00', '2026-10-25 12:13:33', 'menunggu', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','petugas','warga') NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `alamat` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `kode_warga` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `password`, `role`, `no_hp`, `alamat`, `created_at`, `kode_warga`) VALUES
(1, 'Administrator', 'admin', '$2y$10$MGZzMZZD/D0dl9WBvVt65eDTATOsXLFGWmeMhcckMfm0Xdt2UfGF.', 'admin', NULL, NULL, '2026-10-24 06:40:00', NULL),
(2, 'Petugas', 'petugas', '$2y$10$FXx.EtGjW/IC2rYyP4m0b.l6bpYVg2Ynh/pF6ogJLKf5RqtMbI26G', 'petugas', NULL, NULL, '2026-10-24 06:40:00', NULL),
(3, 'Warga', 'warga', '$2y$10$z.bZak0FR.vkEbAWukLuaeCfhrEVzEdxR.5EZLOmkgsap18CmQvlW', 'warga', NULL, NULL, '2026-10-24 06:40:00', 'WRG001');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `jenis_sampah`
--
ALTER TABLE `jenis_sampah`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setoran`
--
ALTER TABLE `setoran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `warga_id` (`warga_id`),
  ADD KEY `petugas_id` (`petugas_id`),
  ADD KEY `jenis_sampah_id` (`jenis_sampah_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `kode_warga` (`kode_warga`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `jenis_sampah`
--
ALTER TABLE `jenis_sampah`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `setoran`
--
ALTER TABLE `setoran`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `setoran`
--
ALTER TABLE `setoran`
  ADD CONSTRAINT `setoran_ibfk_1` FOREIGN KEY (`warga_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `setoran_ibfk_2` FOREIGN KEY (`petugas_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `setoran_ibfk_3` FOREIGN KEY (`jenis_sampah_id`) REFERENCES `jenis_sampah` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
