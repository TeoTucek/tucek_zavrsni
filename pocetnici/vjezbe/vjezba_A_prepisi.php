<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Vježba A - Prepiši i pokreni</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .code-to-copy {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 20px;
            border-radius: 8px;
            font-family: 'Consolas', monospace;
            font-size: 14px;
            line-height: 1.6;
            white-space: pre;
        }
        .task-number {
            width: 50px;
            height: 50px;
            background: #198754;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
        }
    </style>
</head>
<body class="container py-4">
    <a href="../index.php" class="btn btn-outline-secondary btn-sm mb-4">&larr; Natrag</a>

    <h1>Vježba A: Prepiši i pokreni</h1>
    <p class="lead">Prepiši svaku klasu RUČNO. Bez copy-paste! Cilj je da ti prsti zapamte sintaksu.</p>

    <div class="alert alert-warning">
        <strong>Pravila:</strong>
        <ol class="mb-0">
            <li>Otvori novi PHP file za svaki zadatak</li>
            <li>Prepiši kod GLEDAJUĆI, ali PIŠUĆI sam</li>
            <li>Pokreni i provjeri radi li</li>
            <li>Tek kad radi, idi na sljedeći</li>
        </ol>
    </div>

    <hr>

    <!-- ZADATAK 1 -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <div class="task-number me-3">1</div>
                <div>
                    <h5 class="mb-0">Boja</h5>
                    <small class="text-muted">Klasa + svojstvo + ispis</small>
                </div>
            </div>
            <div class="code-to-copy">&lt;?php
class Boja {
    public $naziv;
}

$b = new Boja();
$b->naziv = "plava";
echo "Boja je: " . $b->naziv;
?&gt;</div>
            <p class="mt-2 mb-0"><strong>Očekivani rezultat:</strong> Boja je: plava</p>
        </div>
    </div>

    <!-- ZADATAK 2 -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <div class="task-number me-3">2</div>
                <div>
                    <h5 class="mb-0">Igrac</h5>
                    <small class="text-muted">Više svojstava</small>
                </div>
            </div>
            <div class="code-to-copy">&lt;?php
class Igrac {
    public $ime;
    public $bodovi;
    public $zivoti;
}

$igrac1 = new Igrac();
$igrac1->ime = "Player1";
$igrac1->bodovi = 100;
$igrac1->zivoti = 3;

echo $igrac1->ime . " ima " . $igrac1->bodovi . " bodova";
?&gt;</div>
            <p class="mt-2 mb-0"><strong>Očekivani rezultat:</strong> Player1 ima 100 bodova</p>
        </div>
    </div>

    <!-- ZADATAK 3 -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <div class="task-number me-3">3</div>
                <div>
                    <h5 class="mb-0">Krug</h5>
                    <small class="text-muted">Svojstvo + metoda + $this</small>
                </div>
            </div>
            <div class="code-to-copy">&lt;?php
class Krug {
    public $radius;

    public function opseg() {
        return 2 * 3.14 * $this->radius;
    }

    public function povrsina() {
        return 3.14 * $this->radius * $this->radius;
    }
}

$k = new Krug();
$k->radius = 5;
echo "Opseg: " . $k->opseg();
echo " Povrsina: " . $k->povrsina();
?&gt;</div>
            <p class="mt-2 mb-0"><strong>Očekivani rezultat:</strong> Opseg: 31.4 Povrsina: 78.5</p>
        </div>
    </div>

    <!-- ZADATAK 4 -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <div class="task-number me-3">4</div>
                <div>
                    <h5 class="mb-0">Knjiga</h5>
                    <small class="text-muted">Konstruktor</small>
                </div>
            </div>
            <div class="code-to-copy">&lt;?php
class Knjiga {
    public $naslov;
    public $autor;
    public $stranica;

    public function __construct($naslov, $autor, $stranica) {
        $this->naslov = $naslov;
        $this->autor = $autor;
        $this->stranica = $stranica;
    }

    public function info() {
        return $this->naslov . " - " . $this->autor;
    }
}

$knjiga1 = new Knjiga("Harry Potter", "J.K. Rowling", 350);
$knjiga2 = new Knjiga("Gospodar prstenova", "Tolkien", 500);

echo $knjiga1->info();
?&gt;</div>
            <p class="mt-2 mb-0"><strong>Očekivani rezultat:</strong> Harry Potter - J.K. Rowling</p>
        </div>
    </div>

    <!-- ZADATAK 5 -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <div class="task-number me-3">5</div>
                <div>
                    <h5 class="mb-0">Sat</h5>
                    <small class="text-muted">Private + getter/setter</small>
                </div>
            </div>
            <div class="code-to-copy">&lt;?php
class Sat {
    private $sati;
    private $minute;

    public function __construct($sati, $minute) {
        $this->setSati($sati);
        $this->setMinute($minute);
    }

    public function getSati() {
        return $this->sati;
    }

    public function setSati($sati) {
        if ($sati >= 0 && $sati <= 23) {
            $this->sati = $sati;
        }
    }

    public function getMinute() {
        return $this->minute;
    }

    public function setMinute($minute) {
        if ($minute >= 0 && $minute <= 59) {
            $this->minute = $minute;
        }
    }

    public function prikazi() {
        return $this->sati . ":" . $this->minute;
    }
}

$sat = new Sat(14, 30);
echo "Vrijeme: " . $sat->prikazi();
?&gt;</div>
            <p class="mt-2 mb-0"><strong>Očekivani rezultat:</strong> Vrijeme: 14:30</p>
        </div>
    </div>

    <!-- PROVJERA -->
    <div class="alert alert-success">
        <h5>Gotovo?</h5>
        <p>Ako si sve prepisao/la i sve radi, znači da ti ruke već znaju sintaksu!</p>
        <a href="vjezba_B_dopuni.php" class="btn btn-success">Idi na Vježbu B &rarr;</a>
    </div>
</body>
</html>
