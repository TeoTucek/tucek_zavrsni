<?php
/**
 * KORAK 4: Čarobni $this
 * ═══════════════════════════════════════════════════════════
 * Cilj: Razumjeti $this - pristup svojstvima UNUTAR klase
 */
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Korak 4 - $this</title>
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
        .this { color: #ff79c6; font-weight: bold; }
        .error { color: #f44747; }
    </style>
</head>
<body class="container py-4">
    <nav class="mb-4">
        <a href="../index.php" class="btn btn-outline-secondary btn-sm">&larr; Natrag</a>
        <span class="badge bg-success ms-2">Korak 4 od 8</span>
    </nav>

    <h1>Korak 4: Čarobni <code>$this</code></h1>
    <p class="lead">$this znači "ovaj objekt" - pristup svojim svojstvima iz metode.</p>
    <hr>

    <!-- PROBLEM -->
    <h4>Problem - ovo NE RADI:</h4>
    <div class="code-box mb-3" style="border: 2px solid #f44747;">
<span class="keyword">class</span> Osoba {
    <span class="keyword">public</span> <span class="variable">$ime</span>;

    <span class="keyword">public function</span> <span class="function">pozdrav</span>() {
        <span class="keyword">return</span> <span class="string">"Bok, ja sam "</span> . <span class="error">$ime</span>;  <span class="error">// GREŠKA!</span>
    }
}
    </div>
    <div class="alert alert-danger">
        <strong>Greška:</strong> Undefined variable $ime
    </div>

    <!-- RJEŠENJE -->
    <h4>Rješenje - koristi $this:</h4>
    <div class="code-box mb-3" style="border: 2px solid #28a745;">
<span class="keyword">class</span> Osoba {
    <span class="keyword">public</span> <span class="variable">$ime</span>;

    <span class="keyword">public function</span> <span class="function">pozdrav</span>() {
        <span class="keyword">return</span> <span class="string">"Bok, ja sam "</span> . <span class="this">$this</span>-><span class="variable">ime</span>;  <span style="color:#6a9955">// ISPRAVNO!</span>
    }
}
    </div>

    <!-- REZULTAT -->
    <h4>Primjer u akciji:</h4>
    <div class="code-box mb-4">
<span class="keyword">class</span> Osoba {
    <span class="keyword">public</span> <span class="variable">$ime</span>;
    <span class="keyword">public</span> <span class="variable">$godine</span>;

    <span class="keyword">public function</span> <span class="function">predstaviSe</span>() {
        <span class="keyword">return</span> <span class="string">"Ja sam "</span> . <span class="this">$this</span>-><span class="variable">ime</span> .
               <span class="string">" i imam "</span> . <span class="this">$this</span>-><span class="variable">godine</span> . <span class="string">" godina."</span>;
    }
}

<span class="variable">$osoba1</span> = <span class="keyword">new</span> Osoba();
<span class="variable">$osoba1</span>-><span class="variable">ime</span> = <span class="string">"Ana"</span>;
<span class="variable">$osoba1</span>-><span class="variable">godine</span> = <span class="number">25</span>;
<span class="keyword">echo</span> <span class="variable">$osoba1</span>-><span class="function">predstaviSe</span>();
    </div>

    <div class="alert alert-success">
        <?php
        class Osoba {
            public $ime;
            public $godine;

            public function predstaviSe() {
                return "Ja sam " . $this->ime . " i imam " . $this->godine . " godina.";
            }
        }

        $osoba1 = new Osoba();
        $osoba1->ime = "Ana";
        $osoba1->godine = 25;
        echo $osoba1->predstaviSe();
        ?>
    </div>

    <!-- VIZUALIZACIJA -->
    <div class="card bg-light mb-4">
        <div class="card-body">
            <h5>Kako to radi:</h5>
            <pre class="mb-0">
$osoba1->predstaviSe()
    │
    └── UNUTAR metode, $this POKAZUJE na $osoba1
        │
        └── $this->ime  ZNAČI  $osoba1->ime  ZNAČI  "Ana"
            </pre>
        </div>
    </div>

    <!-- PRAVILO -->
    <div class="alert alert-warning">
        <h5>ZLATNO PRAVILO:</h5>
        <p class="mb-0">
            Kad si <strong>UNUTAR klase</strong> i trebaš pristupiti svojstvu → koristi <code>$this->svojstvo</code><br>
            Kad si <strong>IZVAN klase</strong> → koristi <code>$objekt->svojstvo</code>
        </p>
    </div>

    <!-- TVOJ RED -->
    <div class="card border-warning mt-4">
        <div class="card-header bg-warning">
            <strong>TVOJ RED:</strong>
        </div>
        <div class="card-body">
            <p>Napravi klasu <code>Proizvod</code>:</p>
            <ul>
                <li>Svojstva: <code>$naziv</code>, <code>$cijena</code></li>
                <li>Metoda: <code>opisProizvoda()</code> koja vraća:<br>
                    <code>"[naziv] košta [cijena] kuna"</code></li>
            </ul>
            <p><strong>Koristi $this->naziv i $this->cijena u metodi!</strong></p>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-between">
        <a href="korak03_prva_metoda.php" class="btn btn-outline-secondary">&larr; Prethodni</a>
        <a href="korak05_konstruktor.php" class="btn btn-success">Sljedeći &rarr;</a>
    </div>
</body>
</html>
