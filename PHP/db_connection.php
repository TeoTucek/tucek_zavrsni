<?php
// db_connection.php - PDO verzija
require_once 'config.php';

// Funkcije za rad s bazom (sada koriste PDO)
function getCjenikPoRibnjaku($ribnjak_id) {
    global $pdo;
    
    $ribnjak_id = intval($ribnjak_id);
    
    try {
        $query = "SELECT c.*, r.naziv as ribnjak_naziv 
                 FROM cijene_karata c
                 JOIN vrste_karata vk ON c.vrsta_karte_id = vk.id
                 JOIN ribnjaci r ON vk.ribnjak_id = r.id
                 WHERE vk.ribnjak_id = :ribnjak_id
                 ORDER BY c.cijena_eura";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([':ribnjak_id' => $ribnjak_id]);
        
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("SQL greška u getCjenikPoRibnjaku: " . $e->getMessage());
        return [];
    }
}

function getCjenikPoKategoriji($kategorija) {
    global $pdo;
    
    try {
        $query = "SELECT * FROM vrste_karata 
                 WHERE LOWER(naziv) LIKE :kategorija
                 ORDER BY (
                     SELECT MIN(cijena_eura) 
                     FROM cijene_karata ck 
                     WHERE ck.vrsta_karte_id = vrste_karata.id
                 )";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([':kategorija' => '%' . strtolower($kategorija) . '%']);
        
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("SQL greška u getCjenikPoKategoriji: " . $e->getMessage());
        return [];
    }
}

// Funkcija za kreiranje nove karte
function kreirajKartu($podaci) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Generiraj broj karte
        $broj_karte = 'RK-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        
        // Dohvati djelatnika
        $djelatnik_id = $_SESSION['djelatnik_id'];
        
        // Izračunaj datum važenja
        $datum_vrijedi_od = $podaci['datum'] . ' ' . $podaci['vrijeme_od'];
        $datum_vrijedi_do = $podaci['datum'] . ' ' . $podaci['vrijeme_do'];
        
        // Ako je noćni ribolov, prilagodi datume
        if ($podaci['noćni']) {
            $datum_vrijedi_do = date('Y-m-d', strtotime($podaci['datum'] . ' + ' . ($podaci['trajanje_dana'] - 1) . ' days')) . ' 20:00:00';
        }
        
        // Unesi kartu
        $stmt = $pdo->prepare("
            INSERT INTO karte (
                broj_karte, vrsta_karte_id, datum_izdavanja, 
                datum_vrijedi_od, datum_vrijedi_do, ime_kupca, prezime_kupca, 
                telefon, email, ukupna_cijena, placeno, djelatnik_id, napomena
            ) VALUES (
                :broj_karte, :vrsta_karte_id, NOW(), 
                :datum_vrijedi_od, :datum_vrijedi_do, :ime_kupca, :prezime_kupca, 
                :telefon, :email, :ukupna_cijena, :placeno, :djelatnik_id, :napomena
            )
        ");
        
        $stmt->execute([
            ':broj_karte' => $broj_karte,
            ':vrsta_karte_id' => $podaci['vrsta_karte_id'],
            ':datum_vrijedi_od' => $datum_vrijedi_od,
            ':datum_vrijedi_do' => $datum_vrijedi_do,
            ':ime_kupca' => $podaci['ime_kupca'],
            ':prezime_kupca' => $podaci['prezime_kupca'],
            ':telefon' => $podaci['telefon'],
            ':email' => $podaci['email'] ?? null,
            ':ukupna_cijena' => $podaci['ukupna_cijena'],
            ':placeno' => $podaci['placeno'] ?? 0,
            ':djelatnik_id' => $djelatnik_id,
            ':napomena' => $podaci['napomena'] ?? null
        ]);
        
        $karta_id = $pdo->lastInsertId();
        
        // Ako postoje dodatne usluge, unesi ih
        if (!empty($podaci['dodatne_usluge'])) {
            foreach ($podaci['dodatne_usluge'] as $usluga_id => $kolicina) {
                if ($kolicina > 0) {
                    $stmt = $pdo->prepare("
                        INSERT INTO karta_dodatna_usluga (karta_id, dodatna_usluga_id, kolicina)
                        VALUES (:karta_id, :dodatna_usluga_id, :kolicina)
                    ");
                    
                    $stmt->execute([
                        ':karta_id' => $karta_id,
                        ':dodatna_usluga_id' => $usluga_id,
                        ':kolicina' => $kolicina
                    ]);
                }
            }
        }
        
        $pdo->commit();
        return $karta_id;
        
    } catch(PDOException $e) {
        $pdo->rollBack();
        error_log("Greška pri kreiranju karte: " . $e->getMessage());
        return false;
    }
}

// Funkcija za dohvat statistika
function getStatistike($period = 'month') {
    global $pdo;
    
    try {
        switch($period) {
            case 'today':
                $date_condition = "DATE(k.datum_izdavanja) = CURDATE()";
                break;
            case 'week':
                $date_condition = "k.datum_izdavanja >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                break;
            case 'month':
                $date_condition = "MONTH(k.datum_izdavanja) = MONTH(CURDATE()) AND YEAR(k.datum_izdavanja) = YEAR(CURDATE())";
                break;
            case 'year':
                $date_condition = "YEAR(k.datum_izdavanja) = YEAR(CURDATE())";
                break;
            default:
                $date_condition = "1=1";
        }
        
        // Ukupna zarada
        $query = "SELECT SUM(k.ukupna_cijena) as zarada, COUNT(*) as broj_karata
                  FROM karte k
                  WHERE k.placeno = 1 AND $date_condition";
        $stmt = $pdo->query($query);
        $ukupno = $stmt->fetch();
        
        // Zarada po ribnjaku
        $query = "SELECT r.naziv, SUM(k.ukupna_cijena) as zarada, COUNT(*) as broj_karata
                  FROM karte k
                  JOIN vrste_karata vk ON k.vrsta_karte_id = vk.id
                  JOIN ribnjaci r ON vk.ribnjak_id = r.id
                  WHERE k.placeno = 1 AND $date_condition
                  GROUP BY r.id
                  ORDER BY zarada DESC";
        $stmt = $pdo->query($query);
        $po_ribnjaku = $stmt->fetchAll();
        
        // Najprodavanije karte
        $query = "SELECT vk.naziv, COUNT(*) as broj_prodanih, SUM(k.ukupna_cijena) as zarada
                  FROM karte k
                  JOIN vrste_karata vk ON k.vrsta_karte_id = vk.id
                  WHERE k.placeno = 1 AND $date_condition
                  GROUP BY vk.id
                  ORDER BY broj_prodanih DESC
                  LIMIT 5";
        $stmt = $pdo->query($query);
        $najprodavanije = $stmt->fetchAll();
        
        return [
            'ukupno' => $ukupno,
            'po_ribnjaku' => $po_ribnjaku,
            'najprodavanije' => $najprodavanije
        ];
        
    } catch(PDOException $e) {
        error_log("Greška pri dohvaćanju statistika: " . $e->getMessage());
        return [];
    }
}
?>