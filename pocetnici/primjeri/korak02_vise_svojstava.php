<?php
/**
 * KORAK 2: Više svojstava
 * ═══════════════════════════════════════════════════════════
 * Cilj: Klasa može imati VIŠE svojstava
 */
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Korak 2 - Više svojstava</title>
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
        .number { color: #b5cea8; }
    </style>
</head>
<body class="container py-4">
    <nav class="mb-4">
        <a href="../index.php" class="btn btn-outline-secondary btn-sm">&larr; Natrag</a>
        <span class="badge bg-success ms-2">Korak 2 od 8</span>
    </nav>

    <h1>Korak 2: Više svojstava</h1>
    <p class="lead">Klasa može imati koliko god svojstava trebaš.</p>
    <hr>

    <!-- KOD -->
    <h4>Kod:</h4>
    <div class="code-box mb-4">
<span class="keyword">class</span> Pas {
    <span class="keyword">public</span> <span class="variable">$ime</span>;
    <span class="keyword">public</span> <span class="variable">$rasa</span>;
    <span class="keyword">public</span> <span class="variable">$godine</span>;
}

<span class="variable">$moj_pas</span> = <span class="keyword">new</span> Pas();
<span class="variable">$moj_pas</span>-><span class="variable">ime</span> = <span class="string">"Rex"</span>;
<span class="variable">$moj_pas</span>-><span class="variable">rasa</span> = <span class="string">"Njemački ovčar"</span>;
<span class="variable">$moj_pas</span>-><span class="variable">godine</span> = <span class="number">3</span>;

<span class="keyword">echo</span> <span class="variable">$moj_pas</span>-><span class="variable">ime</span> . <span class="string">" je "</span> . <span class="variable">$moj_pas</span>-><span class="variable">rasa</span>;
    </div>

    <!-- REZULTAT -->
    <h4>Rezultat:</h4>
    <div class="alert alert-success">
        <?php
        class Pas {
            public $ime;
            public $rasa;
            public $godine;
        }

        $moj_pas = new Pas();
        $moj_pas->ime = "Rex";
        $moj_pas->rasa = "Njemački ovčar";
        $moj_pas->godine = 3;

        echo $moj_pas->ime . " je " . $moj_pas->rasa;
        ?>
    </div>

    <!-- VIŠE OBJEKATA -->
    <h4>Bonus: Više objekata iz iste klase</h4>
    <div class="code-box mb-4">
<span class="variable">$pas1</span> = <span class="keyword">new</span> Pas();
<span class="variable">$pas1</span>-><span class="variable">ime</span> = <span class="string">"Rex"</span>;

<span class="variable">$pas2</span> = <span class="keyword">new</span> Pas();
<span class="variable">$pas2</span>-><span class="variable">ime</span> = <span class="string">"Bella"</span>;

<span class="comment">// Svaki objekt ima SVOJE vrijednosti!</span>
    </div>

    <div class="alert alert-success">
        <?php
        $pas1 = new Pas();
        $pas1->ime = "Rex";
        $pas1->godine = 3;

        $pas2 = new Pas();
        $pas2->ime = "Bella";
        $pas2->godine = 5;

        echo "Pas 1: " . $pas1->ime . " (" . $pas1->godine . " god.)<br>";
        echo "Pas 2: " . $pas2->ime . " (" . $pas2->godine . " god.)";
        ?>
    </div>

    <!-- ZAPAMTI -->
    <div class="alert alert-info">
        <h5>Zapamti:</h5>
        <ul class="mb-0">
            <li>Klasa može imati <strong>neograničen broj svojstava</strong></li>
            <li>Iz jedne klase možeš napraviti <strong>više objekata</strong></li>
            <li>Svaki objekt ima <strong>svoje vlastite vrijednosti</strong></li>
        </ul>
    </div>

    <!-- TVOJ RED -->
    <div class="card border-warning mt-4">
        <div class="card-header bg-warning">
            <strong>TVOJ RED:</strong>
        </div>
        <div class="card-body">
            <p>Napravi klasu <code>Mobitel</code> sa svojstvima:</p>
            <ul>
                <li><code>$marka</code></li>
                <li><code>$model</code></li>
                <li><code>$cijena</code></li>
            </ul>
            <p>Kreiraj 2 objekta (dva različita mobitela) i ispiši njihove podatke.</p>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-between">
        <a href="korak01_prva_klasa.php" class="btn btn-outline-secondary">&larr; Prethodni</a>
        <a href="korak03_prva_metoda.php" class="btn btn-success">Sljedeći &rarr;</a>
    </div>
</body>
</html>
