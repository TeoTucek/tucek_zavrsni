<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
require_once '../spoji.php';

// Statistike
$broj_rez = $mysqli->query("SELECT COUNT(*) FROM rezervacije")->fetch_row()[0];
$broj_cek = $mysqli->query("SELECT COUNT(*) FROM rezervacije WHERE status = 'na čekanju'")->fetch_row()[0];
$broj_potv = $mysqli->query("SELECT COUNT(*) FROM rezervacije WHERE status = 'potvrđeno'")->fetch_row()[0];
$broj_otk = $mysqli->query("SELECT COUNT(*) FROM rezervacije WHERE status = 'otkazano'")->fetch_row()[0];

// Dohvati zadnjih 5 rezervacija za brzi pregled
$zadnje_rez = $mysqli->query("
    SELECT r.id_rezervacije, r.datum_rezervacije, r.ime_prezime, r.status, l.naziv AS lokacija
    FROM rezervacije r
    JOIN lokacije l ON r.id_lokacije = l.id_lokacije
    ORDER BY r.id_rezervacije DESC LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin panel - Ribnjačarstvo Končanica</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #0a3b4b 0%, #1a5f6e 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Header */
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: rgba(255,255,255,0.1);
            padding: 20px 25px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .admin-header h1 {
            color: white;
            font-size: 28px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .admin-header h1::before {
            content: '👑';
            font-size: 32px;
        }
        
        .admin-user {
            display: flex;
            align-items: center;
            gap: 20px;
            background: rgba(255,255,255,0.15);
            padding: 10px 20px;
            border-radius: 50px;
        }
        
        .admin-user span {
            color: white;
            font-weight: 500;
        }
        
        .admin-user span::before {
            content: '👤 ';
            opacity: 0.8;
        }
        
        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 8px 20px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .logout-btn:hover {
            background: #c0392b;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.4);
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #e67e22, #f39c12);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
        
        .stat-card h3 {
            color: #2c3e50;
            font-size: 16px;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .stat-number {
            font-size: 48px;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .stat-label {
            color: #7f8c8d;
            font-size: 13px;
            margin-top: 10px;
        }
        
        /* Main Grid */
        .main-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .dashboard-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: block;
            position: relative;
            overflow: hidden;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
        
        .dashboard-card h2 {
            color: #2c3e50;
            font-size: 22px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .dashboard-card p {
            color: #7f8c8d;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        
        .card-link {
            color: #e67e22;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
        }
        
        .dashboard-card:hover .card-link {
            transform: translateX(5px);
        }
        
        /* Recent Reservations */
        .recent-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            margin-top: 30px;
        }
        
        .recent-card h2 {
            color: #2c3e50;
            font-size: 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 15px;
        }
        
        .recent-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .recent-table th {
            text-align: left;
            padding: 12px;
            background: #f8f9fa;
            color: #2c3e50;
            font-weight: 600;
            border-radius: 10px 10px 0 0;
        }
        
        .recent-table td {
            padding: 12px;
            border-bottom: 1px solid #ecf0f1;
        }
        
        .recent-table tr:hover {
            background: #f8f9fa;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-cekanju {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-potvrdeno {
            background: #d4edda;
            color: #155724;
        }
        
        .status-otkazano {
            background: #f8d7da;
            color: #721c24;
        }
        
        .view-all {
            text-align: right;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #ecf0f1;
        }
        
        .view-all a {
            color: #e67e22;
            text-decoration: none;
            font-weight: 600;
        }
        
        .view-all a:hover {
            text-decoration: underline;
        }
        
        /* Responzivnost */
        @media (max-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .main-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 640px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .admin-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .container {
                padding: 15px;
            }
        }
        
        /* Animacije */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .stats-grid > div, .main-grid > a, .recent-card {
            animation: fadeInUp 0.5s ease-out forwards;
            opacity: 0;
        }
        
        .stats-grid > div:nth-child(1) { animation-delay: 0.1s; }
        .stats-grid > div:nth-child(2) { animation-delay: 0.2s; }
        .stats-grid > div:nth-child(3) { animation-delay: 0.3s; }
        .stats-grid > div:nth-child(4) { animation-delay: 0.4s; }
        .main-grid > a:nth-child(1) { animation-delay: 0.5s; }
        .main-grid > a:nth-child(2) { animation-delay: 0.6s; }
        .recent-card { animation-delay: 0.7s; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="admin-header">
            <h1>Admin panel</h1>
            <div class="admin-user">
                <span><?php echo htmlspecialchars($_SESSION['admin_ime']); ?></span>
                <a href="admin_logout.php" class="logout-btn">
                    🚪 Odjava
                </a>
            </div>
        </div>
        
        <!-- Statistike -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>📋 Ukupno rezervacija</h3>
                <div class="stat-number" style="color: #3498db;"><?php echo $broj_rez; ?></div>
                <div class="stat-label">Sve rezervacije</div>
            </div>
            <div class="stat-card">
                <h3>⏳ Na čekanju</h3>
                <div class="stat-number" style="color: #f39c12;"><?php echo $broj_cek; ?></div>
                <div class="stat-label">Čekaju potvrdu</div>
            </div>
            <div class="stat-card">
                <h3>✅ Potvrđeno</h3>
                <div class="stat-number" style="color: #27ae60;"><?php echo $broj_potv; ?></div>
                <div class="stat-label">Potvrđene rezervacije</div>
            </div>
            <div class="stat-card">
                <h3>❌ Otkazano</h3>
                <div class="stat-number" style="color: #e74c3c;"><?php echo $broj_otk; ?></div>
                <div class="stat-label">Otkazane rezervacije</div>
            </div>
        </div>
        
        <!-- Glavne opcije -->
        <div class="main-grid">
            <a href="admin_rezervacije.php" class="dashboard-card">
                <h2>📋 Pregled rezervacija</h2>
                <p>Pregledajte, potvrdite ili otkažite sve rezervacije. Upravljajte terminima i statusima korisnika.</p>
                <span class="card-link">➡️ Pregledaj sve rezervacije</span>
            </a>
            
            <div class="dashboard-card">
                <h2>🎣 Brze informacije</h2>
                <p><strong>📞 Kontakt:</strong> +385 91 139 9709</p>
                <p><strong>⏰ Radno vrijeme:</strong> 06:00 - 20:00 (svaki dan)</p>
                <p><strong>📍 Adresa:</strong> Končanica, Hrvatska</p>
                <p><strong>📧 Email:</strong> info@ribnjacarstvo-koncanica.hr</p>
                <hr style="margin: 15px 0;">
                <p><strong>🏝️ C&R Otok:</strong> Sjenica • Roštilj • Struja • Prijevoz barkom</p>
            </div>
        </div>
        
        <!-- Zadnjih 5 rezervacija -->
        <div class="recent-card">
            <h2>📋 Zadnjih 5 rezervacija</h2>
            <table class="recent-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Datum</th>
                        <th>Lokacija</th>
                        <th>Korisnik</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($zadnje_rez && $zadnje_rez->num_rows > 0): ?>
                        <?php while ($r = $zadnje_rez->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $r['id_rezervacije']; ?></td>
                            <td><?php echo date('d.m.Y.', strtotime($r['datum_rezervacije'])); ?></td>
                            <td><?php echo htmlspecialchars($r['lokacija']); ?></td>
                            <td><?php echo htmlspecialchars($r['ime_prezime']); ?></td>
                            <td>
                                <span class="status-badge status-<?php 
                                    echo $r['status'] == 'na čekanju' ? 'cekanju' : 
                                        ($r['status'] == 'potvrđeno' ? 'potvrdeno' : 'otkazano'); 
                                ?>">
                                    <?php echo $r['status']; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px;">📭 Nema rezervacija</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <div class="view-all">
                <a href="admin_rezervacije.php">Pogledaj sve rezervacije →</a>
            </div>
        </div>
    </div>
</body>
</html>