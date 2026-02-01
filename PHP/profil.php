<?php
require_once 'config.php';
$page_title = "Moj profil - Ribolovni Klub";
check_login();

$user_id = $_SESSION['user_id'];
$user_data = get_user_data($pdo, $user_id);

// Ažuriranje profila
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $ime = clean_input($_POST['ime']);
    $prezime = clean_input($_POST['prezime']);
    $adresa = clean_input($_POST['adresa']);
    $telefon = clean_input($_POST['telefon']);
    $email = clean_input($_POST['email']);
    
    $stmt = $pdo->prepare("UPDATE clan SET ime = ?, prezime = ?, adresa = ?, 
                          telefon = ?, email = ? WHERE id_Clan = ?");
    $stmt->execute([$ime, $prezime, $adresa, $telefon, $email, $user_id]);
    
    // Ažuriraj sesiju
    $_SESSION['user_name'] = $ime . ' ' . $prezime;
    $_SESSION['user_email'] = $email;
    
    $_SESSION['success'] = "Profil uspješno ažuriran!";
    header("Refresh: 0");
    exit();
}

// Kupovina godišnje ulaznice
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buy_annual'])) {
    $stmt = $pdo->prepare("UPDATE clan SET godisnja_ulaznica = 1 WHERE id_Clan = ?");
    $stmt->execute([$user_id]);
    
    $_SESSION['success'] = "Godišnja ulaznica kupljena!";
    header("Refresh: 0");
    exit();
}

// Dohvati statistike korisnika
$stats = [];

// Broj karata
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM karta WHERE clan_id = ?");
$stmt->execute([$user_id]);
$stats['karte'] = $stmt->fetch()['count'];

// Ukupno potrošeno
$stmt = $pdo->prepare("SELECT SUM(ukupna_cijena) as total FROM karta WHERE clan_id = ? AND placeno = 1");
$stmt->execute([$user_id]);
$stats['potroseno'] = $stmt->fetch()['total'] ?? 0;

// Broj izlova
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM izlov WHERE clan_id = ?");
$stmt->execute([$user_id]);
$stats['izlovi'] = $stmt->fetch()['count'];

// Broj natjecanja
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM prijave_natjecanja WHERE clan_id = ?");
$stmt->execute([$user_id]);
$stats['natjecanja'] = $stmt->fetch()['count'];

