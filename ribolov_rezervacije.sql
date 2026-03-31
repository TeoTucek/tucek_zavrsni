-- =====================================================
-- RIBNJAČARSTVO KONČANICA - BAZA PODATAKA
-- =====================================================

-- -----------------------------------------------------
-- 1. OBRISI STARU BAZU AKO POSTOJI
-- -----------------------------------------------------
DROP DATABASE IF EXISTS ribolov_rezervacije;

-- -----------------------------------------------------
-- 2. KREIRAJ NOVU BAZU
-- -----------------------------------------------------
CREATE DATABASE ribolov_rezervacije 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- 3. ODABERI BAZU
-- -----------------------------------------------------
USE ribolov_rezervacije;

-- =====================================================
-- KREIRANJE TABLICA
-- =====================================================

-- -----------------------------------------------------
-- 4. TABLICA: lokacije
-- -----------------------------------------------------
CREATE TABLE lokacije (
    id_lokacije INT AUTO_INCREMENT PRIMARY KEY,
    naziv VARCHAR(100) NOT NULL,
    tip ENUM('R23 pozicija', 'C&R Otok') NOT NULL,
    kapacitet INT NOT NULL DEFAULT 2,
    opis TEXT,
    cijena_osnovna DECIMAL(10,2) NOT NULL,
    cijena_vikend DECIMAL(10,2),
    cijena_sezona DECIMAL(10,2),
    ima_struju BOOLEAN DEFAULT FALSE,
    ima_sjenicu BOOLEAN DEFAULT FALSE,
    aktivno BOOLEAN DEFAULT TRUE
);

-- -----------------------------------------------------
-- 5. TABLICA: mamci
-- -----------------------------------------------------
CREATE TABLE mamci (
    id_mamca INT AUTO_INCREMENT PRIMARY KEY,
    naziv VARCHAR(100) NOT NULL,
    tezina_kg DECIMAL(5,2) NOT NULL,
    cijena_eur DECIMAL(10,2) NOT NULL,
    opis TEXT,
    na_stanju INT DEFAULT 100
);

-- -----------------------------------------------------
-- 6. TABLICA: korisnici (za admina)
-- -----------------------------------------------------
CREATE TABLE korisnici (
    id_korisnika INT AUTO_INCREMENT PRIMARY KEY,
    ime VARCHAR(50) NOT NULL,
    prezime VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    lozinka_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'korisnik') DEFAULT 'korisnik',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- -----------------------------------------------------
-- 7. TABLICA: rezervacije (GLAVNA!)
-- -----------------------------------------------------
CREATE TABLE rezervacije (
    id_rezervacije INT AUTO_INCREMENT PRIMARY KEY,
    id_lokacije INT NOT NULL,
    datum_rezervacije DATE NOT NULL,
    ime_prezime VARCHAR(100) NOT NULL,
    broj_mobitela VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    broj_osoba INT NOT NULL,
    cijena_po_osobi DECIMAL(10,2) NOT NULL,
    napomena TEXT,
    status ENUM('na čekanju', 'potvrđeno', 'otkazano') DEFAULT 'na čekanju',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_lokacije) REFERENCES lokacije(id_lokacije),
    UNIQUE KEY unique_rez (id_lokacije, datum_rezervacije)
);

-- -----------------------------------------------------
-- 8. TABLICA: stavke_mamci (poveznica rezervacija-mamci)
-- -----------------------------------------------------
CREATE TABLE stavke_mamci (
    id_stavke INT AUTO_INCREMENT PRIMARY KEY,
    id_rezervacije INT NOT NULL,
    id_mamca INT NOT NULL,
    kolicina INT NOT NULL,
    cijena_po_komadu DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_rezervacije) REFERENCES rezervacije(id_rezervacije),
    FOREIGN KEY (id_mamca) REFERENCES mamci(id_mamca)
);

-- -----------------------------------------------------
-- 9. TABLICA: blokirani_datumi (kad ribnjak ne radi)
-- -----------------------------------------------------
CREATE TABLE blokirani_datumi (
    id_blokade INT AUTO_INCREMENT PRIMARY KEY,
    id_lokacije INT NULL,
    datum_od DATE NOT NULL,
    datum_do DATE NOT NULL,
    razlog VARCHAR(255),
    FOREIGN KEY (id_lokacije) REFERENCES lokacije(id_lokacije)
);

