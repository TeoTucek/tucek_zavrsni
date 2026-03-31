<?php
// =====================================================
// ADMIN DASHBOARD
// =====================================================
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}
require_once '../spoji.php';

// Statistike
$broj_rez = $mysqli->query("SELECT COUNT(*) FROM rezervacije")->fetch_row()[0];
$broj_cek = $mysqli->query("SELECT COUNT(*) FROM rezervacije WHERE status = 'na čekanju'")->fetch_row()[0];
$broj_potv = $mysqli->query("SELECT COUNT(*) FROM rezervacije WHERE status = 'potvrđeno'")->fetch_row()[0];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin panel</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="container">
        <div style="display: flex; justify-content: space-between; margin: 20px 0;">
            <h1>👑 Admin panel - <?php echo $_SESSION['admin_ime']; ?></h1>
            <a href="logout.php" class="btn" style="background: #e74c3c;">Odjava</a>
        </div>
        
        <div class="grid-3">
            <div class="card" style="text-align: center;">
                <h3>📊 Ukupno rezervacija</h3>
                <p style="font-size: 48px; color: #3498db;"><?php echo $broj_rez; ?></p>
            </div>
            
            <div class="card" style="text-align: center;">
                <h3>⏳ Na čekanju</h3>
                <p style="font-size: 48px; color: #f39c12;"><?php echo $broj_cek; ?></p>
            </div>
            
            <div class="card" style="text-align: center;">
                <h3>✅ Potvrđeno</h3>
                <p style="font-size: 48px; color: #27ae60;"><?php echo $broj_potv; ?></p>
            </div>
        </div>
        
        <div class="grid-2" style="margin-top: 30px;">
            <a href="rezervacije.php" class="card" style="text-decoration: none; color: inherit;">
                <h2>📋 Upravljanje rezervacijama</h2>
                <p>Pregledaj, potvrdi, otkaži rezervacije</p>
            </a>
            
            <a href="lokacije.php" class="card" style="text-decoration: none; color: inherit;">
                <h2>📍 Upravljanje lokacijama</h2>
                <p>Izmijeni cijene, kapacitete, opise</p>
            </a>
            
            <a href="mamci.php" class="card" style="text-decoration: none; color: inherit;">
                <h2>🌽 Upravljanje mamcima</h2>
                <p>Dodaj/ukloni mamce, cijene</p>
            </a>
            
            <a href="blokirani.php" class="card" style="text-decoration: none; color: inherit;">
                <h2>🚫 Blokirani datumi</h2>
                <p>Dani kad ribnjak ne radi</p>
            </a>
        </div>
    </div>
</body>
</html>