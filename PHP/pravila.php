<?php
// pravila.php - Javna pravila
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pravila ribolova - Ribnjak Končanica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .rule-card {
            border-left: 5px solid #2c7873;
            margin-bottom: 20px;
        }
        .rule-number {
            background-color: #2c7873;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
        }
        .rule-section {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .penalty-box {
            background-color: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container py-5">
        <h1 class="text-center mb-5">Pravila ribolova</h1>
        
        <!-- R23 Pravila -->
        <div class="rule-section">
            <h2 class="mb-4"><i class="fas fa-water me-2"></i> Pravila za R23 - Sportski ribnjak</h2>
            
            <?php
            $stmt = $pdo->prepare("
                SELECT * FROM pravila 
                WHERE ribnjak_id = 1 AND za_otok = 0
                ORDER BY redni_broj
            ");
            $stmt->execute();
            $counter = 1;
            while($pravilo = $stmt->fetch()):
            ?>
            <div class="card rule-card">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="rule-number"><?php echo $counter++; ?></div>
                        <div class="flex-grow-1">
                            <p class="card-text mb-0"><?php echo $pravilo['tekst_pravila']; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
            
            <!-- Kazne -->
            <div class="penalty-box">
                <h4><i class="fas fa-exclamation-triangle text-warning me-2"></i> Kazne:</h4>
                <ul class="mb-0">
                    <li>Riba teža od 3.5kg koja se ne vrati u ribnjak: <strong>1000€ + cijena ribe</strong></li>
                    <li>Oštećena riba vraćena u ribnjak: <strong>1000€</strong></li>
                    <li>Korištenje prostirke umjesto kadice: <strong>Uklanjanje s ribnjaka</strong></li>
                    <li>Skrivene ribe prilikom izlaska: <strong>1000€ + trajna zabrana</strong></li>
                </ul>
            </div>
        </div>

        <!-- Otok Pravila -->
        <div class="rule-section">
            <h2 class="mb-4"><i class="fas fa-island-tropical me-2"></i> Pravila za otok R23</h2>
            
            <?php
            $stmt = $pdo->prepare("
                SELECT * FROM pravila 
                WHERE za_otok = 1
                ORDER BY redni_broj
            ");
            $stmt->execute();
            $counter = 1;
            while($pravilo = $stmt->fetch()):
            ?>
            <div class="card rule-card">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="rule-number"><?php echo $counter++; ?></div>
                        <div class="flex-grow-1">
                            <p class="card-text mb-0"><?php echo $pravilo['tekst_pravila']; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <!-- Opća pravila -->
        <div class="rule-section">
            <h2 class="mb-4"><i class="fas fa-fish me-2"></i> Opća pravila za sve ribnjake</h2>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-check-circle text-success me-2"></i>Dozvoljeno:</h5>
                            <ul>
                                <li>Svi vrsti mamaca i hrane (boile, pelete, sjemenke)</li>
                                <li>Brašnasta hrana</li>
                                <li>Brodići na daljinsko upravljanje (ako ne smetaju)</li>
                                <li>Kućni ljubimci na uzici</li>
                                <li>Korištenje kadice za ribu</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-ban text-danger me-2"></i>Zabranjeno:</h5>
                            <ul>
                                <li>Živi mamei</li>
                                <li>Ribolov na dva mjesta istovremeno</li>
                                <li>Čuvanje pozicija bez nadzora</li>
                                <li>Ribolov mrežama, vršama, parangalima</li>
                                <li>Strujom i drugim nesportskim metodama</li>
                                <li>Držanje ribe u čuvarici pa vraćanje</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prihvaćanje pravila -->
        <div class="alert alert-info mt-4">
            <h5><i class="fas fa-handshake me-2"></i> Prihvaćanje pravila:</h5>
            <p class="mb-0">
                Kupnjom ulaznice za Sportski ribnjak Končanica prihvaćate sva pravila, uvjete i sankcije 
                Ribnjačarstva Končanica d.d. navedenih u „Pravilniku na R23 - sportskom ribnjaku Končanica" 
                te ga se obavezujete pridržavati.
            </p>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>