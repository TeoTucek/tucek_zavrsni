<?php
/**
 * PRIMJER 4: Nasljeđivanje (extends)
 * Pokazuje kako jedna klasa može naslijediti drugu
 */

// ═══════════════════════════════════════════════════════════
// BAZNA KLASA (roditelj)
// ═══════════════════════════════════════════════════════════
class Vozilo {
    protected $marka;      // Protected - djeca mogu pristupiti!
    protected $model;
    protected $cijena;
    protected $godinaProizvodnje;

    public function __construct($marka, $model, $cijena, $godina = 2020) {
        $this->marka = $marka;
        $this->model = $model;
        $this->cijena = $cijena;
        $this->godinaProizvodnje = $godina;
    }

    public function getInfo() {
        return $this->marka . " " . $this->model . " (" . $this->godinaProizvodnje . ") - " . $this->cijena . "€/dan";
    }

    public function getMarka() {
        return $this->marka;
    }

    public function getCijena() {
        return $this->cijena;
    }

    public function izracunajCijenu($dana) {
        return $this->cijena * $dana;
    }
}

// ═══════════════════════════════════════════════════════════
// NASLIJEĐENA KLASA - Automobil
// ═══════════════════════════════════════════════════════════
class Automobil extends Vozilo {
    private $brojVrata;
    private $tipGoriva;

    public function __construct($marka, $model, $cijena, $godina, $brojVrata, $tipGoriva = "benzin") {
        // Pozivamo konstruktor RODITELJA
        parent::__construct($marka, $model, $cijena, $godina);

        // Postavljamo vlastita svojstva
        $this->brojVrata = $brojVrata;
        $this->tipGoriva = $tipGoriva;
    }

    // OVERRIDE - nadpisujemo roditeljsku metodu
    public function getInfo() {
        return parent::getInfo() . " | " . $this->brojVrata . " vrata, " . $this->tipGoriva;
    }

    public function getBrojVrata() {
        return $this->brojVrata;
    }

    public function getTipGoriva() {
        return $this->tipGoriva;
    }
}

// ═══════════════════════════════════════════════════════════
// NASLIJEĐENA KLASA - Motocikl
// ═══════════════════════════════════════════════════════════
class Motocikl extends Vozilo {
    private $kubikaza;
    private $tipMotocikla; // sport, cruiser, touring...

    public function __construct($marka, $model, $cijena, $godina, $kubikaza, $tip = "sport") {
        parent::__construct($marka, $model, $cijena, $godina);
        $this->kubikaza = $kubikaza;
        $this->tipMotocikla = $tip;
    }

    public function getInfo() {
        return parent::getInfo() . " | " . $this->kubikaza . "ccm, " . $this->tipMotocikla;
    }

    // Dodatna metoda samo za motocikle
    public function trebaAKategoriju() {
        return $this->kubikaza > 125;
    }
}

// ═══════════════════════════════════════════════════════════
// NASLIJEĐENA KLASA - Kombi
// ═══════════════════════════════════════════════════════════
class Kombi extends Vozilo {
    private $brojSjedala;
    private $nosivost; // u kg

    public function __construct($marka, $model, $cijena, $godina, $brojSjedala, $nosivost) {
        parent::__construct($marka, $model, $cijena, $godina);
        $this->brojSjedala = $brojSjedala;
        $this->nosivost = $nosivost;
    }

    public function getInfo() {
        return parent::getInfo() . " | " . $this->brojSjedala . " sjedala, nosivost " . $this->nosivost . "kg";
    }

    // Override - kombiji imaju popust za duže najme
    public function izracunajCijenu($dana) {
        $cijena = parent::izracunajCijenu($dana);
        if ($dana >= 7) {
            $cijena *= 0.9; // 10% popusta za tjedan ili više
        }
        return $cijena;
    }
}

