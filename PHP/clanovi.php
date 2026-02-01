<?php
require_once 'config.php';
$page_title = "Članovi - Ribolovni Klub";

// Provjera uloga
check_login();
$user_data = get_user_data($pdo, $_SESSION['user_id']);
$is_admin = ($user_data['status'] == 'admin');

// Akcije
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? 0;

// Dodavanje novog člana
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_member'])) {
    $ime = clean_input($_POST['ime']);
    $prezime = clean_input($_POST['prezime']);
    $jmbg = clean_input($_POST['jmbg']);
    $adresa = clean_input($_POST['adresa']);
    $telefon = clean_input($_POST['telefon']);
    $email = clean_input($_POST['email']);
    $godisnja_ulaznica = isset($_POST['godisnja_ulaznica']) ? 1 : 0;
    
    try {
        $broj_clanske_karte = 'CL-' . date('Y') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        
        $stmt = $pdo->prepare("INSERT INTO clan (ime, prezime, jmbg, adresa, telefon, email, godisnja_ulaznica, broj_clanske_karte) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$ime, $prezime, $jmbg, $adresa, $telefon, $email, $godisnja_ulaznica, $broj_clanske_karte]);
        
        $_SESSION['success'] = "Član uspješno dodan!";
        header("Location: clanovi.php");
        exit();
    } catch(PDOException $e) {
        $_SESSION['error'] = "Greška: " . $e->getMessage();
    }
}
require_once 'config.php';
$page_title = "Kupci - Ribnjak Končanica";

// Provjera admin uloge
check_djelatnik_login();
if(!is_admin()) {
    header("Location: index.php");
    exit();
}

// Akcije
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? 0;
// Ažuriranje člana
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_member'])) {
    $ime = clean_input($_POST['ime']);
    $prezime = clean_input($_POST['prezime']);
    $adresa = clean_input($_POST['adresa']);
    $telefon = clean_input($_POST['telefon']);
    $email = clean_input($_POST['email']);
    $status = clean_input($_POST['status']);
    $godisnja_ulaznica = isset($_POST['godisnja_ulaznica']) ? 1 : 0;
    
    $stmt = $pdo->prepare("UPDATE clan SET ime = ?, prezime = ?, adresa = ?, telefon = ?, 
                          email = ?, status = ?, godisnja_ulaznica = ? WHERE id_Clan = ?");
    $stmt->execute([$ime, $prezime, $adresa, $telefon, $email, $status, $godisnja_ulaznica, $id]);
    
    $_SESSION['success'] = "Član uspješno ažuriran!";
    header("Location: clanovi.php");
    exit();
}

