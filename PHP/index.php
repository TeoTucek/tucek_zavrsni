<?php
// index.php - Javna stranica
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ribnjak Končanica - R23 & Crnaja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), 
                        url('https://images.unsplash.com/photo-1467003909585-2f8a72700288?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 120px 0;
            text-align: center;
        }
        .ribnjak-card {
            transition: transform 0.3s;
            border: 2px solid #2c7873;
        }
        .ribnjak-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(44, 120, 115, 0.2);
        }
        .price-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #2c7873;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: bold;
        }
        .feature-icon {
            font-size: 3rem;
            color: #2c7873;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <!-- Hero sekcija -->
    <section class="hero-section">
        <div class="container">
            <h1 class="display-4 mb-4">Dobrodošli u Ribnjak Končanica</h1>
            <p class="lead mb-4">Najbolje ribolovno mjesto s dva odlična ribnjaka: R23 i Crnaja</p>
            <div class="row mt-5">
                <div class="col-md-6">
                    <a href="cjenik.php" class="btn btn-primary btn-lg w-100 mb-2">
                        <i class="fas fa-euro-sign"></i> Pogledaj cjenik
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="pravila.php" class="btn btn-outline-light btn-lg w-100 mb-2">
                        <i class="fas fa-book"></i> Pravila ribolova
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Naši ribnjaci -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Naši ribnjaci</h2>
            <div class="row">
                <?php
                $stmt = $pdo->query("SELECT * FROM ribnjaci ORDER BY id");
                while($ribnjak = $stmt->fetch()):
                ?>
                <div class="col-md-6 mb-4">
                    <div class="card ribnjak-card h-100">
                        <div class="position-relative">
                            <img src="<?php echo $ribnjak['ima_otok'] ? 
                                'https://images.unsplash.com/photo-1509316785289-025f5b846b35?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80' : 
                                'https://images.unsplash.com/photo-1467003909585-2f8a72700288?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80'; ?>" 
                                 class="card-img-top" alt="<?php echo $ribnjak['naziv']; ?>" style="height: 200px; object-fit: cover;">
                            <div class="price-badge">
                                <?php 
                                // Dohvati minimalnu cijenu za ovaj ribnjak
                                $stmt2 = $pdo->prepare("
                                    SELECT MIN(ck.cijena_eura) as min_cijena 
                                    FROM cijene_karata ck
                                    JOIN vrste_karata vk ON ck.vrsta_karte_id = vk.id
                                    WHERE vk.ribnjak_id = ? AND vk.za_otok = 0
                                ");
                                $stmt2->execute([$ribnjak['id']]);
                                $cijena = $stmt2->fetch()['min_cijena'] ?? 0;
                                echo "Od " . number_format($cijena, 0) . "€";
                                ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <h4 class="card-title"><?php echo $ribnjak['naziv']; ?></h4>
                            <p class="card-text">
                                <strong>Vrsta:</strong> 
                                <?php echo $ribnjak['vrsta_ribnjaka'] == 'sportski' ? 'Šaran i amur' : 'Grabežljiva riba'; ?>
                            </p>
                            <p class="card-text"><?php echo $ribnjak['opis']; ?></p>
                            
                            <div class="mb-3">
                                <strong>Vrste riba:</strong>
                                <div class="mt-2">
                                    <?php
                                    $stmt2 = $pdo->prepare("
                                        SELECT vr.naziv 
                                        FROM ribnjak_vrsta_ribe rvr
                                        JOIN vrste_ribe vr ON rvr.vrsta_ribe_id = vr.id
                                        WHERE rvr.ribnjak_id = ?
                                    ");
                                    $stmt2->execute([$ribnjak['id']]);
                                    while($vrsta = $stmt2->fetch()):
                                    ?>
                                        <span class="badge bg-info me-1 mb-1"><?php echo $vrsta['naziv']; ?></span>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted">
                                        <i class="fas fa-expand-arrows-alt"></i> 
                                        <?php echo $ribnjak['povrsina']; ?> ha
                                    </small>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">
                                        <i class="fas fa-users"></i> 
                                        <?php echo $ribnjak['broj_pozicija']; ?> pozicija
                                    </small>
                                </div>
                            </div>
                            
                            <?php if($ribnjak['ima_otok']): ?>
                                <div class="mt-3">
                                    <span class="badge bg-warning">
                                        <i class="fas fa-island-tropical"></i> Dostupan otok
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-transparent">
                            <a href="cjenik.php?ribnjak=<?php echo $ribnjak['id']; ?>" 
                               class="btn btn-outline-primary w-100">
                                <i class="fas fa-euro-sign"></i> Cjenik i rezervacija
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- Informacije -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Zašto odabrati nas?</h2>
            <div class="row">
                <div class="col-md-4 text-center mb-4">
                    <div class="feature-icon">
                        <i class="fas fa-fish"></i>
                    </div>
                    <h4>Kvalitetna riba</h4>
                    <p>Šarani i amuri do 25kg, prosječno 3.5kg. Grabežljiva riba za C&R.</p>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <div class="feature-icon">
                        <i class="fas fa-water"></i>
                    </div>
                    <h4>Dva različita ribnjaka</h4>
                    <p>R23 za šarana i amura, Crnaja isključivo za grabežljivu ribu (C&R).</p>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <div class="feature-icon">
                        <i class="fas fa-island-tropical"></i>
                    </div>
                    <h4>Otok za prave ribolovce</h4>
                    <p>Ekskluzivni otok na R23 sa posebnim pravilima i C&R načinom ribolova.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Kontakt -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h3>Kontakt informacije</h3>
                    <p><i class="fas fa-map-marker-alt me-2"></i> Končanica, Hrvatska</p>
                    <p><i class="fas fa-phone me-2"></i> +385 91 139 9970</p>
                    <p><i class="fas fa-clock me-2"></i> Radno vrijeme: 6:00 - 20:00</p>
                </div>
                <div class="col-md-6">
                    <h3>Rezervacija hrane</h3>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Važno:</strong> Hranjenje (kuhani kukuruz, peleti) mora se naručiti 
                        <strong>2 dana unaprijed</strong> na broj: <strong>091 139 9970</strong>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Ribnjak Končanica</h5>
                    <p>R23 & Ribnjak Crnaja C&R</p>
                </div>
                <div class="col-md-6 text-end">
                    <p>&copy; <?php echo date('Y'); ?> Ribnjak Končanica. Sva prava pridržana.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>