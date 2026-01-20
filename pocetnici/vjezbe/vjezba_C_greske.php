<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Vježba C - Pronađi greške</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .code-box {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 20px;
            border-radius: 8px;
            font-family: 'Consolas', monospace;
            font-size: 14px;
            line-height: 1.8;
            border: 2px solid #dc3545;
        }
        .task-number {
            width: 50px;
            height: 50px;
            background: #dc3545;
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

    <h1>Vježba C: Pronađi greške</h1>
    <p class="lead">Svaki kod ima 1-3 greške. Pronađi ih i ispravi!</p>

    <div class="alert alert-danger">
        <strong>Zadatak:</strong> Prepiši kod, pokreni ga, vidi grešku, ispravi i ponovi dok ne radi.
    </div>

    <hr>

    <!-- GREŠKA 1 -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <div class="task-number me-3">1</div>
                <div>
                    <h5 class="mb-0">1 greška</h5>
                    <small class="text-muted">Definicija klase</small>
                </div>
            </div>
            <div class="code-box">&lt;?php
Class Zivotinja {
    public $ime;
}

$z = new Zivotinja();
$z->ime = "Rex";
echo $z->ime;
?&gt;</div>
            <p class="mt-2 text-muted">Hint: PHP je case-sensitive za neke stvari...</p>
        </div>
    </div>

    <!-- GREŠKA 2 -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <div class="task-number me-3">2</div>
                <div>
                    <h5 class="mb-0">1 greška</h5>
                    <small class="text-muted">Kreiranje objekta</small>
                </div>
            </div>
            <div class="code-box">&lt;?php
class Tocka {
    public $x;
    public $y;
}

$t = Tocka();
$t->x = 10;
$t->y = 20;
echo $t->x;
?&gt;</div>
            <p class="mt-2 text-muted">Hint: Što nedostaje kad kreiramo objekt?</p>
        </div>
    </div>

    <!-- GREŠKA 3 -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <div class="task-number me-3">3</div>
                <div>
                    <h5 class="mb-0">1 greška</h5>
                    <small class="text-muted">Pristup svojstvu izvana</small>
                </div>
            </div>
            <div class="code-box">&lt;?php
class Broj {
    public $vrijednost;
}

$b = new Broj();
$b.vrijednost = 42;
echo $b.vrijednost;
?&gt;</div>
            <p class="mt-2 text-muted">Hint: Kako se u PHP-u pristupa svojstvima objekta?</p>
        </div>
    </div>

    <!-- GREŠKA 4 -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <div class="task-number me-3">4</div>
                <div>
                    <h5 class="mb-0">2 greške</h5>
                    <small class="text-muted">$this unutar metode</small>
                </div>
            </div>
            <div class="code-box">&lt;?php
class Pozdrav {
    public $ime;

    public function reci() {
        return "Bok, " . $ime . "!";
    }
}

$p = new Pozdrav();
$p->ime = "Ana";
echo $p.reci();
?&gt;</div>
            <p class="mt-2 text-muted">Hint: Kako metoda pristupa svojstvu? Kako pozivamo metodu?</p>
        </div>
    </div>

    <!-- GREŠKA 5 -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <div class="task-number me-3">5</div>
                <div>
                    <h5 class="mb-0">2 greške</h5>
                    <small class="text-muted">Konstruktor</small>
                </div>
            </div>
            <div class="code-box">&lt;?php
class Osoba {
    public $ime;

    public function _construct($ime) {
        this->ime = $ime;
    }
}

$o = new Osoba("Marko");
echo $o->ime;
?&gt;</div>
            <p class="mt-2 text-muted">Hint: Koliko donjih crtica ima konstruktor? I još nešto nedostaje...</p>
        </div>
    </div>

    <!-- GREŠKA 6 -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <div class="task-number me-3">6</div>
                <div>
                    <h5 class="mb-0">2 greške</h5>
                    <small class="text-muted">Private pristup</small>
                </div>
            </div>
            <div class="code-box">&lt;?php
class Tajna {
    private $kod;

    public function __construct($kod) {
        $this->kod = $kod;
    }
}

$t = new Tajna("ABC123");
echo $t->kod;
echo $t->getKod();
?&gt;</div>
            <p class="mt-2 text-muted">Hint: Može li se private svojstvu pristupiti izvana? Postoji li getter?</p>
        </div>
    </div>

    <!-- GREŠKA 7 -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <div class="task-number me-3">7</div>
                <div>
                    <h5 class="mb-0">3 greške</h5>
                    <small class="text-muted">Nasljeđivanje</small>
                </div>
            </div>
            <div class="code-box">&lt;?php
class Vozilo {
    public $marka;

    public function __construct($marka) {
        $this->marka = $marka;
    }
}

class Auto extend Vozilo {
    public $vrata;

    public function __construct($marka, $vrata) {
        parent._construct($marka);
        $this->vrata = $vrata;
    }
}

$a = new Auto("Audi", 4);
echo $a->marka;
?&gt;</div>
            <p class="mt-2 text-muted">Hint: extends ili extend? Kako se poziva parent metoda?</p>
        </div>
    </div>

    <!-- RJEŠENJA -->
    <div class="card mt-4">
        <div class="card-header bg-secondary text-white" data-bs-toggle="collapse" data-bs-target="#rjesenja" style="cursor:pointer">
            <strong>Klikni za rješenja</strong>
        </div>
        <div class="collapse" id="rjesenja">
            <div class="card-body">
                <ol>
                    <li><code>Class</code> → <code>class</code> (malo slovo)</li>
                    <li><code>Tocka()</code> → <code>new Tocka()</code> (nedostaje new)</li>
                    <li><code>$b.vrijednost</code> → <code>$b->vrijednost</code> (strelica, ne točka)</li>
                    <li><code>$ime</code> → <code>$this->ime</code> i <code>$p.reci()</code> → <code>$p->reci()</code></li>
                    <li><code>_construct</code> → <code>__construct</code> (dvije crte) i <code>this</code> → <code>$this</code> (dolar)</li>
                    <li>Ne može se pristupiti private izvana + nema getKod() metode - treba ju dodati</li>
                    <li><code>extend</code> → <code>extends</code>, <code>parent._construct</code> → <code>parent::__construct</code></li>
                </ol>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="vjezba_D_napisi.php" class="btn btn-danger">Idi na Vježbu D &rarr;</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
