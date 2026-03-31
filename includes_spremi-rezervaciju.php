<?php
// =====================================================
// SPREMI-REZERVACIJU.PHP - SA PREPARED STATEMENTS!
// =====================================================
session_start();
require_once '../spoji.php';

// ===== CSRF ZAŠTITA =====
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("CSRF napad otkriven!");
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: ../index.php");
    exit();
}

// ===== 1. DOHVATI PODATKE =====
$id_lokacije = (int)$_POST['id_lokacije'];
$datum = $_POST['datum'];
$ime = $_POST['ime'];
$mobitel = $_POST['mobitel'];
$email = isset($_POST['email']) ? $_POST['email'] : '';
$broj_osoba = (int)$_POST['broj_osoba'];
$napomena = isset($_POST['napomena']) ? $_POST['napomena'] : '';

// ===== 2. PROVJERA BLOKIRANIH DATUMA =====
$stmt = $mysqli->prepare("SELECT id_blokade FROM blokirani_datumi 
                          WHERE (id_lokacije = ? OR id_lokacije IS NULL) 
                          AND ? BETWEEN datum_od AND datum_do");
$stmt->bind_param("is", $id_lokacije, $datum);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    header("Location: ../index.php?status=blokirano");
    exit();
}

// ===== 3. PROVJERA ZAUZETOSTI =====
$stmt = $mysqli->prepare("SELECT id_rezervacije FROM rezervacije 
                          WHERE id_lokacije = ? AND datum_rezervacije = ? 
                          AND status != 'otkazano'");
$stmt->bind_param("is", $id_lokacije, $datum);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    header("Location: ../index.php?status=zauzeto");
    exit();
}

// ===== 4. DOHVATI CIJENU (dinamički!) =====
$cijena = getCijenaLokacije($mysqli, $id_lokacije, $datum);

// ===== 5. SPREMI REZERVACIJU =====
$stmt = $mysqli->prepare("INSERT INTO rezervacije 
    (id_lokacije, datum_rezervacije, ime_prezime, broj_mobitela, email, broj_osoba, cijena_po_osobi, napomena) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("issssiis", $id_lokacije, $datum, $ime, $mobitel, $email, $broj_osoba, $cijena, $napomena);

if ($stmt->execute()) {
    $id_rezervacije = $mysqli->insert_id;
    
    // ===== 6. SPREMI MAMCE (dinamički iz baze!) =====
    $mamci = $mysqli->query("SELECT * FROM mamci");
    while ($m = $mamci->fetch_assoc()) {
        $kolicina = isset($_POST['mamac_' . $m['id_mamca']]) ? (int)$_POST['mamac_' . $m['id_mamca']] : 0;
        
        if ($kolicina > 0) {
            $stmt2 = $mysqli->prepare("INSERT INTO stavke_mamci (id_rezervacije, id_mamca, kolicina, cijena_po_komadu) 
                                       VALUES (?, ?, ?, ?)");
            $stmt2->bind_param("iiid", $id_rezervacije, $m['id_mamca'], $kolicina, $m['cijena_eur']);
            $stmt2->execute();
        }
    }
    
    // ===== 7. POŠALJI EMAIL =====
    if (!empty($email)) {
        $to = $email;
        $subject = "Potvrda rezervacije - Ribnjačarstvo Končanica";
        $message = "
        <html>
        <body style='font-family: Arial;'>
            <h2 style='color: #e67e22;'>🎣 Ribnjačarstvo Končanica</h2>
            <p>Poštovani $ime,</p>
            <p>Vaša rezervacija je zaprimljena!</p>
            <p><strong>Broj rezervacije:</strong> #$id_rezervacije</p>
            <p><strong>Datum:</strong> $datum</p>
            <p><strong>Lokacija:</strong> " . $_POST['lokacija_naziv'] . "</p>
            <p>Status: <strong style='color: #f39c12;'>NA ČEKANJU</strong></p>
            <p>Javit ćemo se nakon potvrde administratora.</p>
            <hr>
            <p>Ribnjačarstvo Končanica</p>
        </body>
        </html>
        ";
        
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8\r\n";
        
        // mail($to, $subject, $message, $headers); // za produkciju
    }
    
    header("Location: ../index.php?status=uspjeh&id=$id_rezervacije");
    
} else {
    header("Location: ../index.php?status=greska");
}
?>