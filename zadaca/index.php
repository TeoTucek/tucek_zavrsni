<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OOP PHP - Zadaća</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .hero {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 40px 0;
            margin-bottom: 30px;
        }
        .task-card {
            border-left: 4px solid;
            margin-bottom: 20px;
        }
        .task-1 { border-left-color: #28a745; }
        .task-2 { border-left-color: #ffc107; }
        .task-3 { border-left-color: #dc3545; }
        .task-bonus { border-left-color: #6f42c1; }
        .code-example {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 5px;
            font-family: 'Consolas', monospace;
            font-size: 14px;
            overflow-x: auto;
        }
        .points {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #333;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
        }
        @media print {
            .hero { padding: 20px 0; }
            .code-example { font-size: 11px; }
        }
    </style>
</head>
<body>
    <div class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="bi bi-house-door"></i> OOP PHP - Zadaća</h1>
                    <p class="lead mb-0">Sustav za videoteku</p>
                </div>
                <div class="col-md-4 text-end">
                    <p class="mb-0"><strong>Rok predaje:</strong> ___________</p>
                    <p class="mb-0"><strong>Bodovi:</strong> max 100</p>
                    <p class="mb-0"><strong>Prolaz:</strong> 45 bodova</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- UVOD -->
        <div class="alert alert-info mb-4">
            <h5><i class="bi bi-info-circle"></i> Scenarij</h5>
            <p class="mb-0">
                Radiš u tvrtki koja razvija softver za videoteku. Tvoj zadatak je napraviti OOP sustav
                za upravljanje filmovima, serijama i korisnicima. Koristi znanje s nastave o klasama,
                konstruktorima, modifikatorima pristupa i nasljeđivanju.
            </p>
        </div>

        <!-- ZADATAK 1 -->
        <div class="card task-card task-1 position-relative">
            <span class="points bg-success">20 bodova</span>
            <div class="card-header bg-success text-white">
                <h4 class="mb-0"><i class="bi bi-1-circle"></i> Zadatak 1: Klasa Film</h4>
            </div>
            <div class="card-body">
                <p>Kreiraj datoteku <code>Film.php</code> s klasom <strong>Film</strong>.</p>

                <h6>Zahtjevi:</h6>
                <ul>
                    <li>Public svojstva: <code>naziv</code>, <code>godina</code>, <code>zanr</code></li>
                    <li>Private svojstvo: <code>ocjena</code> (od 1 do 10)</li>
                    <li>Konstruktor koji prima <code>naziv</code>, <code>godina</code>, <code>zanr</code></li>
                    <li>Metoda <code>setOcjena($ocjena)</code> - postavlja ocjenu samo ako je između 1 i 10</li>
                    <li>Metoda <code>getOcjena()</code> - vraća ocjenu</li>
                    <li>Metoda <code>getInfo()</code> - vraća string: <em>"[naziv] ([godina]) - [zanr]"</em></li>
                </ul>

                <h6>Očekivani rezultat:</h6>
                <div class="code-example">
$film = new Film("Inception", 2010, "Sci-Fi");
$film->setOcjena(9);
echo $film->getInfo();     // Inception (2010) - Sci-Fi
echo $film->getOcjena();   // 9

$film->setOcjena(15);      // Ne smije promijeniti (izvan raspona)
echo $film->getOcjena();   // 9 (ostalo isto)
                </div>
            </div>
        </div>

        <!-- ZADATAK 2 -->
        <div class="card task-card task-2 position-relative">
            <span class="points bg-warning text-dark">30 bodova</span>
            <div class="card-header bg-warning">
                <h4 class="mb-0"><i class="bi bi-2-circle"></i> Zadatak 2: Nasljeđivanje - Serija</h4>
            </div>
            <div class="card-body">
                <p>Kreiraj datoteku <code>Serija.php</code> s klasom <strong>Serija</strong> koja nasljeđuje <strong>Film</strong>.</p>

                <h6>Zahtjevi:</h6>
                <ul>
                    <li>Klasa <code>Serija</code> extends <code>Film</code></li>
                    <li>Dodatna private svojstva: <code>brojSezona</code>, <code>brojEpizoda</code></li>
                    <li>Konstruktor koji prima sve parametre (koristi <code>parent::__construct()</code>)</li>
                    <li>Override metodu <code>getInfo()</code> da vraća:<br>
                        <em>"[naziv] ([godina]) - [zanr] | [brojSezona] sezona, [brojEpizoda] epizoda"</em>
                    </li>
                    <li>Nova metoda <code>prosjecnoEpizodaPoSezoni()</code> - vraća prosječan broj epizoda po sezoni</li>
                </ul>

                <h6>Očekivani rezultat:</h6>
                <div class="code-example">
$serija = new Serija("Breaking Bad", 2008, "Drama", 5, 62);
$serija->setOcjena(10);
echo $serija->getInfo();
// Breaking Bad (2008) - Drama | 5 sezona, 62 epizoda

echo $serija->prosjecnoEpizodaPoSezoni();  // 12.4
                </div>

                <h6 class="mt-3">Dijagram:</h6>
                <pre class="bg-light p-3">
       ┌──────────────┐
       │    Film      │
       │──────────────│
       │ + naziv      │
       │ + godina     │
       │ + zanr       │
       │ - ocjena     │
       └──────┬───────┘
              │ extends
       ┌──────┴───────┐
       │   Serija     │
       │──────────────│
       │ - brojSezona │
       │ - brojEpizoda│
       └──────────────┘
                </pre>
            </div>
        </div>

        <!-- ZADATAK 3 -->
        <div class="card task-card task-3 position-relative">
            <span class="points bg-danger">30 bodova</span>
            <div class="card-header bg-danger text-white">
                <h4 class="mb-0"><i class="bi bi-3-circle"></i> Zadatak 3: Klasa Korisnik</h4>
            </div>
            <div class="card-body">
                <p>Kreiraj datoteku <code>Korisnik.php</code> s klasom <strong>Korisnik</strong>.</p>

                <h6>Zahtjevi:</h6>
                <ul>
                    <li>Private svojstva: <code>ime</code>, <code>email</code>, <code>listaZelja</code> (array)</li>
                    <li>Konstruktor koji prima <code>ime</code> i <code>email</code>, a <code>listaZelja</code> inicijalizira kao prazan array</li>
                    <li>Getter metode: <code>getIme()</code>, <code>getEmail()</code></li>
                    <li>Metoda <code>dodajNaListu($film)</code> - dodaje Film objekt na listu želja</li>
                    <li>Metoda <code>ukloniSListe($naziv)</code> - uklanja film po nazivu</li>
                    <li>Metoda <code>prikaziListu()</code> - vraća array naziva filmova na listi</li>
                    <li>Metoda <code>brojFilmovaNaListi()</code> - vraća broj filmova</li>
                </ul>

                <h6>Očekivani rezultat:</h6>
                <div class="code-example">
$korisnik = new Korisnik("Ana", "ana@email.com");

$film1 = new Film("Inception", 2010, "Sci-Fi");
$film2 = new Film("Titanic", 1997, "Drama");
$serija = new Serija("Friends", 1994, "Komedija", 10, 236);

$korisnik->dodajNaListu($film1);
$korisnik->dodajNaListu($film2);
$korisnik->dodajNaListu($serija);

echo $korisnik->brojFilmovaNaListi();  // 3
print_r($korisnik->prikaziListu());    // ["Inception", "Titanic", "Friends"]

$korisnik->ukloniSListe("Titanic");
echo $korisnik->brojFilmovaNaListi();  // 2
                </div>
            </div>
        </div>

        <!-- BONUS -->
        <div class="card task-card task-bonus position-relative">
            <span class="points bg-purple" style="background: #6f42c1;">+20 bodova</span>
            <div class="card-header text-white" style="background: #6f42c1;">
                <h4 class="mb-0"><i class="bi bi-star"></i> BONUS: Videoteka sustav</h4>
            </div>
            <div class="card-body">
                <p>Kreiraj datoteku <code>Videoteka.php</code> koja spaja sve zajedno.</p>

                <h6>Zahtjevi:</h6>
                <ul>
                    <li>Private svojstva: <code>naziv</code>, <code>katalog</code> (array filmova/serija), <code>korisnici</code> (array)</li>
                    <li>Metoda <code>dodajUKatalog($film)</code> - dodaje film ili seriju</li>
                    <li>Metoda <code>registrirajKorisnika($korisnik)</code> - dodaje korisnika</li>
                    <li>Metoda <code>pretraziPoZanru($zanr)</code> - vraća array filmova određenog žanra</li>
                    <li>Metoda <code>najboljeOcijenjeni($n)</code> - vraća top N filmova po ocjeni</li>
                    <li>Metoda <code>statistika()</code> - vraća array s:
                        <ul>
                            <li>ukupno_filmova</li>
                            <li>ukupno_serija</li>
                            <li>ukupno_korisnika</li>
                            <li>prosjecna_ocjena</li>
                        </ul>
                    </li>
                </ul>

                <h6>Hint za provjeru tipa:</h6>
                <div class="code-example">
// Provjera je li objekt instanca određene klase:
if ($film instanceof Serija) {
    echo "Ovo je serija!";
}
                </div>
            </div>
        </div>

        <!-- TESTNA DATOTEKA -->
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-file-code"></i> Testna datoteka (test.php)</h5>
            </div>
            <div class="card-body">
                <p>Kreiraj datoteku <code>test.php</code> koja uključuje sve klase i demonstrira njihov rad:</p>
                <div class="code-example">
&lt;?php
require_once 'Film.php';
require_once 'Serija.php';
require_once 'Korisnik.php';
// require_once 'Videoteka.php';  // Ako radiš bonus

// Kreiraj barem 3 filma i 2 serije
// Kreiraj barem 2 korisnika
// Demonstriraj sve metode

// Primjer:
$film1 = new Film("Inception", 2010, "Sci-Fi");
$film1->setOcjena(9);
echo $film1->getInfo();
// ... ostatak testiranja
?&gt;
                </div>
            </div>
        </div>

        <!-- STRUKTURA PREDAJE -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="bi bi-folder"></i> Struktura mape za predaju</h5>
            </div>
            <div class="card-body">
                <pre class="bg-light p-3 mb-0">
Ime_Prezime/
├── Film.php
├── Serija.php
├── Korisnik.php
├── Videoteka.php      (ako radiš bonus)
└── test.php
                </pre>
            </div>
        </div>

        <!-- BODOVANJE -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-clipboard-check"></i> Kriteriji bodovanja</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Kriterij</th>
                            <th>Bodovi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Ispravno korištenje <code>class</code> i <code>__construct()</code></td>
                            <td>15</td>
                        </tr>
                        <tr>
                            <td>Ispravno korištenje <code>public</code>/<code>private</code></td>
                            <td>15</td>
                        </tr>
                        <tr>
                            <td>Ispravno nasljeđivanje (<code>extends</code>, <code>parent::</code>)</td>
                            <td>20</td>
                        </tr>
                        <tr>
                            <td>Validacija podataka u metodama</td>
                            <td>10</td>
                        </tr>
                        <tr>
                            <td>Kod radi bez grešaka</td>
                            <td>15</td>
                        </tr>
                        <tr>
                            <td>Test datoteka demonstrira sve funkcionalnosti</td>
                            <td>5</td>
                        </tr>
                        <tr class="table-warning">
                            <td><strong>BONUS: Videoteka klasa</strong></td>
                            <td><strong>+20</strong></td>
                        </tr>
                    </tbody>
                    <tfoot class="table-secondary">
                        <tr>
                            <th>UKUPNO (bez bonusa)</th>
                            <th>80</th>
                        </tr>
                        <tr>
                            <th>MAKSIMALNO (s bonusom)</th>
                            <th>100</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- UPOZORENJA -->
        <div class="alert alert-danger">
            <h5><i class="bi bi-exclamation-triangle"></i> Važno!</h5>
            <ul class="mb-0">
                <li>Plagijat = 0 bodova za obje strane</li>
                <li>Kod mora raditi - testiraj prije predaje!</li>
                <li>Koristi znanje s nastave - ne kopiraj s interneta</li>
                <li>Pitaj ako nešto nije jasno!</li>
            </ul>
        </div>

        <div class="text-center mb-4">
            <a href="../index.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Natrag na vježbe
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="bi bi-printer"></i> Ispiši zadaću
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
