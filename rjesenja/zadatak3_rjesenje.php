<?php
/**
 * ╔═══════════════════════════════════════════════════════════════╗
 * ║                  RJEŠENJE ZAVRŠNOG ZADATKA                    ║
 * ║                     Sustav za školu                           ║
 * ╚═══════════════════════════════════════════════════════════════╝
 */

// ═══════════════════════════════════════════════════════════════
// BAZNA KLASA: Osoba
// ═══════════════════════════════════════════════════════════════
class Osoba {
    // Protected - dostupno ovoj klasi i svim nasljednicima
    protected $ime;
    protected $prezime;
    protected $email;

    public function __construct($ime, $prezime, $email) {
        $this->ime = $ime;
        $this->prezime = $prezime;
        $this->email = $email;
    }

    public function predstaviSe() {
        return "Ja sam " . $this->ime . " " . $this->prezime;
    }

    // Getteri za pristup protected svojstvima izvana
    public function getIme() {
        return $this->ime;
    }

    public function getPrezime() {
        return $this->prezime;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getPunoIme() {
        return $this->ime . " " . $this->prezime;
    }
}

// ═══════════════════════════════════════════════════════════════
// NASLIJEĐENA KLASA: Ucenik
// ═══════════════════════════════════════════════════════════════
class Ucenik extends Osoba {
    private $razred;
    private $prosjek;

    public function __construct($ime, $prezime, $email, $razred, $prosjek) {
        // Pozivamo konstruktor roditelja (Osoba)
        parent::__construct($ime, $prezime, $email);

        // Postavljamo vlastita svojstva
        $this->razred = $razred;
        $this->prosjek = $prosjek;
    }

    // OVERRIDE - nadpisujemo roditeljsku metodu
    public function predstaviSe() {
        return "Ja sam " . $this->ime . " " . $this->prezime .
               ", učenik " . $this->razred . " razreda";
    }

    // BONUS: Provjera prolaznosti
    public function daLiPolozio() {
        return $this->prosjek >= 2.0;
    }

    // Getteri
    public function getRazred() {
        return $this->razred;
    }

    public function getProsjek() {
        return $this->prosjek;
    }

    // Dodatna metoda
    public function getOcjena() {
        if ($this->prosjek >= 4.5) return "Odličan";
        if ($this->prosjek >= 3.5) return "Vrlo dobar";
        if ($this->prosjek >= 2.5) return "Dobar";
        if ($this->prosjek >= 2.0) return "Dovoljan";
        return "Nedovoljan";
    }
}

// ═══════════════════════════════════════════════════════════════
// NASLIJEĐENA KLASA: Profesor
// ═══════════════════════════════════════════════════════════════
class Profesor extends Osoba {
    private $predmet;
    private $godinaIskustva;

    public function __construct($ime, $prezime, $email, $predmet, $godinaIskustva) {
        parent::__construct($ime, $prezime, $email);
        $this->predmet = $predmet;
        $this->godinaIskustva = $godinaIskustva;
    }

    // OVERRIDE
    public function predstaviSe() {
        return "Ja sam " . $this->ime . " " . $this->prezime .
               ", profesor/ica predmeta " . $this->predmet;
    }

    // Getteri
    public function getPredmet() {
        return $this->predmet;
    }

    public function getGodinaIskustva() {
        return $this->godinaIskustva;
    }

    // Dodatna metoda
    public function getStatus() {
        if ($this->godinaIskustva >= 20) return "Senior";
        if ($this->godinaIskustva >= 10) return "Iskusan";
        if ($this->godinaIskustva >= 5) return "Srednje iskusan";
        return "Početnik";
    }
}

// ═══════════════════════════════════════════════════════════════
// TESTIRANJE
// ═══════════════════════════════════════════════════════════════

$ucenici = [
    new Ucenik("Marko", "Markovic", "marko@skola.hr", "4.a", 4.5),
    new Ucenik("Ana", "Anic", "ana@skola.hr", "4.a", 3.8),
    new Ucenik("Ivan", "Ivic", "ivan@skola.hr", "4.b", 1.8),
    new Ucenik("Petra", "Petric", "petra@skola.hr", "4.b", 2.1),
];

$profesori = [
    new Profesor("Josip", "Josipovic", "josip@skola.hr", "Informatika", 15),
    new Profesor("Maja", "Majic", "maja@skola.hr", "Matematika", 8),
    new Profesor("Tomislav", "Tomic", "tomislav@skola.hr", "Hrvatski jezik", 25),
];

?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Rješenje 3 - Škola</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
    <h1>Rješenje završnog zadatka: Sustav za školu</h1>
    <span class="badge bg-success">RJEŠENJE</span>
    <hr>

