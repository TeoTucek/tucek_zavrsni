<?php
/**
 * ╔═══════════════════════════════════════════════════════════════╗
 * ║                  RJEŠENJE ZADATKA 1                           ║
 * ║                   Klasa Korisnik                              ║
 * ╚═══════════════════════════════════════════════════════════════╝
 */

class Korisnik {
    // Svojstva (public - dostupna svima)
    public $ime;
    public $prezime;
    public $email;

    // Metoda koja vraća pozdrav
    public function pozdrav() {
        return "Bok, ja sam " . $this->ime . " " . $this->prezime;
    }

    // BONUS: Getter za email
    public function getEmail() {
        return $this->email;
    }
}

// Kreiranje objekata
$korisnik1 = new Korisnik();
$korisnik1->ime = "Ivan";
$korisnik1->prezime = "Horvat";
$korisnik1->email = "ivan@mail.com";

$korisnik2 = new Korisnik();
$korisnik2->ime = "Ana";
$korisnik2->prezime = "Kovacic";
$korisnik2->email = "ana@mail.com";

?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Rješenje 1 - Korisnik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
    <h1>Rješenje zadatka 1: Klasa Korisnik</h1>
    <span class="badge bg-success">RJEŠENJE</span>
    <hr>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">Korisnik 1</div>
                <div class="card-body">
                    <p><strong>Ime:</strong> <?= $korisnik1->ime ?></p>
                    <p><strong>Prezime:</strong> <?= $korisnik1->prezime ?></p>
                    <p><strong>Email:</strong> <?= $korisnik1->getEmail() ?></p>
                    <div class="alert alert-info">
                        <?= $korisnik1->pozdrav() ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">Korisnik 2</div>
                <div class="card-body">
                    <p><strong>Ime:</strong> <?= $korisnik2->ime ?></p>
                    <p><strong>Prezime:</strong> <?= $korisnik2->prezime ?></p>
                    <p><strong>Email:</strong> <?= $korisnik2->getEmail() ?></p>
                    <div class="alert alert-info">
                        <?= $korisnik2->pozdrav() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 p-3 bg-light">
        <h5>Kod rješenja:</h5>
        <pre><code>&lt;?php
class Korisnik {
    public $ime;
    public $prezime;
    public $email;

    public function pozdrav() {
        return "Bok, ja sam " . $this->ime . " " . $this->prezime;
    }

    public function getEmail() {
        return $this->email;
    }
}

$korisnik1 = new Korisnik();
$korisnik1->ime = "Ivan";
$korisnik1->prezime = "Horvat";
$korisnik1->email = "ivan@mail.com";

echo $korisnik1->pozdrav(); // Bok, ja sam Ivan Horvat
?&gt;</code></pre>
    </div>
</body>
</html>
