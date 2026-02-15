<?php
// =====================================================
// SPREMI_REZERVACIJU.PHP - NOVA VERZIJA KOJA SIGURNO RADI
// =====================================================

require_once 'spoji.php';

// Provjera dal je poslano POST metodom
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // ===== 1. DOHVATI PODATKE IZ FORME =====
    $id_lokacije = (int)$_POST['id_lokacije'];
    $datum = $mysqli->real_escape_string($_POST['datum']);
    $ime = $mysqli->real_escape_string($_POST['ime']);
    $mobitel = $mysqli->real_escape_string($_POST['mobitel']);
    $email = isset($_POST['email']) ? $mysqli->real_escape_string($_POST['email']) : '';
    $broj_osoba = (int)$_POST['broj_osoba'];
    $napomena = isset($_POST['napomena']) ? $mysqli->real_escape_string($_POST['napomena']) : '';
    
    // ===== 2. MAMCI - TVOJ TRAŽENI DIO =====
    $kukuruz_kolicina = isset($_POST['kukuruz_kolicina']) ? (int)$_POST['kukuruz_kolicina'] : 0;
    $pelet_kolicina = isset($_POST['pelet_kolicina']) ? (int)$_POST['pelet_kolicina'] : 0;
    
    // ===== 3. PROVJERA DAL JE TERMIN SLOBODAN =====
    $provjera = $mysqli->query("SELECT id_rezervacije FROM rezervacije 
                                WHERE id_lokacije = $id_lokacije 
                                AND datum_rezervacije = '$datum'");
    
    if ($provjera && $provjera->num_rows > 0) {
        header("Location: rezervacija.php?status=zauzeto");
        exit();
    }
    
    // ===== 4. SPREMANJE U BAZU =====
    $upit = "INSERT INTO rezervacije 
            (id_lokacije, datum_rezervacije, ime_prezime, broj_mobitela, email, broj_osoba, napomena) 
            VALUES 
            ($id_lokacije, '$datum', '$ime', '$mobitel', '$email', $broj_osoba, '$napomena')";
    
    if ($mysqli->query($upit)) {
        $id_rezervacije = $mysqli->insert_id;
        
        // ===== 5. SPREMI MAMCE AKO SU ODABRANI =====
        
        // KUKURUZ (id_mamca = 1)
        if ($kukuruz_kolicina > 0) {
            $ukupno_kukuruz = $kukuruz_kolicina * 6;
            $mysqli->query("INSERT INTO stavke_rezervacije_mamci 
                           (id_rezervacije, id_mamca, kolicina, cijena_po_jedinici, ukupna_cijena_stavke) 
                           VALUES 
                           ($id_rezervacije, 1, $kukuruz_kolicina, 6.00, $ukupno_kukuruz)");
        }
        
        // PELET (id_mamca = 2)
        if ($pelet_kolicina > 0) {
            $ukupno_pelet = $pelet_kolicina * 2;
            $mysqli->query("INSERT INTO stavke_rezervacije_mamci 
                           (id_rezervacije, id_mamca, kolicina, cijena_po_jedinici, ukupna_cijena_stavke) 
                           VALUES 
                           ($id_rezervacije, 2, $pelet_kolicina, 2.00, $ukupno_pelet)");
        }
        
        // ===== 6. PREUSMJERI NA STRANICU S USPJEHOM =====
        header("Location: rezervacija.php?status=uspjeh&id=$id_rezervacije");
        exit();
        
    } else {
        // ===== 7. GREŠKA =====
        echo "<h3>Greška pri spremanju:</h3>";
        echo $mysqli->error;
        echo "<br><a href='rezervacija.php'>Pokušaj ponovno</a>";
    }
    
// OVO JE KRAJ if-a - ZATVARA onaj prvi if na liniji 8!
}

// ===== KRAJ DATOTEKE =====
?>