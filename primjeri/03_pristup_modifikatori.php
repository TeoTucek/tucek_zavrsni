<?php
/**
 * PRIMJER 3: Public, Private, Protected
 * Demonstrira razlike između modifikatora pristupa
 */

class BankovniRacun {
    // PUBLIC - svi mogu vidjeti i mijenjati
    public $vlasnik;

    // PRIVATE - samo metode OVE klase mogu pristupiti
    private $stanje = 0;
    private $pin;
    private $historijaTransakcija = [];

    public function __construct($vlasnik, $pin) {
        $this->vlasnik = $vlasnik;
        $this->pin = $pin;
        $this->dodajUHistoriju("Račun otvoren");
    }

    // GETTER - siguran način za čitanje privatnih podataka
    public function getStanje() {
        return $this->stanje;
    }

    public function getHistorija() {
        return $this->historijaTransakcija;
    }

    // Javne metode za siguran rad s privatnim podacima
    public function uplati($iznos) {
        if ($iznos > 0) {
            $this->stanje += $iznos;
            $this->dodajUHistoriju("Uplata: +{$iznos}€");
            return true;
        }
        return false;
    }

    public function podigni($iznos, $uneseniPin) {
        // Provjera PIN-a prije bilo kakve operacije!
        if ($uneseniPin !== $this->pin) {
            $this->dodajUHistoriju("Neuspjeli pokušaj podizanja - krivi PIN");
            return ["uspjeh" => false, "poruka" => "Krivi PIN!"];
        }

        if ($iznos > $this->stanje) {
            $this->dodajUHistoriju("Neuspjeli pokušaj podizanja - nedovoljno sredstava");
            return ["uspjeh" => false, "poruka" => "Nedovoljno sredstava!"];
        }

        if ($iznos <= 0) {
            return ["uspjeh" => false, "poruka" => "Iznos mora biti pozitivan!"];
        }

        $this->stanje -= $iznos;
        $this->dodajUHistoriju("Podizanje: -{$iznos}€");
        return ["uspjeh" => true, "poruka" => "Uspješno podignuto {$iznos}€"];
    }

    // PRIVATNA METODA - samo za internu upotrebu
    private function dodajUHistoriju($opis) {
        $this->historijaTransakcija[] = [
            "vrijeme" => date("H:i:s"),
            "opis" => $opis,
            "stanje" => $this->stanje
        ];
    }
}

// Testiranje
$racun = new BankovniRacun("Ivan Horvat", "1234");
$racun->uplati(1000);
$racun->uplati(500);
$rezultat1 = $racun->podigni(200, "1234");  // Ispravan PIN
$rezultat2 = $racun->podigni(100, "0000");  // Krivi PIN
$rezultat3 = $racun->podigni(5000, "1234"); // Nedovoljno sredstava

?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Primjer 3 - Modifikatori pristupa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
    <h1>Primjer 3: Public, Private, Protected</h1>
    <hr>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card h-100 border-success">
                <div class="card-header bg-success text-white">PUBLIC</div>
                <div class="card-body">
                    <p>Dostupno <strong>svima</strong> - i unutar i izvan klase.</p>
                    <code>$racun->vlasnik</code>
                    <p class="mt-2">Rezultat: <strong><?= $racun->vlasnik ?></strong></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-danger">
                <div class="card-header bg-danger text-white">PRIVATE</div>
                <div class="card-body">
                    <p>Dostupno <strong>samo unutar klase</strong>.</p>
                    <code>$racun->stanje</code> ❌
                    <p class="mt-2 text-danger">Fatal error: Cannot access private property</p>
                    <code>$racun->getStanje()</code> ✅
                    <p class="mt-2">Rezultat: <strong><?= $racun->getStanje() ?>€</strong></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-warning">
                <div class="card-header bg-warning">PROTECTED</div>
                <div class="card-body">
                    <p>Dostupno <strong>klasi i njenim nasljednicima</strong>.</p>
                    <p class="text-muted">Vidjet ćemo u primjeru s nasljeđivanjem!</p>
                </div>
            </div>
        </div>
    </div>

    <h4>Bankovni račun - <?= $racun->vlasnik ?></h4>
    <div class="alert alert-info">
        <strong>Trenutno stanje:</strong> <?= $racun->getStanje() ?>€
    </div>

    <h5>Testiranje operacija:</h5>
    <table class="table table-bordered">
        <tr>
            <td><code>podigni(200, "1234")</code></td>
            <td class="<?= $rezultat1['uspjeh'] ? 'text-success' : 'text-danger' ?>">
                <?= $rezultat1['poruka'] ?>
            </td>
        </tr>
        <tr>
            <td><code>podigni(100, "0000")</code></td>
            <td class="<?= $rezultat2['uspjeh'] ? 'text-success' : 'text-danger' ?>">
                <?= $rezultat2['poruka'] ?>
            </td>
        </tr>
        <tr>
            <td><code>podigni(5000, "1234")</code></td>
            <td class="<?= $rezultat3['uspjeh'] ? 'text-success' : 'text-danger' ?>">
                <?= $rezultat3['poruka'] ?>
            </td>
        </tr>
    </table>

    <h5>Povijest transakcija:</h5>
    <table class="table table-sm table-striped">
        <thead>
            <tr><th>Vrijeme</th><th>Opis</th><th>Stanje</th></tr>
        </thead>
        <tbody>
            <?php foreach ($racun->getHistorija() as $t): ?>
            <tr>
                <td><?= $t['vrijeme'] ?></td>
                <td><?= $t['opis'] ?></td>
                <td><?= $t['stanje'] ?>€</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="mt-4 p-3 bg-light">
        <h5>Zašto koristiti PRIVATE?</h5>
        <ul>
            <li><strong>Sigurnost</strong> - nitko ne može direktno promijeniti stanje računa</li>
            <li><strong>Validacija</strong> - sve prolazi kroz metode koje provjeravaju ispravnost</li>
            <li><strong>Enkapsulacija</strong> - skrivamo internu implementaciju</li>
        </ul>
    </div>
</body>
</html>
