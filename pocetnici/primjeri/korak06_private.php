<?php
/**
 * KORAK 6: Private svojstva
 * ═══════════════════════════════════════════════════════════
 * Cilj: Razumjeti razliku public vs private
 */
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Korak 6 - Private</title>
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
        .private { color: #f44747; }
        .public { color: #4ec9b0; }
    </style>
</head>
<body class="container py-4">
    <nav class="mb-4">
        <a href="../index.php" class="btn btn-outline-secondary btn-sm">&larr; Natrag</a>
        <span class="badge bg-success ms-2">Korak 6 od 8</span>
    </nav>

    <h1>Korak 6: Private svojstva</h1>
    <p class="lead">Private = samo klasa može pristupiti. Izvana - NE MOŽE.</p>
    <hr>

    <!-- USPOREDBA -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card h-100 border-success">
                <div class="card-header bg-success text-white">PUBLIC - svi mogu</div>
                <div class="card-body">
                    <div class="code-box">
<span class="public">public</span> <span class="variable">$ime</span>;

<span style="color:#6a9955">// Izvana:</span>
<span class="variable">$obj</span>-><span class="variable">ime</span> = <span class="string">"Ana"</span>; <span style="color:#4ec9b0">// OK!</span>
<span class="keyword">echo</span> <span class="variable">$obj</span>-><span class="variable">ime</span>;      <span style="color:#4ec9b0">// OK!</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100 border-danger">
                <div class="card-header bg-danger text-white">PRIVATE - samo klasa</div>
                <div class="card-body">
                    <div class="code-box">
<span class="private">private</span> <span class="variable">$lozinka</span>;

<span style="color:#6a9955">// Izvana:</span>
<span class="variable">$obj</span>-><span class="variable">lozinka</span> = <span class="string">"123"</span>; <span style="color:#f44747">// GREŠKA!</span>
<span class="keyword">echo</span> <span class="variable">$obj</span>-><span class="variable">lozinka</span>;      <span style="color:#f44747">// GREŠKA!</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PRIMJER -->
    <h4>Primjer - zašto koristimo private:</h4>
    <div class="code-box mb-4">
<span class="keyword">class</span> Korisnik {
    <span class="public">public</span> <span class="variable">$ime</span>;         <span style="color:#6a9955">// Svi mogu vidjeti ime</span>
    <span class="private">private</span> <span class="variable">$lozinka</span>;    <span style="color:#6a9955">// Nitko ne smije vidjeti lozinku!</span>

    <span class="keyword">public function</span> __construct(<span class="variable">$ime</span>, <span class="variable">$lozinka</span>) {
        <span class="variable">$this</span>->ime = <span class="variable">$ime</span>;
        <span class="variable">$this</span>->lozinka = <span class="variable">$lozinka</span>;
    }

    <span style="color:#6a9955">// Samo KLASA može provjeriti lozinku</span>
    <span class="keyword">public function</span> provjeriLozinku(<span class="variable">$unos</span>) {
        <span class="keyword">return</span> <span class="variable">$this</span>->lozinka === <span class="variable">$unos</span>;
    }
}
    </div>

    <!-- REZULTAT -->
    <h4>Testiranje:</h4>
    <?php
    class Korisnik {
        public $ime;
        private $lozinka;

        public function __construct($ime, $lozinka) {
            $this->ime = $ime;
            $this->lozinka = $lozinka;
        }

        public function provjeriLozinku($unos) {
            return $this->lozinka === $unos;
        }
    }

    $user = new Korisnik("Ana", "tajna123");
    ?>

    <div class="alert alert-success">
        <strong>$user->ime:</strong> <?= $user->ime ?> <span class="badge bg-success">RADI - public</span>
    </div>

    <div class="alert alert-danger">
        <strong>$user->lozinka:</strong>
        <span class="text-danger">Fatal error: Cannot access private property</span>
        <span class="badge bg-danger">NE RADI - private</span>
    </div>

    <div class="alert alert-info">
        <strong>$user->provjeriLozinku("tajna123"):</strong>
        <?= $user->provjeriLozinku("tajna123") ? "true" : "false" ?>
        <span class="badge bg-info">RADI - preko metode</span>
    </div>

    <!-- ZAPAMTI -->
    <div class="alert alert-warning">
        <h5>Zapamti:</h5>
        <table class="table table-sm mb-0">
            <tr>
                <td><code>public</code></td>
                <td>Svi mogu pristupiti (unutar i izvan klase)</td>
            </tr>
            <tr>
                <td><code>private</code></td>
                <td>Samo ova klasa (samo $this-> unutar klase)</td>
            </tr>
        </table>
    </div>

    <!-- TVOJ RED -->
    <div class="card border-warning mt-4">
        <div class="card-header bg-warning">
            <strong>TVOJ RED:</strong>
        </div>
        <div class="card-body">
            <p>Napravi klasu <code>BankovniRacun</code>:</p>
            <ul>
                <li><code>public $vlasnik</code></li>
                <li><code>private $stanje</code> (počinje s 0)</li>
                <li>Metoda <code>uplati($iznos)</code> - dodaje na stanje</li>
                <li>Metoda <code>getStanje()</code> - vraća stanje</li>
            </ul>
            <p>Testiraj: pokušaj direktno pristupiti <code>$racun->stanje</code> - što se dogodi?</p>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-between">
        <a href="korak05_konstruktor.php" class="btn btn-outline-secondary">&larr; Prethodni</a>
        <a href="korak07_getter_setter.php" class="btn btn-success">Sljedeći &rarr;</a>
    </div>
</body>
</html>