// Brisanje člana
if($action == 'delete' && $is_admin) {
    $stmt = $pdo->prepare("DELETE FROM clan WHERE id_Clan = ?");
    $stmt->execute([$id]);
    
    $_SESSION['success'] = "Član uspješno obrisan!";
    header("Location: clanovi.php");
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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <style>
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.9em;
        }
        .status-aktivan { background-color: #d4edda; color: #155724; }
        .status-neaktivan { background-color: #fff3cd; color: #856404; }
        .status-suspendiran { background-color: #f8d7da; color: #721c24; }
        .card-header {
            background-color: #2c7873;
            color: white;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <!-- Poruke -->
                <?php if(isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Akcijski gumbi -->
                <div class="d-flex justify-content-between mb-4">
                    <h2>Članovi kluba</h2>
                    <div>
                        <a href="clanovi.php?action=new" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Novi član
                        </a>
                        <a href="clanovi.php?action=export" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Izvezi
                        </a>
                    </div>
                </div>

                <!-- Forma za novog člana/uređivanje -->
                <?php if($action == 'new' || $action == 'edit'): ?>
                    <?php
                    $member = null;
                    if($action == 'edit' && $id > 0) {
                        $stmt = $pdo->prepare("SELECT * FROM clan WHERE id_Clan = ?");
                        $stmt->execute([$id]);
                        $member = $stmt->fetch();
                    }
                    ?>
                    
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <?php echo $action == 'new' ? 'Dodaj novog člana' : 'Uredi člana'; ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Ime</label>
                                            <input type="text" class="form-control" name="ime" 
                                                   value="<?php echo $member['ime'] ?? ''; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Prezime</label>
                                            <input type="text" class="form-control" name="prezime" 
                                                   value="<?php echo $member['prezime'] ?? ''; ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">JMBG</label>
                                            <input type="text" class="form-control" name="jmbg" 
                                                   value="<?php echo $member['jmbg'] ?? ''; ?>" 
                                                   <?php echo $action == 'edit' ? 'readonly' : 'required'; ?>>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Telefon</label>
                                            <input type="text" class="form-control" name="telefon" 
                                                   value="<?php echo $member['telefon'] ?? ''; ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Adresa</label>
                                    <input type="text" class="form-control" name="adresa" 
                                           value="<?php echo $member['adresa'] ?? ''; ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" 
                                           value="<?php echo $member['email'] ?? ''; ?>">
                                </div>
                                
                                <?php if($action == 'edit' && $is_admin): ?>
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" name="status">
                                            <option value="aktivan" <?php echo ($member['status'] ?? '') == 'aktivan' ? 'selected' : ''; ?>>Aktivan</option>
                                            <option value="neaktivan" <?php echo ($member['status'] ?? '') == 'neaktivan' ? 'selected' : ''; ?>>Neaktivan</option>
                                            <option value="suspendiran" <?php echo ($member['status'] ?? '') == 'suspendiran' ? 'selected' : ''; ?>>Suspendiran</option>
                                        </select>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" name="godisnja_ulaznica" 
                                           id="godisnja_ulaznica" value="1" 
                                           <?php echo ($member['godisnja_ulaznica'] ?? 0) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="godisnja_ulaznica">
                                        Godišnja ulaznica
                                    </label>
                                </div>
                                
                                <div class="d-flex justify-content-end">
                                    <a href="clanovi.php" class="btn btn-secondary me-2">Odustani</a>
                                    <button type="submit" name="<?php echo $action == 'new' ? 'add_member' : 'update_member'; ?>" 
                                            class="btn btn-primary">
                                        <?php echo $action == 'new' ? 'Dodaj člana' : 'Spremi promjene'; ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <a href="clanovi.php" class="btn btn-outline-secondary mb-4">
                        <i class="fas fa-arrow-left"></i> Natrag na popis
                    </a>
                
                <!-- Popis članova -->
                <?php else: ?>
                    <div class="card">
                        <div class="card-body">
                            <table id="clanoviTable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Ime i prezime</th>
                                        <th>JMBG</th>
                                        <th>Telefon</th>
                                        <th>Status</th>
                                        <th>Članska karta</th>
                                        <th>Godišnja</th>
                                        <th>Akcije</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $pdo->query("SELECT * FROM clan ORDER BY prezime, ime");
                                    $counter = 1;
                                    while($row = $stmt->fetch()):
                                        $status_class = 'status-' . $row['status'];
                                    ?>
                                    <tr>
                                        <td><?php echo $counter++; ?></td>
                                        <td><?php echo $row['ime'] . ' ' . $row['prezime']; ?></td>
                                        <td><?php echo $row['jmbg']; ?></td>
                                        <td><?php echo $row['telefon']; ?></td>
                                        <td>
                                            <span class="status-badge <?php echo $status_class; ?>">
                                                <?php echo ucfirst($row['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $row['broj_clanske_karte']; ?></td>
                                        <td>
                                            <?php if($row['godisnja_ulaznica']): ?>
                                                <span class="badge bg-success">DA</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">NE</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="clanovi.php?action=edit&id=<?php echo $row['id_Clan']; ?>" 
                                               class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if($is_admin && $row['id_Clan'] != $_SESSION['user_id']): ?>
                                                <a href="clanovi.php?action=delete&id=<?php echo $row['id_Clan']; ?>" 
                                                   class="btn btn-sm btn-danger"
                                                   onclick="return confirm('Jeste li sigurni?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Statistike -->
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="card text-white bg-primary">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <?php 
                                        $stmt = $pdo->query("SELECT COUNT(*) as count FROM clan");
                                        echo $stmt->fetch()['count'];
                                        ?>
                                    </h5>
                                    <p class="card-text">Ukupno članova</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-white bg-success">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <?php 
                                        $stmt = $pdo->query("SELECT COUNT(*) as count FROM clan WHERE status = 'aktivan'");
                                        echo $stmt->fetch()['count'];
                                        ?>
                                    </h5>
                                    <p class="card-text">Aktivnih članova</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-white bg-warning">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <?php 
                                        $stmt = $pdo->query("SELECT COUNT(*) as count FROM clan WHERE godisnja_ulaznica = 1");
                                        echo $stmt->fetch()['count'];
                                        ?>
                                    </h5>
                                    <p class="card-text">Godišnjih ulaznica</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-white bg-info">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <?php 
                                        $stmt = $pdo->query("SELECT COUNT(*) as count FROM clan WHERE status = 'suspendiran'");
                                        echo $stmt->fetch()['count'];
                                        ?>
                                    </h5>
                                    <p class="card-text">Suspendiranih</p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#clanoviTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/hr.json"
                }
            });
        });
    </script>
</body>
</html>