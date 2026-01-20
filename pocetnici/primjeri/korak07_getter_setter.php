<?php
/**
 * KORAK 7: Getter i Setter
 * ═══════════════════════════════════════════════════════════
 * Cilj: Siguran pristup privatnim podacima s validacijom
 */
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Korak 7 - Getter i Setter</title>
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
        .function { color: #dcdcaa; }
        .comment { color: #6a9955; }
    </style>
</head>
<body class="container py-4">
    <nav class="mb-4">
        <a href="../index.php" class="btn btn-outline-secondary btn-sm">&larr; Natrag</a>
        <span class="badge bg-success ms-2">Korak 7 od 8</span>
    </nav>

    <h1>Korak 7: Getter i Setter</h1>
    <p class="lead">Getter = čita privatno svojstvo. Setter = postavlja s validacijom.</p>
    <hr>

    <!-- ZAŠTO -->
    <div class="alert alert-info mb-4">
        <h5>Zašto ne samo public?</h5>
        <p class="mb-0">
            Jer s <code>private</code> + <code>setter</code> možeš <strong>KONTROLIRATI</strong> što se sprema!
            <br>Npr. ocjena mora biti 1-5, dob ne može biti negativna, email mora imati @...
        </p>
    </div>

    <!-- PRIMJER -->
    <h4>Primjer s validacijom:</h4>
    <div class="code-box mb-4">
<span class="keyword">class</span> Ucenik {
    <span class="keyword">private</span> <span class="variable">$ime</span>;
    <span class="keyword">private</span> <span class="variable">$ocjena</span>;

    <span class="comment">// GETTER - čita vrijednost</span>
    <span class="keyword">public function</span> <span class="function">getIme</span>() {
        <span class="keyword">return</span> <span class="variable">$this</span>->ime;
    }

    <span class="comment">// SETTER - postavlja vrijednost</span>
    <span class="keyword">public function</span> <span class="function">setIme</span>(<span class="variable">$ime</span>) {
        <span class="variable">$this</span>->ime = <span class="variable">$ime</span>;
    }

    <span class="comment">// GETTER za ocjenu</span>
    <span class="keyword">public function</span> <span class="function">getOcjena</span>() {
        <span class="keyword">return</span> <span class="variable">$this</span>->ocjena;
    }

    <span class="comment">// SETTER s VALIDACIJOM!</span>
    <span class="keyword">public function</span> <span class="function">setOcjena</span>(<span class="variable">$ocjena</span>) {
        <span class="keyword">if</span> (<span class="variable">$ocjena</span> >= 1 && <span class="variable">$ocjena</span> <= 5) {
            <span class="variable">$this</span>->ocjena = <span class="variable">$ocjena</span>;
            <span class="keyword">return true</span>;
        }
        <span class="keyword">return false</span>;  <span class="comment">// Neispravna ocjena!</span>
    }
}
    </div>

    <!-- REZULTAT -->
    <h4>Testiranje:</h4>
    <?php
    class Ucenik {
        private $ime;
        private $ocjena;

        public function getIme() {
            return $this->ime;
        }

        public function setIme($ime) {
            $this->ime = $ime;
        }

        public function getOcjena() {
            return $this->ocjena;
        }

        public function setOcjena($ocjena) {
            if ($ocjena >= 1 && $ocjena <= 5) {
                $this->ocjena = $ocjena;
                return true;
            }
            return false;
        }
    }

    $ucenik = new Ucenik();
    $ucenik->setIme("Marko");
    ?>

    <table class="table table-bordered">
        <tr>
            <td><code>$ucenik->setOcjena(5)</code></td>
            <td><?= $ucenik->setOcjena(5) ? "true" : "false" ?></td>
            <td class="text-success">Prihvaćeno - ocjena je 5</td>
        </tr>
        <tr>
            <td><code>$ucenik->getOcjena()</code></td>
            <td><?= $ucenik->getOcjena() ?></td>
            <td>Vraća trenutnu ocjenu</td>
        </tr>
        <tr>
            <td><code>$ucenik->setOcjena(7)</code></td>
            <td><?= $ucenik->setOcjena(7) ? "true" : "false" ?></td>
            <td class="text-danger">Odbijeno - 7 nije između 1-5!</td>
        </tr>
        <tr>
            <td><code>$ucenik->getOcjena()</code></td>
            <td><?= $ucenik->getOcjena() ?></td>
            <td>Ostala je 5 (7 nije prihvaćena)</td>
        </tr>
    </table>

    <!-- KONVENCIJA -->
    <div class="alert alert-warning">
        <h5>Konvencija imenovanja:</h5>
        <table class="table table-sm mb-0">
            <tr>
                <td>Getter:</td>
                <td><code>get</code> + <code>ImeSvojstva</code></td>
                <td><code>getIme()</code>, <code>getOcjena()</code></td>
            </tr>
            <tr>
                <td>Setter:</td>
                <td><code>set</code> + <code>ImeSvojstva</code></td>
                <td><code>setIme($v)</code>, <code>setOcjena($v)</code></td>
            </tr>
        </table>
    </div>

    <!-- TVOJ RED -->
    <div class="card border-warning mt-4">
        <div class="card-header bg-warning">
            <strong>TVOJ RED:</strong>
        </div>
        <div class="card-body">
            <p>Napravi klasu <code>Osoba</code>:</p>
            <ul>
                <li><code>private $dob</code></li>
                <li><code>getDob()</code> - vraća dob</li>
                <li><code>setDob($dob)</code> - postavlja samo ako je dob između 0 i 150</li>
            </ul>
            <p>Testiraj: <code>setDob(-5)</code> i <code>setDob(25)</code></p>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-between">
        <a href="korak06_private.php" class="btn btn-outline-secondary">&larr; Prethodni</a>
        <a href="korak08_extends.php" class="btn btn-success">Sljedeći &rarr;</a>
    </div>
</body>
</html>
