<?php
/**
 * KORAK 5: Konstruktor
 * ═══════════════════════════════════════════════════════════
 * Cilj: Naučiti __construct() - automatsko postavljanje vrijednosti
 */
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Korak 5 - Konstruktor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .code-box {
            background: #1e1e1e;
            color: #9cdcfe;
            padding: 20px;
            border-radius: 8px;
            font-family: 'Consolas', monospace;
            font-size: 16px;
            line-height: 1.8;
        }
        .keyword { color: #569cd6; }
        .variable { color: #9cdcfe; }
        .string { color: #ce9178; }
        .function { color: #dcdcaa; }
        .magic { color: #c586c0; }
    </style>
</head>
<body class="container py-4">
    <nav class="mb-4">
        <a href="../index.php" class="btn btn-outline-secondary btn-sm">&larr; Natrag</a>
        <span class="badge bg-success ms-2">Korak 5 od 8</span>
    </nav>

    <h1>Korak 5: Konstruktor <code>__construct()</code></h1>
    <p class="lead">Konstruktor se automatski poziva kad napišeš <code>new</code>.</p>
    <hr>

    <!-- PRIJE I POSLIJE -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h5>BEZ konstruktora (zamorno):</h5>
            <div class="code-box">
<span class="variable">$auto</span> = <span class="keyword">new</span> Auto();
<span class="variable">$auto</span>->marka = <span class="string">"Fiat"</span>;
<span class="variable">$auto</span>->model = <span class="string">"Punto"</span>;
<span class="variable">$auto</span>->godina = <span class="number">2019</span>;

<span style="color:#6a9955">// 4 linije koda...</span>
            </div>
        </div>
        <div class="col-md-6">
            <h5>S konstruktorom (elegantno):</h5>
            <div class="code-box" style="border: 2px solid #28a745;">
<span class="variable">$auto</span> = <span class="keyword">new</span> Auto(<span class="string">"Fiat"</span>, <span class="string">"Punto"</span>, <span class="number">2019</span>);

<span style="color:#6a9955">// 1 linija koda!</span>
            </div>
        </div>
    </div>

    <!-- KAKO NAPISATI -->
    <h4>Kako napisati konstruktor:</h4>
    <div class="code-box mb-4">
<span class="keyword">class</span> Auto {
    <span class="keyword">public</span> <span class="variable">$marka</span>;
    <span class="keyword">public</span> <span class="variable">$model</span>;
    <span class="keyword">public</span> <span class="variable">$godina</span>;

    <span style="color:#6a9955">// Konstruktor - DVA DONJA CRTICA!</span>
    <span class="keyword">public function</span> <span class="magic">__construct</span>(<span class="variable">$marka</span>, <span class="variable">$model</span>, <span class="variable">$godina</span>) {
        <span class="variable">$this</span>-><span class="variable">marka</span> = <span class="variable">$marka</span>;
        <span class="variable">$this</span>-><span class="variable">model</span> = <span class="variable">$model</span>;
        <span class="variable">$this</span>-><span class="variable">godina</span> = <span class="variable">$godina</span>;
    }

    <span class="keyword">public function</span> <span class="function">info</span>() {
        <span class="keyword">return</span> <span class="variable">$this</span>->marka . <span class="string">" "</span> . <span class="variable">$this</span>->model . <span class="string">" ("</span> . <span class="variable">$this</span>->godina . <span class="string">")"</span>;
    }
}

<span class="variable">$auto1</span> = <span class="keyword">new</span> Auto(<span class="string">"Fiat"</span>, <span class="string">"Punto"</span>, <span class="number">2019</span>);
<span class="variable">$auto2</span> = <span class="keyword">new</span> Auto(<span class="string">"BMW"</span>, <span class="string">"320"</span>, <span class="number">2021</span>);

<span class="keyword">echo</span> <span class="variable">$auto1</span>-><span class="function">info</span>();
<span class="keyword">echo</span> <span class="variable">$auto2</span>-><span class="function">info</span>();
    </div>

    <!-- REZULTAT -->
    <h4>Rezultat:</h4>
    <div class="alert alert-success">
        <?php
        class Auto {
            public $marka;
            public $model;
            public $godina;

            public function __construct($marka, $model, $godina) {
                $this->marka = $marka;
                $this->model = $model;
                $this->godina = $godina;
            }

            public function info() {
                return $this->marka . " " . $this->model . " (" . $this->godina . ")";
            }
        }

        $auto1 = new Auto("Fiat", "Punto", 2019);
        $auto2 = new Auto("BMW", "320", 2021);

        echo $auto1->info() . "<br>";
        echo $auto2->info();
        ?>
    </div>

    <!-- ZAPAMTI -->
    <div class="alert alert-danger">
        <h5>PAZI:</h5>
        <ul class="mb-0">
            <li><code>__construct</code> - <strong>DVA</strong> donja crtica na početku!</li>
            <li>Parametar <code>$marka</code> i svojstvo <code>$this->marka</code> - <strong>NISU ISTO!</strong></li>
            <li>U konstruktoru kopiramo parametre u svojstva</li>
        </ul>
    </div>

    <!-- VIZUALIZACIJA -->
    <div class="card bg-light mb-4">
        <div class="card-body">
            <h5>Što se događa:</h5>
            <pre class="mb-0">
new Auto("Fiat", "Punto", 2019)
    │
    └── AUTOMATSKI poziva __construct("Fiat", "Punto", 2019)
        │
        ├── $this->marka = "Fiat";
        ├── $this->model = "Punto";
        └── $this->godina = 2019;
            </pre>
        </div>
    </div>

    <!-- TVOJ RED -->
    <div class="card border-warning mt-4">
        <div class="card-header bg-warning">
            <strong>TVOJ RED:</strong>
        </div>
        <div class="card-body">
            <p>Napravi klasu <code>Ucenik</code> s konstruktorom:</p>
            <ul>
                <li>Svojstva: <code>$ime</code>, <code>$prezime</code>, <code>$razred</code></li>
                <li>Konstruktor prima sva 3 parametra</li>
                <li>Metoda <code>info()</code> vraća: <code>"[ime] [prezime] - [razred]"</code></li>
            </ul>
            <p>Kreiraj 3 učenika u jednoj liniji svaki!</p>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-between">
        <a href="korak04_this.php" class="btn btn-outline-secondary">&larr; Prethodni</a>
        <a href="korak06_private.php" class="btn btn-success">Sljedeći &rarr;</a>
    </div>
</body>
</html>
