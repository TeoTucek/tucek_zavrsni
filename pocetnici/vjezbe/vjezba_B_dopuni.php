<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Vježba B - Dopuni kod</title>
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
        }
        .blank {
            background: #ffeb3b;
            color: #000;
            padding: 2px 20px;
            border-radius: 3px;
            font-weight: bold;
        }
        .task-number {
            width: 50px;
            height: 50px;
            background: #ffc107;
            color: #000;
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

    <h1>Vježba B: Dopuni kod</h1>
    <p class="lead">Nedostaju dijelovi koda (označeni žuto). Prepiši kod i dopuni praznine!</p>

    <div class="alert alert-warning">
        <strong>Upute:</strong> Prepiši cijeli kod u novu datoteku i zamijeni <span class="blank">???</span> s ispravnim kodom.
    </div>

    <hr>

    <!-- ZADATAK 1 -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <div class="task-number me-3">1</div>
                <h5 class="mb-0">Dopuni definiciju klase</h5>
            </div>
            <div class="code-box">&lt;?php
<span class="blank">???</span> Telefon {
    <span class="blank">???</span> $marka;
    public $model;
}

$tel = <span class="blank">???</span> Telefon();
$tel->marka = "Samsung";
$tel->model = "Galaxy S21";

echo $tel->marka;
?&gt;</div>
            <p class="mt-2 text-muted">Hint: Kako se definira klasa? Kako se definira svojstvo? Kako se kreira objekt?</p>
        </div>
    </div>

    <!-- ZADATAK 2 -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <div class="task-number me-3">2</div>
                <h5 class="mb-0">Dopuni pristup svojstvima</h5>
            </div>
            <div class="code-box">&lt;?php
class Student {
    public $ime;
    public $prosjek;

    public function status() {
        if (<span class="blank">???</span>->prosjek >= 4.0) {
            return <span class="blank">???</span>->ime . " je odlican";
        }
        return <span class="blank">???</span>->ime . " nije odlican";
    }
}

$s = new Student();
$s<span class="blank">???</span>ime = "Ana";
$s<span class="blank">???</span>prosjek = 4.5;

echo $s<span class="blank">???</span>status();
?&gt;</div>
            <p class="mt-2 text-muted">Hint: Kako se pristupa svojstvima iznutra? Kako izvana?</p>
        </div>
    </div>

    <!-- ZADATAK 3 -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <div class="task-number me-3">3</div>
                <h5 class="mb-0">Dopuni konstruktor</h5>
            </div>
            <div class="code-box">&lt;?php
class Pravokutnik {
    public $sirina;
    public $visina;

    public function <span class="blank">???</span>($sirina, $visina) {
        <span class="blank">???</span>->sirina = $sirina;
        $this-><span class="blank">???</span> = $visina;
    }

    public function povrsina() {
        return $this->sirina * $this->visina;
    }
}

$p = new Pravokutnik(10, 5);
echo "Povrsina: " . $p->povrsina();
?&gt;</div>
            <p class="mt-2 text-muted">Hint: Kako se zove metoda koja se automatski poziva kod "new"?</p>
        </div>
    </div>

    <!-- ZADATAK 4 -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <div class="task-number me-3">4</div>
                <h5 class="mb-0">Dopuni private i getter</h5>
            </div>
            <div class="code-box">&lt;?php
class Osoba {
    public $ime;
    <span class="blank">???</span> $godine;

    public function __construct($ime, $godine) {
        $this->ime = $ime;
        $this->godine = $godine;
    }

    public function <span class="blank">???</span>() {
        return $this->godine;
    }

    public function setGodine($godine) {
        if ($godine > 0) {
            <span class="blank">???</span>->godine = $godine;
        }
    }
}

$o = new Osoba("Marko", 25);
echo $o->ime . " ima " . $o-><span class="blank">???</span>() . " godina";
?&gt;</div>
            <p class="mt-2 text-muted">Hint: Kako sakriti svojstvo? Kako se zove metoda za čitanje?</p>
        </div>
    </div>

    <!-- ZADATAK 5 -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <div class="task-number me-3">5</div>
                <h5 class="mb-0">Dopuni nasljeđivanje</h5>
            </div>
            <div class="code-box">&lt;?php
class Vozilo {
    public $marka;

    public function __construct($marka) {
        $this->marka = $marka;
    }
}

class Auto <span class="blank">???</span> Vozilo {
    public $vrata;

    public function __construct($marka, $vrata) {
        <span class="blank">???</span>::__construct($marka);
        $this->vrata = $vrata;
    }
}

$a = new Auto("BMW", 4);
echo $a->marka . " ima " . $a->vrata . " vrata";
?&gt;</div>
            <p class="mt-2 text-muted">Hint: Kako klasa nasljeđuje drugu? Kako pozivamo roditeljev konstruktor?</p>
        </div>
    </div>

    <!-- RJEŠENJA -->
    <div class="card mt-4">
        <div class="card-header bg-secondary text-white" data-bs-toggle="collapse" data-bs-target="#rjesenja" style="cursor:pointer">
            <strong>Klikni za rješenja</strong> (samo ako si zapeo/la!)
        </div>
        <div class="collapse" id="rjesenja">
            <div class="card-body">
                <ol>
                    <li><code>class</code>, <code>public</code>, <code>new</code></li>
                    <li><code>$this</code> (3x unutar klase), <code>-></code> (3x izvana)</li>
                    <li><code>__construct</code>, <code>$this</code>, <code>visina</code></li>
                    <li><code>private</code>, <code>getGodine</code>, <code>$this</code>, <code>getGodine</code></li>
                    <li><code>extends</code>, <code>parent</code></li>
                </ol>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="vjezba_C_greske.php" class="btn btn-warning">Idi na Vježbu C &rarr;</a>
    </div>
</body>
</html>
