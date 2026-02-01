<?php
session_start();
require_once 'db_connection.php';

// Provjera je li admin prijavljen
if (!isset($_SESSION['djelatnik']) || $_SESSION['djelatnik']['uloga'] !== 'admin') {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Ribnjak Končanica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container mt-5 pt-4">
        <h2 class="mb-4"><i class="fas fa-cog me-2"></i> Admin Panel</h2>
        
        <!-- Admin kartice -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <div class="admin-icon mb-3">
                            <i class="fas fa-user-plus fa-2x text-primary"></i>
                        </div>
                        <h5>Dodaj djelatnika</h5>
                        <p class="text-muted small">Kreiraj novog djelatnika</p>
                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#dodajDjelatnikaModal">
                            Otvori
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <div class="admin-icon mb-3">
                            <i class="fas fa-edit fa-2x text-success"></i>
                        </div>
                        <h5>Uredi cjenik</h5>
                        <p class="text-muted small">Upravljaj cijenama</p>
                        <a href="cjenik.php" class="btn btn-outline-success btn-sm">
                            Otvori
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <div class="admin-icon mb-3">
                            <i class="fas fa-water fa-2x text-info"></i>
                        </div>
                        <h5>Upravljaj ribnjacima</h5>
                        <p class="text-muted small">Dodaj/uredi ribnjake</p>
                        <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#upravljajRibnjacimaModal">
                            Otvori
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <div class="admin-icon mb-3">
                            <i class="fas fa-chart-line fa-2x text-warning"></i>
                        </div>
                        <h5>Detaljne statistike</h5>
                        <p class="text-muted small">Pregled performansi</p>
                        <a href="statistike.php" class="btn btn-outline-warning btn-sm">
                            Otvori
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Djelatnici -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-users-cog me-2"></i> Djelatnici</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Korisničko ime</th>
                                <th>Ime i prezime</th>
                                <th>Uloga</th>
                                <th>Status</th>
                                <th>Akcije</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM djelatnici ORDER BY uloga, prezime";
                            $result = mysqli_query($conn, $query);
                            
                            while ($djelatnik = mysqli_fetch_assoc($result)):
                                $status_class = $djelatnik['aktivan'] == 1 ? 'badge bg-success' : 'badge bg-danger';
                                $status_text = $djelatnik['aktivan'] == 1 ? 'Aktivan' : 'Neaktivan';
                                $uloga_class = $djelatnik['uloga'] === 'admin' ? 'badge bg-warning' : 'badge bg-secondary';
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($djelatnik['korisnicko_ime']) ?></td>
                                <td><?= htmlspecialchars($djelatnik['ime'] . ' ' . $djelatnik['prezime']) ?></td>
                                <td><span class="<?= $uloga_class ?>"><?= ucfirst($djelatnik['uloga']) ?></span></td>
                                <td><span class="<?= $status_class ?>"><?= $status_text ?></span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-warning btn-edit-djelatnik" data-id="<?= $djelatnik['id_djelatnik'] ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php if ($djelatnik['id_djelatnik'] != $_SESSION['djelatnik']['id_djelatnik']): ?>
                                            <button class="btn btn-danger btn-delete-djelatnik" data-id="<?= $djelatnik['id_djelatnik'] ?>" data-name="<?= htmlspecialchars($djelatnik['ime'] . ' ' . $djelatnik['prezime']) ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Financijski pregled -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i> Financijski pregled</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Ukupna zarada po mjesecima (<?= date('Y') ?>)</h6>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Mjesec</th>
                                    <th class="text-end">Zarada</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT 
                                            MONTH(datum) as mjesec,
                                            SUM(cijena) as zarada
                                         FROM izlov 
                                         WHERE YEAR(datum) = YEAR(CURDATE()) AND placeno = 1
                                         GROUP BY MONTH(datum)
                                         ORDER BY MONTH(datum)";
                                $result = mysqli_query($conn, $query);
                                
                                $ukupno = 0;
                                while ($row = mysqli_fetch_assoc($result)):
                                    $ukupno += $row['zarada'];
                                ?>
                                <tr>
                                    <td><?= date('F', mktime(0, 0, 0, $row['mjesec'], 1)) ?></td>
                                    <td class="text-end"><?= number_format($row['zarada'], 2) ?> €</td>
                                </tr>
                                <?php endwhile; ?>
                                <tr class="table-primary">
                                    <td><strong>UKUPNO</strong></td>
                                    <td class="text-end"><strong><?= number_format($ukupno, 2) ?> €</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Statistika ulova</h6>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Pokazatelj</th>
                                    <th class="text-end">Vrijednost</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Najveći ulov
                                $query = "SELECT MAX(tezina_kg) as max_tezina FROM izlov WHERE tezina_kg > 0";
                                $result = mysqli_query($conn, $query);
                                $max_tezina = mysqli_fetch_assoc($result)['max_tezina'];
                                
                                // Prosječna težina
                                $query = "SELECT AVG(tezina_kg) as avg_tezina FROM izlov WHERE tezina_kg > 0";
                                $result = mysqli_query($conn, $query);
                                $avg_tezina = mysqli_fetch_assoc($result)['avg_tezina'];
                                
                                // Ukupno izlova
                                $query = "SELECT COUNT(*) as total_izlovi FROM izlov WHERE YEAR(datum) = YEAR(CURDATE())";
                                $result = mysqli_query($conn, $query);
                                $total_izlovi = mysqli_fetch_assoc($result)['total_izlovi'];
                                ?>
                                <tr>
                                    <td>Najveći ulov</td>
                                    <td class="text-end"><?= number_format($max_tezina ?: 0, 2) ?> kg</td>
                                </tr>
                                <tr>
                                    <td>Prosječna težina</td>
                                    <td class="text-end"><?= number_format($avg_tezina ?: 0, 2) ?> kg</td>
                                </tr>
                                <tr>
                                    <td>Ukupno izlova (<?= date('Y') ?>)</td>
                                    <td class="text-end"><?= $total_izlovi ?></td>
                                </tr>
                                <tr>
                                    <td>Neplaćeni izlovi</td>
                                    <td class="text-end">
                                        <?php
                                        $query = "SELECT COUNT(*) as neplaceni FROM izlov WHERE placeno = 0";
                                        $result = mysqli_query($conn, $query);
                                        echo mysqli_fetch_assoc($result)['neplaceni'];
                                        ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal za dodavanje djelatnika -->
    <div class="modal fade" id="dodajDjelatnikaModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Dodaj novog djelatnika</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="dodajDjelatnikaForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ime *</label>
                                <input type="text" class="form-control" name="ime" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Prezime *</label>
                                <input type="text" class="form-control" name="prezime" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Korisničko ime *</label>
                            <input type="text" class="form-control" name="korisnicko_ime" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lozinka *</label>
                            <input type="password" class="form-control" name="lozinka" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Uloga</label>
                                <select class="form-select" name="uloga">
                                    <option value="radnik">Radnik</option>
                                    <option value="admin">Administrator</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="aktivan">
                                    <option value="1">Aktivan</option>
                                    <option value="0">Neaktivan</option>
                                </select>
                            </div>
                        </div>
                        <div class="alert alert-danger d-none" id="djelatnikError"></div>
                        <div class="alert alert-success d-none" id="djelatnikSuccess"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Odustani</button>
                        <button type="submit" class="btn btn-primary">Spremi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Dodavanje djelatnika
        document.getElementById('dodajDjelatnikaForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'dodaj_djelatnika');
            
            fetch('ajax_operacije.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('djelatnikSuccess').textContent = data.message;
                    document.getElementById('djelatnikSuccess').classList.remove('d-none');
                    document.getElementById('djelatnikError').classList.add('d-none');
                    
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    document.getElementById('djelatnikError').textContent = data.message;
                    document.getElementById('djelatnikError').classList.remove('d-none');
                }
            });
        });

        // Brisanje djelatnika
        document.querySelectorAll('.btn-delete-djelatnik').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                
                if (confirm(`Jeste li sigurni da želite obrisati djelatnika: ${name}?`)) {
                    const formData = new FormData();
                    formData.append('action', 'obrisi_djelatnika');
                    formData.append('id', id);
                    
                    fetch('ajax_operacije.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>