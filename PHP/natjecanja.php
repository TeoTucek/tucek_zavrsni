<?php
require_once 'config.php';
$page_title = "Natjecanja - Ribolovni Klub";
check_login();
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
        .competition-card {
            border: 2px solid #2c7873;
            border-radius: 10px;
            transition: transform 0.3s;
            overflow: hidden;
        }
        .competition-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .competition-header {
            background-color: #2c7873;
            color: white;
            padding: 15px;
        }
        .competition-body {
            padding: 20px;
        }
        .competition-status {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: bold;
        }
        .status-upcoming {
            background-color: #d4edda;
            color: #155724;
        }
        .status-ongoing {
            background-color: #cce5ff;
            color: #004085;
        }
        .status-finished {
            background-color: #f8d7da;
            color: #721c24;
        }
        .fish-icon {
            color: #2c7873;
            margin-right: 10px;
        }
        .countdown {
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
            color: #2c7873;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between mb-4">
                    <h2>Natjecanja</h2>
                    <div>
                        <a href="natjecanje_novo.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Novo natjecanje
                        </a>
                        <a href="moja_natjecanja.php" class="btn btn-outline-primary">
                            <i class="fas fa-trophy"></i> Moja natjecanja
                        </a>
                    </div>
                </div>

                <!-- Filteri -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="">Sva</option>
                                    <option value="upcoming" <?php echo ($_GET['status'] ?? '') == 'upcoming' ? 'selected' : ''; ?>>Nadolazeća</option>
                                    <option value="ongoing" <?php echo ($_GET['status'] ?? '') == 'ongoing' ? 'selected' : ''; ?>>Aktivna</option>
                                    <option value="finished" <?php echo ($_GET['status'] ?? '') == 'finished' ? 'selected' : ''; ?>>Završena</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Ribnjak</label>
                                <select class="form-select" name="ribnjak_id">
                                    <option value="">Svi</option>
                                    <?php
                                    $stmt = $pdo->query("SELECT * FROM ribnjaci WHERE aktivan = 1 ORDER BY naziv");
                                    while($ribnjak = $stmt->fetch()):
                                    ?>
                                        <option value="<?php echo $ribnjak['id_ribnjaci']; ?>" 
                                                <?php echo ($_GET['ribnjak_id'] ?? '') == $ribnjak['id_ribnjaci'] ? 'selected' : ''; ?>>
                                            <?php echo $ribnjak['naziv']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Vrsta ribe</label>
                                <select class="form-select" name="vrsta_ribe_id">
                                    <option value="">Sve</option>
                                    <?php
                                    $stmt = $pdo->query("SELECT * FROM vrsta_ribe ORDER BY naziv_ribe");
                                    while($vrsta = $stmt->fetch()):
                                    ?>
                                        <option value="<?php echo $vrsta['id_vrsta_ribe']; ?>" 
                                                <?php echo ($_GET['vrsta_ribe_id'] ?? '') == $vrsta['id_vrsta_ribe'] ? 'selected' : ''; ?>>
                                            <?php echo $vrsta['naziv_ribe']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Cijena prijave do</label>
                                <input type="number" class="form-control" name="max_price" 
                                       value="<?php echo $_GET['max_price'] ?? ''; ?>" step="0.01">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Filtriraj</button>
                                <a href="natjecanja.php" class="btn btn-secondary">Poništi</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Statistike -->
                <div class="row mb-4">
                    <?php
                    // Ukupno natjecanja
                    $stmt = $pdo->query("SELECT COUNT(*) as total FROM natjecanja");
                    $total = $stmt->fetch()['total'];
                    
                    // Aktívna natjecanja
                    $stmt = $pdo->query("SELECT COUNT(*) as active FROM natjecanja 
                                         WHERE aktivan = 1 AND datum_od > NOW()");
                    $active = $stmt->fetch()['active'];
                    
                    // Ukupno prijava
                    $stmt = $pdo->query("SELECT COUNT(*) as prijave FROM prijave_natjecanja");
                    $prijave = $stmt->fetch()['prijave'];
                    
                    // Ukupni prihodi
                    $stmt = $pdo->query("SELECT SUM(n.cijena_prijave) as prihodi 
                                         FROM prijave_natjecanja p
                                         JOIN natjecanja n ON p.natjecanje_id = n.id_natjecanje
                                         WHERE p.placeno = 1");
                    $prihodi = $stmt->fetch()['prihodi'];
                    ?>
                    
                    <div class="col-md-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $total; ?></h5>
                                <p class="card-text">Ukupno natjecanja</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $active; ?></h5>
                                <p class="card-text">Nadolazećih</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $prijave; ?></h5>
                                <p class="card-text">Ukupno prijava</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo number_format($prihodi, 2); ?> kn</h5>
                                <p class="card-text">Prihodi od natjecanja</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Najbliže natjecanje -->
                <?php
                $stmt = $pdo->prepare("SELECT n.*, r.naziv as ribnjak_naziv, vr.naziv_ribe,
                                      (SELECT COUNT(*) FROM prijave_natjecanja pn WHERE pn.natjecanje_id = n.id_natjecanje) as broj_prijava
                                      FROM natjecanja n
                                      JOIN ribnjaci r ON n.ribnjak_id = r.id_ribnjaci
                                      LEFT JOIN vrsta_ribe vr ON n.vrsta_ribe_id = vr.id_vrsta_ribe
                                      WHERE n.aktivan = 1 AND n.datum_od > NOW()
                                      ORDER BY n.datum_od ASC
                                      LIMIT 1");
                $stmt->execute();
                $closest_competition = $stmt->fetch();
                
                if($closest_competition):
                    $time_left = strtotime($closest_competition['datum_od']) - time();
                    $days_left = floor($time_left / (60 * 60 * 24));
                    $hours_left = floor(($time_left % (60 * 60 * 24)) / (60 * 60));
                ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h4>Najbliže natjecanje:</h4>
                                    <h3><?php echo $closest_competition['naziv']; ?></h3>
                                    <p>
                                        <i class="fas fa-calendar-alt"></i> 
                                        <?php echo date('d.m.Y H:i', strtotime($closest_competition['datum_od'])); ?> - 
                                        <?php echo date('H:i', strtotime($closest_competition['datum_do'])); ?>
                                    </p>
                                    <p>
                                        <i class="fas fa-map-marker-alt"></i> 
                                        <?php echo $closest_competition['ribnjak_naziv']; ?>
                                    </p>
                                    <?php if($closest_competition['naziv_ribe']): ?>
                                        <p>
                                            <i class="fas fa-fish"></i> 
                                            Vrsta ribe: <?php echo $closest_competition['naziv_ribe']; ?>
                                        </p>
                                    <?php endif; ?>
                                    <p>
                                        <i class="fas fa-users"></i> 
                                        Prijavljeno: <?php echo $closest_competition['broj_prijava']; ?> / 
                                        <?php echo $closest_competition['max_broj_sudionika']; ?>
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <div class="countdown">
                                        <div>Do natjecanja:</div>
                                        <div id="countdownTimer">
                                            <?php echo $days_left; ?> dana i <?php echo $hours_left; ?> sati
                                        </div>
                                    </div>
                                    <div class="d-grid gap-2 mt-3">
                                        <a href="natjecanje_detalji.php?id=<?php echo $closest_competition['id_natjecanje']; ?>" 
                                           class="btn btn-primary">Detalji</a>
                                        <a href="natjecanje_prijava.php?id=<?php echo $closest_competition['id_natjecanje']; ?>" 
                                           class="btn btn-success">Prijavi se</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Popis natjecanja -->
                <div class="row">
                    <?php
                    // Priprema query-ja s filterima
                    $query = "SELECT n.*, r.naziv as ribnjak_naziv, vr.naziv_ribe,
                             (SELECT COUNT(*) FROM prijave_natjecanja pn WHERE pn.natjecanje_id = n.id_natjecanje) as broj_prijava
                             FROM natjecanja n
                             JOIN ribnjaci r ON n.ribnjak_id = r.id_ribnjaci
                             LEFT JOIN vrsta_ribe vr ON n.vrsta_ribe_id = vr.id_vrsta_ribe
                             WHERE n.aktivan = 1";
                    
                    $params = [];
                    
                    // Filter po statusu
                    if(isset($_GET['status']) && !empty($_GET['status'])) {
                        $now = date('Y-m-d H:i:s');
                        if($_GET['status'] == 'upcoming') {
                            $query .= " AND n.datum_od > ?";
                            $params[] = $now;
                        } elseif($_GET['status'] == 'ongoing') {
                            $query .= " AND n.datum_od <= ? AND n.datum_do >= ?";
                            $params[] = $now;
                            $params[] = $now;
                        } elseif($_GET['status'] == 'finished') {
                            $query .= " AND n.datum_do < ?";
                            $params[] = $now;
                        }
                    }
                    
                    if(isset($_GET['ribnjak_id']) && !empty($_GET['ribnjak_id'])) {
                        $query .= " AND n.ribnjak_id = ?";
                        $params[] = $_GET['ribnjak_id'];
                    }
                    
                    if(isset($_GET['vrsta_ribe_id']) && !empty($_GET['vrsta_ribe_id'])) {
                        $query .= " AND n.vrsta_ribe_id = ?";
                        $params[] = $_GET['vrsta_ribe_id'];
                    }
                    
                    if(isset($_GET['max_price']) && !empty($_GET['max_price'])) {
                        $query .= " AND n.cijena_prijave <= ?";
                        $params[] = $_GET['max_price'];
                    }
                    
                    $query .= " ORDER BY n.datum_od ASC";
                    
                    $stmt = $pdo->prepare($query);
                    $stmt->execute($params);
                    
                    while($competition = $stmt->fetch()):
                        // Odredi status
                        $now = date('Y-m-d H:i:s');
                        if($competition['datum_od'] > $now) {
                            $status = 'upcoming';
                            $status_class = 'status-upcoming';
                            $status_text = 'Nadolazeće';
                        } elseif($competition['datum_do'] < $now) {
                            $status = 'finished';
                            $status_class = 'status-finished';
                            $status_text = 'Završeno';
                        } else {
                            $status = 'ongoing';
                            $status_class = 'status-ongoing';
                            $status_text = 'U tijeku';
                        }
                        
                        // Progres prijava
                        $progress = $competition['max_broj_sudionika'] > 0 
                            ? min(100, ($competition['broj_prijava'] / $competition['max_broj_sudionika']) * 100)
                            : 0;
                    ?>
                    <div class="col-md-4 mb-4">
                        <div class="competition-card h-100">
                            <div class="competition-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><?php echo $competition['naziv']; ?></h5>
                                    <span class="competition-status <?php echo $status_class; ?>">
                                        <?php echo $status_text; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="competition-body">
                                <p>
                                    <i class="fas fa-calendar-alt"></i> 
                                    <strong>Datum:</strong><br>
                                    <?php echo date('d.m.Y H:i', strtotime($competition['datum_od'])); ?> - 
                                    <?php echo date('H:i', strtotime($competition['datum_do'])); ?>
                                </p>
                                
                                <p>
                                    <i class="fas fa-map-marker-alt"></i> 
                                    <strong>Lokacija:</strong><br>
                                    <?php echo $competition['ribnjak_naziv']; ?>
                                </p>
                                
                                <?php if($competition['naziv_ribe']): ?>
                                    <p>
                                        <i class="fas fa-fish fish-icon"></i>
                                        <strong>Vrsta ribe:</strong><br>
                                        <?php echo $competition['naziv_ribe']; ?>
                                    </p>
                                <?php endif; ?>
                                
                                <p>
                                    <i class="fas fa-users"></i> 
                                    <strong>Prijave:</strong><br>
                                    <?php echo $competition['broj_prijava']; ?> / <?php echo $competition['max_broj_sudionika'] ?? '∞'; ?>
                                </p>
                                
                                <?php if($progress < 100 || $competition['max_broj_sudionika'] === null): ?>
                                    <div class="progress mb-3">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: <?php echo $progress; ?>%">
                                            <?php echo round($progress); ?>%
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-danger mb-3">
                                        <i class="fas fa-exclamation-triangle"></i> Popunjeno!
                                    </div>
                                <?php endif; ?>
                                
                                <p class="text-center mb-3">
                                    <span class="badge bg-success" style="font-size: 1.2em;">
                                        <?php echo number_format($competition['cijena_prijave'], 2); ?> kn
                                    </span>
                                </p>
                                
                                <div class="d-grid gap-2">
                                    <a href="natjecanje_detalji.php?id=<?php echo $competition['id_natjecanje']; ?>" 
                                       class="btn btn-outline-primary">Detalji</a>
                                    
                                    <?php if($status == 'upcoming'): ?>
                                        <a href="natjecanje_prijava.php?id=<?php echo $competition['id_natjecanje']; ?>" 
                                           class="btn btn-primary">Prijavi se</a>
                                    <?php elseif($status == 'finished'): ?>
                                        <a href="natjecanje_rezultati.php?id=<?php echo $competition['id_natjecanje']; ?>" 
                                           class="btn btn-success">Rezultati</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                    
                    <?php if($stmt->rowCount() == 0): ?>
                        <div class="col-12">
                            <div class="alert alert-info">
                                Nema natjecanja koja odgovaraju vašim filterima.
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Countdown timer
        function updateCountdown() {
            <?php if(isset($closest_competition)): ?>
                var endDate = new Date("<?php echo $closest_competition['datum_od']; ?>");
                var now = new Date();
                var timeDiff = endDate - now;
                
                if (timeDiff > 0) {
                    var days = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
                    var hours = Math.floor((timeDiff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var minutes = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((timeDiff % (1000 * 60)) / 1000);
                    
                    $('#countdownTimer').html(days + ' dana, ' + hours + 'h ' + minutes + 'm ' + seconds + 's');
                } else {
                    $('#countdownTimer').html('Natjecanje je počelo!');
                }
            <?php endif; ?>
        }
        
        // Ažuriraj svake sekunde
        setInterval(updateCountdown, 1000);
        updateCountdown();
    </script>
</body>
</html>