// Posljednji izlov
$stmt = $pdo->prepare("SELECT * FROM izlov WHERE clan_id = ? ORDER BY datum DESC LIMIT 1");
$stmt->execute([$user_id]);
$last_catch = $stmt->fetch();
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
        .profile-header {
            background: linear-gradient(135deg, #2c7873 0%, #245c58 100%);
            color: white;
            padding: 40px 0;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .profile-avatar {
            width: 150px;
            height: 150px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            color: #2c7873;
            margin: 0 auto 20px;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-card .icon {
            font-size: 2rem;
            color: #2c7873;
            margin-bottom: 10px;
        }
        .nav-tabs .nav-link {
            color: #2c7873;
        }
        .nav-tabs .nav-link.active {
            background-color: #2c7873;
            color: white;
            border-color: #2c7873;
        }
        .badge-member {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            color: #333;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <!-- Poruke -->
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Profil header -->
        <div class="profile-header">
            <div class="row align-items-center">
                <div class="col-md-4 text-center">
                    <div class="profile-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <h3><?php echo $user_data['ime'] . ' ' . $user_data['prezime']; ?></h3>
                    <p class="mb-0">Član od: <?php echo date('d.m.Y', strtotime($user_data['datum_clanstva'])); ?></p>
                </div>
                <div class="col-md-8">
                    <div class="row">
                        <?php if($user_data['broj_clanske_karte']): ?>
                        <div class="col-md-6">
                            <div class="bg-white text-dark p-3 rounded mb-3">
                                <small>Broj članske karte:</small><br>
                                <strong><?php echo $user_data['broj_clanske_karte']; ?></strong>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="col-md-6">
                            <div class="bg-white text-dark p-3 rounded mb-3">
                                <small>Status:</small><br>
                                <?php if($user_data['status'] == 'aktivan'): ?>
                                    <span class="badge bg-success">AKTIVAN</span>
                                <?php elseif($user_data['status'] == 'neaktivan'): ?>
                                    <span class="badge bg-warning">NEAKTIVAN</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">SUSPENDIRAN</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="bg-white text-dark p-3 rounded">
                                <small>Godišnja ulaznica:</small><br>
                                <?php if($user_data['godisnja_ulaznica']): ?>
                                    <span class="badge bg-success">
                                        <i class="fas fa-check"></i> AKTIVNA
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning">
                                        <i class="fas fa-times"></i> NEMA
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="bg-white text-dark p-3 rounded">
                                <small>JMBG:</small><br>
                                <strong><?php echo $user_data['jmbg']; ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4" id="profileTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" 
                        data-bs-target="#overview" type="button">Pregled</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="edit-tab" data-bs-toggle="tab" 
                        data-bs-target="#edit" type="button">Uredi profil</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="history-tab" data-bs-toggle="tab" 
                        data-bs-target="#history" type="button">Povijest</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="annual-tab" data-bs-toggle="tab" 
                        data-bs-target="#annual" type="button">Godišnja ulaznica</button>
            </li>
        </ul>

        <div class="tab-content" id="profileTabContent">
            <!-- Tab 1: Pregled -->
            <div class="tab-pane fade show active" id="overview" role="tabpanel">
                <div class="row">
                    <!-- Statistike -->
                    <div class="col-md-3">
                        <div class="stat-card text-center">
                            <div class="icon">
                                <i class="fas fa-ticket-alt"></i>
                            </div>
                            <h3><?php echo $stats['karte']; ?></h3>
                            <p class="text-muted">Kupovine karata</p>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stat-card text-center">
                            <div class="icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <h3><?php echo number_format($stats['potroseno'], 2); ?> kn</h3>
                            <p class="text-muted">Ukupno potrošeno</p>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stat-card text-center">
                            <div class="icon">
                                <i class="fas fa-fish"></i>
                            </div>
                            <h3><?php echo $stats['izlovi']; ?></h3>
                            <p class="text-muted">Ukupno izlova</p>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stat-card text-center">
                            <div class="icon">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <h3><?php echo $stats['natjecanja']; ?></h3>
                            <p class="text-muted">Natjecanja</p>
                        </div>
                    </div>
                </div>

                <!-- Posljednji izlov -->
                <?php if($last_catch): ?>
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Posljednji izlov</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <p>
                                        <strong>Datum:</strong> 
                                        <?php echo date('d.m.Y', strtotime($last_catch['datum'])); ?>
                                    </p>
                                    <p>
                                        <strong>Vrijeme:</strong> 
                                        <?php echo $last_catch['vrijeme_pocetka'] . ' - ' . $last_catch['vrijeme_zavrsetka']; ?>
                                    </p>
                                    <p>
                                        <strong>Ukupno ulova:</strong> <?php echo $last_catch['ukupno_ulova']; ?>
                                    </p>
                                    <p>
                                        <strong>Ukupna težina:</strong> <?php echo $last_catch['ukupna_tezina']; ?> kg
                                    </p>
                                    <?php if($last_catch['napomena']): ?>
                                        <p><strong>Napomena:</strong> <?php echo $last_catch['napomena']; ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-4">
                                    <?php if($last_catch['ocjena_iskustva']): ?>
                                        <h5>Ocjena iskustva:</h5>
                                        <div class="star-rating">
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star <?php echo $i <= $last_catch['ocjena_iskustva'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="mt-3">
                                        <a href="izlov_detalji.php?id=<?php echo $last_catch['id_izlov']; ?>" 
                                           class="btn btn-primary btn-sm">Vidi detalje</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Najnovije karte -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Najnovije karte</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Ribnjak</th>
                                        <th>Datum</th>
                                        <th>Iznos</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $pdo->prepare("SELECT k.*, r.naziv as ribnjak_naziv 
                                                          FROM karta k
                                                          JOIN ribnjaci r ON k.ribnjak_id = r.id_ribnjaci
                                                          WHERE k.clan_id = ?
                                                          ORDER BY k.datum DESC
                                                          LIMIT 5");
                                    $stmt->execute([$user_id]);
                                    $counter = 1;
                                    while($karta = $stmt->fetch()):
                                    ?>
                                    <tr>
                                        <td><?php echo $counter++; ?></td>
                                        <td><?php echo $karta['ribnjak_naziv']; ?></td>
                                        <td><?php echo date('d.m.Y', strtotime($karta['datum'])); ?></td>
                                        <td><?php echo number_format($karta['ukupna_cijena'], 2); ?> kn</td>
                                        <td>
                                            <?php if($karta['placeno']): ?>
                                                <span class="badge bg-success">Plaćeno</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Na čekanju</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="karta_detalji.php?id=<?php echo $karta['id_Karta']; ?>" 
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="karte.php" class="btn btn-outline-primary">Vidi sve karte</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Uredi profil -->
            <div class="tab-pane fade" id="edit" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Uredi profil</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Ime *</label>
                                        <input type="text" class="form-control" name="ime" 
                                               value="<?php echo $user_data['ime']; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Prezime *</label>
                                        <input type="text" class="form-control" name="prezime" 
                                               value="<?php echo $user_data['prezime']; ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Adresa *</label>
                                <input type="text" class="form-control" name="adresa" 
                                       value="<?php echo $user_data['adresa']; ?>" required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Telefon *</label>
                                        <input type="text" class="form-control" name="telefon" 
                                               value="<?php echo $user_data['telefon']; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" 
                                               value="<?php echo $user_data['email']; ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                JMBG se ne može mijenjati. Za promjenu drugih podataka kontaktirajte administratora.
                            </div>
                            
                            <button type="submit" name="update_profile" class="btn btn-primary">
                                <i class="fas fa-save"></i> Spremi promjene
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Povijest -->
            <div class="tab-pane fade" id="history" role="tabpanel">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Povijest karata</h5>
                            </div>
                            <div class="card-body">
                                <?php
                                $stmt = $pdo->prepare("SELECT k.*, r.naziv as ribnjak_naziv 
                                                      FROM karta k
                                                      JOIN ribnjaci r ON k.ribnjak_id = r.id_ribnjaci
                                                      WHERE k.clan_id = ?
                                                      ORDER BY k.datum DESC");
                                $stmt->execute([$user_id]);
                                
                                if($stmt->rowCount() > 0):
                                ?>
                                    <div class="list-group">
                                        <?php while($karta = $stmt->fetch()): ?>
                                            <a href="karta_detalji.php?id=<?php echo $karta['id_Karta']; ?>" 
                                               class="list-group-item list-group-item-action">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-1"><?php echo $karta['ribnjak_naziv']; ?></h6>
                                                    <small><?php echo date('d.m.Y', strtotime($karta['datum'])); ?></small>
                                                </div>
                                                <p class="mb-1">
                                                    Trajanje: <?php echo $karta['trajanje']; ?>h | 
                                                    Iznos: <?php echo number_format($karta['ukupna_cijena'], 2); ?> kn
                                                </p>
                                                <small>
                                                    <?php if($karta['placeno']): ?>
                                                        <span class="text-success">Plaćeno</span>
                                                    <?php else: ?>
                                                        <span class="text-warning">Na čekanju</span>
                                                    <?php endif; ?>
                                                </small>
                                            </a>
                                        <?php endwhile; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted">Nema kupovina karata.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Povijest izlova</h5>
                            </div>
                            <div class="card-body">
                                <?php
                                $stmt = $pdo->prepare("SELECT i.* FROM izlov i WHERE i.clan_id = ? ORDER BY i.datum DESC");
                                $stmt->execute([$user_id]);
                                
                                if($stmt->rowCount() > 0):
                                ?>
                                    <div class="list-group">
                                        <?php while($izlov = $stmt->fetch()): ?>
                                            <a href="izlov_detalji.php?id=<?php echo $izlov['id_izlov']; ?>" 
                                               class="list-group-item list-group-item-action">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-1">Izlov #<?php echo $izlov['id_izlov']; ?></h6>
                                                    <small><?php echo date('d.m.Y', strtotime($izlov['datum'])); ?></small>
                                                </div>
                                                <p class="mb-1">
                                                    Ulova: <?php echo $izlov['ukupno_ulova']; ?> | 
                                                    Težina: <?php echo $izlov['ukupna_tezina']; ?> kg
                                                </p>
                                                <?php if($izlov['ocjena_iskustva']): ?>
                                                    <small>
                                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                                            <i class="fas fa-star <?php echo $i <= $izlov['ocjena_iskustva'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                                        <?php endfor; ?>
                                                    </small>
                                                <?php endif; ?>
                                            </a>
                                        <?php endwhile; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted">Nema zabilježenih izlova.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 4: Godišnja ulaznica -->
            <div class="tab-pane fade" id="annual" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Godišnja ulaznica</h5>
                    </div>
                    <div class="card-body">
                        <?php if($user_data['godisnja_ulaznica']): ?>
                            <div class="alert alert-success">
                                <h4><i class="fas fa-crown"></i> Čestitamo!</h4>
                                <p>Već imate aktivnu godišnju ulaznicu. Vaši benefiti:</p>
                                <ul>
                                    <li>20% popusta na sve dnevne karte</li>
                                    <li>Prioritetna rezervacija mjesta</li>
                                    <li>Pristup ekskluzivnim natjecanjima</li>
                                    <li>Besplatni najam opreme jednom mjesečno</li>
                                </ul>
                                <p class="mb-0">
                                    <strong>Vaša ulaznica vrijedi do:</strong> 
                                    <?php 
                                    $expiry_date = date('Y-m-d', strtotime($user_data['datum_clanstva'] . ' +1 year'));
                                    echo date('d.m.Y', strtotime($expiry_date));
                                    ?>
                                </p>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <div class="col-md-8">
                                    <h4>Kupite godišnju ulaznicu</h4>
                                    <p>Godišnja ulaznica vam donosi brojne pogodnosti:</p>
                                    
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-body">
                                                    <h5 class="card-title text-success">
                                                        <i class="fas fa-percentage"></i> 20% popusta
                                                    </h5>
                                                    <p class="card-text">Na sve dnevne karte tijekom godine.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-body">
                                                    <h5 class="card-title text-primary">
                                                        <i class="fas fa-star"></i> Prioritet
                                                    </h5>
                                                    <p class="card-text">Prioritetna rezervacija na popularnim ribnjacima.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-body">
                                                    <h5 class="card-title text-warning">
                                                        <i class="fas fa-trophy"></i> Natjecanja
                                                    </h5>
                                                    <p class="card-text">Pristup ekskluzivnim članskim natjecanjima.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-body">
                                                    <h5 class="card-title text-info">
                                                        <i class="fas fa-tools"></i> Oprema
                                                    </h5>
                                                    <p class="card-text">Besplatni najam opreme jednom mjesečno.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        Godišnja ulaznica vrijedi 365 dana od dana kupnje.
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="card text-center">
                                        <div class="card-header bg-primary text-white">
                                            <h4 class="mb-0">Cijena</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="price-display mb-4">
                                                <h1>500,00 kn</h1>
                                                <p class="text-muted">godišnje</p>
                                            </div>
                                            
                                            <?php if($user_data['status'] == 'aktivan'): ?>
                                                <form method="POST" action="">
                                                    <div class="mb-3">
                                                        <label class="form-label">Odaberite način plaćanja:</label>
                                                        <select class="form-select" name="nacin_placanja" required>
                                                            <option value="gotovina">Gotovina</option>
                                                            <option value="kartica">Kartica</option>
                                                            <option value="virman">Virman</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="form-check mb-3">
                                                        <input class="form-check-input" type="checkbox" required 
                                                               id="terms_accept">
                                                        <label class="form-check-label" for="terms_accept">
                                                            Slažem se s uvjetima
                                                        </label>
                                                    </div>
                                                    
                                                    <button type="submit" name="buy_annual" class="btn btn-success btn-lg w-100">
                                                        <i class="fas fa-shopping-cart"></i> Kupi godišnju ulaznicu
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <div class="alert alert-warning">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    Samo aktivni članovi mogu kupiti godišnju ulaznicu.
                                                    Vaš status je: <?php echo strtoupper($user_data['status']); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>