<?php
// cjenik.php - Javni cjenik
require_once 'config.php';

$ribnjak_id = $_GET['ribnjak'] ?? 0;
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cjenik - Ribnjak Končanica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .price-card {
            border: 2px solid #2c7873;
            border-radius: 10px;
            transition: transform 0.3s;
        }
        .price-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(44, 120, 115, 0.1);
        }
        .price-header {
            background-color: #2c7873;
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
        }
        .price-amount {
            font-size: 2.5rem;
            font-weight: bold;
            color: #2c7873;
        }
        .tab-content {
            padding: 20px 0;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container py-5">
        <h1 class="text-center mb-5">Cjenik ribolova</h1>
        
        <!-- Tabovi za ribnjake -->
        <ul class="nav nav-tabs justify-content-center mb-4" id="ribnjakTabs">
            <li class="nav-item">
                <a class="nav-link <?php echo !$ribnjak_id || $ribnjak_id == 1 ? 'active' : ''; ?>" 
                   data-bs-toggle="tab" href="#r23">
                    R23 - Sportski ribnjak
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $ribnjak_id == 2 ? 'active' : ''; ?>" 
                   data-bs-toggle="tab" href="#crnaja">
                    Ribnjak Crnaja C&R
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#otok">
                    Otok R23
                </a>
            </li>
        </ul>

        <div class="tab-content" id="ribnjakTabsContent">
            <!-- R23 -->
            <div class="tab-pane fade <?php echo !$ribnjak_id || $ribnjak_id == 1 ? 'show active' : ''; ?>" id="r23">
                <div class="row">
                    <!-- Dnevne karte -->
                    <div class="col-md-6 mb-4">
                        <div class="card price-card h-100">
                            <div class="price-header">
                                <h3 class="mb-0">Dnevne karte</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                $stmt = $pdo->prepare("
                                    SELECT vk.naziv, vk.opis, ck.cijena_eura, ck.radni_dan
                                    FROM cijene_karata ck
                                    JOIN vrste_karata vk ON ck.vrsta_karte_id = vk.id
                                    WHERE vk.ribnjak_id = 1 
                                    AND vk.za_otok = 0 
                                    AND vk.noćni = 0
                                    AND ck.trajanje_dana = 1
                                    ORDER BY ck.cijena_eura
                                ");
                                $stmt->execute();
                                while($karta = $stmt->fetch()):
                                ?>
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                    <div>
                                        <h5 class="mb-1"><?php echo $karta['naziv']; ?></h5>
                                        <?php if($karta['opis']): ?>
                                            <small class="text-muted"><?php echo $karta['opis']; ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="price-amount"><?php echo number_format($karta['cijena_eura'], 0); ?>€</div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Noćni ribolov -->
                    <div class="col-md-6 mb-4">
                        <div class="card price-card h-100">
                            <div class="price-header">
                                <h3 class="mb-0">Noćni ribolov</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                $stmt = $pdo->prepare("
                                    SELECT vk.naziv, vk.opis, ck.cijena_eura, ck.trajanje_dana
                                    FROM cijene_karata ck
                                    JOIN vrste_karata vk ON ck.vrsta_karte_id = vk.id
                                    WHERE vk.ribnjak_id = 1 
                                    AND vk.za_otok = 0 
                                    AND vk.noćni = 1
                                    ORDER BY ck.trajanje_dana
                                ");
                                $stmt->execute();
                                while($karta = $stmt->fetch()):
                                ?>
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                    <div>
                                        <h5 class="mb-1"><?php echo $karta['naziv']; ?> (<?php echo $karta['trajanje_dana']; ?> dana)</h5>
                                        <?php if($karta['opis']): ?>
                                            <small class="text-muted"><?php echo $karta['opis']; ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="price-amount"><?php echo number_format($karta['cijena_eura'], 0); ?>€</div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Dodatne usluge -->
                    <div class="col-md-12">
                        <div class="card price-card">
                            <div class="price-header">
                                <h3 class="mb-0">Dodatne usluge - R23</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php
                                    $stmt = $pdo->prepare("
                                        SELECT * FROM dodatne_usluge 
                                        WHERE ribnjak_id = 1 AND za_otok = 0
                                        ORDER BY cijena_eura
                                    ");
                                    $stmt->execute();
                                    while($usluga = $stmt->fetch()):
                                    ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1"><?php echo $usluga['naziv']; ?></h6>
                                                <?php if($usluga['opis']): ?>
                                                    <small class="text-muted"><?php echo $usluga['opis']; ?></small>
                                                <?php endif; ?>
                                            </div>
                                            <div class="price-amount"><?php echo number_format($usluga['cijena_eura'], 2); ?>€</div>
                                        </div>
                                    </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Crnaja -->
            <div class="tab-pane fade <?php echo $ribnjak_id == 2 ? 'show active' : ''; ?>" id="crnaja">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card price-card">
                            <div class="price-header">
                                <h3 class="mb-0">Ribnjak Crnaja C&R</h3>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info mb-4">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Napomena:</strong> Ribnjak Crnaja je isključivo Catch & Release (C&R) ribnjak.
                                    Sve ribe se moraju vratiti u vodu.
                                </div>
                                
                                <?php
                                $stmt = $pdo->prepare("
                                    SELECT vk.naziv, vk.opis, ck.cijena_eura
                                    FROM cijene_karata ck
                                    JOIN vrste_karata vk ON ck.vrsta_karte_id = vk.id
                                    WHERE vk.ribnjak_id = 2
                                    ORDER BY ck.cijena_eura
                                ");
                                $stmt->execute();
                                while($karta = $stmt->fetch()):
                                ?>
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                    <div>
                                        <h5 class="mb-1"><?php echo $karta['naziv']; ?></h5>
                                        <?php if($karta['opis']): ?>
                                            <small class="text-muted"><?php echo $karta['opis']; ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="price-amount"><?php echo number_format($karta['cijena_eura'], 0); ?>€</div>
                                </div>
                                <?php endwhile; ?>
                                
                                <!-- Dodatne usluge za Crnaju -->
                                <h5 class="mt-4 mb-3">Najam pribora</h5>
                                <?php
                                $stmt = $pdo->prepare("
                                    SELECT * FROM dodatne_usluge 
                                    WHERE ribnjak_id = 2
                                    ORDER BY cijena_eura
                                ");
                                $stmt->execute();
                                while($usluga = $stmt->fetch()):
                                ?>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <h6 class="mb-1"><?php echo $usluga['naziv']; ?></h6>
                                        <?php if($usluga['opis']): ?>
                                            <small class="text-muted"><?php echo $usluga['opis']; ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-primary fw-bold"><?php echo number_format($usluga['cijena_eura'], 2); ?>€</div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Otok -->
            <div class="tab-pane fade" id="otok">
                <div class="row">
                    <div class="col-md-12 mb-4">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Važno:</strong> Za posjetu otoku obavezna je prethodna rezervacija na broj: 
                            <strong>091 139 9970</strong>
                        </div>
                    </div>

                    <!-- Dnevne karte otok -->
                    <div class="col-md-6 mb-4">
                        <div class="card price-card h-100">
                            <div class="price-header">
                                <h3 class="mb-0">Dnevne karte - Otok</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                $stmt = $pdo->prepare("
                                    SELECT vk.naziv, vk.opis, ck.cijena_eura
                                    FROM cijene_karata ck
                                    JOIN vrste_karata vk ON ck.vrsta_karte_id = vk.id
                                    WHERE vk.za_otok = 1 AND vk.noćni = 0
                                    AND ck.trajanje_dana = 1
                                    ORDER BY ck.cijena_eura
                                ");
                                $stmt->execute();
                                while($karta = $stmt->fetch()):
                                ?>
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                    <div>
                                        <h5 class="mb-1"><?php echo $karta['naziv']; ?></h5>
                                        <?php if($karta['opis']): ?>
                                            <small class="text-muted"><?php echo $karta['opis']; ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="price-amount"><?php echo number_format($karta['cijena_eura'], 0); ?>€</div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Noćni ribolov otok -->
                    <div class="col-md-6 mb-4">
                        <div class="card price-card h-100">
                            <div class="price-header">
                                <h3 class="mb-0">Noćni ribolov - Otok</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                $stmt = $pdo->prepare("
                                    SELECT vk.naziv, vk.opis, ck.cijena_eura, ck.trajanje_dana
                                    FROM cijene_karata ck
                                    JOIN vrste_karata vk ON ck.vrsta_karte_id = vk.id
                                    WHERE vk.za_otok = 1 AND vk.noćni = 1
                                    ORDER BY ck.trajanje_dana
                                ");
                                $stmt->execute();
                                while($karta = $stmt->fetch()):
                                ?>
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                    <div>
                                        <h5 class="mb-1"><?php echo $karta['naziv']; ?> (<?php echo $karta['trajanje_dana']; ?> dana)</h5>
                                        <?php if($karta['opis']): ?>
                                            <small class="text-muted"><?php echo $karta['opis']; ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="price-amount"><?php echo number_format($karta['cijena_eura'], 0); ?>€</div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Dodatne usluge otok -->
                    <div class="col-md-12">
                        <div class="card price-card">
                            <div class="price-header">
                                <h3 class="mb-0">Dodatne usluge - Otok</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php
                                    $stmt = $pdo->prepare("
                                        SELECT * FROM dodatne_usluge 
                                        WHERE za_otok = 1
                                        ORDER BY cijena_eura
                                    ");
                                    $stmt->execute();
                                    while($usluga = $stmt->fetch()):
                                    ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1"><?php echo $usluga['naziv']; ?></h6>
                                                <?php if($usluga['opis']): ?>
                                                    <small class="text-muted"><?php echo $usluga['opis']; ?></small>
                                                <?php endif; ?>
                                            </div>
                                            <div class="price-amount"><?php echo number_format($usluga['cijena_eura'], 2); ?>€</div>
                                        </div>
                                    </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informacije -->
        <div class="row mt-5">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <h5><i class="fas fa-info-circle"></i> Važne informacije:</h5>
                    <ul class="mb-0">
                        <li>Radno vrijeme ribolova: radnim danima 7:00-19:00, vikendom 6:00-20:00</li>
                        <li>Noćni ribolov: od 6h prvog dana do 20h zadnjeg dana</li>
                        <li>Maksimalno 3 štapa po ribiču</li>
                        <li>Obavezna rezervacija hrane 2 dana unaprijed: 091 139 9970</li>
                        <li>Na Crnaji isključivo C&R (catch & release)</li>
                        <li>Za otok obavezna prethodna rezervacija</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Aktiviraj tab ovisno o URL parametru
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const ribnjak = urlParams.get('ribnjak');
            
            if(ribnjak === '2') {
                const tabTrigger = document.querySelector('[href="#crnaja"]');
                if(tabTrigger) {
                    new bootstrap.Tab(tabTrigger).show();
                }
            }
        });
    </script>
</body>
</html>