<?php
require_once 'spoji.php';

echo "<h2>Test direktnog spremanja u bazu</h2>";

// Test podaci
$id_lokacije = 1;
$id_tipa_ulaznice = 1;
$datum = date('Y-m-d');
$ime = "TEST KORISNIK";
$mobitel = "0912345678";
$email = "test@test.com";
$broj_osoba = 2;
$cijena = 13.90; // R23 cijena
$napomena = "Test rezervacija";

echo "<p>Pokušavam spremiti:</p>";
echo "Lokacija: $id_lokacije<br>";
echo "Tip ulaznice: $id_tipa_ulaznice<br>";
echo "Datum: $datum<br>";
echo "Ime: $ime<br><br>";

// Pokušaj upisa SA SVIM STUPCIMA
$sql = "INSERT INTO rezervacije 
        (id_lokacije, id_tipa_ulaznice, datum_rezervacije, ime_prezime, broj_mobitela, email, broj_osoba, cijena_po_osobi, napomena) 
        VALUES ($id_lokacije, $id_tipa_ulaznice, '$datum', '$ime', '$mobitel', '$email', $broj_osoba, $cijena, '$napomena')";

echo "<p><strong>SQL upit:</strong><br>" . $sql . "</p>";

if ($mysqli->query($sql)) {
    $id = $mysqli->insert_id;
    echo "<p style='color: green;'>✅ Rezervacija spremljena! ID: $id</p>";
} else {
    echo "<p style='color: red;'>❌ Greška: " . $mysqli->error . "</p>";
}

// Prikaži zadnjih 5 rezervacija
echo "<h3>Zadnjih 5 rezervacija u bazi:</h3>";
$rez = $mysqli->query("SELECT * FROM rezervacije ORDER BY id_rezervacije DESC LIMIT 5");
if ($rez->num_rows > 0) {
    while ($r = $rez->fetch_assoc()) {
        echo "- ID: {$r['id_rezervacije']} | {$r['ime_prezime']} | {$r['datum_rezervacije']}<br>";
    }
} else {
    echo "<p>Nema rezervacija u bazi!</p>";
}
?>