    <div class="row">
        <!-- UČENICI -->
        <div class="col-md-6">
            <h4>👨‍🎓 Učenici</h4>
            <?php foreach ($ucenici as $ucenik): ?>
            <div class="card mb-2">
                <div class="card-body">
                    <h6 class="card-title"><?= $ucenik->getPunoIme() ?></h6>
                    <p class="card-text small mb-1">
                        <?= $ucenik->predstaviSe() ?>
                    </p>
                    <p class="card-text small mb-1">
                        <strong>Prosjek:</strong> <?= $ucenik->getProsjek() ?>
                        (<?= $ucenik->getOcjena() ?>)
                    </p>
                    <p class="card-text">
                        <?php if ($ucenik->daLiPolozio()): ?>
                            <span class="badge bg-success">✅ Položio</span>
                        <?php else: ?>
                            <span class="badge bg-danger">❌ Nije položio</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- PROFESORI -->
        <div class="col-md-6">
            <h4>👨‍🏫 Profesori</h4>
            <?php foreach ($profesori as $profesor): ?>
            <div class="card mb-2">
                <div class="card-body">
                    <h6 class="card-title"><?= $profesor->getPunoIme() ?></h6>
                    <p class="card-text small mb-1">
                        <?= $profesor->predstaviSe() ?>
                    </p>
                    <p class="card-text small mb-1">
                        <strong>Iskustvo:</strong> <?= $profesor->getGodinaIskustva() ?> godina
                    </p>
                    <p class="card-text">
                        <span class="badge bg-info"><?= $profesor->getStatus() ?></span>
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <hr>

    <div class="mt-4">
        <h5>Dijagram nasljeđivanja:</h5>
        <pre class="bg-light p-3">
              ┌─────────────────────┐
              │       Osoba         │  ← BAZNA KLASA
              │─────────────────────│
              │ # ime               │  # = protected
              │ # prezime           │
              │ # email             │
              │─────────────────────│
              │ + __construct()     │
              │ + predstaviSe()     │  ← Ovu metodu OVERRIDE-amo
              │ + getIme()          │
              └──────────┬──────────┘
                         │ extends
            ┌────────────┴────────────┐
            │                         │
      ┌─────┴─────┐            ┌──────┴──────┐
      │  Ucenik   │            │  Profesor   │
      │───────────│            │─────────────│
      │ - razred  │            │ - predmet   │
      │ - prosjek │            │ - iskustvo  │
      │───────────│            │─────────────│
      │ + predstaviSe()        │ + predstaviSe()
      │ + daLiPolozio()        │ + getStatus()
      └───────────┘            └─────────────┘
        </pre>
    </div>

    <div class="mt-4 p-3 bg-light">
        <h5>Ključni koncepti u ovom zadatku:</h5>
        <table class="table table-sm">
            <tr>
                <td><code>protected</code></td>
                <td>Svojstva dostupna i u Osoba i u Ucenik/Profesor</td>
            </tr>
            <tr>
                <td><code>extends</code></td>
                <td>Ucenik i Profesor nasljeđuju od Osoba</td>
            </tr>
            <tr>
                <td><code>parent::__construct()</code></td>
                <td>Pozivamo konstruktor roditelja da inicijalizira ime, prezime, email</td>
            </tr>
            <tr>
                <td>Override <code>predstaviSe()</code></td>
                <td>Svaka klasa ima svoju verziju iste metode</td>
            </tr>
        </table>
    </div>
</body>
</html>
