<?php
/**
 * ╔═══════════════════════════════════════════════════════════════╗
 * ║                    ZAVRŠNI ZADATAK                            ║
 * ║                   Sustav za školu                             ║
 * ╠═══════════════════════════════════════════════════════════════╣
 * ║                                                               ║
 * ║  1. Kreiraj BAZNU klasu: Osoba                                ║
 * ║     - protected svojstva: ime, prezime, email                 ║
 * ║     - konstruktor koji prima sva 3 parametra                  ║
 * ║     - metoda: predstaviSe() vraća "Ja sam [ime] [prezime]"    ║
 * ║                                                               ║
 * ║  2. Kreiraj klasu: Ucenik EXTENDS Osoba                       ║
 * ║     - private svojstva: razred, prosjek                       ║
 * ║     - konstruktor koji prima sve + razred i prosjek           ║
 * ║     - OVERRIDE predstaviSe() vraća:                           ║
 * ║       "Ja sam [ime] [prezime], učenik [razred] razreda"       ║
 * ║                                                               ║
 * ║  3. Kreiraj klasu: Profesor EXTENDS Osoba                     ║
 * ║     - private svojstva: predmet, godinaIskustva               ║
 * ║     - konstruktor koji prima sve + predmet i godine           ║
 * ║     - OVERRIDE predstaviSe() vraća:                           ║
 * ║       "Ja sam [ime] [prezime], profesor/ica [predmet]"        ║
 * ║                                                               ║
 * ║  BONUS: Dodaj metodu daLiPolozio() u Ucenik                   ║
 * ║         - vraća true ako je prosjek >= 2.0                    ║
 * ║                                                               ║
 * ╚═══════════════════════════════════════════════════════════════╝
 */

// TVOJ KOD OVDJE:

// Klasa Osoba (bazna)


// Klasa Ucenik extends Osoba


// Klasa Profesor extends Osoba



// TESTIRANJE - NE MIJENJAJ:
// (Ukloni komentare kad završiš klase)

/*
$ucenik1 = new Ucenik("Marko", "Markovic", "marko@skola.hr", "4.a", 4.5);
$ucenik2 = new Ucenik("Ana", "Anic", "ana@skola.hr", "4.a", 1.8);
$profesor = new Profesor("Ivan", "Ivic", "ivan@skola.hr", "Informatika", 15);

echo $ucenik1->predstaviSe();  // Ja sam Marko Markovic, učenik 4.a razreda
echo $ucenik2->predstaviSe();  // Ja sam Ana Anic, učenik 4.a razreda
echo $profesor->predstaviSe(); // Ja sam Ivan Ivic, profesor/ica Informatika
*/

?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Završni zadatak - Škola</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
    <h1>Završni zadatak: Sustav za školu</h1>
    <hr>

    <div class="alert alert-info">
        <h5>Dijagram klasa:</h5>
        <pre>
              ┌─────────────────┐
              │     Osoba       │
              │─────────────────│
              │ # ime           │
              │ # prezime       │
              │ # email         │
              │─────────────────│
              │ + predstaviSe() │
              └────────┬────────┘
                       │ extends
              ┌────────┴────────┐
              │                 │
        ┌─────┴─────┐    ┌──────┴──────┐
        │  Ucenik   │    │  Profesor   │
        │───────────│    │─────────────│
        │ - razred  │    │ - predmet   │
        │ - prosjek │    │ - iskustvo  │
        └───────────┘    └─────────────┘
        </pre>
    </div>

    <h4>Rezultati:</h4>
    <div class="row">
        <!-- Rezultati će se prikazati ovdje kad završiš -->
        <p class="text-muted">Završi klase i ukloni komentare s testnog koda...</p>
    </div>
</body>
</html>
