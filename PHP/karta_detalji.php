<?php
require_once 'config.php';
$page_title = "Detalji karte - Ribolovni Klub";
check_login();

$karta_id = $_GET['id'] ?? 0;
if(!$karta_id) {
    header("Location: karte.php");
    exit();
}

// Dohvati podatke o karti
$stmt = $pdo->prepare("SELECT k.*, r.naziv as ribnjak_naziv, r.lokacija, t.naziv as tarifa_naziv,
                      t.cijena as tarifa_cijena, t.kategorija,
                      CONCAT(k.ime_kupca, ' ', k.prezime_kupca) as kupac
                      FROM karta k
                      JOIN ribnjaci r ON k.ribnjak_id = r.id_ribnjaci
                      JOIN tarifa t ON k.tarifa_id = t.id_Tarifa
                      WHERE k.id_Karta = ?");
$stmt->execute([$karta_id]);
$karta = $stmt->fetch();

if(!$karta) {
    header("Location: karte.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .receipt {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border: 1px solid #ddd;
            padding: 30px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .receipt-header {
            text-align: center;
            border-bottom: 2px solid #2c7873;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .receipt-details {
            margin-bottom: 30px;
        }
        .receipt-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .receipt-total {
            display: flex;
            justify-content: space-between;
            font-size: 1.5rem;
            font-weight: bold;
            padding: 20px 0;
            border-top: 2px solid #2c7873;
        }
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 1rem;
        }
        .status-paid {
            background-color: #d4edda;
            color: #155724;
        }
        .status-unpaid {
            background-color: #f8d7da;
            color: #721c24;
        }
        .qr-code {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px dashed #ddd;
        }
        .print-only {
            display: none;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            .print-only {
                display: block;
            }
            .receipt {
                box-shadow: none;
                border: none;
                padding: 0;
            }
            body {
                font-size: 12pt;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4 no-print">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Detalji karte #<?php echo $karta_id; ?></h2>
            <div>
                <button onclick="window.print()" class="btn btn-secondary">
                    <i class="fas fa-print"></i> Ispiši
                </button>
                <a href="karte.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Natrag
                </a>
            </div>
        </div>
        
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Račun -->
    <div class="receipt">
        <div class="receipt-header">
            <div class="print-only">
                <h1>RIBA.NET</h1>
                <p>Ribolovni Klub d.o.o.<br>
                Ribarska ulica 15, 10000 Zagreb<br>
                OIB: 12345678901</p>
            </div>
            
            <h3>RAČUN / KARTA ZA RIBOLOV</h3>
            <p class="text-muted">Broj: <?php echo sprintf('RK-%06d', $karta_id); ?></p>
            <p>Datum izdavanja: <?php echo date('d.m.Y', strtotime($karta['kreirano'])); ?></p>
            
            <div class="status-badge <?php echo $karta['placeno'] ? 'status-paid' : 'status-unpaid'; ?>">
                <?php echo $karta['placeno'] ? 'PLAĆENO' : 'NAPLATI PRIJE DOLASKA'; ?>
            </div>
        </div>

        <div class="receipt-details">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Podaci o kupcu:</h5>
                    <p>
                        <strong><?php echo $karta['kupac']; ?></strong><br>
                        <?php echo $karta['adresa']; ?><br>
                        Tel: <?php echo $karta['telefon']; ?>
                    </p>
                    <?php if($karta['je_clan']): ?>
                        <span class="badge bg-info">ČLAN KLUBA</span>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <h5>Podaci o ribolovu:</h5>
                    <p>
                        <strong>Ribnjak:</strong> <?php echo $karta['ribnjak_naziv']; ?><br>
                        <strong>Lokacija:</strong> <?php echo $karta['lokacija']; ?><br>
                        <strong>Datum:</strong> <?php echo date('d.m.Y', strtotime($karta['datum'])); ?><br>
                        <strong>Vrijeme:</strong> <?php echo $karta['vrijeme_od'] . ' - ' . $karta['vrijeme_do']; ?>
                    </p>
                </div>
            </div>

            <h5>Detalji usluge:</h5>
            <div class="receipt-item">
                <div>Ribnjak "<?php echo $karta['ribnjak_naziv']; ?>"</div>
                <div><?php echo number_format($karta['tarifa_cijena'], 2); ?> kn/h × <?php echo $karta['trajanje']; ?>h</div>
                <div><?php echo number_format($karta['tarifa_cijena'] * $karta['trajanje'], 2); ?> kn</div>
            </div>
            
            <div class="receipt-item">
                <div>Tarifa: <?php echo $karta['tarifa_naziv']; ?> (<?php echo $karta['kategorija']; ?>)</div>
                <div><?php echo $karta['tarifa_cijena']; ?> kn/h × <?php echo $karta['trajanje']; ?>h</div>
                <div><?php echo number_format($karta['tarifa_cijena'] * $karta['trajanje'], 2); ?> kn</div>
            </div>
            
            <div class="receipt-item">
                <div>Broj osoba</div>
                <div><?php echo $karta['broj_osoba']; ?> osobe</div>
                <div></div>
            </div>
            
            <?php if($karta['je_clan']): ?>
                <div class="receipt-item text-success">
                    <div>Popust za člana (20%)</div>
                    <div>-20%</div>
                    <div>-<?php echo number_format($karta['ukupna_cijena'] * 0.2, 2); ?> kn</div>
                </div>
            <?php endif; ?>
        </div>

        <div class="receipt-total">
            <div>UKUPNO ZA PLATITI:</div>
            <div><?php echo number_format($karta['ukupna_cijena'], 2); ?> kn</div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <h5>Način plaćanja:</h5>
                <p>
                    <?php 
                    $payment_methods = [
                        'gotovina' => 'Gotovina',
                        'kartica' => 'Kartica',
                        'virman' => 'Virman',
                        'cek' => 'Ček'
                    ];
                    echo $payment_methods[$karta['nacin_placanja']] ?? $karta['nacin_placanja'];
                    ?>
                </p>
                
                <?php if(!$karta['placeno']): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        Molimo platite prije početka ribolova.
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <h5>Napomene:</h5>
                <p><?php echo $karta['napomena'] ?: 'Nema napomena.'; ?></p>
            </div>
        </div>

        <div class="qr-code">
            <p><strong>QR kod za provjeru:</strong></p>
            <!-- Ovdje bi bio generirani QR kod -->
            <div style="width: 150px; height: 150px; background: #f0f0f0; margin: 0 auto; 
                        display: flex; align-items: center; justify-content: center;">
                <small>QR KOD</small>
            </div>
            <p class="text-muted mt-2">Skenirajte za provjeru valjanosti karte</p>
        </div>

        <div class="mt-4 pt-4 border-top text-center text-muted">
            <small>
                Hvala što ste odabrali naše ribnjake!<br>
                Za sva pitanja kontaktirajte nas na: info@ribolovniklub.hr ili 01/234-567
            </small>
        </div>
    </div>

    <!-- Akcije -->
    <div class="container mt-4 no-print">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Akcije</h5>
                <div class="d-flex gap-2">
                    <?php if(!$karta['placeno']): ?>
                        <a href="karta_plati.php?id=<?php echo $karta_id; ?>" 
                           class="btn btn-success">
                            <i class="fas fa-check"></i> Označi kao plaćeno
                        </a>
                    <?php endif; ?>
                    
                    <?php 
                    // Provjeri je li danas ili kasnije
                    $today = date('Y-m-d');
                    if($karta['datum'] >= $today):
                    ?>
                        <a href="izlov_novi.php?karta=<?php echo $karta_id; ?>" 
                           class="btn btn-primary">
                            <i class="fas fa-fish"></i> Zabilježi izlov
                        </a>
                    <?php endif; ?>
                    
                    <a href="karta_edit.php?id=<?php echo $karta_id; ?>" 
                       class="btn btn-warning">
                        <i class="fas fa-edit"></i> Uredi
                    </a>
                    
                    <button onclick="window.print()" class="btn btn-secondary">
                        <i class="fas fa-print"></i> Ispiši
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Povijest izlova -->
        <?php
        $stmt = $pdo->prepare("SELECT i.* FROM izlov i WHERE i.karta_id = ?");
        $stmt->execute([$karta_id]);
        if($stmt->rowCount() > 0):
        ?>
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Povijest izlova</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Datum</th>
                                <th>Vrijeme</th>
                                <th>Ukupno ulova</th>
                                <th>Ukupna težina</th>
                                <th>Ocjena</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($izlov = $stmt->fetch()): ?>
                            <tr>
                                <td><?php echo date('d.m.Y', strtotime($izlov['datum'])); ?></td>
                                <td><?php echo $izlov['vrijeme_pocetka'] . ' - ' . $izlov['vrijeme_zavrsetka']; ?></td>
                                <td><?php echo $izlov['ukupno_ulova']; ?></td>
                                <td><?php echo $izlov['ukupna_tezina']; ?> kg</td>
                                <td>
                                    <?php if($izlov['ocjena_iskustva']): ?>
                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?php echo $i <= $izlov['ocjena_iskustva'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                        <?php endfor; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="izlov_detalji.php?id=<?php echo $izlov['id_izlov']; ?>" 
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>