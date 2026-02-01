<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['djelatnik'])) {
    header('Location: index.php');
    exit;
}

$is_admin = $_SESSION['djelatnik']['uloga'] === 'admin';
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Izlovi - Ribnjak Končanica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container mt-5 pt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-calendar-alt me-2"></i> Izlovi</h2>
            <div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#dodajIzlovModal">
                    <i class="fas fa-plus me-1"></i> Novi izlov
                </button>
            </div>
        </div>

        <!-- Filteri -->
        <div class="row mb-4">
            <div class="col-md-3">
                <input type="date" class="form-control" id="datumFilter" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-md-3">
                <select class="form-select" id="ribnjakFilter">
                    <option value="">Svi ribnjaci</option>
                    <?php
                    $query = "SELECT id_ribnjaci, naziv FROM ribnjaci WHERE aktivan = 1 ORDER BY naziv";
                    $result = mysqli_query($conn, $query);
                    while ($ribnjak = mysqli_fetch_assoc($result)):
                    ?>
                        <option value="<?= $ribnjak['id_ribnjaci'] ?>"><?= htmlspecialchars($ribnjak['naziv']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="statusFilter">
                    <option value="">Svi statusi</option>
                    <option value="0">Neplaćeno</option>
                    <option value="1">Plaćeno</option>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-outline-secondary w-100" id="resetFilter">
                    <i class="fas fa-redo me-1"></i> Resetiraj
                </button>
            </div>
        </div>

        <!-- Tabela izlova -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Popis izlova</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="izloviTable">
                        <thead>
                            <tr>
                                <th>Datum</th>
                                <th>Član</th>
                                <th>Ribnjak</th>
                                <th>Trajanje</th>
                                <th>Težina ulova</th>
                                <th>Cijena</th>
                                <th>Status</th>
                                <th>Akcije</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT i.*, c.ime, c.prezime, r.naziv as ribnjak_naziv 
                                     FROM izlov i
                                     JOIN clan c ON i.clan_id = c.id_Clan
                                     JOIN ribnjaci r ON i.ribnjak_id = r.id_ribnjaci
                                     ORDER BY i.datum DESC, i.vrijeme DESC";
                            $result = mysqli_query($conn, $query);
                            
                            while ($row = mysqli_fetch_assoc($result)):
                                $status_class = $row['placeno'] == 1 ? 'badge bg-success' : 'badge bg-danger';
                                $status_text = $row['placeno'] == 1 ? 'Plaćeno' : 'Neplaćeno';
                            ?>
                            <tr data-datum="<?= $row['datum'] ?>" data-ribnjak="<?= $row['ribnjak_id'] ?>" data-status="<?= $row['placeno'] ?>">
                                <td>
                                    <?= date('d.m.Y', strtotime($row['datum'])) ?><br>
                                    <small class="text-muted"><?= substr($row['vrijeme'], 0, 5) ?></small>
                                </td>
                                <td><?= htmlspecialchars($row['ime'] . ' ' . $row['prezime']) ?></td>
                                <td><?= htmlspecialchars($row['ribnjak_naziv']) ?></td>
                                <td><?= $row['trajanje'] ?? '?' ?> h</td>
                                <td><?= $row['tezina_kg'] ? $row['tezina_kg'] . ' kg' : '-' ?></td>
                                <td><?= number_format($row['cijena'], 2) ?> €</td>
                                <td>
                                    <span class="<?= $status_class ?>"><?= $status_text ?></span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <?php if ($row['placeno'] == 0): ?>
                                            <button class="btn btn-success btn-mark-paid" data-id="<?= $row['id_izlov'] ?>">
                                                <i class="fas fa-check"></i> Označi plaćeno
                                            </button>
                                        <?php endif; ?>
                                        <button class="btn btn-danger btn-delete" data-id="<?= $row['id_izlov'] ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal za dodavanje izlova -->
    <div class="modal fade" id="dodajIzlovModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Dodaj novi izlov</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="dodajIzlovForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Član</label>
                            <select class="form-select" name="clan_id" required>
                                <option value="">Odaberite člana</option>
                                <?php
                                $query = "SELECT id_Clan, ime, prezime FROM clan WHERE status = 'aktivan' ORDER BY prezime, ime";
                                $result = mysqli_query($conn, $query);
                                while ($clan = mysqli_fetch_assoc($result)):
                                ?>
                                    <option value="<?= $clan['id_Clan'] ?>">
                                        <?= htmlspecialchars($clan['ime'] . ' ' . $clan['prezime']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ribnjak</label>
                            <select class="form-select" name="ribnjak_id" required>
                                <option value="">Odaberite ribnjak</option>
                                <?php
                                $query = "SELECT id_ribnjaci, naziv FROM ribnjaci WHERE aktivan = 1 ORDER BY naziv";
                                $result = mysqli_query($conn, $query);
                                while ($ribnjak = mysqli_fetch_assoc($result)):
                                ?>
                                    <option value="<?= $ribnjak['id_ribnjaci'] ?>">
                                        <?= htmlspecialchars($ribnjak['naziv']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Datum</label>
                                <input type="date" class="form-control" name="datum" required value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Vrijeme</label>
                                <input type="time" class="form-control" name="vrijeme" required value="08:00">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Trajanje (sati)</label>
                                <input type="number" class="form-control" name="trajanje" min="1" max="24" value="4" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Težina ulova (kg)</label>
                                <input type="number" class="form-control" name="tezina_kg" step="0.1" min="0">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cijena (€)</label>
                            <input type="number" class="form-control" name="cijena" step="0.01" min="0" required>
                        </div>
                        <div class="alert alert-danger d-none" id="izlovFormError"></div>
                        <div class="alert alert-success d-none" id="izlovFormSuccess"></div>
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
        // Filteri
        function filterTable() {
            const datumFilter = document.getElementById('datumFilter').value;
            const ribnjakFilter = document.getElementById('ribnjakFilter').value;
            const statusFilter = document.getElementById('statusFilter').value;
            
            const rows = document.querySelectorAll('#izloviTable tbody tr');
            
            rows.forEach(row => {
                const datum = row.getAttribute('data-datum');
                const ribnjak = row.getAttribute('data-ribnjak');
                const status = row.getAttribute('data-status');
                
                const matchesDatum = !datumFilter || datum === datumFilter;
                const matchesRibnjak = !ribnjakFilter || ribnjak === ribnjakFilter;
                const matchesStatus = statusFilter === '' || status === statusFilter;
                
                row.style.display = matchesDatum && matchesRibnjak && matchesStatus ? '' : 'none';
            });
        }

        document.getElementById('datumFilter').addEventListener('change', filterTable);
        document.getElementById('ribnjakFilter').addEventListener('change', filterTable);
        document.getElementById('statusFilter').addEventListener('change', filterTable);
        document.getElementById('resetFilter').addEventListener('click', function() {
            document.getElementById('datumFilter').value = '<?= date('Y-m-d') ?>';
            document.getElementById('ribnjakFilter').value = '';
            document.getElementById('statusFilter').value = '';
            filterTable();
        });

        // Dodavanje izlova
        document.getElementById('dodajIzlovForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'dodaj_izlov');
            
            fetch('ajax_operacije.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('izlovFormSuccess').textContent = data.message;
                    document.getElementById('izlovFormSuccess').classList.remove('d-none');
                    document.getElementById('izlovFormError').classList.add('d-none');
                    
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    document.getElementById('izlovFormError').textContent = data.message;
                    document.getElementById('izlovFormError').classList.remove('d-none');
                }
            });
        });

        // Označi plaćeno
        document.querySelectorAll('.btn-mark-paid').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                
                if (confirm('Označiti izlov kao plaćen?')) {
                    const formData = new FormData();
                    formData.append('action', 'oznaci_placeno');
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

        // Brisanje izlova
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                
                if (confirm('Jeste li sigurni da želite obrisati ovaj izlov?')) {
                    const formData = new FormData();
                    formData.append('action', 'obrisi_izlov');
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