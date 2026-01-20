<?php
/**
 * PRIMJER 1: Prva klasa i objekti
 */

class Automobil {
    // SVOJSTVA (properties) - podaci koje auto ima
    public $marka;
    public $model;
    public $cijena;

    // METODA (method) - što auto može raditi
    public function predstaviSe() {
        return "Ja sam " . $this->marka . " " . $this->model;
    }
}

// Kreiramo OBJEKTE iz klase
$auto1 = new Automobil();
$auto1->marka = "Fiat";
$auto1->model = "Punto";
$auto1->cijena = 50;

$auto2 = new Automobil();
$auto2->marka = "BMW";
$auto2->model = "320";
$auto2->cijena = 120;

echo $auto1->predstaviSe();
echo "<br>";
echo $auto2->predstaviSe();

?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Primjer 1 - Prva klasa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
    <h1>Primjer 1: Klasa i objekti</h1>
    <hr>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">Auto 1</div>
                <div class="card-body">
                    <p><strong>Marka:</strong> <?= $auto1->marka ?></p>
                    <p><strong>Model:</strong> <?= $auto1->model ?></p>
                    <p><strong>Cijena:</strong> <?= $auto1->cijena ?>€/dan</p>
                    <p class="alert alert-info"><?= $auto1->predstaviSe() ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">Auto 2</div>
                <div class="card-body">
                    <p><strong>Marka:</strong> <?= $auto2->marka ?></p>
                    <p><strong>Model:</strong> <?= $auto2->model ?></p>
                    <p><strong>Cijena:</strong> <?= $auto2->cijena ?>€/dan</p>
                    <p class="alert alert-info"><?= $auto2->predstaviSe() ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 p-3 bg-light">
        <h5>Što smo naučili:</h5>
        <ul>
            <li><code>class</code> - ključna riječ za definiranje klase</li>
            <li><code>public</code> - svojstvo/metoda dostupna svima</li>
            <li><code>$this</code> - referenca na trenutni objekt</li>
            <li><code>new</code> - kreiranje novog objekta</li>
            <li><code>-></code> - pristup svojstvima i metodama objekta</li>
        </ul>
    </div>
</body>
</html>
