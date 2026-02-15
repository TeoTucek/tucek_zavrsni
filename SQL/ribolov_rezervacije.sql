-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 15, 2026 at 02:51 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

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
-- Stand-in structure for view `aktivne_lokacije`
-- (See below for the actual view)
--
CREATE TABLE `aktivne_lokacije` (
`id_lokacije` int(11)
,`naziv` varchar(100)
,`tip` enum('R23 pozicija','C&R Otok')
,`kapacitet` int(11)
,`opis` text
,`cijena_po_osobi` decimal(10,2)
,`ima_struju` tinyint(1)
,`ima_sjenicu` tinyint(1)
,`udaljenost_od_parkinga` int(11)
);

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
(1, NULL, '2024-12-24', '2024-12-26', 'Božićni blagdani - zatvoreno'),
(2, NULL, '2024-12-31', '2025-01-02', 'Nova godina - zatvoreno');

-- --------------------------------------------------------

--
-- Table structure for table `cjenik`
--

CREATE TABLE `cjenik` (
  `id_cijene` int(11) NOT NULL,
  `id_lokacije` int(11) DEFAULT NULL,
  `tip_cijene` enum('regularna','vikend','sezonska','promo') DEFAULT 'regularna',
  `cijena_po_osobi` decimal(10,2) NOT NULL,
  `vrijedi_od` date NOT NULL,
  `vrijedi_do` date NOT NULL,
  `aktivan` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cjenik`
--

INSERT INTO `cjenik` (`id_cijene`, `id_lokacije`, `tip_cijene`, `cijena_po_osobi`, `vrijedi_od`, `vrijedi_do`, `aktivan`) VALUES
(1, 1, 'regularna', 70.00, '2024-01-01', '2024-12-31', 1),
(2, 5, 'vikend', 90.00, '2024-01-01', '2024-12-31', 1),
(3, 13, 'sezonska', 120.00, '2024-06-01', '2024-08-31', 1),
(4, 18, 'sezonska', 200.00, '2024-07-01', '2024-08-31', 1);

-- --------------------------------------------------------

--
-- Stand-in structure for view `dostupni_mamci`
-- (See below for the actual view)
--
CREATE TABLE `dostupni_mamci` (
`id_mamca` int(11)
,`naziv` varchar(100)
,`tip` enum('kukuruz','pelet','ostalo')
,`pakiranje` varchar(9)
,`cijena_formatirano` varchar(14)
,`cijena_eur` decimal(10,2)
,`na_stanju` int(11)
,`opis` text
);

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
  `broj_telefona` varchar(20) DEFAULT NULL,
  `role` enum('admin','korisnik') DEFAULT 'korisnik',
  `registriran` timestamp NOT NULL DEFAULT current_timestamp(),
  `zadnja_aktivnost` timestamp NULL DEFAULT NULL,
  `aktivan` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `korisnici`
--

INSERT INTO `korisnici` (`id_korisnika`, `ime`, `prezime`, `email`, `lozinka_hash`, `broj_telefona`, `role`, `registriran`, `zadnja_aktivnost`, `aktivan`) VALUES
(1, 'Admin', 'Administrator', 'admin@ribnjacstvo.hr', 'admin123', '091 234 5678', 'admin', '2026-02-15 13:26:31', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `lokacije`
--

CREATE TABLE `lokacije` (
  `id_lokacije` int(11) NOT NULL,
  `naziv` varchar(100) NOT NULL,
  `tip` enum('R23 pozicija','C&R Otok') NOT NULL,
  `kapacitet` int(11) NOT NULL DEFAULT 2,
  `aktivno` tinyint(1) DEFAULT 1,
  `opis` text DEFAULT NULL,
  `cijena_po_osobi` decimal(10,2) DEFAULT NULL,
  `ima_struju` tinyint(1) DEFAULT 0,
  `ima_sjenicu` tinyint(1) DEFAULT 0,
  `ima_roštilj` tinyint(1) DEFAULT 0,
  `udaljenost_od_parkinga` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lokacije`
--

INSERT INTO `lokacije` (`id_lokacije`, `naziv`, `tip`, `kapacitet`, `aktivno`, `opis`, `cijena_po_osobi`, `ima_struju`, `ima_sjenicu`, `ima_roštilj`, `udaljenost_od_parkinga`, `created_at`, `updated_at`) VALUES
(1, 'Pozicija 1', 'R23 pozicija', 2, 1, '', 70.00, 1, 0, 0, 50, '2026-02-15 13:26:31', NULL),
(2, 'Pozicija 2', 'R23 pozicija', 2, 1, '', 70.00, 0, 0, 0, 65, '2026-02-15 13:26:31', NULL),
(3, 'Pozicija 3', 'R23 pozicija', 2, 1, '', 70.00, 1, 0, 0, 80, '2026-02-15 13:26:31', NULL),
(4, 'Pozicija 4', 'R23 pozicija', 2, 1, '', 75.00, 0, 0, 0, 95, '2026-02-15 13:26:31', NULL),
(5, 'Pozicija 5', 'R23 pozicija', 2, 1, '', 70.00, 1, 0, 0, 110, '2026-02-15 13:26:31', NULL),
(6, 'Pozicija 6', 'R23 pozicija', 2, 1, '', 80.00, 0, 0, 0, 125, '2026-02-15 13:26:31', NULL),
(7, 'Pozicija 7', 'R23 pozicija', 2, 1, '', 75.00, 1, 0, 0, 140, '2026-02-15 13:26:31', NULL),
(8, 'Pozicija 8', 'R23 pozicija', 2, 1, '', 70.00, 0, 0, 0, 155, '2026-02-15 13:26:31', NULL),
(9, 'Pozicija 9', 'R23 pozicija', 2, 1, '', 70.00, 1, 0, 0, 170, '2026-02-15 13:26:31', NULL),
(10, 'Pozicija 10', 'R23 pozicija', 2, 1, '', 65.00, 0, 0, 0, 185, '2026-02-15 13:26:31', NULL),
(11, 'Pozicija 11', 'R23 pozicija', 2, 1, '', 70.00, 1, 0, 0, 200, '2026-02-15 13:26:31', NULL),
(12, 'Pozicija 12', 'R23 pozicija', 2, 1, '', 70.00, 0, 0, 0, 215, '2026-02-15 13:26:31', NULL),
(13, 'Pozicija 13', 'R23 pozicija', 2, 1, '', 90.00, 1, 1, 0, 230, '2026-02-15 13:26:31', NULL),
(14, 'Pozicija 14', 'R23 pozicija', 2, 1, '', 75.00, 0, 0, 0, 245, '2026-02-15 13:26:31', NULL),
(15, 'Pozicija 15', 'R23 pozicija', 2, 1, '', 80.00, 1, 0, 0, 260, '2026-02-15 13:26:31', NULL),
(16, 'Pozicija 16', 'R23 pozicija', 2, 1, '', 75.00, 0, 0, 0, 275, '2026-02-15 13:26:31', NULL),
(17, 'Pozicija 17', 'R23 pozicija', 2, 1, '', 85.00, 1, 1, 0, 300, '2026-02-15 13:26:31', NULL),
(18, 'C&R Otok', 'C&R Otok', 10, 1, '', 150.00, 1, 1, 0, 50, '2026-02-15 13:26:31', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `mamci`
--

CREATE TABLE `mamci` (
  `id_mamca` int(11) NOT NULL,
  `naziv` varchar(100) NOT NULL,
  `tip` enum('kukuruz','pelet','ostalo') NOT NULL,
  `velicina_mm` int(11) DEFAULT NULL,
  `tezina_kg` decimal(5,2) NOT NULL,
  `cijena_eur` decimal(10,2) NOT NULL,
  `na_stanju` int(11) DEFAULT 0,
  `aktivan` tinyint(1) DEFAULT 1,
  `opis` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mamci`
--

INSERT INTO `mamci` (`id_mamca`, `naziv`, `tip`, `velicina_mm`, `tezina_kg`, `cijena_eur`, `na_stanju`, `aktivan`, `opis`, `created_at`) VALUES
(1, 'Kukuruz zrnati', 'kukuruz', NULL, 10.00, 6.00, 50, 1, 'Kukuruz za mamac - 10kg pakiranje. Idealno za šarane!', '2026-02-15 13:26:31'),
(2, 'Pelet 5mm', 'pelet', 5, 1.00, 2.00, 100, 1, 'Plutajući pelet 5mm - 1kg. Visokokvalitetni proteinski pelet.', '2026-02-15 13:26:31'),
(3, 'Pelet 8mm', 'pelet', 8, 1.00, 2.50, 80, 1, 'Toneći pelet 8mm - 1kg. Za veće ribe.', '2026-02-15 13:26:31'),
(4, 'Kukuruz slatki', 'kukuruz', NULL, 10.00, 7.00, 30, 1, 'Slatki kukuruz - posebna priprema.', '2026-02-15 13:26:31'),
(5, 'Kukuruz (pola)', 'kukuruz', NULL, 5.00, 3.50, 25, 1, 'Manje pakiranje kukuruza - 5kg.', '2026-02-15 13:26:31');

-- --------------------------------------------------------

--
-- Table structure for table `rezervacije`
--

CREATE TABLE `rezervacije` (
  `id_rezervacije` int(11) NOT NULL,
  `id_lokacije` int(11) NOT NULL,
  `id_korisnika` int(11) DEFAULT NULL,
  `datum_rezervacije` date NOT NULL,
  `vrijeme_dolaska` time DEFAULT '06:00:00',
  `vrijeme_odlaska` time DEFAULT '20:00:00',
  `ime_prezime` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `broj_mobitela` varchar(20) NOT NULL,
  `broj_osoba` int(11) NOT NULL,
  `broj_stapova` int(11) DEFAULT 2,
  `napomena` text DEFAULT NULL,
  `status` enum('potvrđeno','na čekanju','otkazano','odrađeno') DEFAULT 'na čekanju',
  `treba_struju` tinyint(1) DEFAULT 0,
  `treba_roštilj` tinyint(1) DEFAULT 0,
  `ukupna_cijena_lokacije` decimal(10,2) DEFAULT NULL,
  `ukupna_cijena_mamaca` decimal(10,2) DEFAULT 0.00,
  `ukupno_placeno` decimal(10,2) DEFAULT NULL,
  `placeno` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rezervacije`
--

INSERT INTO `rezervacije` (`id_rezervacije`, `id_lokacije`, `id_korisnika`, `datum_rezervacije`, `vrijeme_dolaska`, `vrijeme_odlaska`, `ime_prezime`, `email`, `broj_mobitela`, `broj_osoba`, `broj_stapova`, `napomena`, `status`, `treba_struju`, `treba_roštilj`, `ukupna_cijena_lokacije`, `ukupna_cijena_mamaca`, `ukupno_placeno`, `placeno`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, '2026-02-18', '06:00:00', '20:00:00', 'Ivan Horvat', NULL, '091 234 5678', 2, 2, NULL, 'potvrđeno', 0, 0, 140.00, 10.00, 150.00, 0, '2026-02-15 13:26:31', NULL),
(2, 5, NULL, '2026-02-20', '06:00:00', '20:00:00', 'Ana Kovačević', NULL, '098 876 5432', 1, 2, NULL, 'na čekanju', 0, 0, 70.00, 6.00, 76.00, 0, '2026-02-15 13:26:31', NULL),
(3, 13, NULL, '2026-02-22', '06:00:00', '20:00:00', 'Stjepan Jurić', NULL, '092 345 6789', 2, 2, NULL, 'potvrđeno', 0, 0, 180.00, 14.00, 194.00, 0, '2026-02-15 13:26:31', NULL),
(4, 18, NULL, '2026-02-25', '06:00:00', '20:00:00', 'Marija Majdak', NULL, '099 456 7890', 8, 2, NULL, 'potvrđeno', 0, 0, 1200.00, 30.00, 1230.00, 0, '2026-02-15 13:26:31', NULL),
(5, 2, NULL, '2026-02-17', '06:00:00', '20:00:00', 'Pero Perić', 'teotucek@gmail.com', '099 661 4188', 2, 2, 'Budemo dosli do 07:00', 'na čekanju', 0, 0, NULL, 0.00, NULL, 0, '2026-02-15 13:34:53', NULL),
(6, 1, NULL, '2026-02-15', '06:00:00', '20:00:00', 'TEST KORISNIK', NULL, '0912345678', 2, 2, NULL, 'na čekanju', 0, 0, NULL, 0.00, NULL, 0, '2026-02-15 13:37:23', NULL);

-- --------------------------------------------------------

--
-- Stand-in structure for view `rezervacije_sa_mamcima`
-- (See below for the actual view)
--
CREATE TABLE `rezervacije_sa_mamcima` (
`id_rezervacije` int(11)
,`datum_rezervacije` date
,`lokacija` varchar(100)
,`tip_lokacije` enum('R23 pozicija','C&R Otok')
,`ime_prezime` varchar(100)
,`broj_mobitela` varchar(20)
,`broj_osoba` int(11)
,`status` enum('potvrđeno','na čekanju','otkazano','odrađeno')
,`ukupna_cijena_lokacije` decimal(10,2)
,`ukupna_cijena_mamaca` decimal(10,2)
,`ukupno_placeno` decimal(10,2)
,`popis_mamaca` mediumtext
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `statistika_mamaca`
-- (See below for the actual view)
--
CREATE TABLE `statistika_mamaca` (
`naziv` varchar(100)
,`tip` enum('kukuruz','pelet','ostalo')
,`broj_prodaja` bigint(21)
,`ukupno_paketa_prodano` decimal(32,0)
,`ukupni_prihod_eur` decimal(32,2)
);

-- --------------------------------------------------------

--
-- Table structure for table `stavke_rezervacije_mamci`
--

CREATE TABLE `stavke_rezervacije_mamci` (
  `id_stavke` int(11) NOT NULL,
  `id_rezervacije` int(11) NOT NULL,
  `id_mamca` int(11) NOT NULL,
  `kolicina` int(11) NOT NULL DEFAULT 1,
  `cijena_po_jedinici` decimal(10,2) NOT NULL,
  `ukupna_cijena_stavke` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stavke_rezervacije_mamci`
--

INSERT INTO `stavke_rezervacije_mamci` (`id_stavke`, `id_rezervacije`, `id_mamca`, `kolicina`, `cijena_po_jedinici`, `ukupna_cijena_stavke`) VALUES
(1, 1, 2, 2, 2.00, 4.00),
(2, 1, 1, 1, 6.00, 6.00),
(3, 2, 1, 1, 6.00, 6.00),
(4, 3, 1, 2, 6.00, 12.00),
(5, 3, 2, 1, 2.00, 2.00),
(6, 4, 1, 5, 6.00, 30.00);

-- --------------------------------------------------------

--
-- Structure for view `aktivne_lokacije`
--
DROP TABLE IF EXISTS `aktivne_lokacije`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `aktivne_lokacije`  AS SELECT `lokacije`.`id_lokacije` AS `id_lokacije`, `lokacije`.`naziv` AS `naziv`, `lokacije`.`tip` AS `tip`, `lokacije`.`kapacitet` AS `kapacitet`, `lokacije`.`opis` AS `opis`, `lokacije`.`cijena_po_osobi` AS `cijena_po_osobi`, `lokacije`.`ima_struju` AS `ima_struju`, `lokacije`.`ima_sjenicu` AS `ima_sjenicu`, `lokacije`.`udaljenost_od_parkinga` AS `udaljenost_od_parkinga` FROM `lokacije` WHERE `lokacije`.`aktivno` = 1 ORDER BY `lokacije`.`tip` ASC, `lokacije`.`naziv` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `dostupni_mamci`
--
DROP TABLE IF EXISTS `dostupni_mamci`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `dostupni_mamci`  AS SELECT `mamci`.`id_mamca` AS `id_mamca`, `mamci`.`naziv` AS `naziv`, `mamci`.`tip` AS `tip`, concat(`mamci`.`tezina_kg`,'kg') AS `pakiranje`, concat(`mamci`.`cijena_eur`,' €') AS `cijena_formatirano`, `mamci`.`cijena_eur` AS `cijena_eur`, `mamci`.`na_stanju` AS `na_stanju`, `mamci`.`opis` AS `opis` FROM `mamci` WHERE `mamci`.`aktivan` = 1 ORDER BY `mamci`.`tip` ASC, `mamci`.`naziv` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `rezervacije_sa_mamcima`
--
DROP TABLE IF EXISTS `rezervacije_sa_mamcima`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `rezervacije_sa_mamcima`  AS SELECT `r`.`id_rezervacije` AS `id_rezervacije`, `r`.`datum_rezervacije` AS `datum_rezervacije`, `l`.`naziv` AS `lokacija`, `l`.`tip` AS `tip_lokacije`, `r`.`ime_prezime` AS `ime_prezime`, `r`.`broj_mobitela` AS `broj_mobitela`, `r`.`broj_osoba` AS `broj_osoba`, `r`.`status` AS `status`, `r`.`ukupna_cijena_lokacije` AS `ukupna_cijena_lokacije`, `r`.`ukupna_cijena_mamaca` AS `ukupna_cijena_mamaca`, `r`.`ukupno_placeno` AS `ukupno_placeno`, (select group_concat(concat(`s`.`kolicina`,'× ',`m`.`naziv`,' (',`m`.`tezina_kg`,'kg)') separator ', ') from (`stavke_rezervacije_mamci` `s` join `mamci` `m` on(`s`.`id_mamca` = `m`.`id_mamca`)) where `s`.`id_rezervacije` = `r`.`id_rezervacije`) AS `popis_mamaca` FROM (`rezervacije` `r` join `lokacije` `l` on(`r`.`id_lokacije` = `l`.`id_lokacije`)) ORDER BY `r`.`datum_rezervacije` DESC ;

-- --------------------------------------------------------

--
-- Structure for view `statistika_mamaca`
--
DROP TABLE IF EXISTS `statistika_mamaca`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `statistika_mamaca`  AS SELECT `m`.`naziv` AS `naziv`, `m`.`tip` AS `tip`, count(`s`.`id_stavke`) AS `broj_prodaja`, sum(`s`.`kolicina`) AS `ukupno_paketa_prodano`, sum(`s`.`ukupna_cijena_stavke`) AS `ukupni_prihod_eur` FROM (`mamci` `m` left join `stavke_rezervacije_mamci` `s` on(`m`.`id_mamca` = `s`.`id_mamca`)) GROUP BY `m`.`id_mamca` ORDER BY sum(`s`.`ukupna_cijena_stavke`) DESC ;

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
-- Indexes for table `cjenik`
--
ALTER TABLE `cjenik`
  ADD PRIMARY KEY (`id_cijene`),
  ADD KEY `id_lokacije` (`id_lokacije`);

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
  ADD PRIMARY KEY (`id_lokacije`),
  ADD KEY `idx_tip_lokacije` (`tip`);

--
-- Indexes for table `mamci`
--
ALTER TABLE `mamci`
  ADD PRIMARY KEY (`id_mamca`),
  ADD KEY `idx_mamci_tip` (`tip`);

--
-- Indexes for table `rezervacije`
--
ALTER TABLE `rezervacije`
  ADD PRIMARY KEY (`id_rezervacije`),
  ADD UNIQUE KEY `unique_rezervacija` (`id_lokacije`,`datum_rezervacije`),
  ADD KEY `id_korisnika` (`id_korisnika`),
  ADD KEY `idx_datum_rezervacije` (`datum_rezervacije`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `stavke_rezervacije_mamci`
--
ALTER TABLE `stavke_rezervacije_mamci`
  ADD PRIMARY KEY (`id_stavke`),
  ADD KEY `id_mamca` (`id_mamca`),
  ADD KEY `id_rezervacije` (`id_rezervacije`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blokirani_datumi`
--
ALTER TABLE `blokirani_datumi`
  MODIFY `id_blokade` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cjenik`
--
ALTER TABLE `cjenik`
  MODIFY `id_cijene` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `korisnici`
--
ALTER TABLE `korisnici`
  MODIFY `id_korisnika` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lokacije`
--
ALTER TABLE `lokacije`
  MODIFY `id_lokacije` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `mamci`
--
ALTER TABLE `mamci`
  MODIFY `id_mamca` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `rezervacije`
--
ALTER TABLE `rezervacije`
  MODIFY `id_rezervacije` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `stavke_rezervacije_mamci`
--
ALTER TABLE `stavke_rezervacije_mamci`
  MODIFY `id_stavke` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blokirani_datumi`
--
ALTER TABLE `blokirani_datumi`
  ADD CONSTRAINT `blokirani_datumi_ibfk_1` FOREIGN KEY (`id_lokacije`) REFERENCES `lokacije` (`id_lokacije`) ON DELETE CASCADE;

--
-- Constraints for table `cjenik`
--
ALTER TABLE `cjenik`
  ADD CONSTRAINT `cjenik_ibfk_1` FOREIGN KEY (`id_lokacije`) REFERENCES `lokacije` (`id_lokacije`) ON DELETE CASCADE;

--
-- Constraints for table `rezervacije`
--
ALTER TABLE `rezervacije`
  ADD CONSTRAINT `rezervacije_ibfk_1` FOREIGN KEY (`id_lokacije`) REFERENCES `lokacije` (`id_lokacije`) ON DELETE CASCADE,
  ADD CONSTRAINT `rezervacije_ibfk_2` FOREIGN KEY (`id_korisnika`) REFERENCES `korisnici` (`id_korisnika`) ON DELETE SET NULL;

--
-- Constraints for table `stavke_rezervacije_mamci`
--
ALTER TABLE `stavke_rezervacije_mamci`
  ADD CONSTRAINT `stavke_rezervacije_mamci_ibfk_1` FOREIGN KEY (`id_rezervacije`) REFERENCES `rezervacije` (`id_rezervacije`) ON DELETE CASCADE,
  ADD CONSTRAINT `stavke_rezervacije_mamci_ibfk_2` FOREIGN KEY (`id_mamca`) REFERENCES `mamci` (`id_mamca`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
