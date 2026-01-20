<?php
/**
 * KORAK 1: Najjednostavnija klasa
 * ═══════════════════════════════════════════════════════════
 * Cilj: Naučiti kako se PIŠE klasa u PHP-u
 */
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Korak 1 - Prva klasa</title>
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
        .comment { color: #6a9955; }
        .highlight { background: #264f78; padding: 2px 5px; border-radius: 3px; }
    </style>
</head>
<body class="container py-4">
    <nav class="mb-4">
        <a href="../index.php" class="btn btn-outline-secondary btn-sm">&larr; Natrag</a>
        <span class="badge bg-success ms-2">Korak 1 od 8</span>
    </nav>

    <h1>Korak 1: Najjednostavnija klasa</h1>
    <p class="lead">Samo 5 linija koda. Ništa više.</p>
    <hr>

    <!-- KOD -->
    <h4>Kod:</h4>
    <div class="code-box mb-4">
<span class="keyword">class</span> <span class="highlight">Kutija</span> {
    <span class="keyword">public</span> <span class="variable">$boja</span>;
}

<span class="variable">$k</span> = <span class="keyword">new</span> Kutija();
<span class="variable">$k</span>-><span class="variable">boja</span> = <span class="string">"crvena"</span>;
<span class="keyword">echo</span> <span class="variable">$k</span>-><span class="variable">boja</span>;
    </div>

    <!-- REZULTAT -->
    <h4>Rezultat:</h4>
    <div class="alert alert-success">
        <?php
        class Kutija {
            public $boja;
        }

        $k = new Kutija();
        $k->boja = "crvena";
        echo $k->boja;
        ?>
    </div>

    <!-- OBJAŠNJENJE -->
    <h4>Objašnjenje - liniju po liniju:</h4>
    <table class="table table-bordered">
        <tr>
            <td><code>class Kutija { }</code></td>
            <td>Definiramo klasu imena "Kutija"</td>
        </tr>
        <tr>
            <td><code>public $boja;</code></td>
            <td>Klasa ima svojstvo <code>$boja</code></td>
        </tr>
        <tr>
            <td><code>$k = new Kutija();</code></td>
            <td>Kreiramo objekt iz klase, spremamo u varijablu <code>$k</code></td>
        </tr>
        <tr>
            <td><code>$k->boja = "crvena";</code></td>
            <td>Postavljamo vrijednost svojstva</td>
        </tr>
        <tr>
            <td><code>echo $k->boja;</code></td>
            <td>Ispisujemo vrijednost svojstva</td>
        </tr>
    </table>

    <!-- ZAPAMTI -->
    <div class="alert alert-info">
        <h5><i class="bi bi-lightbulb"></i> Zapamti:</h5>
        <ul class="mb-0">
            <li><code>class ImeKlase { }</code> - tako se definira klasa</li>
            <li><code>public $ime;</code> - tako se definira svojstvo</li>
            <li><code>new ImeKlase()</code> - tako se kreira objekt</li>
            <li><code>$objekt->svojstvo</code> - tako se pristupa svojstvu (strelica!)</li>
        </ul>
    </div>

    <!-- TVOJ RED -->
    <div class="card border-warning mt-4">
        <div class="card-header bg-warning">
            <strong>TVOJ RED - Prepiši i promijeni:</strong>
        </div>
        <div class="card-body">
            <ol>
                <li>Kreiraj novu datoteku <code>moj_korak1.php</code></li>
                <li>Prepiši gornji kod RUČNO (ne copy-paste!)</li>
                <li>Promijeni "Kutija" u "Loptica"</li>
                <li>Promijeni "$boja" u "$velicina"</li>
                <li>Postavi velicinu na "velika"</li>
                <li>Pokreni i provjeri radi li</li>
            </ol>
        </div>
    </div>

    <div class="mt-4">
        <a href="korak02_vise_svojstava.php" class="btn btn-success">
            Sljedeći korak &rarr;
        </a>
    </div>
</body>
</html>
