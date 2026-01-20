<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Vježba D - Napiši sam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .task-number {
            width: 50px;
            height: 50px;
            background: #0d6efd;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
        }
        .expected {
            background: #e8f5e9;
            padding: 10px;
            border-radius: 5px;
            font-family: monospace;
        }
    </style>
</head>
<body class="container py-4">
    <a href="../index.php" class="btn btn-outline-secondary btn-sm mb-4">&larr; Natrag</a>

    <h1>Vježba D: Napiši sam</h1>
    <p class="lead">Nema koda za prepisivanje. Samo opis - ti napiši od nule!</p>

    <div class="alert alert-primary">
        <strong>Ovo je pravi test!</strong> Ako možeš napisati ove klase bez gledanja u primjere, naučio/la si sintaksu.
    </div>

    <hr>

    <!-- ZADATAK 1 -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <div class="task-number me-3">1</div>
                <h5 class="mb-0">Klasa Grad</h5>
            </div>
            <p>Napiši klasu <code>Grad</code>:</p>
            <ul>
                <li>Public svojstva: <code>naziv</code>, <code>drzava</code>, <code>stanovnici</code></li>
                <li>Bez konstruktora, bez metoda</li>
            </ul>
            <p>Kreiraj objekt za Zagreb (Hrvatska, 800000 stanovnika) i ispiši naziv.</p>
            <div class="expected"><strong>Očekivani ispis:</strong> Zagreb</div>
        </div>
    </div>

    <!-- ZADATAK 2 -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <div class="task-number me-3">2</div>
                <h5 class="mb-0">Klasa Proizvod</h5>
            </div>
            <p>Napiši klasu <code>Proizvod</code>:</p>
            <ul>
                <li>Public svojstva: <code>naziv</code>, <code>cijena</code></li>
                <li>Metoda <code>formatiranacijena()</code> koja vraća: <code>"[naziv]: [cijena] EUR"</code></li>
            </ul>
            <p>Kreiraj proizvod "Laptop" s cijenom 999 i pozovi metodu.</p>
            <div class="expected"><strong>Očekivani ispis:</strong> Laptop: 999 EUR</div>
        </div>
    </div>

    <!-- ZADATAK 3 -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <div class="task-number me-3">3</div>
                <h5 class="mb-0">Klasa Igrač s konstruktorom</h5>
            </div>
            <p>Napiši klasu <code>Igrac</code>:</p>
            <ul>
                <li>Public svojstva: <code>nadimak</code>, <code>level</code></li>
                <li><strong>Konstruktor</strong> koji prima oba parametra</li>
                <li>Metoda <code>info()</code> vraća: <code>"[nadimak] - Level [level]"</code></li>
            </ul>
            <p>Kreiraj igrača "DragonSlayer" s levelom 50.</p>
            <div class="expected"><strong>Očekivani ispis:</strong> DragonSlayer - Level 50</div>
        </div>
    </div>

    <!-- ZADATAK 4 -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <div class="task-number me-3">4</div>
                <h5 class="mb-0">Klasa Termometar s private</h5>
            </div>
            <p>Napiši klasu <code>Termometar</code>:</p>
            <ul>
                <li><strong>Private</strong> svojstvo: <code>temperatura</code></li>
                <li>Konstruktor koji postavlja početnu temperaturu</li>
                <li><code>getTemperatura()</code> - vraća trenutnu temperaturu</li>
                <li><code>setTemperatura($t)</code> - postavlja samo ako je između -50 i 50</li>
                <li><code>status()</code> - vraća "hladno" ako je &lt; 10, "ugodno" ako 10-25, "vruće" ako &gt; 25</li>
            </ul>
            <div class="expected">
                <strong>Test:</strong><br>
                $t = new Termometar(22);<br>
                echo $t->status(); // ugodno<br>
                $t->setTemperatura(100); // ne smije promijeniti (izvan raspona)<br>
                echo $t->getTemperatura(); // 22
            </div>
        </div>
    </div>

    <!-- ZADATAK 5 -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <div class="task-number me-3">5</div>
                <h5 class="mb-0">Nasljeđivanje - Lik i Krug</h5>
            </div>
            <p>Napiši dvije klase:</p>
            <p><strong>Klasa <code>Lik</code> (bazna):</strong></p>
            <ul>
                <li>Public svojstvo: <code>boja</code></li>
                <li>Konstruktor koji prima boju</li>
            </ul>
            <p><strong>Klasa <code>Krug</code> extends <code>Lik</code>:</strong></p>
            <ul>
                <li>Public svojstvo: <code>radius</code></li>
                <li>Konstruktor prima boju i radius (koristi parent::__construct)</li>
                <li>Metoda <code>povrsina()</code> vraća: 3.14 * radius * radius</li>
                <li>Metoda <code>opis()</code> vraća: <code>"[boja] krug s radiusom [radius]"</code></li>
            </ul>
            <div class="expected">
                <strong>Test:</strong><br>
                $k = new Krug("crveni", 5);<br>
                echo $k->opis(); // crveni krug s radiusom 5<br>
                echo $k->povrsina(); // 78.5
            </div>
        </div>
    </div>

    <!-- BONUS -->
    <div class="card mb-4 border-warning">
        <div class="card-header bg-warning">
            <strong>BONUS IZAZOV</strong>
        </div>
        <div class="card-body">
            <h5>Klasa Kosarica (Shopping Cart)</h5>
            <ul>
                <li>Private: <code>artikli</code> (array)</li>
                <li>Konstruktor inicijalizira prazan array</li>
                <li><code>dodaj($naziv, $cijena)</code> - dodaje artikl u array</li>
                <li><code>ukloni($naziv)</code> - uklanja artikl po nazivu</li>
                <li><code>ukupno()</code> - vraća sumu svih cijena</li>
                <li><code>brojArtikala()</code> - vraća broj artikala</li>
            </ul>
            <p class="text-muted">Hint: Artikl možeš spremiti kao asocijativni array: <code>["naziv" => $naziv, "cijena" => $cijena]</code></p>
        </div>
    </div>

    <!-- GOTOVO -->
    <div class="alert alert-success">
        <h5>Čestitamo!</h5>
        <p>Ako si uspješno napisao/la sve ove klase bez gledanja u primjere - NAUČIO/LA SI PHP OOP SINTAKSU!</p>
        <p class="mb-0">
            <a href="../../index.php" class="btn btn-success">Vrati se na glavne materijale</a>
        </p>
    </div>
</body>
</html>
