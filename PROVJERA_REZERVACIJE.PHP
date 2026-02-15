<?php
// =====================================================
// DATOTEKA: provjera_rezervacije.php
// OPIS: AJAX provjera - jel termin slobodan
// =====================================================

require_once 'spoji.php';

if (isset($_POST['id_lokacije']) && isset($_POST['datum'])) {
    $id_lokacije = $mysqli->real_escape_string($_POST['id_lokacije']);
    $datum = $mysqli->real_escape_string($_POST['datum']);
    
    $upit = "SELECT id_rezervacije FROM rezervacije 
             WHERE id_lokacije = $id_lokacije 
             AND datum_rezervacije = '$datum'
             AND status != 'otkazano'";
    
    $rezultat = $mysqli->query($upit);
    
    if ($rezultat->num_rows > 0) {
        echo 'zauzeto';
    } else {
        echo 'slobodno';
    }
} else {
    echo 'greska';
}
?>