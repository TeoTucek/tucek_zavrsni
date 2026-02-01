<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['djelatnik'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistike - Ribnjak Končanica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container mt-5 pt-4">
        <h2 class="mb-4"><i class="fas fa-chart-bar me-2"></i> Statistike</h2>
        
        <!-- Brze statistike -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-primary">
                            <?php
                            $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM izlov WHERE placeno = 0");
                            echo mysqli_fetch_assoc($result)['total'];
                            ?>
                        </h3>
                        <p class="text-muted">Neplaćeni izlovi</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-success">
                            <?php
                            $result = mysqli_query($conn, "SELECT SUM(cijena) as total FROM izlov WHERE placeno = 1 AND MONTH(datum) = MONTH(CURDATE())");
                            $total = mysqli_fetch_assoc($result)['total'];
                            echo number_format($total ?: 0, 2) . ' €';
                            ?>
                        </h3>
                        <p class="text-muted">Zarada ovaj mjesec</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-warning">
                            <?php
                            $result = mysqli_query($conn, "SELECT COUNT(DISTINCT clan_id) as total FROM izlov WHERE YEAR(datum) = YEAR(CURDATE())");
                            echo mysqli_fetch_assoc($result)['total'];
                            ?>
                        </h3>
                        <p class="text-muted">Aktivnih ribiča ove godine</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-info">
                            <?php
                            $result = mysqli_query($conn, "SELECT AVG(tezina_kg) as avg FROM izlov WHERE tezina_kg > 0");
                            $avg = mysqli_fetch_assoc($result)['avg'];
                            echo number_format($avg ?: 0, 2) . ' kg';
                            ?>
                        </h3>
                        <p class="text-muted">Prosječna težina ulova</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafikoni -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Izlovi po ribnjacima</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="ribnjaciChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Mjesečna zarada</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="zaradaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detaljna statistika -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Detaljna statistika izlova</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Ribnjak</th>
                                <th>Broj izlova</th>
                                <th>Ukupna zarada</th>
                                <th>Prosječna težina</th>
                                <th>Najveći ulov</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT 
                                        r.naziv,
                                        COUNT(i.id_izlov) as broj_izlova,
                                        SUM(i.cijena) as ukupna_zarada,
                                        AVG(i.tezina_kg) as prosjek_tezine,
                                        MAX(i.tezina_kg) as najveci_ulov
                                     FROM ribnjaci r
                                     LEFT JOIN izlov i ON r.id_ribnjaci = i.ribnjak_id
                                     WHERE r.aktivan = 1
                                     GROUP BY r.id_ribnjaci
                                     ORDER BY broj_izlova DESC";
                            $result = mysqli_query($conn, $query);
                            
                            while ($row = mysqli_fetch_assoc($result)):
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($row['naziv']) ?></td>
                                <td><?= $row['broj_izlova'] ?></td>
                                <td><?= number_format($row['ukupna_zarada'] ?: 0, 2) ?> €</td>
                                <td><?= number_format($row['prosjek_tezine'] ?: 0, 2) ?> kg</td>
                                <td><?= number_format($row['najveci_ulov'] ?: 0, 2) ?> kg</td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Priprema podataka za grafikone
        const ribnjaciData = {
            labels: [],
            datasets: [{
                data: [],
                backgroundColor: ['#1a6fc9', '#2c3e50', '#f8c537', '#28a745', '#dc3545']
            }]
        };

        const zaradaData = {
            labels: ['Sij', 'Velj', 'Ožu', 'Tra', 'Svi', 'Lip', 'Srp', 'Kol', 'Ruj', 'Lis', 'Stu', 'Pro'],
            datasets: [{
                label: 'Zarada (€)',
                data: [],
                borderColor: '#1a6fc9',
                backgroundColor: 'rgba(26, 111, 201, 0.1)',
                fill: true
            }]
        };

        // Dohvati podatke za grafikone
        fetch('ajax_operacije.php?action=get_chart_data')
            .then(response => response.json())
            .then(data => {
                if (data.ribnjaci) {
                    data.ribnjaci.forEach(item => {
                        ribnjaciData.labels.push(item.naziv);
                        ribnjaciData.datasets[0].data.push(item.broj_izlova);
                    });
                    
                    new Chart(document.getElementById('ribnjaciChart'), {
                        type: 'doughnut',
                        data: ribnjaciData,
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                }

                if (data.zarada) {
                    zaradaData.datasets[0].data = data.zarada;
                    
                    new Chart(document.getElementById('zaradaChart'), {
                        type: 'line',
                        data: zaradaData,
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
            });
    </script>
</body>
</html>