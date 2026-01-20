<?php
/**
 * KORAK 3: Prva metoda
 * ═══════════════════════════════════════════════════════════
 * Cilj: Naučiti kako se piše METODA (funkcija u klasi)
 */
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Korak 3 - Prva metoda</title>
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
        .highlight { background: #264f78; padding: 2px 5px; border-radius: 3px; }
    </style>
</head>
<body class="container py-4">
    <nav class="mb-4">
        <a href="../index.php" class="btn btn-outline-secondary btn-sm">&larr; Natrag</a>
        <span class="badge bg-success ms-2">Korak 3 od 8</span>
    </nav>

    <h1>Korak 3: Prva metoda</h1>
    <p class="lead">Metoda = funkcija koja živi unutar klase.</p>
    <hr>

    <!-- USPOREDBA -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h5>Obična funkcija:</h5>
            <div class="code-box">
<span class="keyword">function</span> <span class="function">pozdrav</span>() {
    <span class="keyword">return</span> <span class="string">"Bok!"</span>;
}

<span class="keyword">echo</span> <span class="function">pozdrav</span>();
            </div>
        </div>
        <div class="col-md-6">
            <h5>Metoda u klasi:</h5>
            <div class="code-box">
<span class="keyword">class</span> Robot {
    <span class="keyword">public function</span> <span class="function">pozdrav</span>() {
        <span class="keyword">return</span> <span class="string">"Bok!"</span>;
    }
}

<span class="variable">$r</span> = <span class="keyword">new</span> Robot();
<span class="keyword">echo</span> <span class="variable">$r</span>-><span class="function">pozdrav</span>();
            </div>
        </div>
    </div>

    <!-- GLAVNI PRIMJER -->
    <h4>Primjer:</h4>
    <div class="code-box mb-4">
<span class="keyword">class</span> Kalkulator {
    <span class="keyword">public function</span> <span class="function">zbroji</span>(<span class="variable">$a</span>, <span class="variable">$b</span>) {
        <span class="keyword">return</span> <span class="variable">$a</span> + <span class="variable">$b</span>;
    }

    <span class="keyword">public function</span> <span class="function">oduzmi</span>(<span class="variable">$a</span>, <span class="variable">$b</span>) {
        <span class="keyword">return</span> <span class="variable">$a</span> - <span class="variable">$b</span>;
    }
}

<span class="variable">$calc</span> = <span class="keyword">new</span> Kalkulator();
<span class="keyword">echo</span> <span class="variable">$calc</span>-><span class="function">zbroji</span>(<span class="number">5</span>, <span class="number">3</span>);
<span class="keyword">echo</span> <span class="variable">$calc</span>-><span class="function">oduzmi</span>(<span class="number">10</span>, <span class="number">4</span>);
    </div>

    <!-- REZULTAT -->
    <h4>Rezultat:</h4>
    <div class="alert alert-success">
        <?php
        class Kalkulator {
            public function zbroji($a, $b) {
                return $a + $b;
            }

            public function oduzmi($a, $b) {
                return $a - $b;
            }
        }

        $calc = new Kalkulator();
        echo "5 + 3 = " . $calc->zbroji(5, 3) . "<br>";
        echo "10 - 4 = " . $calc->oduzmi(10, 4);
        ?>
    </div>

    <!-- ZAPAMTI -->
    <div class="alert alert-info">
        <h5>Zapamti sintaksu:</h5>
        <table class="table table-sm mb-0">
            <tr>
                <td>Definicija metode:</td>
                <td><code>public function imeMetode() { }</code></td>
            </tr>
            <tr>
                <td>Poziv metode:</td>
                <td><code>$objekt->imeMetode()</code></td>
            </tr>
            <tr>
                <td>S parametrima:</td>
                <td><code>$objekt->imeMetode($param1, $param2)</code></td>
            </tr>
        </table>
    </div>

    <!-- TVOJ RED -->
    <div class="card border-warning mt-4">
        <div class="card-header bg-warning">
            <strong>TVOJ RED:</strong>
        </div>
        <div class="card-body">
            <p>Dodaj klasi <code>Kalkulator</code> još dvije metode:</p>
            <ul>
                <li><code>pomnozi($a, $b)</code> - vraća umnožak</li>
                <li><code>podijeli($a, $b)</code> - vraća količnik</li>
            </ul>
            <p>Testiraj sve 4 metode.</p>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-between">
        <a href="korak02_vise_svojstava.php" class="btn btn-outline-secondary">&larr; Prethodni</a>
        <a href="korak04_this.php" class="btn btn-success">Sljedeći &rarr;</a>
    </div>
</body>
</html>