// ═══════════════════════════════════════════════════════════
// TESTIRANJE
// ═══════════════════════════════════════════════════════════
$vozila = [
    new Automobil("Fiat", "Punto", 50, 2019, 5, "benzin"),
    new Automobil("BMW", "320d", 120, 2021, 4, "dizel"),
    new Motocikl("Yamaha", "R1", 80, 2020, 1000, "sport"),
    new Motocikl("Honda", "PCX", 30, 2022, 125, "skuter"),
    new Kombi("Mercedes", "Sprinter", 100, 2020, 9, 1500),
];

?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Primjer 4 - Nasljeđivanje</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .class-diagram {
            font-family: monospace;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }
    </style>
</head>
<body class="container py-4">
    <h1>Primjer 4: Nasljeđivanje (extends)</h1>
    <hr>

    <div class="class-diagram mb-4">
        <pre>
              ┌─────────────────┐
              │     Vozilo      │  ← BAZNA KLASA (roditelj)
              │─────────────────│
              │ # marka         │    # = protected
              │ # model         │
              │ # cijena        │
              │ # godina        │
              │─────────────────│
              │ + getInfo()     │    + = public
              │ + getCijena()   │
              └────────┬────────┘
                       │ extends
         ┌─────────────┼─────────────┐
         │             │             │
    ┌────┴────┐  ┌─────┴─────┐  ┌────┴────┐
    │Automobil│  │ Motocikl  │  │  Kombi  │  ← NASLIJEĐENE KLASE
    │─────────│  │───────────│  │─────────│
    │-brojVr. │  │-kubikaza  │  │-sjedala │    - = private
    │-gorivo  │  │-tip       │  │-nosiv.  │
    └─────────┘  └───────────┘  └─────────┘
        </pre>
    </div>

    <h4>Sva vozila u floti:</h4>
    <div class="row">
        <?php foreach ($vozila as $vozilo): ?>
        <div class="col-md-6 mb-3">
            <div class="card h-100 <?php
                if ($vozilo instanceof Automobil) echo 'border-primary';
                elseif ($vozilo instanceof Motocikl) echo 'border-danger';
                else echo 'border-success';
            ?>">
                <div class="card-header <?php
                    if ($vozilo instanceof Automobil) echo 'bg-primary text-white';
                    elseif ($vozilo instanceof Motocikl) echo 'bg-danger text-white';
                    else echo 'bg-success text-white';
                ?>">
                    <?php
                    if ($vozilo instanceof Automobil) echo '🚗 Automobil';
                    elseif ($vozilo instanceof Motocikl) echo '🏍️ Motocikl';
                    else echo '🚐 Kombi';
                    ?>
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?= $vozilo->getMarka() ?></h5>
                    <p class="card-text"><?= $vozilo->getInfo() ?></p>
                    <p class="text-muted">
                        Cijena za 7 dana: <strong><?= number_format($vozilo->izracunajCijenu(7), 2) ?>€</strong>
                        <?php if ($vozilo instanceof Kombi): ?>
                            <span class="badge bg-success">Uključen popust 10%!</span>
                        <?php endif; ?>
                    </p>
                    <?php if ($vozilo instanceof Motocikl): ?>
                        <p>
                            <?php if ($vozilo->trebaAKategoriju()): ?>
                                <span class="badge bg-warning text-dark">Potrebna A kategorija</span>
                            <?php else: ?>
                                <span class="badge bg-info">Dovoljno AM/A1</span>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="mt-4 p-3 bg-light">
        <h5>Ključne riječi:</h5>
        <table class="table table-sm">
            <tr>
                <td><code>extends</code></td>
                <td>Klasa nasljeđuje drugu klasu</td>
            </tr>
            <tr>
                <td><code>parent::</code></td>
                <td>Poziva metodu ili konstruktor roditelja</td>
            </tr>
            <tr>
                <td><code>protected</code></td>
                <td>Dostupno klasi i svim nasljednicima</td>
            </tr>
            <tr>
                <td><code>instanceof</code></td>
                <td>Provjerava je li objekt instanca neke klase</td>
            </tr>
        </table>
    </div>
</body>
</html>
