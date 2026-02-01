-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 01, 2026 at 05:27 PM
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
-- Database: `ribolovni_koncanica`
--

-- --------------------------------------------------------

--
-- Table structure for table `cijene_karata`
--

CREATE TABLE `cijene_karata` (
  `id` int(11) NOT NULL,
  `vrsta_karte_id` int(11) NOT NULL,
  `trajanje_dana` int(11) NOT NULL DEFAULT 1,
  `cijena_eura` decimal(10,2) NOT NULL,
  `radni_dan` tinyint(1) DEFAULT 1 COMMENT '1=radni dan, 0=vikend',
  `vrijeme_od` time NOT NULL,
  `vrijeme_do` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_croatian_ci;

--
-- Dumping data for table `cijene_karata`
--

INSERT INTO `cijene_karata` (`id`, `vrsta_karte_id`, `trajanje_dana`, `cijena_eura`, `radni_dan`, `vrijeme_od`, `vrijeme_do`) VALUES
(1, 1, 1, 11.00, 1, '07:00:00', '19:00:00'),
(2, 2, 1, 15.00, 1, '07:00:00', '19:00:00'),
(3, 3, 1, 15.00, 1, '07:00:00', '19:00:00'),
(4, 1, 1, 11.00, 0, '06:00:00', '20:00:00'),
(5, 2, 1, 15.00, 0, '06:00:00', '20:00:00'),
(6, 3, 1, 15.00, 0, '06:00:00', '20:00:00'),
(7, 4, 1, 25.00, 1, '07:00:00', '19:00:00'),
(8, 5, 1, 29.00, 1, '07:00:00', '19:00:00'),
(9, 4, 1, 25.00, 0, '06:00:00', '20:00:00'),
(10, 5, 1, 29.00, 0, '06:00:00', '20:00:00'),
(11, 6, 2, 30.00, 0, '06:00:00', '20:00:00'),
(12, 7, 3, 40.00, 0, '06:00:00', '20:00:00'),
(13, 8, 2, 55.00, 0, '06:00:00', '20:00:00'),
(14, 9, 3, 80.00, 0, '06:00:00', '20:00:00'),
(15, 10, 4, 110.00, 0, '06:00:00', '20:00:00'),
(16, 11, 7, 170.00, 0, '06:00:00', '20:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `djelatnici`
--

CREATE TABLE `djelatnici` (
  `id` int(11) NOT NULL,
  `ime` varchar(50) NOT NULL,
  `prezime` varchar(50) NOT NULL,
  `korisnicko_ime` varchar(50) NOT NULL,
  `lozinka` varchar(255) NOT NULL,
  `uloga` enum('admin','djelatnik') DEFAULT 'djelatnik',
  `aktivan` tinyint(1) DEFAULT 1,
  `kreirano` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `djelatnici`
--

INSERT INTO `djelatnici` (`id`, `ime`, `prezime`, `korisnicko_ime`, `lozinka`, `uloga`, `aktivan`, `kreirano`) VALUES
(1, 'Admin', 'Administrator', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, '2026-01-29 17:08:23'),
(2, 'Ivan', 'Horvat', 'ihorvat', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'djelatnik', 1, '2026-01-29 17:08:23'),
(3, 'Ana', 'Kovač', 'akovac', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'djelatnik', 1, '2026-01-29 17:08:23');

-- --------------------------------------------------------

--
-- Table structure for table `dodatne_usluge`
--

CREATE TABLE `dodatne_usluge` (
  `id` int(11) NOT NULL,
  `naziv` varchar(100) NOT NULL,
  `opis` text DEFAULT NULL,
  `cijena_eura` decimal(10,2) NOT NULL,
  `ribnjak_id` int(11) DEFAULT NULL,
  `za_otok` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_croatian_ci;

--
-- Dumping data for table `dodatne_usluge`
--

INSERT INTO `dodatne_usluge` (`id`, `naziv`, `opis`, `cijena_eura`, `ribnjak_id`, `za_otok`) VALUES
(1, 'Najam prostirke (za Crnaju)', 'Prostirka za ribolov na Crnaji', 2.65, 2, 0),
(2, 'Najam šaranske kadice', 'Kadica za šarana', 3.50, 1, 0),
(3, 'Najam šaranske kadice (Otok)', 'Kadica za šarana na otoku', 3.50, 1, 1),
(4, 'Hranjenje 1 - 10kg kukuruza', 'Kuhani kukuruz (naručiti 2 dana unaprijed)', 6.00, 1, 0),
(5, 'Hranjenje 2 - 1kg peleta', 'Peleti 5mm (38% ribljeg brašna)', 2.00, 1, 0),
(6, 'Hranjenje 1 - 10kg kukuruza (Otok)', 'Kuhani kukuruz za otok', 6.00, 1, 1),
(7, 'Hranjenje 2 - 1kg peleta (Otok)', 'Peleti za otok', 2.00, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `karta_dodatna_usluga`
--

CREATE TABLE `karta_dodatna_usluga` (
  `id` int(11) NOT NULL,
  `karta_id` int(11) NOT NULL,
  `dodatna_usluga_id` int(11) NOT NULL,
  `kolicina` int(11) DEFAULT 1,
  `cijena` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_croatian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `karte`
--

CREATE TABLE `karte` (
  `id` int(11) NOT NULL,
  `broj_karte` varchar(50) NOT NULL,
  `vrsta_karte_id` int(11) NOT NULL,
  `datum_izdavanja` date NOT NULL,
  `datum_vrijedi_od` datetime NOT NULL,
  `datum_vrijedi_do` datetime NOT NULL,
  `ime_kupca` varchar(100) NOT NULL,
  `prezime_kupca` varchar(100) NOT NULL,
  `telefon` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `ukupna_cijena` decimal(10,2) NOT NULL,
  `placeno` tinyint(1) DEFAULT 0,
  `rezervacija_otoka` tinyint(1) DEFAULT 0,
  `broj_osoba` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_croatian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pravila`
--

CREATE TABLE `pravila` (
  `id` int(11) NOT NULL,
  `ribnjak_id` int(11) DEFAULT NULL,
  `za_otok` tinyint(1) DEFAULT 0,
  `redni_broj` int(11) NOT NULL,
  `tekst_pravila` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_croatian_ci;

--
-- Dumping data for table `pravila`
--

INSERT INTO `pravila` (`id`, `ribnjak_id`, `za_otok`, `redni_broj`, `tekst_pravila`) VALUES
(1, 1, 0, 1, 'Dnevna ulaznica vrijedi za ribolov sa najviše tri štapa sa po jednom udicom.'),
(2, 1, 0, 2, 'Ulovljena riba može se kupiti po važećem cjeniku ribe objavljenom na ribočuvarskoj kućici ili neoštećena vratiti u ribnjak.'),
(3, 1, 0, 3, 'Sva riba lakša od 1kg i teža od 3.5 kg mora se neoštećena vratiti u ribnjak, ne smije se stavljati u čuvaricu i ne može se kupiti. U protivnom plaća se kazna u iznosu od 1000 € plus težina ribe po važećem cjeniku.'),
(4, 1, 0, 4, 'Zabranjeno je držanje ribe u čuvarici i onda vraćanje iste natrag u ribnjak.'),
(5, 1, 0, 5, 'Obavezna je upotreba kadice (prostirka NIJE DOZVOLJENA) za ribu te antiseptika za dezinfekciju rane od udice. Ako ribič nema vlastitu kadicu može ju unajmiti na ulasku. Ukoliko se prilikom obilaska ribočuvara utvrdi da ribič nema ili ne koristi kadicu biti će udaljen sa ribnjaka.'),
(6, 1, 0, 6, 'Obavezna dezinfekcija pribora (podmetač, kadica, polagaljka) prilikom ulaska na ribnjak.'),
(7, 1, 0, 7, 'Ribič koji oštećenu ribu vrati u ribnjak kažnjava se sa 1000 € i biti će prekršajno prijavljen.'),
(8, 1, 0, 8, 'Zabranjen je ribolov na dva različita mjesta istovremeno.'),
(9, 1, 0, 9, 'Zabranjeno zauzimanje pozicije za ribolov u svrhu čuvanja mjesta i ostavljanje pribora bez nadzora.'),
(10, 1, 0, 10, 'U ribolovu je dozvoljena upotreba svih vrsta mamaca i hrane (boile, pelete i sve sjemenke prethodno prokuhane te je dozvoljena sva brašnasta hrana) primjerenih rekreacijskom ribolovu.'),
(11, 1, 0, 11, 'Upotreba svih živih mamaca je zabranjena.'),
(12, 1, 0, 12, 'Maloljetne osobe bez nadzora punoljetnih osoba same su odgovorne za sebe te im je dozvoljen ribolov samo sa strane ribnjaka na kojem se nalazi ribočuvarska kućica.'),
(13, 1, 0, 13, 'Svi ribiči obavezni su svoje torbe i pribor na zahtjev ribočuvara ustupiti na pregled prilikom ulaska i izlaska sa ribnjaka. U slučaju nepoštivanja ovog pravila i pronalaska skrivenih riba slijedi kazna od 1000 €, privremeno oduzimanje pribora i ulova te prekršajna prijava uz trajnu zabranu dolaska na ribolovne vode Ribnjačarstva Končanica.'),
(14, 1, 0, 14, 'Ribolov mrežama, vršama, parangalima, strujom te ostalim ne sportskim metodama nije dozvoljen.'),
(15, 1, 0, 15, 'U ribolovu je dozvoljeno korištenje brodića na daljinsko upravljanje ukoliko ni na koji način ne ometaju ostale ribiče.'),
(16, 1, 0, 16, 'Voditelj sportskog ribnjaka, čuvari i službene osobe Ribnjačarstva Končanica d.d. imaju pravo kontrole načina ribolova, prtljage i vozila u krugu ribnjaka.'),
(17, 1, 0, 17, 'Dovođenje kućnih ljubimaca na sportski ribnjak dozvoljeno je samo na uzici, ukoliko oni ne ometaju red i mir na ribnjaku.'),
(18, 1, 0, 18, 'Kupnjom ulaznice za Sportski ribnjak Končanica prihvaćate sva pravila, uvjete i sankcije Ribnjačarstva Končanica d.d. navedenih u „Pravilniku na R23 - sportskom ribnjaku Končanica\" te ga se obavezujete pridržavati.'),
(19, 1, 0, 19, 'Kupljenju kartu dužni ste držati na vidljivom mjestu te ju pokazati na zahtjev, voditelja Sportskog ribnjaka Končanica, čuvara, djelatnika Ribnjačarstva Končanica d.d. ili druge ovlaštene osobe.'),
(20, 1, 1, 1, 'Radno vrijeme otoka: Otok je otvoren za ribolov 24 sata dnevno, 7 dana u tjednu. Uz redoviti nadzor ribočuvara i čuvara tokom dana i noći.'),
(21, 1, 1, 2, 'Rezervacija: Za posjetu otoku obavezna je prethodna rezervacija. Rezervaciju možete izvršiti putem telefona na broj: +385911399709.'),
(22, 1, 1, 3, 'Sigurnost na vodi: Pri vožnji čamcem obavezno je korištenje prsluka za spašavanje koji ćete dobiti na korištenje prilikom kupnje ulaznice.'),
(23, 1, 1, 4, 'Kretanje čamcem: Dozvoljeno je hranjenje ribe čamcem. Kretanje čamcem unutar svoje pozicije dopušteno je, no potrebno je pridržavati se granica svog sektora.'),
(24, 1, 1, 5, 'Ograničenja kretanja čamcem: Zabranjeno je kretanje čamcem tijekom noći, osim u hitnim situacijama (npr. u slučaju nužde ili opasnosti). Noćni ribolov je dopušten, ali uz pridržavanje svih sigurnosnih mjera.'),
(25, 1, 1, 6, 'Odgovornost: Ribolov na otoku odvija se isključivo na vlastitu odgovornost. Organizator ne preuzima odgovornost za bilo kakve nezgode ili štete koje mogu nastati.'),
(26, 1, 1, 7, 'Kapacitet otoka: Otok ima tri pozicije za ribolov. Maksimalni broj štapova po poziciji je 3, što znači da se ukupno može koristiti najviše 9 štapova na cijelom otoku u jednom trenutku za obavljanje ribolova. Rezervacijom najmanje 2 pozicije cijeli otok se smatra rezerviranim (na taj način osigurava se da samo vi na otoku bez nepoznatih dodatnih gostiju na drugim pozicijama).'),
(27, 1, 1, 8, 'Ribolov: Za ribolov na otoku vrijedi isključivo C&R (Uhvati i pusti) način ribolova. Ukoliko ribolovac želi ribu, može ju kupiti po povlaštenoj cijeni u ribočuvarskoj kućici nakon što završi ribolov.'),
(28, 1, 1, 9, 'Sigurnosna pravila boravka: Zbog sigurnosnih razloga, na otoku ne može boraviti samo jedna osoba. Uvijek je potrebno imati najmanje dvije ljudi na otoku u isto vrijeme.'),
(29, 1, 1, 10, 'Roštiljanje: Na otoku je dozvoljeno roštiljanje, ali isključivo na za to predviđenim i označenim mjestima. Molimo da se pridržavate svih sigurnosnih uputa prilikom korištenja roštilja.'),
(30, 1, 1, 11, 'Prijevoz opreme: Ribočuvar prevozi opremu na otok i s otoka velikim čamcem, osiguravajući siguran transport vaše opreme. Vrijeme polaska na otok dogovarate sa ribočuvarom.'),
(31, 1, 1, 12, 'Čamac za ribiće: Čamac koji ostaje ribićima na otoku je gumenjak marke JRC dužine 330 cm, opremljen veslima. Udaljenost između mola na otoku i na obali iznosi 170 metara.'),
(32, 1, 1, 13, 'Opća pravila: Uz posebna pravila za ribolov na R23 otoku, za ostatak pravila vrijede opća pravila iz Pravilnika o ribolovu na R23 Sportskom ribnjaku Končanica. Molimo vas da se upoznate i pridržavate tih pravila tijekom boravka i ribolova.');

-- --------------------------------------------------------

--
-- Table structure for table `ribnjaci`
--

CREATE TABLE `ribnjaci` (
  `id` int(11) NOT NULL,
  `naziv` varchar(50) NOT NULL,
  `lokacija` varchar(100) NOT NULL DEFAULT 'Končanica',
  `vrsta_ribnjaka` enum('sportski','grabezljivi') NOT NULL,
  `opis` text DEFAULT NULL,
  `povrsina` float DEFAULT NULL,
  `broj_pozicija` int(11) DEFAULT NULL,
  `ima_otok` tinyint(1) DEFAULT 0,
  `max_stapova_po_poziciji` int(11) DEFAULT 3
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_croatian_ci;

--
-- Dumping data for table `ribnjaci`
--

INSERT INTO `ribnjaci` (`id`, `naziv`, `lokacija`, `vrsta_ribnjaka`, `opis`, `povrsina`, `broj_pozicija`, `ima_otok`, `max_stapova_po_poziciji`) VALUES
(1, 'R23 - Sportski ribnjak', 'Končanica', 'sportski', 'Ribnjak sa šaranom i amurom do 25kg, prosječna težina 3.5kg', 3.5, 25, 1, 3),
(2, 'Ribnjak Crnaja C&R', 'Končanica', 'grabezljivi', 'Isključivo catch & release ribnjak sa grabežljivom ribom', 2.8, 15, 0, 3);

-- --------------------------------------------------------

--
-- Table structure for table `ribnjak_vrsta_ribe`
--

CREATE TABLE `ribnjak_vrsta_ribe` (
  `id` int(11) NOT NULL,
  `ribnjak_id` int(11) NOT NULL,
  `vrsta_ribe_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_croatian_ci;

--
-- Dumping data for table `ribnjak_vrsta_ribe`
--

INSERT INTO `ribnjak_vrsta_ribe` (`id`, `ribnjak_id`, `vrsta_ribe_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 3),
(4, 2, 4),
(5, 2, 5);

-- --------------------------------------------------------

--
-- Table structure for table `ulovi`
--

CREATE TABLE `ulovi` (
  `id` int(11) NOT NULL,
  `karta_id` int(11) NOT NULL,
  `vrsta_ribe_id` int(11) NOT NULL,
  `tezina` float DEFAULT NULL,
  `datum_vrijeme` datetime NOT NULL,
  `vraceno_u_ribnjak` tinyint(1) DEFAULT 0,
  `kupljeno` tinyint(1) DEFAULT 0,
  `cijena_kupljenog` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_croatian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vrste_karata`
--

CREATE TABLE `vrste_karata` (
  `id` int(11) NOT NULL,
  `naziv` varchar(100) NOT NULL,
  `opis` text DEFAULT NULL,
  `ribnjak_id` int(11) DEFAULT NULL,
  `za_otok` tinyint(1) DEFAULT 0,
  `ukljucuje_hranu` tinyint(1) DEFAULT 0,
  `noćni` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_croatian_ci;

--
-- Dumping data for table `vrste_karata`
--

INSERT INTO `vrste_karata` (`id`, `naziv`, `opis`, `ribnjak_id`, `za_otok`, `ukljucuje_hranu`, `noćni`) VALUES
(1, 'Dnevna karta R23', 'Osnovna dnevna karta za R23', 1, 0, 0, 0),
(2, 'Dnevna karta R23 PLUS', 'Uključuje 4kg kuhanog kukuruzu i 1kg peleta', 1, 0, 1, 0),
(3, 'Dnevna karta Crnaja C&R', 'Catch & Release za Ribnjak Crnaja', 2, 0, 0, 0),
(4, 'Dnevna karta R23 Otok', 'Dnevna karta za otok na R23', 1, 1, 0, 0),
(5, 'Dnevna karta R23 PLUS Otok', 'Dnevna karta za otok uključuje hranu', 1, 1, 1, 0),
(6, 'Noćni ribolov (2 dana)', 'Petak-subota ili subota-nedjelja', 1, 0, 0, 1),
(7, 'Noćni ribolov (3 dana)', 'Petak-nedjelja', 1, 0, 0, 1),
(8, 'Noćni ribolov Otok (2 dana)', '2 dana na otoku', 1, 1, 0, 1),
(9, 'Noćni ribolov Otok (3 dana)', '3 dana na otoku', 1, 1, 0, 1),
(10, 'Noćni ribolov Otok (4 dana)', '4 dana na otoku', 1, 1, 0, 1),
(11, 'Noćni ribolov Otok (7 dana)', '7 dana na otoku', 1, 1, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `vrste_ribe`
--

CREATE TABLE `vrste_ribe` (
  `id` int(11) NOT NULL,
  `naziv` varchar(50) NOT NULL,
  `latinski_naziv` varchar(100) DEFAULT NULL,
  `min_tezina_za_odnos` float DEFAULT NULL COMMENT 'Minimalna težina za uzimanje ribe (kg)',
  `max_tezina_za_odnos` float DEFAULT NULL COMMENT 'Maksimalna težina za uzimanje ribe (kg)',
  `obavezno_cr` tinyint(1) DEFAULT 0 COMMENT 'Da li je obavezno Catch & Release'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_croatian_ci;

--
-- Dumping data for table `vrste_ribe`
--

INSERT INTO `vrste_ribe` (`id`, `naziv`, `latinski_naziv`, `min_tezina_za_odnos`, `max_tezina_za_odnos`, `obavezno_cr`) VALUES
(1, 'Šaran', 'Cyprinus carpio', 1, 3.5, 0),
(2, 'Amur', 'Ctenopharyngodon idella', 1, 25, 0),
(3, 'Štuka', 'Esox lucius', NULL, NULL, 1),
(4, 'Smud', 'Sander lucioperca', NULL, NULL, 1),
(5, 'Som', 'Silurus glanis', NULL, NULL, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cijene_karata`
--
ALTER TABLE `cijene_karata`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vrsta_karte_id` (`vrsta_karte_id`);

--
-- Indexes for table `djelatnici`
--
ALTER TABLE `djelatnici`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `korisnicko_ime` (`korisnicko_ime`);

--
-- Indexes for table `dodatne_usluge`
--
ALTER TABLE `dodatne_usluge`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ribnjak_id` (`ribnjak_id`);

--
-- Indexes for table `karta_dodatna_usluga`
--
ALTER TABLE `karta_dodatna_usluga`
  ADD PRIMARY KEY (`id`),
  ADD KEY `karta_id` (`karta_id`),
  ADD KEY `dodatna_usluga_id` (`dodatna_usluga_id`);

--
-- Indexes for table `karte`
--
ALTER TABLE `karte`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `broj_karte` (`broj_karte`),
  ADD KEY `vrsta_karte_id` (`vrsta_karte_id`);

--
-- Indexes for table `pravila`
--
ALTER TABLE `pravila`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ribnjak_id` (`ribnjak_id`);

--
-- Indexes for table `ribnjaci`
--
ALTER TABLE `ribnjaci`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ribnjak_vrsta_ribe`
--
ALTER TABLE `ribnjak_vrsta_ribe`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ribnjak_id` (`ribnjak_id`),
  ADD KEY `vrsta_ribe_id` (`vrsta_ribe_id`);

--
-- Indexes for table `ulovi`
--
ALTER TABLE `ulovi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `karta_id` (`karta_id`),
  ADD KEY `vrsta_ribe_id` (`vrsta_ribe_id`);

--
-- Indexes for table `vrste_karata`
--
ALTER TABLE `vrste_karata`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ribnjak_id` (`ribnjak_id`);

--
-- Indexes for table `vrste_ribe`
--
ALTER TABLE `vrste_ribe`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cijene_karata`
--
ALTER TABLE `cijene_karata`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `djelatnici`
--
ALTER TABLE `djelatnici`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `dodatne_usluge`
--
ALTER TABLE `dodatne_usluge`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `karta_dodatna_usluga`
--
ALTER TABLE `karta_dodatna_usluga`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `karte`
--
ALTER TABLE `karte`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pravila`
--
ALTER TABLE `pravila`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `ribnjaci`
--
ALTER TABLE `ribnjaci`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ribnjak_vrsta_ribe`
--
ALTER TABLE `ribnjak_vrsta_ribe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ulovi`
--
ALTER TABLE `ulovi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vrste_karata`
--
ALTER TABLE `vrste_karata`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `vrste_ribe`
--
ALTER TABLE `vrste_ribe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cijene_karata`
--
ALTER TABLE `cijene_karata`
  ADD CONSTRAINT `cijene_karata_ibfk_1` FOREIGN KEY (`vrsta_karte_id`) REFERENCES `vrste_karata` (`id`);

--
-- Constraints for table `dodatne_usluge`
--
ALTER TABLE `dodatne_usluge`
  ADD CONSTRAINT `dodatne_usluge_ibfk_1` FOREIGN KEY (`ribnjak_id`) REFERENCES `ribnjaci` (`id`);

--
-- Constraints for table `karta_dodatna_usluga`
--
ALTER TABLE `karta_dodatna_usluga`
  ADD CONSTRAINT `karta_dodatna_usluga_ibfk_1` FOREIGN KEY (`karta_id`) REFERENCES `karte` (`id`),
  ADD CONSTRAINT `karta_dodatna_usluga_ibfk_2` FOREIGN KEY (`dodatna_usluga_id`) REFERENCES `dodatne_usluge` (`id`);

--
-- Constraints for table `karte`
--
ALTER TABLE `karte`
  ADD CONSTRAINT `karte_ibfk_1` FOREIGN KEY (`vrsta_karte_id`) REFERENCES `vrste_karata` (`id`);

--
-- Constraints for table `pravila`
--
ALTER TABLE `pravila`
  ADD CONSTRAINT `pravila_ibfk_1` FOREIGN KEY (`ribnjak_id`) REFERENCES `ribnjaci` (`id`);

--
-- Constraints for table `ribnjak_vrsta_ribe`
--
ALTER TABLE `ribnjak_vrsta_ribe`
  ADD CONSTRAINT `ribnjak_vrsta_ribe_ibfk_1` FOREIGN KEY (`ribnjak_id`) REFERENCES `ribnjaci` (`id`),
  ADD CONSTRAINT `ribnjak_vrsta_ribe_ibfk_2` FOREIGN KEY (`vrsta_ribe_id`) REFERENCES `vrste_ribe` (`id`);

--
-- Constraints for table `ulovi`
--
ALTER TABLE `ulovi`
  ADD CONSTRAINT `ulovi_ibfk_1` FOREIGN KEY (`karta_id`) REFERENCES `karte` (`id`),
  ADD CONSTRAINT `ulovi_ibfk_2` FOREIGN KEY (`vrsta_ribe_id`) REFERENCES `vrste_ribe` (`id`);

--
-- Constraints for table `vrste_karata`
--
ALTER TABLE `vrste_karata`
  ADD CONSTRAINT `vrste_karata_ibfk_1` FOREIGN KEY (`ribnjak_id`) REFERENCES `ribnjaci` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