-- -----------------------------------------------------
-- 10. TABLICA: povijest_statusa (tko je što mijenjao)
-- -----------------------------------------------------
CREATE TABLE povijest_statusa (
    id_povijest INT AUTO_INCREMENT PRIMARY KEY,
    id_rezervacije INT NOT NULL,
    stari_status VARCHAR(50),
    novi_status VARCHAR(50),
    promijenio VARCHAR(100),
    vrijeme TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_rezervacije) REFERENCES rezervacije(id_rezervacije)
);

-- =====================================================
-- PUNJENJE PODACIMA
-- =====================================================

-- -----------------------------------------------------
-- 11. ADMIN KORISNIK (lozinka: admin123)
-- -----------------------------------------------------
INSERT INTO korisnici (ime, prezime, email, lozinka_hash, role) VALUES 
('Admin', 'Administrator', 'admin@ribnjacstvo.hr', '$2y$10$YourHashHere', 'admin');

-- -----------------------------------------------------
-- 12. MAMCI (KUKURUZ 10kg=6€, PELET 1kg=2€)
-- -----------------------------------------------------
INSERT INTO mamci (naziv, tezina_kg, cijena_eur, opis) VALUES
('Kukuruz', 10.00, 6.00, 'Kukuruz za mamac - pakiranje 10kg'),
('Pelet 5mm', 1.00, 2.00, 'Fini pelet 5mm - 1kg');

-- -----------------------------------------------------
-- 13. R23 POZICIJE (17 komada)
-- -----------------------------------------------------
INSERT INTO lokacije (naziv, tip, kapacitet, opis, cijena_osnovna, ima_struju) VALUES
('Pozicija 1', 'R23 pozicija', 2, 'Lijevo krilo, uz mostić', 70, 1),
('Pozicija 2', 'R23 pozicija', 2, 'Lijevo krilo, sredina', 70, 0),
('Pozicija 3', 'R23 pozicija', 2, 'Lijevo krilo, kraj', 70, 1),
('Pozicija 4', 'R23 pozicija', 2, 'Sredina, hladovina', 75, 0),
('Pozicija 5', 'R23 pozicija', 2, 'Sredina, plićak', 70, 1),
('Pozicija 6', 'R23 pozicija', 2, 'Sredina, dubina', 80, 0),
('Pozicija 7', 'R23 pozicija', 2, 'Desno krilo, uz trsku', 75, 1),
('Pozicija 8', 'R23 pozicija', 2, 'Desno krilo', 70, 0),
('Pozicija 9', 'R23 pozicija', 2, 'Desno krilo, kraj', 70, 1),
('Pozicija 10', 'R23 pozicija', 2, 'Zabat, lijevo', 65, 0),
('Pozicija 11', 'R23 pozicija', 2, 'Zabat, sredina', 70, 1),
('Pozicija 12', 'R23 pozicija', 2, 'Zabat, desno', 70, 0),
('Pozicija 13', 'R23 pozicija', 2, 'Poluotok', 85, 1),
('Pozicija 14', 'R23 pozicija', 2, 'Uvala', 75, 0),
('Pozicija 15', 'R23 pozicija', 2, 'Izvor vode', 80, 1),
('Pozicija 16', 'R23 pozicija', 2, 'Most', 75, 0),
('Pozicija 17', 'R23 pozicija', 2, 'Stara hrastovina', 85, 1);

-- -----------------------------------------------------
-- 14. C&R OTOK
-- -----------------------------------------------------
INSERT INTO lokacije (naziv, tip, kapacitet, opis, cijena_osnovna, cijena_vikend, cijena_sezona, ima_struju, ima_sjenicu) VALUES
('C&R Otok', 'C&R Otok', 10, 'OTOK - sjenica, roštilj, struja, prijevoz barkom', 150, 180, 200, 1, 1);

-- -----------------------------------------------------
-- 15. BLOKIRANI DATUMI (primjeri)
-- -----------------------------------------------------
INSERT INTO blokirani_datumi (datum_od, datum_do, razlog) VALUES
('2024-12-24', '2024-12-26', 'Božićni blagdani'),
('2024-12-31', '2025-01-02', 'Nova godina'),
('2024-05-01', '2024-05-01', 'Praznik rada');

-- =====================================================
-- KRAJ - BAZA JE SPREMNA!
-- =====================================================

-- Poruka o uspjehu
SELECT '🎣 BAZA USPJEŠNO KREIRANA!' AS PORUKA;
SELECT 'Ime baze: ribolov_rezervacije' AS INFO;
SELECT COUNT(*) AS 'Broj lokacija' FROM lokacije;
SELECT COUNT(*) AS 'Broj mamaca' FROM mamci;