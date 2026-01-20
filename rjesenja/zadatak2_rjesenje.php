<?php
/**
 * ╔═══════════════════════════════════════════════════════════════╗
 * ║                  RJEŠENJE ZADATKA 2                           ║
 * ║              Korisnik s lozinkom (private)                    ║
 * ╚═══════════════════════════════════════════════════════════════╝
 */

class Korisnik {
    // Public svojstva
    public $ime;
    public $prezime;
    public $email;

    // PRIVATE - nitko izvana ne može pristupiti direktno!
    private $lozinka;

    // BONUS: Konstruktor
    public function __construct($ime = "", $prezime = "", $email = "") {
        $this->ime = $ime;
        $this->prezime = $prezime;
        $this->email = $email;
    }

    public function pozdrav() {
        return "Bok, ja sam " . $this->ime . " " . $this->prezime;
    }

    /**
     * Postavlja lozinku ako ima minimalno 6 znakova
     * @param string $novaLozinka
     * @return bool true ako je uspješno, false ako nije
     */
    public function setLozinka($novaLozinka) {
        // Provjera minimalne duljine
        if (strlen($novaLozinka) < 6) {
            return false;
        }

        // Sve OK - postavi lozinku
        $this->lozinka = $novaLozinka;
        return true;
    }

    /**
     * Provjerava je li unesena lozinka ispravna
     * @param string $unesena
     * @return bool
     */
    public function provjeriLozinku($unesena) {
        return $this->lozinka === $unesena;
    }

    // Dodatno: provjera je li lozinka postavljena
    public function imaLozinku() {
        return !empty($this->lozinka);
    }
}

// ═══════════════════════════════════════════════════════════════
// TESTIRANJE
// ═══════════════════════════════════════════════════════════════

// Verzija 1: Bez konstruktora
$korisnik1 = new Korisnik();
$korisnik1->ime = "Ana";
$korisnik1->prezime = "Anic";
$korisnik1->email = "ana@mail.com";

// Verzija 2: S konstruktorom (BONUS)
$korisnik2 = new Korisnik("Marko", "Maric", "marko@mail.com");

// Testovi za korisnik1
$test1 = $korisnik1->setLozinka("abc");        // false - prekratka
$test2 = $korisnik1->setLozinka("tajna123");   // true
$test3 = $korisnik1->provjeriLozinku("krivo"); // false
$test4 = $korisnik1->provjeriLozinku("tajna123"); // true

?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Rješenje 2 - Lozinka</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
    <h1>Rješenje zadatka 2: Private svojstva</h1>
    <span class="badge bg-success">RJEŠENJE</span>
    <hr>

    <h4>Rezultati testova:</h4>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Test</th>
                <th>Očekivano</th>
                <th>Dobiveno</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr class="<?= $test1 === false ? 'table-success' : 'table-danger' ?>">
                <td><code>setLozinka("abc")</code></td>
                <td>false</td>
                <td><?= $test1 ? "true" : "false" ?></td>
                <td><?= $test1 === false ? "✅ PROLAZI" : "❌ NE PROLAZI" ?></td>
            </tr>
            <tr class="<?= $test2 === true ? 'table-success' : 'table-danger' ?>">
                <td><code>setLozinka("tajna123")</code></td>
                <td>true</td>
                <td><?= $test2 ? "true" : "false" ?></td>
                <td><?= $test2 === true ? "✅ PROLAZI" : "❌ NE PROLAZI" ?></td>
            </tr>
            <tr class="<?= $test3 === false ? 'table-success' : 'table-danger' ?>">
                <td><code>provjeriLozinku("krivo")</code></td>
                <td>false</td>
                <td><?= $test3 ? "true" : "false" ?></td>
                <td><?= $test3 === false ? "✅ PROLAZI" : "❌ NE PROLAZI" ?></td>
            </tr>
            <tr class="<?= $test4 === true ? 'table-success' : 'table-danger' ?>">
                <td><code>provjeriLozinku("tajna123")</code></td>
                <td>true</td>
                <td><?= $test4 ? "true" : "false" ?></td>
                <td><?= $test4 === true ? "✅ PROLAZI" : "❌ NE PROLAZI" ?></td>
            </tr>
        </tbody>
    </table>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Korisnik 1 (bez konstruktora)</div>
                <div class="card-body">
                    <p><?= $korisnik1->pozdrav() ?></p>
                    <p>Email: <?= $korisnik1->email ?></p>
                    <p>Ima lozinku: <?= $korisnik1->imaLozinku() ? "Da" : "Ne" ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Korisnik 2 (s konstruktorom)</div>
                <div class="card-body">
                    <p><?= $korisnik2->pozdrav() ?></p>
                    <p>Email: <?= $korisnik2->email ?></p>
                    <p>Ima lozinku: <?= $korisnik2->imaLozinku() ? "Da" : "Ne" ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 p-3 bg-light">
        <h5>Ključni dijelovi koda:</h5>
        <pre><code>class Korisnik {
    private $lozinka;  // PRIVATE - skriveno!

    public function setLozinka($novaLozinka) {
        if (strlen($novaLozinka) < 6) {
            return false;  // Prekratka
        }
        $this->lozinka = $novaLozinka;
        return true;
    }

    public function provjeriLozinku($unesena) {
        return $this->lozinka === $unesena;
    }
}</code></pre>

        <h5 class="mt-3">Zašto je ovo važno?</h5>
        <ul>
            <li><code>private $lozinka</code> - nitko ne može direktno čitati/pisati lozinku</li>
            <li>Validacija u <code>setLozinka()</code> - osiguravamo kvalitetu podataka</li>
            <li>Provjera u <code>provjeriLozinku()</code> - siguran način za autentifikaciju</li>
        </ul>
    </div>
</body>
</html>
