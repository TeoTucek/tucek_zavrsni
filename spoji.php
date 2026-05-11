<?php
// =====================================================
// SPOJI.PHP - Spajanje na bazu
// Konfiguracija dolazi iz config.php (nije u git-u)
// =====================================================

$config_path = __DIR__ . '/config.php';
if (!file_exists($config_path)) {
    die("<div style='background:#f8d7da;padding:20px;'>
            <h2>❌ config.php ne postoji!</h2>
            <p>Kopiraj config.example.php u config.php i popuni vrijednosti.</p>
         </div>");
}
require_once $config_path;

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("<div style='background: #f8d7da; padding: 20px;'>
            <h2>❌ Baza ne radi!</h2>
            <p>Pokreni install.php prvo!</p>
         </div>");
}

$mysqli->set_charset("utf8mb4");

// ===== POMOĆNE FUNKCIJE =====

/**
 * Dohvati cijenu za tip ulaznice (R23, R23 PLUS)
 */
function getCijenaUlaznice($mysqli, $id_tipa) {
    $stmt = $mysqli->prepare("SELECT cijena_eur FROM tipovi_ulaznica WHERE id_tipa = ?");
    $stmt->bind_param("i", $id_tipa);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return $row ? $row['cijena_eur'] : 0;
}

/**
 * Dohvati naziv tipa ulaznice
 */
function getNazivUlaznice($mysqli, $id_tipa) {
    $stmt = $mysqli->prepare("SELECT naziv FROM tipovi_ulaznica WHERE id_tipa = ?");
    $stmt->bind_param("i", $id_tipa);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return $row ? $row['naziv'] : 'Nepoznato';
}

/**
 * Dohvati cijenu za noćni paket
 */
function getCijenaNocni($mysqli, $id_paketa) {
    if (!$id_paketa) return 0;
    $stmt = $mysqli->prepare("SELECT cijena_eur FROM nocni_ribolov WHERE id_paketa = ?");
    $stmt->bind_param("i", $id_paketa);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return $row ? $row['cijena_eur'] : 0;
}

/**
 * Dohvati naziv lokacije
 */
function getNazivLokacije($mysqli, $id_lokacije) {
    $stmt = $mysqli->prepare("SELECT naziv FROM lokacije WHERE id_lokacije = ?");
    $stmt->bind_param("i", $id_lokacije);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return $row ? $row['naziv'] : 'Nepoznato';
}

/**
 * Dohvati kapacitet lokacije
 */
function getKapacitetLokacije($mysqli, $id_lokacije) {
    $stmt = $mysqli->prepare("SELECT kapacitet FROM lokacije WHERE id_lokacije = ?");
    $stmt->bind_param("i", $id_lokacije);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return $row ? $row['kapacitet'] : 2;
}
?>