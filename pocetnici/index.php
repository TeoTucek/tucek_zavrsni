<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OOP PHP - Za početnike</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .hero {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 40px 0;
            margin-bottom: 30px;
        }
        .step-card {
            border-left: 4px solid #11998e;
            transition: all 0.3s ease;
        }
        .step-card:hover {
            transform: translateX(5px);
        }
        .step-number {
            width: 40px;
            height: 40px;
            background: #11998e;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <div class="hero">
        <div class="container">
            <h1><i class="bi bi-egg"></i> OOP PHP - Za početnike</h1>
            <p class="lead mb-0">Korak po korak, od nule do klase</p>
        </div>
    </div>

    <div class="container">
        <div class="alert alert-success mb-4">
            <h5><i class="bi bi-lightbulb"></i> Kako koristiti ove materijale?</h5>
            <ol class="mb-0">
                <li>Idi redom - <strong>nemoj preskakati</strong></li>
                <li>Svaki primjer <strong>prepiši sam/a</strong> - ne copy-paste!</li>
                <li>Pokreni i vidi rezultat</li>
                <li>Tek onda idi na sljedeći</li>
            </ol>
        </div>

        <!-- PRIMJERI -->
        <h2 class="mb-4">Primjeri - Korak po korak</h2>

        <div class="card step-card mb-3">
            <div class="card-body d-flex align-items-center">
                <div class="step-number me-3">1</div>
                <div class="flex-grow-1">
                    <h5 class="mb-1">Najjednostavnija klasa</h5>
                    <p class="text-muted mb-0">Samo klasa i jedno svojstvo - 5 linija koda</p>
                </div>
                <a href="primjeri/korak01_prva_klasa.php" class="btn btn-outline-success">Otvori</a>
            </div>
        </div>

        <div class="card step-card mb-3">
            <div class="card-body d-flex align-items-center">
                <div class="step-number me-3">2</div>
                <div class="flex-grow-1">
                    <h5 class="mb-1">Više svojstava</h5>
                    <p class="text-muted mb-0">Dodajemo još svojstava u klasu</p>
                </div>
                <a href="primjeri/korak02_vise_svojstava.php" class="btn btn-outline-success">Otvori</a>
            </div>
        </div>

        <div class="card step-card mb-3">
            <div class="card-body d-flex align-items-center">
                <div class="step-number me-3">3</div>
                <div class="flex-grow-1">
                    <h5 class="mb-1">Prva metoda</h5>
                    <p class="text-muted mb-0">Klasa koja radi nešto - funkcija unutar klase</p>
                </div>
                <a href="primjeri/korak03_prva_metoda.php" class="btn btn-outline-success">Otvori</a>
            </div>
        </div>

        <div class="card step-card mb-3">
            <div class="card-body d-flex align-items-center">
                <div class="step-number me-3">4</div>
                <div class="flex-grow-1">
                    <h5 class="mb-1">Čarobni $this</h5>
                    <p class="text-muted mb-0">Kako metoda pristupa svojim svojstvima</p>
                </div>
                <a href="primjeri/korak04_this.php" class="btn btn-outline-success">Otvori</a>
            </div>
        </div>

        <div class="card step-card mb-3">
            <div class="card-body d-flex align-items-center">
                <div class="step-number me-3">5</div>
                <div class="flex-grow-1">
                    <h5 class="mb-1">Konstruktor</h5>
                    <p class="text-muted mb-0">Automatsko postavljanje vrijednosti kod kreiranja</p>
                </div>
                <a href="primjeri/korak05_konstruktor.php" class="btn btn-outline-success">Otvori</a>
            </div>
        </div>

        <div class="card step-card mb-3">
            <div class="card-body d-flex align-items-center">
                <div class="step-number me-3">6</div>
                <div class="flex-grow-1">
                    <h5 class="mb-1">Private svojstva</h5>
                    <p class="text-muted mb-0">Skrivanje podataka - zaštita od direktnog pristupa</p>
                </div>
                <a href="primjeri/korak06_private.php" class="btn btn-outline-success">Otvori</a>
            </div>
        </div>

        <div class="card step-card mb-3">
            <div class="card-body d-flex align-items-center">
                <div class="step-number me-3">7</div>
                <div class="flex-grow-1">
                    <h5 class="mb-1">Getter i Setter</h5>
                    <p class="text-muted mb-0">Siguran pristup privatnim podacima</p>
                </div>
                <a href="primjeri/korak07_getter_setter.php" class="btn btn-outline-success">Otvori</a>
            </div>
        </div>

        <div class="card step-card mb-3">
            <div class="card-body d-flex align-items-center">
                <div class="step-number me-3">8</div>
                <div class="flex-grow-1">
                    <h5 class="mb-1">Nasljeđivanje</h5>
                    <p class="text-muted mb-0">Jedna klasa nasljeđuje drugu - extends</p>
                </div>
                <a href="primjeri/korak08_extends.php" class="btn btn-outline-success">Otvori</a>
            </div>
        </div>

        <hr class="my-5">

        <!-- VJEŽBE -->
        <h2 class="mb-4"><i class="bi bi-pencil"></i> Vježbe sintakse</h2>
        <p class="text-muted">Zadaci za vježbanje pisanja - fokus na ponavljanje dok ne postane automatsko.</p>

        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card h-100 border-warning">
                    <div class="card-header bg-warning">
                        <strong>Vježba A</strong> - Prepiši i pokreni
                    </div>
                    <div class="card-body">
                        <p>Prepiši 5 mini-klasa bez gledanja u rješenje</p>
                        <a href="vjezbe/vjezba_A_prepisi.php" class="btn btn-warning">Radi</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card h-100 border-warning">
                    <div class="card-header bg-warning">
                        <strong>Vježba B</strong> - Dopuni kod
                    </div>
                    <div class="card-body">
                        <p>Nedostaju dijelovi koda - ti ih dopuni</p>
                        <a href="vjezbe/vjezba_B_dopuni.php" class="btn btn-warning">Radi</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card h-100 border-warning">
                    <div class="card-header bg-warning">
                        <strong>Vježba C</strong> - Pronađi grešku
                    </div>
                    <div class="card-body">
                        <p>Kod ima greške - pronađi ih i ispravi</p>
                        <a href="vjezbe/vjezba_C_greske.php" class="btn btn-warning">Radi</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card h-100 border-warning">
                    <div class="card-header bg-warning">
                        <strong>Vježba D</strong> - Napiši sam
                    </div>
                    <div class="card-body">
                        <p>Napiši klase od nule prema opisu</p>
                        <a href="vjezbe/vjezba_D_napisi.php" class="btn btn-warning">Radi</a>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-4">
        <div class="text-center">
            <a href="../index.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Natrag na glavnu stranicu
            </a>
        </div>
    </div>
</body>
</html>
