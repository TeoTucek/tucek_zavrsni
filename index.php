<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OOP u PHP-u - Vježbe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 40px;
        }
    </style>
</head>
<body>
    <div class="hero">
        <div class="container text-center">
            <h1><i class="bi bi-code-slash"></i> OOP u PHP-u</h1>
            <p class="lead">Objektno orijentirano programiranje - Vježbe za 4. razred</p>
            <p class="mb-0">Tehničar za računalstvo</p>
        </div>
    </div>

    <div class="container">
        <!-- ZA POČETNIKE -->
        <div class="alert alert-success mb-5">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="alert-heading mb-1"><i class="bi bi-egg"></i> Novi u OOP-u?</h4>
                    <p class="mb-0">Korak-po-korak primjeri i vježbe sintakse za totalne početnike.</p>
                </div>
                <a href="pocetnici/index.php" class="btn btn-success btn-lg">
                    <i class="bi bi-arrow-right"></i> Kreni od početka
                </a>
            </div>
        </div>

        <!-- PRIMJERI -->
        <h2 class="mb-4"><i class="bi bi-play-circle text-primary"></i> Primjeri s predavanja</h2>
        <div class="row mb-5">
            <div class="col-md-3 mb-3">
                <div class="card card-hover h-100 border-primary">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-1-circle"></i> Prva klasa</h5>
                        <p class="card-text small">Osnove: class, object, $this, new</p>
                        <a href="primjeri/01_prva_klasa.php" class="btn btn-primary btn-sm">Otvori</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card card-hover h-100 border-primary">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-2-circle"></i> Konstruktor</h5>
                        <p class="card-text small">__construct() metoda</p>
                        <a href="primjeri/02_konstruktor.php" class="btn btn-primary btn-sm">Otvori</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card card-hover h-100 border-primary">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-3-circle"></i> Pristup</h5>
                        <p class="card-text small">public, private, protected</p>
                        <a href="primjeri/03_pristup_modifikatori.php" class="btn btn-primary btn-sm">Otvori</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card card-hover h-100 border-primary">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-4-circle"></i> Nasljeđivanje</h5>
                        <p class="card-text small">extends, parent::</p>
                        <a href="primjeri/04_nasljedjivanje.php" class="btn btn-primary btn-sm">Otvori</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ZADACI -->
        <h2 class="mb-4"><i class="bi bi-pencil text-warning"></i> Zadaci za vježbu</h2>
        <div class="row mb-5">
            <div class="col-md-4 mb-3">
                <div class="card card-hover h-100 border-warning">
                    <div class="card-header bg-warning">
                        <strong>Zadatak 1</strong> - Lagano
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Klasa Korisnik</h5>
                        <p class="card-text">Kreiraj prvu klasu s svojstvima i metodama</p>
                        <a href="zadaci/zadatak1_korisnik.php" class="btn btn-outline-warning">Radi zadatak</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card card-hover h-100 border-warning">
                    <div class="card-header bg-warning">
                        <strong>Zadatak 2</strong> - Srednje
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Private lozinka</h5>
                        <p class="card-text">Dodaj privatno svojstvo i validaciju</p>
                        <a href="zadaci/zadatak2_lozinka.php" class="btn btn-outline-warning">Radi zadatak</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card card-hover h-100 border-warning">
                    <div class="card-header bg-warning">
                        <strong>Zadatak 3</strong> - Napredno
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Sustav za školu</h5>
                        <p class="card-text">Nasljeđivanje: Osoba, Učenik, Profesor</p>
                        <a href="zadaci/zadatak3_skola.php" class="btn btn-outline-warning">Radi zadatak</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- RJEŠENJA -->
        <h2 class="mb-4"><i class="bi bi-check-circle text-success"></i> Rješenja</h2>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Rješenja otvori tek kad završiš zadatak!
        </div>
        <div class="row mb-5">
            <div class="col-md-4 mb-3">
                <a href="rjesenja/zadatak1_rjesenje.php" class="btn btn-success w-100">
                    <i class="bi bi-check"></i> Rješenje 1
                </a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="rjesenja/zadatak2_rjesenje.php" class="btn btn-success w-100">
                    <i class="bi bi-check"></i> Rješenje 2
                </a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="rjesenja/zadatak3_rjesenje.php" class="btn btn-success w-100">
                    <i class="bi bi-check"></i> Rješenje 3
                </a>
            </div>
        </div>

        <!-- ZADAĆA -->
        <h2 class="mb-4"><i class="bi bi-house-door text-danger"></i> Zadaća</h2>
        <div class="row mb-5">
            <div class="col-md-12">
                <div class="card border-danger">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Sustav za videoteku</h5>
                            <p class="card-text text-muted mb-0">Film, Serija, Korisnik, Videoteka klase</p>
                        </div>
                        <a href="zadaca/index.php" class="btn btn-danger btn-lg">
                            <i class="bi bi-download"></i> Otvori zadaću
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- CHEAT SHEET -->
        <h2 class="mb-4"><i class="bi bi-file-text text-info"></i> Pomoćni materijali</h2>
        <div class="row mb-5">
            <div class="col-md-6">
                <a href="cheat_sheet.php" class="btn btn-info btn-lg w-100">
                    <i class="bi bi-file-earmark-code"></i> OOP Cheat Sheet
                </a>
            </div>
        </div>

        <hr>
        <p class="text-muted text-center">
            <small>Materijali za nastavu - OOP PHP - <?= date('Y') ?></small>
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
