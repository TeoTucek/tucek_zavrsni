<?php
/**
 * KORAK 8: Nasljeđivanje (extends)
 * ═══════════════════════════════════════════════════════════
 * Cilj: Naučiti kako jedna klasa nasljeđuje drugu
 */
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Korak 8 - Extends</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .code-box {
            background: #1e1e1e;
            color: #9cdcfe;
            padding: 20px;
            border-radius: 8px;
            font-family: 'Consolas', monospace;
            font-size: 15px;
            line-height: 1.8;
        }
        .keyword { color: #569cd6; }
        .variable { color: #9cdcfe; }
        .string { color: #ce9178; }
        .extends { color: #c586c0; }
        .comment { color: #6a9955; }
    </style>
</head>
<body class="container py-4">
    <nav class="mb-4">
        <a href="../index.php" class="btn btn-outline-secondary btn-sm">&larr; Natrag</a>
        <span class="badge bg-success ms-2">Korak 8 od 8 - ZADNJI!</span>
    </nav>

    <h1>Korak 8: Nasljeđivanje <code>extends</code></h1>
    <p class="lead">Dijete nasljeđuje sve od roditelja + može dodati svoje.</p>
    <hr>

    <!-- DIJAGRAM -->
    <div class="card bg-light mb-4">
        <div class="card-body text-center">
            <pre class="mb-0" style="display: inline-block; text-align: left;">
         ┌─────────────┐
         │   Zivotinja │  ← RODITELJ
         │─────────────│
         │ $ime        │
         │ jedi()      │
         └──────┬──────┘
                │ extends
         ┌──────┴──────┐
         │     Pas     │  ← DIJETE
         │─────────────│
         │ $rasa       │  (ima svoje + naslijeđeno)
         │ laj()       │
         └─────────────┘
            </pre>
        </div>
    </div>

    <!-- KOD -->
    <h4>Kod:</h4>
    <div class="code-box mb-4">
<span class="comment">// RODITELJ</span>
<span class="keyword">class</span> Zivotinja {
    <span class="keyword">public</span> <span class="variable">$ime</span>;

    <span class="keyword">public function</span> __construct(<span class="variable">$ime</span>) {
        <span class="variable">$this</span>->ime = <span class="variable">$ime</span>;
    }

    <span class="keyword">public function</span> jedi() {
        <span class="keyword">return</span> <span class="variable">$this</span>->ime . <span class="string">" jede."</span>;
    }
}

<span class="comment">// DIJETE - nasljeđuje Zivotinja</span>
<span class="keyword">class</span> Pas <span class="extends">extends</span> Zivotinja {
    <span class="keyword">public</span> <span class="variable">$rasa</span>;

    <span class="keyword">public function</span> __construct(<span class="variable">$ime</span>, <span class="variable">$rasa</span>) {
        <span class="extends">parent</span>::__construct(<span class="variable">$ime</span>);  <span class="comment">// Pozovi roditeljev konstruktor!</span>
        <span class="variable">$this</span>->rasa = <span class="variable">$rasa</span>;
    }

    <span class="comment">// Nova metoda samo za Pas</span>
    <span class="keyword">public function</span> laj() {
        <span class="keyword">return</span> <span class="variable">$this</span>->ime . <span class="string">" laje: Vau vau!"</span>;
    }
}

<span class="variable">$rex</span> = <span class="keyword">new</span> Pas(<span class="string">"Rex"</span>, <span class="string">"Njemački ovčar"</span>);
<span class="keyword">echo</span> <span class="variable">$rex</span>->jedi();  <span class="comment">// Naslijeđeno od Zivotinja!</span>
<span class="keyword">echo</span> <span class="variable">$rex</span>->laj();   <span class="comment">// Vlastita metoda</span>
    </div>

    <!-- REZULTAT -->
    <h4>Rezultat:</h4>
    <?php
    class Zivotinja {
        public $ime;

        public function __construct($ime) {
            $this->ime = $ime;
        }

        public function jedi() {
            return $this->ime . " jede.";
        }
    }

    class Pas extends Zivotinja {
        public $rasa;

        public function __construct($ime, $rasa) {
            parent::__construct($ime);
            $this->rasa = $rasa;
        }

        public function laj() {
            return $this->ime . " laje: Vau vau!";
        }
    }

    $rex = new Pas("Rex", "Njemački ovčar");
    ?>

    <div class="alert alert-success">
        <strong>$rex->ime:</strong> <?= $rex->ime ?> <small class="text-muted">(naslijeđeno)</small><br>
        <strong>$rex->rasa:</strong> <?= $rex->rasa ?> <small class="text-muted">(vlastito)</small><br>
        <strong>$rex->jedi():</strong> <?= $rex->jedi() ?> <small class="text-muted">(naslijeđeno)</small><br>
        <strong>$rex->laj():</strong> <?= $rex->laj() ?> <small class="text-muted">(vlastito)</small>
    </div>

    <!-- KLJUČNE RIJEČI -->
    <div class="alert alert-info">
        <h5>Ključne riječi:</h5>
        <table class="table table-sm mb-0">
            <tr>
                <td><code>extends</code></td>
                <td>Klasa nasljeđuje drugu</td>
                <td><code>class Pas extends Zivotinja</code></td>
            </tr>
            <tr>
                <td><code>parent::</code></td>
                <td>Poziva nešto od roditelja</td>
                <td><code>parent::__construct($ime)</code></td>
            </tr>
        </table>
    </div>

    <!-- ČESTITKA -->
    <div class="card border-success mt-4">
        <div class="card-header bg-success text-white">
            <strong>ČESTITAMO!</strong>
        </div>
        <div class="card-body">
            <p>Prošao/la si sve korake! Sada znaš:</p>
            <ul>
                <li>Kako napraviti klasu i objekt</li>
                <li>Kako napisati svojstva i metode</li>
                <li>Što je <code>$this</code></li>
                <li>Kako radi konstruktor</li>
                <li>Razliku public/private</li>
                <li>Getter i setter metode</li>
                <li>Nasljeđivanje s extends</li>
            </ul>
            <a href="../vjezbe/vjezba_A_prepisi.php" class="btn btn-success">
                Sada vježbaj! &rarr;
            </a>
        </div>
    </div>

    <div class="mt-4">
        <a href="korak07_getter_setter.php" class="btn btn-outline-secondary">&larr; Prethodni</a>
    </div>
</body>
</html>
