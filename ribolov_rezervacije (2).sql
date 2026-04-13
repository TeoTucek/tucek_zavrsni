-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 13, 2026 at 08:01 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ribolov_rezervacije`
--

-- --------------------------------------------------------

--
-- Table structure for table `blokirani_datumi`
--

CREATE TABLE `blokirani_datumi` (
  `id_blokade` int(11) NOT NULL,
  `id_lokacije` int(11) DEFAULT NULL,
  `datum_od` date NOT NULL,
  `datum_do` date NOT NULL,
  `razlog` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blokirani_datumi`
--

INSERT INTO `blokirani_datumi` (`id_blokade`, `id_lokacije`, `datum_od`, `datum_do`, `razlog`) VALUES
(1, NULL, '2024-12-24', '2024-12-26', 'Božićni blagdani'),
(2, NULL, '2024-12-31', '2025-01-02', 'Nova godina'),
(3, NULL, '2024-05-01', '2024-05-01', 'Praznik rada');

-- --------------------------------------------------------

--
-- Table structure for table `dodatne_usluge`
--

CREATE TABLE `dodatne_usluge` (
  `id_usluge` int(11) NOT NULL,
  `naziv` varchar(100) NOT NULL,
  `jedinica_mjere` varchar(50) DEFAULT NULL,
  `cijena` decimal(10,2) NOT NULL,
  `potrebna_najava` tinyint(1) DEFAULT 0,
  `aktivan` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dodatne_usluge`
--

INSERT INTO `dodatne_usluge` (`id_usluge`, `naziv`, `jedinica_mjere`, `cijena`, `potrebna_najava`, `aktivan`) VALUES
(1, 'Šaranska kadica', 'kom', 3.90, 0, 1),
(2, 'Prihrana (hranidbeni tretman)', 'tretman', 4.90, 1, 1),
(3, 'Pelet 5mm', 'kg', 2.00, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `korisnici`
--

CREATE TABLE `korisnici` (
  `id_korisnika` int(11) NOT NULL,
  `ime` varchar(50) NOT NULL,
  `prezime` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `lozinka_hash` varchar(255) NOT NULL,
  `role` enum('admin','korisnik') DEFAULT 'korisnik',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `korisnici`
--

INSERT INTO `korisnici` (`id_korisnika`, `ime`, `prezime`, `email`, `lozinka_hash`, `role`, `created_at`) VALUES
(10, 'Admin', 'Administrator', 'admin@ribnjacstvo.hr', '$2y$10$Dk6oAN7s.L45S6//rDPyEuKlvWRx.XMrMG6.1mfR7nCuwriHz04Ai', 'admin', '2026-03-30 20:37:10');

-- --------------------------------------------------------

--
-- Table structure for table `lokacije`
--

CREATE TABLE `lokacije` (
  `id_lokacije` int(11) NOT NULL,
  `naziv` varchar(100) NOT NULL,
  `tip` enum('R23 pozicija','C&R Otok') NOT NULL,
  `kapacitet` int(11) NOT NULL DEFAULT 2,
  `opis` text DEFAULT NULL,
  `ima_struju` tinyint(1) DEFAULT 0,
  `ima_sjenicu` tinyint(1) DEFAULT 0,
  `aktivno` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lokacije`
--

INSERT INTO `lokacije` (`id_lokacije`, `naziv`, `tip`, `kapacitet`, `opis`, `ima_struju`, `ima_sjenicu`, `aktivno`) VALUES
(1, 'Pozicija 1', 'R23 pozicija', 2, 'Lijevo krilo, uz mostić', 1, 0, 1),
(2, 'Pozicija 2', 'R23 pozicija', 2, 'Lijevo krilo, sredina', 0, 0, 1),
(3, 'Pozicija 3', 'R23 pozicija', 2, 'Lijevo krilo, kraj', 1, 0, 1),
(4, 'Pozicija 4', 'R23 pozicija', 2, 'Sredina, hladovina', 0, 0, 1),
(5, 'Pozicija 5', 'R23 pozicija', 2, 'Sredina, plićak', 1, 0, 1),
(6, 'Pozicija 6', 'R23 pozicija', 2, 'Sredina, dubina', 0, 0, 1),
(7, 'Pozicija 7', 'R23 pozicija', 2, 'Desno krilo, uz trsku', 1, 0, 1),
(8, 'Pozicija 8', 'R23 pozicija', 2, 'Desno krilo', 0, 0, 1),
(9, 'Pozicija 9', 'R23 pozicija', 2, 'Desno krilo, kraj', 1, 0, 1),
(10, 'Pozicija 10', 'R23 pozicija', 2, 'Zabat, lijevo', 0, 0, 1),
(11, 'Pozicija 11', 'R23 pozicija', 2, 'Zabat, sredina', 1, 0, 1),
(12, 'Pozicija 12', 'R23 pozicija', 2, 'Zabat, desno', 0, 0, 1),
(13, 'Pozicija 13', 'R23 pozicija', 2, 'Poluotok', 1, 0, 1),
(14, 'Pozicija 14', 'R23 pozicija', 2, 'Uvala', 0, 0, 1),
(15, 'Pozicija 15', 'R23 pozicija', 2, 'Izvor vode', 1, 0, 1),
(16, 'Pozicija 16', 'R23 pozicija', 2, 'Most', 0, 0, 1),
(17, 'Pozicija 17', 'R23 pozicija', 2, 'Stara hrastovina', 1, 0, 1),
(18, 'C&R Otok', 'C&R Otok', 10, 'OTOK - sjenica, roštilj, struja, prijevoz barkom', 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `mamci`
--

CREATE TABLE `mamci` (
  `id_mamca` int(11) NOT NULL,
  `naziv` varchar(100) NOT NULL,
  `tezina_kg` decimal(5,2) NOT NULL,
  `cijena_eur` decimal(10,2) NOT NULL,
  `opis` text DEFAULT NULL,
  `na_stanju` int(11) DEFAULT 100
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mamci`
--

INSERT INTO `mamci` (`id_mamca`, `naziv`, `tezina_kg`, `cijena_eur`, `opis`, `na_stanju`) VALUES
(1, 'Kukuruz', 10.00, 6.00, 'Kukuruz za mamac - pakiranje 10kg', 100),
(2, 'Pelet 5mm', 1.00, 2.00, 'Fini pelet 5mm - 1kg', 100);

-- --------------------------------------------------------

--
-- Table structure for table `nocni_ribolov`
--

CREATE TABLE `nocni_ribolov` (
  `id_paketa` int(11) NOT NULL,
  `naziv` varchar(100) NOT NULL,
  `dani` int(11) NOT NULL,
  `cijena` decimal(10,2) NOT NULL,
  `aktivan` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nocni_ribolov`
--

INSERT INTO `nocni_ribolov` (`id_paketa`, `naziv`, `dani`, `cijena`, `aktivan`) VALUES
(1, 'Dvodnevni', 2, 35.00, 1),
(2, 'Trodnevni', 3, 45.00, 1),
(3, 'Dvodnevni - Otok', 2, 65.00, 1),
(4, 'Trodnevni - Otok', 3, 95.00, 1),
(5, 'Četverodnevni - Otok', 4, 119.00, 1),
(6, 'Sedmodnevni - Otok', 7, 179.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `povijest_statusa`
--

CREATE TABLE `povijest_statusa` (
  `id_povijest` int(11) NOT NULL,
  `id_rezervacije` int(11) NOT NULL,
  `stari_status` varchar(50) DEFAULT NULL,
  `novi_status` varchar(50) DEFAULT NULL,
  `promijenio` varchar(100) DEFAULT NULL,
  `vrijeme` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `povijest_statusa`
--

INSERT INTO `povijest_statusa` (`id_povijest`, `id_rezervacije`, `stari_status`, `novi_status`, `promijenio`, `vrijeme`) VALUES
(4, 15, 'na čekanju', 'potvrđeno', 'Admin Administrator', '2026-03-31 11:20:07'),
(5, 14, 'na čekanju', 'otkazano', 'Admin Administrator', '2026-03-31 11:29:22'),
(6, 16, 'na čekanju', 'potvrđeno', 'Admin Administrator', '2026-03-31 11:36:12'),
(7, 17, 'na čekanju', 'potvrđeno', 'Admin Administrator', '2026-03-31 11:45:03'),
(8, 18, 'na čekanju', 'potvrđeno', 'Admin Administrator', '2026-03-31 11:47:22'),
(9, 19, 'na čekanju', 'potvrđeno', 'Admin Administrator', '2026-03-31 11:55:05'),
(10, 20, 'na čekanju', 'potvrđeno', 'Admin Administrator', '2026-03-31 11:58:43'),
(11, 22, 'na čekanju', 'potvrđeno', 'Admin Administrator', '2026-03-31 17:31:21'),
(12, 22, 'potvrđeno', 'potvrđeno', 'Admin Administrator', '2026-03-31 17:35:41'),
(13, 21, 'na čekanju', 'potvrđeno', 'Admin Administrator', '2026-03-31 17:36:01'),
(14, 23, 'na čekanju', 'na čekanju', 'Admin Administrator', '2026-03-31 17:42:41'),
(15, 23, 'na čekanju', 'potvrđeno', 'Admin Administrator', '2026-03-31 17:42:47'),
(16, 24, 'na čekanju', 'otkazano', 'Admin Administrator', '2026-03-31 17:45:52'),
(17, 27, 'na čekanju', 'otkazano', 'Admin Administrator', '2026-03-31 17:56:23'),
(18, 26, 'na čekanju', 'na čekanju', 'Admin Administrator', '2026-03-31 18:02:12'),
(19, 26, 'na čekanju', 'otkazano', 'Admin Administrator', '2026-03-31 18:02:21'),
(20, 28, 'na čekanju', 'otkazano', 'Admin Administrator', '2026-03-31 22:20:43'),
(21, 31, 'na čekanju', 'otkazano', 'Admin Administrator', '2026-03-31 22:31:24'),
(22, 32, 'na čekanju', 'otkazano', 'Admin Administrator', '2026-03-31 22:33:37'),
(23, 33, 'na čekanju', 'potvrđeno', 'Admin Administrator', '2026-04-05 17:46:17');

-- --------------------------------------------------------

--
-- Table structure for table `rezervacije`
--

CREATE TABLE `rezervacije` (
  `id_rezervacije` int(11) NOT NULL,
  `id_lokacije` int(11) NOT NULL,
  `id_tipa_ulaznice` int(11) DEFAULT NULL,
  `id_paketa_nocni` int(11) DEFAULT NULL,
  `datum_rezervacije` date NOT NULL,
  `ime_prezime` varchar(100) NOT NULL,
  `broj_mobitela` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `broj_osoba` int(11) NOT NULL,
  `cijena_po_osobi` decimal(10,2) NOT NULL,
  `napomena` text DEFAULT NULL,
  `status` enum('na čekanju','potvrđeno','otkazano') DEFAULT 'na čekanju',
  `razlog_otkazivanja` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rezervacije`
--

INSERT INTO `rezervacije` (`id_rezervacije`, `id_lokacije`, `id_tipa_ulaznice`, `id_paketa_nocni`, `datum_rezervacije`, `ime_prezime`, `broj_mobitela`, `email`, `broj_osoba`, `cijena_po_osobi`, `napomena`, `status`, `razlog_otkazivanja`, `created_at`) VALUES
(14, 1, NULL, NULL, '2026-03-31', 'Lara Tuček', '099 661 4188', 'laratucek@gmail.com', 1, 13.90, '', 'otkazano', NULL, '2026-03-31 11:18:48'),
(15, 18, NULL, NULL, '2026-04-15', 'Pero Perić', '0912345678', 'teotucek@gmail.com', 2, 29.90, '', 'potvrđeno', NULL, '2026-03-31 11:19:29'),
(16, 9, NULL, NULL, '2026-03-31', 'Mihael', '099 661 4188', 'teotucek@gmail.com', 2, 13.90, '', 'potvrđeno', NULL, '2026-03-31 11:35:44'),
(17, 8, NULL, NULL, '2026-03-31', 'teo', '099 661 4188', 'teotucek@gmail.com', 2, 13.90, '', 'potvrđeno', NULL, '2026-03-31 11:38:29'),
(18, 18, NULL, NULL, '2026-03-31', 'Pero Perić', '0912345678', 'darkotucek9@gmail.com', 1, 29.90, '', 'potvrđeno', NULL, '2026-03-31 11:46:49'),
(19, 18, NULL, NULL, '2026-04-01', 'Pero Perić', '099 661 4188', 'darkotucek9@gmail.com', 2, 29.90, '', 'potvrđeno', NULL, '2026-03-31 11:54:52'),
(20, 9, NULL, NULL, '2026-04-30', 'Pero Perić', '0981689310', 'teotucek@gmail.com', 2, 13.90, '', 'potvrđeno', NULL, '2026-03-31 11:58:15'),
(21, 9, NULL, NULL, '2026-04-29', 'TEO', '0912345678', 'darkotucek9@gmail.com', 1, 17.50, 'kasnim', 'potvrđeno', NULL, '2026-03-31 17:26:12'),
(22, 8, NULL, NULL, '2026-04-29', 'Pero Perić', '0912345678', 'teo.tucek@skole.hr', 1, 13.90, '', 'potvrđeno', NULL, '2026-03-31 17:29:05'),
(23, 6, NULL, NULL, '2026-03-31', 'Mihael', '099 661 4188', 'teotucek@gmail.com', 2, 13.90, '', 'potvrđeno', NULL, '2026-03-31 17:38:38'),
(24, 10, NULL, NULL, '2026-03-31', 'Pero Perić', '0912345678', 'teotucek@gmail.com', 2, 17.50, '', 'otkazano', NULL, '2026-03-31 17:45:40'),
(26, 1, NULL, NULL, '2026-04-01', 'Pero Perić', '099 661 4188', 'tucekteops@gmail.com', 1, 13.90, '', 'otkazano', NULL, '2026-03-31 17:48:12'),
(27, 18, NULL, NULL, '2026-04-10', 'Pero Perić', '0912345678', 'tucekteops@gmail.com', 1, 29.90, '', 'otkazano', NULL, '2026-03-31 17:51:49'),
(28, 1, NULL, NULL, '2026-06-10', 'lkgf', '099 661 4188', '', 2, 13.90, '', 'otkazano', NULL, '2026-03-31 18:15:55'),
(31, 18, NULL, NULL, '2026-04-14', 'Pero Perić', '9292929', 'teotucek@gmail.com', 1, 29.90, '', 'otkazano', 'aaaaaaaaaaaaaaaaa', '2026-03-31 22:31:01'),
(32, 7, NULL, NULL, '2026-04-02', 'Pero Perić', '0912345678', 'tucekteops@gmail.com', 1, 13.90, '', 'otkazano', 'Vaša rezervacija je otkazana jer je u istom terminu organizirano tradicionalno ribičko natjecanje \"Končanički šaran 2026\" koje okuplja preko 50 natjecatelja iz cijele Hrvatske. Tijekom natjecanja, ribnjak je rezerviran isključivo za sudionike. Ispričavamo se na neugodnosti, a ukoliko želite sudjelovati kao natjecatelj ili posjetitelj, javite nam se za više informacija. Slobodni termini za individualni ribolov dostupni su već sljedeći vikend.', '2026-03-31 22:33:21'),
(33, 1, NULL, NULL, '2026-04-08', 'teo', '099 661 4188', 'teotucek@gmail.com', 1, 17.50, '', 'potvrđeno', NULL, '2026-04-05 17:45:00');

-- --------------------------------------------------------

--
-- Table structure for table `stavke_mamci`
--

CREATE TABLE `stavke_mamci` (
  `id_stavke` int(11) NOT NULL,
  `id_rezervacije` int(11) NOT NULL,
  `id_mamca` int(11) NOT NULL,
  `kolicina` int(11) NOT NULL,
  `cijena_po_komadu` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stavke_usluga`
--

CREATE TABLE `stavke_usluga` (
  `id_stavke` int(11) NOT NULL,
  `id_rezervacije` int(11) NOT NULL,
  `id_usluge` int(11) NOT NULL,
  `kolicina` int(11) NOT NULL,
  `cijena_po_komadu` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stavke_usluga`
--

INSERT INTO `stavke_usluga` (`id_stavke`, `id_rezervacije`, `id_usluge`, `kolicina`, `cijena_po_komadu`) VALUES
(1, 27, 1, 1, 3.90),
(2, 27, 2, 3, 4.90),
(3, 27, 3, 1, 2.00),
(4, 31, 1, 1, 3.90),
(5, 31, 2, 1, 4.90),
(6, 31, 3, 1, 2.00),
(7, 33, 1, 1, 3.90),
(8, 33, 2, 1, 4.90),
(9, 33, 3, 1, 2.00);

-- --------------------------------------------------------

--
-- Table structure for table `tipovi_ulaznica`
--

CREATE TABLE `tipovi_ulaznica` (
  `id_tipa` int(11) NOT NULL,
  `naziv` varchar(100) NOT NULL,
  `opis` text DEFAULT NULL,
  `cijena` decimal(10,2) NOT NULL,
  `aktivan` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tipovi_ulaznica`
--

INSERT INTO `tipovi_ulaznica` (`id_tipa`, `naziv`, `opis`, `cijena`, `aktivan`) VALUES
(1, 'R23', 'Dnevna karta R23', 13.90, 1),
(2, 'R23 PLUS', 'Dnevna karta R23 PLUS', 17.50, 1),
(3, 'R23 - Otok', 'Dnevna karta za C&R Otok', 29.90, 1),
(4, 'R23 PLUS - Otok', 'Dnevna karta PLUS za C&R Otok (uključuje hranidbeno upravljanje)', 34.90, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blokirani_datumi`
--
ALTER TABLE `blokirani_datumi`
  ADD PRIMARY KEY (`id_blokade`),
  ADD KEY `id_lokacije` (`id_lokacije`);

--
-- Indexes for table `dodatne_usluge`
--
ALTER TABLE `dodatne_usluge`
  ADD PRIMARY KEY (`id_usluge`);

--
-- Indexes for table `korisnici`
--
ALTER TABLE `korisnici`
  ADD PRIMARY KEY (`id_korisnika`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `lokacije`
--
ALTER TABLE `lokacije`
  ADD PRIMARY KEY (`id_lokacije`);

--
-- Indexes for table `mamci`
--
ALTER TABLE `mamci`
  ADD PRIMARY KEY (`id_mamca`);

--
-- Indexes for table `nocni_ribolov`
--
ALTER TABLE `nocni_ribolov`
  ADD PRIMARY KEY (`id_paketa`);

--
-- Indexes for table `povijest_statusa`
--
ALTER TABLE `povijest_statusa`
  ADD PRIMARY KEY (`id_povijest`),
  ADD KEY `id_rezervacije` (`id_rezervacije`);

--
-- Indexes for table `rezervacije`
--
ALTER TABLE `rezervacije`
  ADD PRIMARY KEY (`id_rezervacije`),
  ADD UNIQUE KEY `unique_rez` (`id_lokacije`,`datum_rezervacije`);

--
-- Indexes for table `stavke_mamci`
--
ALTER TABLE `stavke_mamci`
  ADD PRIMARY KEY (`id_stavke`),
  ADD KEY `id_rezervacije` (`id_rezervacije`),
  ADD KEY `id_mamca` (`id_mamca`);

--
-- Indexes for table `stavke_usluga`
--
ALTER TABLE `stavke_usluga`
  ADD PRIMARY KEY (`id_stavke`),
  ADD KEY `id_rezervacije` (`id_rezervacije`),
  ADD KEY `id_usluge` (`id_usluge`);

--
-- Indexes for table `tipovi_ulaznica`
--
ALTER TABLE `tipovi_ulaznica`
  ADD PRIMARY KEY (`id_tipa`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blokirani_datumi`
--
ALTER TABLE `blokirani_datumi`
  MODIFY `id_blokade` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `dodatne_usluge`
--
ALTER TABLE `dodatne_usluge`
  MODIFY `id_usluge` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `korisnici`
--
ALTER TABLE `korisnici`
  MODIFY `id_korisnika` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `lokacije`
--
ALTER TABLE `lokacije`
  MODIFY `id_lokacije` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `mamci`
--
ALTER TABLE `mamci`
  MODIFY `id_mamca` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `nocni_ribolov`
--
ALTER TABLE `nocni_ribolov`
  MODIFY `id_paketa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `povijest_statusa`
--
ALTER TABLE `povijest_statusa`
  MODIFY `id_povijest` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `rezervacije`
--
ALTER TABLE `rezervacije`
  MODIFY `id_rezervacije` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `stavke_mamci`
--
ALTER TABLE `stavke_mamci`
  MODIFY `id_stavke` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stavke_usluga`
--
ALTER TABLE `stavke_usluga`
  MODIFY `id_stavke` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tipovi_ulaznica`
--
ALTER TABLE `tipovi_ulaznica`
  MODIFY `id_tipa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blokirani_datumi`
--
ALTER TABLE `blokirani_datumi`
  ADD CONSTRAINT `blokirani_datumi_ibfk_1` FOREIGN KEY (`id_lokacije`) REFERENCES `lokacije` (`id_lokacije`);

--
-- Constraints for table `povijest_statusa`
--
ALTER TABLE `povijest_statusa`
  ADD CONSTRAINT `povijest_statusa_ibfk_1` FOREIGN KEY (`id_rezervacije`) REFERENCES `rezervacije` (`id_rezervacije`);

--
-- Constraints for table `rezervacije`
--
ALTER TABLE `rezervacije`
  ADD CONSTRAINT `rezervacije_ibfk_1` FOREIGN KEY (`id_lokacije`) REFERENCES `lokacije` (`id_lokacije`);

--
-- Constraints for table `stavke_mamci`
--
ALTER TABLE `stavke_mamci`
  ADD CONSTRAINT `stavke_mamci_ibfk_1` FOREIGN KEY (`id_rezervacije`) REFERENCES `rezervacije` (`id_rezervacije`),
  ADD CONSTRAINT `stavke_mamci_ibfk_2` FOREIGN KEY (`id_mamca`) REFERENCES `mamci` (`id_mamca`);

--
-- Constraints for table `stavke_usluga`
--
ALTER TABLE `stavke_usluga`
  ADD CONSTRAINT `stavke_usluga_ibfk_1` FOREIGN KEY (`id_rezervacije`) REFERENCES `rezervacije` (`id_rezervacije`),
  ADD CONSTRAINT `stavke_usluga_ibfk_2` FOREIGN KEY (`id_usluge`) REFERENCES `dodatne_usluge` (`id_usluge`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
