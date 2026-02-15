<?php
// =============================================
// TEST BAZE - PROVJERA DALI UOPĆE VIDI TABLICE
// =============================================

echo "<h2>🔍 TEST BAZE PODATAKA</h1>";

// Spajanje
require_once 'spoji.php';

// Provjera tablica
$tablice = $mysqli->query("SHOW TABLES");

echo "<h3>📊 Pronađene tablice:</h3>";
echo "<ul>";
$broj = 0;
while ($row = $tablice->fetch_array()) {
    echo "<li>" . $row[0] . "</li>";
    $broj++;
}
echo "</ul>";
echo "<p>Ukupno tablica: <strong>" . $broj . "</strong></p>";

// Provjera strukture
echo "<h3>📝 Struktura tablice 'rezervacije':</h3>";
$struktura = $mysqli->query("DESCRIBE rezervacije");
if ($struktura) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Polje</th><th>Tip</th></tr>";
    while ($row = $struktura->fetch_assoc()) {
        echo "<tr><td>" . $row['Field'] . "</td><td>" . $row['Type'] . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ Tablica 'rezervacije' ne postoji!</p>";
}

// Provjera mamaca
echo "<h3>🌽 Provjera tablice 'mamci':</h3>";
$mamci = $mysqli->query("SELECT * FROM mamci");
if ($mamci && $mamci->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Naziv</th><th>Cijena</th></tr>";
    while ($row = $mamci->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id_mamca'] . "</td>";
        echo "<td>" . $row['naziv'] . "</td>";
        echo "<td>" . $row['cijena_eur'] . " €</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: orange;'>⚠️ Nema mamaca ili tablica ne postoji</p>";
}

?>