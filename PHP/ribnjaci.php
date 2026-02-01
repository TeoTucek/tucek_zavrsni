<?php
require_once 'config.php';
$page_title = "Ribnjaci - Ribolovni Klub";
check_login();
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
        .ribnjak-card {
            transition: transform 0.3s;
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
        }
        .ribnjak-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .ribnjak-image {
            height: 200px;
            background-size: cover;
            background-position: center;
        }
        .ribnjak-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2c7873;
        }
        .vrsta-ribe-badge {
            background-color: #e8f4f8;
            color: #2c7873;
            padding: 3px 8px;
            border-radius: 15px;
            font-size: 0.8rem;
            margin: 2px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <h2 class="mb-4">Naši ribnjaci</h2>
                
                <!-- Filteri -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Naziv</label>
                                <input type="text" class="form-control" name="search" 
                                       value="<?php echo $_GET['search'] ?? ''; ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Max cijena po satu</label>
                                <input type="number" class="form-control" name="max_price" 
                                       value="<?php echo $_GET['max_price'] ?? ''; ?>" step="0.01">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Min površina (ha)</label>
                                <input type="number" class="form-control" name="min_area" 
                                       value="<?php echo $_GET['min_area'] ?? ''; ?>" step="0.1">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Filtriraj</button>
                                <a href="ribnjaci.php" class="btn btn-secondary">Poništi</a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Popis ribnjaka -->
                <div class="row">
                    <?php
                    // Priprema query-ja s filterima
                    $query = "SELECT r.*, 
                             (SELECT GROUP_CONCAT(vr.naziv_ribe) 
                              FROM ribnjak_vrsta_ribe rvr 
                              JOIN vrsta_ribe vr ON rvr.vrsta_id = vr.id_vrsta_ribe 
                              WHERE rvr.ribnjaci_id = r.id_ribnjaci) as vrste_ribe
                             FROM ribnjaci r 
                             WHERE r.aktivan = 1";
                    
                    $params = [];
                    
                    if(isset($_GET['search']) && !empty($_GET['search'])) {
                        $query .= " AND r.naziv LIKE ?";
                        $params[] = '%' . $_GET['search'] . '%';
                    }
                    
                    if(isset($_GET['max_price']) && !empty($_GET['max_price'])) {
                        $query .= " AND r.cijena_po_satu <= ?";
                        $params[] = $_GET['max_price'];
                    }
                    
                    if(isset($_GET['min_area']) && !empty($_GET['min_area'])) {
                        $query .= " AND r.povrsina >= ?";
                        $params[] = $_GET['min_area'];
                    }
                    
                    $query .= " ORDER BY r.cijena_po_satu ASC";
                    
                    $stmt = $pdo->prepare($query);
                    $stmt->execute($params);
                    
                    while($ribnjak = $stmt->fetch()):
                        // Slike za različite ribnjake
                        $images = [
                            'https://images.unsplash.com/photo-1467003909585-2f8a72700288?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80',
                            'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80',
                            'https://images.unsplash.com/photo-1439066615861-d1af74d74000?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80',
                            'https://images.unsplash.com/photo-1509316785289-025f5b846b35?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80',
                            'https://images.unsplash.com/photo-1475924156734-496f6cac6ec1?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80'
                        ];
                        $image_index = ($ribnjak['id_ribnjaci'] - 1) % count($images);
                    ?>
                    <div class="col-md-4 mb-4">
                        <div class="card ribnjak-card h-100">
                            <div class="ribnjak-image" 
                                 style="background-image: url('<?php echo $images[$image_index]; ?>')">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $ribnjak['naziv']; ?></h5>
                                <p class="card-text">
                                    <i class="fas fa-map-marker-alt"></i> 
                                    <?php echo $ribnjak['lokacija']; ?>
                                </p>
                                <p class="card-text">
                                    <i class="fas fa-expand-arrows-alt"></i> 
                                    <?php echo $ribnjak['povrsina']; ?> ha
                                </p>
                                <p class="card-text">
                                    <i class="fas fa-user-friends"></i> 
                                    Max: <?php echo $ribnjak['max_ribolovaca']; ?> ribolovaca
                                </p>
                                
                                <?php if($ribnjak['vrste_ribe']): ?>
                                    <div class="mb-3">
                                        <strong>Vrste riba:</strong><br>
                                        <?php 
                                        $vrste = explode(',', $ribnjak['vrste_ribe']);
                                        foreach($vrste as $vrsta):
                                        ?>
                                            <span class="vrsta-ribe-badge"><?php echo trim($vrsta); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="ribnjak-price">
                                        <?php echo number_format($ribnjak['cijena_po_satu'], 2); ?> kn/h
                                    </div>
                                    <div>
                                        <a href="karta_nova.php?ribnjak=<?php echo $ribnjak['id_ribnjaci']; ?>" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-ticket-alt"></i> Rezerviraj
                                        </a>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#detailsModal<?php echo $ribnjak['id_ribnjaci']; ?>">
                                            Detalji
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modal za detalje -->
                    <div class="modal fade" id="detailsModal<?php echo $ribnjak['id_ribnjaci']; ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title"><?php echo $ribnjak['naziv']; ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Lokacija:</strong><br><?php echo $ribnjak['lokacija']; ?></p>
                                            <p><strong>Površina:</strong><br><?php echo $ribnjak['povrsina']; ?> ha</p>
                                            <p><strong>Max broj ribolovaca:</strong><br><?php echo $ribnjak['max_ribolovaca']; ?></p>
                                            <p><strong>Cijena po satu:</strong><br><?php echo $ribnjak['cijena_po_satu']; ?> kn</p>
                                        </div>
                                        <div class="col-md-6">
                                            <?php if($ribnjak['opis']): ?>
                                                <p><strong>Opis:</strong><br><?php echo $ribnjak['opis']; ?></p>
                                            <?php endif; ?>
                                            
                                            <?php if($ribnjak['napomena']): ?>
                                                <p><strong>Napomena:</strong><br><?php echo $ribnjak['napomena']; ?></p>
                                            <?php endif; ?>
                                            
                                            <!-- Vrste riba -->
                                            <p><strong>Vrste riba u ribnjaku:</strong></p>
                                            <?php
                                            $stmt2 = $pdo->prepare("SELECT vr.naziv_ribe, rvr.prosjecna_tezina, rvr.zastupljenost
                                                                    FROM ribnjak_vrsta_ribe rvr
                                                                    JOIN vrsta_ribe vr ON rvr.vrsta_id = vr.id_vrsta_ribe
                                                                    WHERE rvr.ribnjaci_id = ?");
                                            $stmt2->execute([$ribnjak['id_ribnjaci']]);
                                            while($vrsta = $stmt2->fetch()):
                                            ?>
                                                <div class="d-flex justify-content-between border-bottom py-1">
                                                    <span><?php echo $vrsta['naziv_ribe']; ?></span>
                                                    <span class="text-muted">
                                                        Prosječno: <?php echo $vrsta['prosjecna_tezina']; ?> kg
                                                        (<?php echo $vrsta['zastupljenost']; ?>)
                                                    </span>
                                                </div>
                                            <?php endwhile; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zatvori</button>
                                    <a href="karta_nova.php?ribnjak=<?php echo $ribnjak['id_ribnjaci']; ?>" 
                                       class="btn btn-primary">Rezerviraj kartu</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                    
                    <?php if($stmt->rowCount() == 0): ?>
                        <div class="col-12">
                            <div class="alert alert-info">
                                Nema ribnjaka koji odgovaraju vašim filterima.
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Statistike -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Statistika ribnjaka</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php
                            // Ukupna površina
                            $stmt = $pdo->query("SELECT SUM(povrsina) as ukupna_povrsina FROM ribnjaci WHERE aktivan = 1");
                            $row = $stmt->fetch();
                            ?>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h3><?php echo number_format($row['ukupna_povrsina'], 1); ?> ha</h3>
                                    <p class="text-muted">Ukupna površina</p>
                                </div>
                            </div>
                            
                            <?php
                            // Prosječna cijena
                            $stmt = $pdo->query("SELECT AVG(cijena_po_satu) as prosjecna_cijena FROM ribnjaci WHERE aktivan = 1");
                            $row = $stmt->fetch();
                            ?>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h3><?php echo number_format($row['prosjecna_cijena'], 2); ?> kn</h3>
                                    <p class="text-muted">Prosječna cijena po satu</p>
                                </div>
                            </div>
                            
                            <?php
                            // Ukupni kapacitet
                            $stmt = $pdo->query("SELECT SUM(max_ribolovaca) as ukupni_kapacitet FROM ribnjaci WHERE aktivan = 1");
                            $row = $stmt->fetch();
                            ?>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h3><?php echo $row['ukupni_kapacitet']; ?></h3>
                                    <p class="text-muted">Ukupni kapacitet</p>
                                </div>
                            </div>
                            
                            <?php
                            // Broj vrsta riba
                            $stmt = $pdo->query("SELECT COUNT(DISTINCT vrsta_id) as broj_vrsta FROM ribnjak_vrsta_ribe");
                            $row = $stmt->fetch();
                            ?>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h3><?php echo $row['broj_vrsta']; ?></h3>
                                    <p class="text-muted">Različitih vrsta riba</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>