<?php
require_once 'config.php';
$page_title = "Prodaja karata - Ribnjak Končanica";

// Provjera prijave djelatnika
check_djelatnik_login();

// Dohvati djelatnikove podatke
$djelatnik_id = $_SESSION['djelatnik_id'];
$stmt = $pdo->prepare("SELECT * FROM djelatnici WHERE id = ?");
$stmt->execute([$djelatnik_id]);
$djelatnik = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .karta-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.9em;
        }
        .status-placeno { background-color: #d4edda; color: #155724; }
        .status-neplaceno { background-color: #f8d7da; color: #721c24; }
        .karta-card {
            border-left: 5px solid #2c7873;
            transition: transform 0.2s;
        }
        .filter-card {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        .operator-badge {
            background-color: #2c7873;
            color: white;
            padding: 3px 8px;
            border-radius: 15px;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between mb-4">
                    <h2>Prodaja karata</h2>
                    <div>
                        <a href="karta_nova.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nova karta
                        </a>
                        <span class="operator-badge ms-2">
                            <i class="fas fa-user"></i> <?php echo $djelatnik['ime'] . ' ' . $djelatnik['prezime']; ?>
                        </span>
                    </div>
                </div>

                <!-- Filteri -->
                <div class="card filter-card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Datum od</label>
                                <input type="date" class="form-control" name="datum_od" 
                                       value="<?php echo $_GET['datum_od'] ?? ''; ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Datum do</label>
                                <input type="date" class="form-control" name="datum_do" 
                                       value="<?php echo $_GET['datum_do'] ?? ''; ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Status plaćanja</label>
                                <select class="form-select" name="placeno">
                                    <option value="">Sve</option>
                                    <option value="1" <?php echo ($_GET['placeno'] ?? '') == '1' ? 'selected' : ''; ?>>Plaćeno</option>
                                    <option value="0" <?php echo ($_GET['placeno'] ?? '') == '0' ? 'selected' : ''; ?>>Neplaćeno</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Ribnjak</label>
                                <select class="form-select" name="ribnjak_id">
                                    <option value="">Svi</option>
                                    <option value="1" <?php echo ($_GET['ribnjak_id'] ?? '') == '1' ? 'selected' : ''; ?>>R23</option>
                                    <option value="2" <?php echo ($_GET['ribnjak_id'] ?? '') == '2' ? 'selected' : ''; ?>>Crnaja</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Filtriraj</button>
                                <a href="karte.php" class="btn btn-secondary">Poništi</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Statistike -->
                <div class="row mb-4">
                    <?php
                    // Ukupno karata danas
                    $stmt = $pdo->query("SELECT COUNT(*) as today FROM karte WHERE DATE(datum_izdavanja) = CURDATE()");
                    $today = $stmt->fetch()['today'];
                    
                    // Ukupni prihodi danas
                    $stmt = $pdo->query("SELECT SUM(ukupna_cijena) as income_today FROM karte WHERE DATE(datum_izdavanja) = CURDATE() AND placeno = 1");
                    $income_today = $stmt->fetch()['income_today'];
                    
                    // Neplaćene karte
                    $stmt = $pdo->query("SELECT COUNT(*) as unpaid FROM karte WHERE placeno = 0");
                    $unpaid = $stmt->fetch()['unpaid'];
                    
                    // Karte izdao ovaj djelatnik
                    $stmt = $pdo->prepare("SELECT COUNT(*) as my_tickets FROM karte WHERE djelatnik_id = ? AND DATE(datum_izdavanja) = CURDATE()");
                    $stmt->execute([$djelatnik_id]);
                    $my_tickets = $stmt->fetch()['my_tickets'];
                    ?>
                    
                    <div class="col-md-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $today; ?></h5>
                                <p class="card-text">Današnjih karata</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo number_format($income_today, 2); ?> €</h5>
                                <p class="card-text">Današnji prihodi</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $unpaid; ?></h5>
                                <p class="card-text">Neplaćenih karata</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $my_tickets; ?></h5>
                                <p class="card-text">Vaše današnje karte</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Popis karata -->
                <div class="card">
                    <div class="card-body">
                        <table id="karteTable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Broj karte</th>
                                    <th>Kupac</th>
                                    <th>Ribnjak</th>
                                    <th>Datum</th>
                                    <th>Iznos</th>
                                    <th>Status</th>
                                    <th>Operater</th>
                                    <th>Akcije</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Priprema query-ja s filterima
                                $query = "SELECT k.*, r.naziv as ribnjak_naziv, 
                                         d.ime as djelatnik_ime, d.prezime as djelatnik_prezime
                                         FROM karte k
                                         JOIN ribnjaci r ON k.vrsta_karte_id IN (
                                             SELECT id FROM vrste_karata WHERE ribnjak_id = r.id
                                         )
                                         JOIN djelatnici d ON k.djelatnik_id = d.id
                                         WHERE 1=1";
                                
                                $params = [];
                                
                                if(isset($_GET['datum_od']) && !empty($_GET['datum_od'])) {
                                    $query .= " AND k.datum_vrijedi_od >= ?";
                                    $params[] = $_GET['datum_od'];
                                }
                                
                                if(isset($_GET['datum_do']) && !empty($_GET['datum_do'])) {
                                    $query .= " AND k.datum_vrijedi_do <= ?";
                                    $params[] = $_GET['datum_do'];
                                }
                                
                                if(isset($_GET['placeno']) && $_GET['placeno'] !== '') {
                                    $query .= " AND k.placeno = ?";
                                    $params[] = $_GET['placeno'];
                                }
                                
                                if(isset($_GET['ribnjak_id']) && !empty($_GET['ribnjak_id'])) {
                                    $query .= " AND r.id = ?";
                                    $params[] = $_GET['ribnjak_id'];
                                }
                                
                                $query .= " ORDER BY k.datum_izdavanja DESC";
                                
                                $stmt = $pdo->prepare($query);
                                $stmt->execute($params);
                                $counter = 1;
                                
                                while($karta = $stmt->fetch()):
                                    $status_class = $karta['placeno'] ? 'status-placeno' : 'status-neplaceno';
                                    $status_text = $karta['placeno'] ? 'Plaćeno' : 'Neplaćeno';
                                ?>
                                <tr>
                                    <td><?php echo $counter++; ?></td>
                                    <td><strong><?php echo $karta['broj_karte']; ?></strong></td>
                                    <td><?php echo $karta['ime_kupca'] . ' ' . $karta['prezime_kupca']; ?></td>
                                    <td><?php echo $karta['ribnjak_naziv']; ?></td>
                                    <td><?php echo date('d.m.Y', strtotime($karta['datum_vrijedi_od'])); ?></td>
                                    <td><?php echo number_format($karta['ukupna_cijena'], 2); ?> €</td>
                                    <td>
                                        <span class="karta-status <?php echo $status_class; ?>">
                                            <?php echo $status_text; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small><?php echo $karta['djelatnik_ime'] . ' ' . $karta['djelatnik_prezime']; ?></small>
                                    </td>
                                    <td>
                                        <a href="karta_detalji.php?id=<?php echo $karta['id']; ?>" 
                                           class="btn btn-sm btn-info" title="Detalji">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if(!$karta['placeno']): ?>
                                            <a href="karta_plati.php?id=<?php echo $karta['id']; ?>" 
                                               class="btn btn-sm btn-success" title="Označi kao plaćeno">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="karta_print.php?id=<?php echo $karta['id']; ?>" 
                                           target="_blank" class="btn btn-sm btn-secondary" title="Ispiši">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#karteTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/hr.json"
                }
            });
        });
    </script>
</body>
</html>