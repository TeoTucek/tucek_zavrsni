<?php
// =====================================================
// INSTALL.PHP - Instalira bazu sa svime!
// =====================================================
// Samo pokreni JEDNOM pa OBRADI!
// =====================================================

echo "<h1 style='color: #e67e22;'>🎣 Instalacija baze - Ribnjačarstvo Končanica</h1>";

$mysqli = new mysqli('localhost', 'root', '');

if ($mysqli->connect_error) {
    die("<p style='color: red;'>❌ Nema MySQL-a! Pokreni XAMPP!</p>");
}

// Kreiraj bazu
$mysqli->query("DROP DATABASE IF EXISTS ribolov_rezervacije");
$mysqli->query("CREATE DATABASE ribolov_rezervacije CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$mysqli->select_db('ribolov_rezervacije');

echo "<p style='color: green;'>✅ Baza kreirana!</p>";

// ===== 1. LOKACIJE =====
$mysqli->query("
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
)");

// ===== 2. MAMCI =====
$mysqli->query("
CREATE TABLE mamci (
    id_mamca INT AUTO_INCREMENT PRIMARY KEY,
    naziv VARCHAR(100) NOT NULL,
    tezina_kg DECIMAL(5,2) NOT NULL,
    cijena_eur DECIMAL(10,2) NOT NULL,
    opis TEXT,
    na_stanju INT DEFAULT 100
)");

// ===== 3. KORISNICI (s hashiranom lozinkom!) =====
$mysqli->query("
CREATE TABLE korisnici (
    id_korisnika INT AUTO_INCREMENT PRIMARY KEY,
    ime VARCHAR(50) NOT NULL,
    prezime VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    lozinka_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'korisnik') DEFAULT 'korisnik',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// ===== 4. REZERVACIJE (glavna) =====
$mysqli->query("
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
)");

// ===== 5. STAVKE MAMACA =====
$mysqli->query("
CREATE TABLE stavke_mamci (
    id_stavke INT AUTO_INCREMENT PRIMARY KEY,
    id_rezervacije INT NOT NULL,
    id_mamca INT NOT NULL,
    kolicina INT NOT NULL,
    cijena_po_komadu DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_rezervacije) REFERENCES rezervacije(id_rezervacije),
    FOREIGN KEY (id_mamca) REFERENCES mamci(id_mamca)
)");

// ===== 6. BLOKIRANI DATUMI =====
$mysqli->query("
CREATE TABLE blokirani_datumi (
    id_blokade INT AUTO_INCREMENT PRIMARY KEY,
    id_lokacije INT NULL,
    datum_od DATE NOT NULL,
    datum_do DATE NOT NULL,
    razlog VARCHAR(255),
    FOREIGN KEY (id_lokacije) REFERENCES lokacije(id_lokacije)
)");

// ===== 7. POVIJEST STATUSA =====
$mysqli->query("
CREATE TABLE povijest_statusa (
    id_povijest INT AUTO_INCREMENT PRIMARY KEY,
    id_rezervacije INT NOT NULL,
    stari_status VARCHAR(50),
    novi_status VARCHAR(50),
    promijenio VARCHAR(100),
    vrijeme TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_rezervacije) REFERENCES rezervacije(id_rezervacije)
)");

// ===== PUNJENJE PODACIMA =====

// Admin (HASHIRANA LOZINKA: admin123)
$hash = password_hash('admin123', PASSWORD_DEFAULT);
$mysqli->query("INSERT INTO korisnici (ime, prezime, email, lozinka_hash, role) VALUES 
                ('Admin', 'Administrator', 'admin@ribnjacstvo.hr', '$hash', 'admin')");

// Mamci
$mysqli->query("INSERT INTO mamci (naziv, tezina_kg, cijena_eur, opis) VALUES
    ('Kukuruz', 10.00, 6.00, 'Kukuruz za mamac - pakiranje 10kg'),
    ('Pelet 5mm', 1.00, 2.00, 'Fini pelet 5mm - 1kg')");

// R23 pozicije
for ($i = 1; $i <= 17; $i++) {
    $struja = ($i % 3 == 0) ? 1 : 0;
    $cijena = 70;
    if ($i == 13 || $i == 17) $cijena = 85;
    
    $mysqli->query("INSERT INTO lokacije (naziv, tip, kapacitet, opis, cijena_osnovna, ima_struju) VALUES
        ('Pozicija $i', 'R23 pozicija', 2, 'Pozicija na R23 jezeru', $cijena, $struja)");
}

// C&R Otok
$mysqli->query("INSERT INTO lokacije (naziv, tip, kapacitet, opis, cijena_osnovna, cijena_vikend, cijena_sezona, ima_struju, ima_sjenicu) VALUES
    ('C&R Otok', 'C&R Otok', 10, 'OTOK - sjenica, roštilj, struja', 150, 180, 200, 1, 1)");

// Blokirani datumi (primjer)
$mysqli->query("INSERT INTO blokirani_datumi (datum_od, datum_do, razlog) VALUES
    ('2024-12-24', '2024-12-26', 'Božićni blagdani'),
    ('2024-12-31', '2025-01-02', 'Nova godina')");

echo "<p style='color: green;'>✅ SVI PODACI UNESENI!</p>";
echo "<h2 style='color: green;'>🎉 INSTALACIJA GOTOVA!</h2>";
echo "<p><strong>Admin login:</strong> admin@ribnjacstvo.hr / admin123</p>";
echo "<p style='color: red;'><strong>🔥 OBRISI OVAJ FILE ODMAH!</strong></p>";
echo "<p><a href='index.php'>Idi na stranicu →</a></p>";
?>