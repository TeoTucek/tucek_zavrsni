<?php
// =====================================================
// DATOTEKA: spoji.php
// OPIS: Spajanje na bazu podataka - UKLJUČI OVO NA SVAKOJ STRANICI
// =====================================================

// PODACI ZA SPOJANJE NA BAZU - PROMIJENI AKO TREBA!
$host = 'localhost';        // Server (obično localhost)
$baza = 'ribolov_rezervacije';   // IME BAZE - OVO JE VAŽNO!
$user = 'root';             // Korisnik (xampp je root)
$pass = '';                 // Lozinka (xampp je prazno)

// Pokušaj spajanja na bazu
$mysqli = new mysqli($host, $user, $pass, $baza);

// Provjera jel uspjelo spajanje
if ($mysqli->connect_error) {
    die("
        <div style='color: red; padding: 20px; border: 2px solid red; margin: 20px; background: #ffeeee;'>
            <h2>❌ GREŠKA U SPAJANJU NA BAZU!</h2>
            <p><strong>Provjeri:</strong></p>
            <ul>
                <li>Jesi li pokrenuo/la XAMPP (MySQL)?</li>
                <li>Jesi li stvorio/la bazu 'ribolov_rezervacije'?</li>
                <li>Podaci u spoji.php: host='$host', baza='$baza'</li>
            </ul>
            <p><strong>Greška:</strong> " . $mysqli->connect_error . "</p>
        </div>
    ");
}

// Postavi encoding na UTF-8 (za hrvatska slova)
$mysqli->set_charset("utf8mb4");

// Ako sve radi - sve je OK!
// (nema ispisa, samo tiho spajanje)

// Ovu datoteku ćeš uključiti na svakoj stranici sa:
// require_once 'spoji.php';
?>