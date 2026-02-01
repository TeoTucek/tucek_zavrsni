<?php
require_once 'config.php';
$page_title = "Nova karta - Ribolovni Klub";
check_login();

// Dohvati podatke korisnika
$user_id = $_SESSION['user_id'];
$user_data = get_user_data($pdo, $user_id);

// Dohvati ribnjake i tarife
$ribnjaci_stmt = $pdo->query("SELECT * FROM ribnjaci WHERE aktivan = 1 ORDER BY naziv");
$tarife_stmt = $pdo->query("SELECT * FROM tarifa ORDER BY cijena");

// Podrazumijevani ribnjak iz GET parametra
$default_ribnjak = $_GET['ribnjak'] ?? 1;

// Ako je poslan POST zahtjev (kreiranje karte)
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ribnjak_id = clean_input($_POST['ribnjak_id']);
    $tarifa_id = clean_input($_POST['tarifa_id']);
    $datum = clean_input($_POST['datum']);
    $trajanje = clean_input($_POST['trajanje']);
    $broj_osoba = clean_input($_POST['broj_osoba']);
    $nacin_placanja = clean_input($_POST['nacin_placanja']);
    
    // Provjeri je li korisnik član
    $je_clan = isset($_POST['je_clan']) ? 1 : 0;
    
    // Dohvati cijenu tarife
    $tarifa_stmt = $pdo->prepare("SELECT cijena FROM tarifa WHERE id_Tarifa = ?");
    $tarifa_stmt->execute([$tarifa_id]);
    $tarifa = $tarifa_stmt->fetch();
    
    // Dohvati cijenu ribnjaka po satu
    $ribnjak_stmt = $pdo->prepare("SELECT cijena_po_satu FROM ribnjaci WHERE id_ribnjaci = ?");
    $ribnjak_stmt->execute([$ribnjak_id]);
    $ribnjak = $ribnjak_stmt->fetch();
    
    // Izračunaj ukupnu cijenu
    $ukupna_cijena = ($tarifa['cijena'] + $ribnjak['cijena_po_satu']) * $trajanje * $broj_osoba;
    
    // Ako je član, primijeni popust
    if($je_clan && $user_data['godisnja_ulaznica']) {
        $ukupna_cijena *= 0.8; // 20% popusta
    }
    
    // Spremi kartu
    $stmt = $pdo->prepare("INSERT INTO karta (clan_id, ime_kupca, prezime_kupca, adresa, telefon, 
                         je_clan, tarifa_id, ribnjak_id, datum, trajanje, broj_osoba, 
                         ukupna_cijena, nacin_placanja) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $user_id,
        $user_data['ime'],
        $user_data['prezime'],
        $user_data['adresa'],
        $user_data['telefon'],
        $je_clan,
        $tarifa_id,
        $ribnjak_id,
        $datum,
        $trajanje,
        $broj_osoba,
        $ukupna_cijena,
        $nacin_placanja
    ]);
    
    $karta_id = $pdo->lastInsertId();
    
    // Preusmjeri na detalje karte
    $_SESSION['success'] = "Karta uspješno kreirana!";
    header("Location: karta_detalji.php?id=$karta_id");
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
        .calculator-card {
            background-color: #f8f9fa;
            border: 2px solid #2c7873;
            border-radius: 10px;
        }
        .price-display {
            font-size: 2rem;
            font-weight: bold;
            color: #2c7873;
            text-align: center;
        }
        .form-section {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #dee2e6;
        }
        .form-section:last-child {
            border-bottom: none;
        }
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
        }
        .step {
            text-align: center;
            flex: 1;
            position: relative;
        }
        .step-number {
            width: 40px;
            height: 40px;
            background-color: #6c757d;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
        }
        .step.active .step-number {
            background-color: #2c7873;
        }
        .step-line {
            position: absolute;
            top: 20px;
            left: 50%;
            right: 50%;
            height: 2px;
            background-color: #dee2e6;
            z-index: -1;
        }
        .step:first-child .step-line {
            left: 50%;
            right: -50%;
        }
        .step:last-child .step-line {
            left: -50%;
            right: 50%;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8">
                <!-- Koraci -->
                <div class="step-indicator">
                    <div class="step active">
                        <div class="step-number">1</div>
                        <div>Odabir ribnjaka</div>
                    </div>
                    <div class="step">
                        <div class="step-number">2</div>
                        <div>Odabir tarife</div>
                    </div>
                    <div class="step">
                        <div class="step-number">3</div>
                        <div>Detalji</div>
                    </div>
                    <div class="step">
                        <div class="step-number">4</div>
                        <div>Plaćanje</div>
                    </div>
                </div>

                <!-- Forma -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Nova karta za ribolov</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="kartaForm">
                            <!-- Sekcija 1: Ribnjak -->
                            <div class="form-section">
                                <h5><i class="fas fa-water"></i> Odabir ribnjaka</h5>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Ribnjak *</label>
                                            <select class="form-select" name="ribnjak_id" id="ribnjak_id" required>
                                                <option value="">Odaberite ribnjak...</option>
                                                <?php while($ribnjak = $ribnjaci_stmt->fetch()): ?>
                                                    <option value="<?php echo $ribnjak['id_ribnjaci']; ?>" 
                                                            data-price="<?php echo $ribnjak['cijena_po_satu']; ?>"
                                                            <?php echo $ribnjak['id_ribnjaci'] == $default_ribnjak ? 'selected' : ''; ?>>
                                                        <?php echo $ribnjak['naziv']; ?> - 
                                                        <?php echo $ribnjak['lokacija']; ?> - 
                                                        <?php echo $ribnjak['cijena_po_satu']; ?> kn/h
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row" id="ribnjak_info" style="display: none;">
                                    <div class="col-md-12">
                                        <div class="alert alert-info">
                                            <strong id="ribnjak_naziv"></strong><br>
                                            <span id="ribnjak_lokacija"></span><br>
                                            Površina: <span id="ribnjak_povrsina"></span> ha<br>
                                            Max ribolovaca: <span id="ribnjak_max"></span><br>
                                            Cijena po satu: <span id="ribnjak_cijena"></span> kn
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sekcija 2: Tarifa -->
                            <div class="form-section">
                                <h5><i class="fas fa-tag"></i> Odabir tarife</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Tarifa *</label>
                                            <select class="form-select" name="tarifa_id" id="tarifa_id" required>
                                                <option value="">Odaberite tarifu...</option>
                                                <?php while($tarifa = $tarife_stmt->fetch()): ?>
                                                    <option value="<?php echo $tarifa['id_Tarifa']; ?>" 
                                                            data-price="<?php echo $tarifa['cijena']; ?>">
                                                        <?php echo $tarifa['naziv']; ?> - 
                                                        <?php echo $tarifa['cijena']; ?> kn
                                                        (<?php echo $tarifa['kategorija']; ?>)
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Datum *</label>
                                            <input type="date" class="form-control" name="datum" 
                                                   value="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y-m-d'); ?>" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sekcija 3: Detalji -->
                            <div class="form-section">
                                <h5><i class="fas fa-info-circle"></i> Detalji</h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Trajanje (sati) *</label>
                                            <input type="number" class="form-control" name="trajanje" 
                                                   id="trajanje" value="4" min="1" max="24" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Broj osoba *</label>
                                            <input type="number" class="form-control" name="broj_osoba" 
                                                   id="broj_osoba" value="1" min="1" max="10" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Način plaćanja *</label>
                                            <select class="form-select" name="nacin_placanja" required>
                                                <option value="gotovina">Gotovina</option>
                                                <option value="kartica">Kartica</option>
                                                <option value="virman">Virman</option>
                                                <option value="cek">Ček</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" name="je_clan" 
                                           id="je_clan" <?php echo $user_data['godisnja_ulaznica'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="je_clan">
                                        Ja sam član kluba 
                                        <?php if($user_data['godisnja_ulaznica']): ?>
                                            (imate godišnju ulaznicu - 20% popusta!)
                                        <?php endif; ?>
                                    </label>
                                </div>
                            </div>

                            <!-- Sekcija 4: Plaćanje -->
                            <div class="form-section">
                                <h5><i class="fas fa-credit-card"></i> Plaćanje</h5>
                                <div class="price-display mb-3">
                                    Ukupno: <span id="total_price">0.00</span> kn
                                </div>
                                
                                <div class="alert alert-warning">
                                    <small>
                                        <i class="fas fa-exclamation-circle"></i>
                                        Napomena: Karta će biti aktivna tek nakon uplate.
                                        Možete platiti na licu mjesta ili putem virmana.
                                    </small>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="karte.php" class="btn btn-secondary">Odustani</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-shopping-cart"></i> Kreiraj kartu
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Desna strana - Kalkulator -->
            <div class="col-md-4">
                <div class="card calculator-card sticky-top" style="top: 20px;">
                    <div class="card-header">
                        <h5 class="mb-0">Kalkulator cijene</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Cijena ribnjaka po satu:</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="ribnjak_price_input" 
                                       value="0" readonly>
                                <span class="input-group-text">kn</span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Cijena tarife:</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="tarifa_price_input" 
                                       value="0" readonly>
                                <span class="input-group-text">kn</span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Ukupno po satu:</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="price_per_hour" 
                                       value="0" readonly>
                                <span class="input-group-text">kn</span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Trajanje:</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="duration_input" 
                                       value="4" min="1" max="24">
                                <span class="input-group-text">h</span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Broj osoba:</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="persons_input" 
                                       value="1" min="1" max="10">
                                <span class="input-group-text">os.</span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Popust za člana:</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" 
                                       id="discount_switch" <?php echo $user_data['godisnja_ulaznica'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="discount_switch">
                                    20% popusta
                                </label>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="text-center">
                            <h4>Ukupno:</h4>
                            <div class="price-display">
                                <span id="calculator_total">0.00</span> kn
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Info o članu -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0">Vaši podaci</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Ime i prezime:</strong><br>
                           <?php echo $user_data['ime'] . ' ' . $user_data['prezime']; ?>
                        </p>
                        <p><strong>Adresa:</strong><br><?php echo $user_data['adresa']; ?></p>
                        <p><strong>Telefon:</strong><br><?php echo $user_data['telefon']; ?></p>
                        <p><strong>Status:</strong><br>
                            <?php if($user_data['godisnja_ulaznica']): ?>
                                <span class="badge bg-success">Godišnja ulaznica</span>
                            <?php else: ?>
                                <span class="badge bg-warning">Nema godišnju ulaznicu</span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Funkcija za ažuriranje cijene
            function updatePrice() {
                var ribnjakPrice = parseFloat($('#ribnjak_id option:selected').data('price')) || 0;
                var tarifaPrice = parseFloat($('#tarifa_id option:selected').data('price')) || 0;
                var duration = parseInt($('#trajanje').val()) || 0;
                var persons = parseInt($('#broj_osoba').val()) || 0;
                var discount = $('#je_clan').is(':checked') ? 0.8 : 1;
                
                // Ažuriraj inpute
                $('#ribnjak_price_input').val(ribnjakPrice.toFixed(2));
                $('#tarifa_price_input').val(tarifaPrice.toFixed(2));
                $('#price_per_hour').val((ribnjakPrice + tarifaPrice).toFixed(2));
                $('#duration_input').val(duration);
                $('#persons_input').val(persons);
                $('#discount_switch').prop('checked', discount == 0.8);
                
                // Izračunaj ukupnu cijenu
                var total = (ribnjakPrice + tarifaPrice) * duration * persons * discount;
                
                // Ažuriraj prikaz
                $('#total_price').text(total.toFixed(2));
                $('#calculator_total').text(total.toFixed(2));
            }
            
            // Dohvati detalje ribnjaka
            function loadRibnjakDetails(ribnjakId) {
                if(!ribnjakId) {
                    $('#ribnjak_info').hide();
                    return;
                }
                
                $.ajax({
                    url: 'ajax_ribnjak.php',
                    method: 'GET',
                    data: { id: ribnjakId },
                    dataType: 'json',
                    success: function(data) {
                        if(data.success) {
                            $('#ribnjak_naziv').text(data.naziv);
                            $('#ribnjak_lokacija').text(data.lokacija);
                            $('#ribnjak_povrsina').text(data.povrsina);
                            $('#ribnjak_max').text(data.max_ribolovaca);
                            $('#ribnjak_cijena').text(data.cijena_po_satu);
                            $('#ribnjak_info').show();
                        }
                    }
                });
            }
            
            // Event listeneri
            $('#ribnjak_id').change(function() {
                updatePrice();
                loadRibnjakDetails($(this).val());
            });
            
            $('#tarifa_id').change(updatePrice);
            $('#trajanje, #broj_osoba').on('input', updatePrice);
            $('#je_clan').change(updatePrice);
            
            // Kalkulator inputs
            $('#duration_input').on('input', function() {
                $('#trajanje').val($(this).val());
                updatePrice();
            });
            
            $('#persons_input').on('input', function() {
                $('#broj_osoba').val($(this).val());
                updatePrice();
            });
            
            $('#discount_switch').change(function() {
                $('#je_clan').prop('checked', $(this).is(':checked'));
                updatePrice();
            });
            
            // Inicijalizacija
            updatePrice();
            if($('#ribnjak_id').val()) {
                loadRibnjakDetails($('#ribnjak_id').val());
            }
        });
    </script>
</body>
</html>