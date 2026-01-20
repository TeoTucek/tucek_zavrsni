<?php
/**
 * ╔═══════════════════════════════════════════════════════════════╗
 * ║                      ZADATAK 2                                ║
 * ║              Korisnik s lozinkom (private)                    ║
 * ╠═══════════════════════════════════════════════════════════════╣
 * ║  Nadogradi klasu Korisnik:                                    ║
 * ║                                                               ║
 * ║  1. Dodaj PRIVATE svojstvo: $lozinka                          ║
 * ║                                                               ║
 * ║  2. Dodaj metodu: setLozinka($novaLozinka)                    ║
 * ║     - Lozinka mora imati MINIMALNO 6 znakova                  ║
 * ║     - Ako je prekratka, vrati false                           ║
 * ║     - Ako je OK, postavi lozinku i vrati true                 ║
 * ║                                                               ║
 * ║  3. Dodaj metodu: provjeriLozinku($unesena)                   ║
 * ║     - Vraća true ako je unesena lozinka ispravna              ║
 * ║     - Vraća false ako nije                                    ║
 * ║                                                               ║
 * ║  BONUS: Dodaj konstruktor koji prima ime, prezime, email      ║
 * ╚═══════════════════════════════════════════════════════════════╝
 */

// TVOJ KOD OVDJE:

class Korisnik {
    public $ime;
    public $prezime;
    public $email;
    private $lozinka;

    // Konstruktor (BONUS)
    public function __construct($ime = "", $prezime = "", $email = "") {
        $this->ime = $ime;
        $this->prezime = $prezime;
        $this->email = $email;
        $this->lozinka = "";
    }


    public function pozdrav() {
        return "Bok, ja sam " . $this->ime . " " . $this->prezime;
    }

    public function setLozinka($novaLozinka) {
        if (strlen($novaLozinka) < 6) {
            return false;
        }
        $this->lozinka = $novaLozinka;
        return true;
    }

    public function provjeriLozinku($unesena) {
        return $this->lozinka === $unesena;
    }

}

// TESTIRANJE - ovo NE MIJENJAJ:
$korisnik = new Korisnik();
$korisnik->ime = "Ana";
$korisnik->prezime = "Anic";
$korisnik->email = "ana@mail.com";

// Testiraj metode:
$test1 = $korisnik->setLozinka("abc");        // Trebalo bi vratiti false (prekratka)
$test2 = $korisnik->setLozinka("tajna123");   // Trebalo bi vratiti true
$test3 = $korisnik->provjeriLozinku("krivo"); // Trebalo bi vratiti false
$test4 = $korisnik->provjeriLozinku("tajna123"); // Trebalo bi vratiti true

?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Zadatak 2 - Lozinka</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
    <h1>Zadatak 2: Private svojstva</h1>
    <hr>

    <h4>Rezultati testova:</h4>
    <table class="table table-bordered">
        <tr>
            <td><code>setLozinka("abc")</code></td>
            <td>Očekivano: <strong>false</strong></td>
            <td>Dobiveno: <strong><?= $test1 ? "true" : "false" ?></strong></td>
            <td><?= $test1 === false ? "✅" : "❌" ?></td>
        </tr>
        <tr>
            <td><code>setLozinka("tajna123")</code></td>
            <td>Očekivano: <strong>true</strong></td>
            <td>Dobiveno: <strong><?= $test2 ? "true" : "false" ?></strong></td>
            <td><?= $test2 === true ? "✅" : "❌" ?></td>
        </tr>
        <tr>
            <td><code>provjeriLozinku("krivo")</code></td>
            <td>Očekivano: <strong>false</strong></td>
            <td>Dobiveno: <strong><?= $test3 ? "true" : "false" ?></strong></td>
            <td><?= $test3 === false ? "✅" : "❌" ?></td>
        </tr>
        <tr>
            <td><code>provjeriLozinku("tajna123")</code></td>
            <td>Očekivano: <strong>true</strong></td>
            <td>Dobiveno: <strong><?= $test4 ? "true" : "false" ?></strong></td>
            <td><?= $test4 === true ? "✅" : "❌" ?></td>
        </tr>
    </table>

    <div class="alert alert-warning">
        <strong>Napomena:</strong> Ako dobivaš greške, provjeri:
        <ul>
            <li>Je li <code>$lozinka</code> definirana kao <code>private</code>?</li>
            <li>Koristiš li <code>$this->lozinka</code> za pristup unutar klase?</li>
            <li>Vraća li <code>setLozinka()</code> boolean vrijednost?</li>
        </ul>
    </div>
</body>
</html>
