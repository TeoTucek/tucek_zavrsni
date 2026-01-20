<?php
/**
 * PRIMJER 2: Konstruktor __construct()
 * Pokazuje kako automatski inicijalizirati objekt
 */

class Automobil {
    public $marka;
    public $model;
    public $cijena;

    // KONSTRUKTOR - poziva se automatski kod "new"
    public function __construct($marka, $model, $cijena) {
        $this->marka = $marka;
        $this->model = $model;
        $this->cijena = $cijena;

        // Možemo dodati i neku logiku
        echo "<!-- Kreiran novi automobil: $marka $model -->\n";
    }

    public function predstaviSe() {
        return "Ja sam " . $this->marka . " " . $this->model .
               " i koštam " . $this->cijena . "€/dan";
    }

    public function izracunajCijenu($brojDana) {
        return $this->cijena * $brojDana;
    }
}

// Sada je puno jednostavnije kreirati objekte!
$auto1 = new Automobil("Fiat", "Punto", 50);
$auto2 = new Automobil("BMW", "320", 120);
$auto3 = new Automobil("Mercedes", "C200", 150);

// Lista automobila
$automobili = [$auto1, $auto2, $auto3];

?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Primjer 2 - Konstruktor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
    <h1>Primjer 2: Konstruktor</h1>
    <hr>

    <h4>Usporedba - PRIJE i POSLIJE konstruktora:</h4>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">❌ Bez konstruktora (zamorno)</div>
                <div class="card-body">
<pre><code>$auto = new Automobil();
$auto->marka = "Fiat";
$auto->model = "Punto";
$auto->cijena = 50;</code></pre>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-success">
                <div class="card-header bg-success text-white">✅ S konstruktorom (elegantno)</div>
                <div class="card-body">
<pre><code>$auto = new Automobil("Fiat", "Punto", 50);

// Jedna linija - sve je postavljeno!</code></pre>
                </div>
            </div>
        </div>
    </div>

    <h4>Svi automobili:</h4>
    <div class="row">
        <?php foreach ($automobili as $auto): ?>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><?= $auto->marka ?> <?= $auto->model ?></h5>
                    <p class="card-text"><?= $auto->predstaviSe() ?></p>
                    <p class="text-muted">
                        Cijena za 7 dana: <strong><?= $auto->izracunajCijenu(7) ?>€</strong>
                    </p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
