<?php
// =====================================================
// ADMIN - UPRAVLJANJE REZERVACIJAMA
// =====================================================
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}
require_once '../spoji.php';

// Promjena statusa
if (isset($_GET['promijeni']) && isset($_GET['id']) && isset($_GET['status'])) {
    $id = (int)$_GET['id'];
    $status = $_GET['status'];
    $admin = $_SESSION['admin_ime'];
    
    // Prepared statement
    $stmt = $mysqli->prepare("UPDATE rezervacije SET status = ? WHERE id_rezervacije = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    
    // Zapiši u povijest
    $stmt2 = $mysqli->prepare("INSERT INTO povijest_statusa (id_rezervacije, novi_status, promijenio) VALUES (?, ?, ?)");
    $stmt2->bind_param("iss", $id, $status, $admin);
    $stmt2->execute();
    
    header("Location: rezervacije.php");
    exit();
}

// Dohvati sve rezervacije
$rez = $mysqli->query("
    SELECT r.*, l.naziv AS lokacija 
    FROM rezervacije r
    JOIN lokacije l ON r.id_lokacije = l.id_lokacije
    ORDER BY r.datum_rezervacije DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Rezervacije</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="container">
        <div style="margin: 20px 0;">
            <a href="dashboard.php" class="btn">← Nazad</a>
            <h2>📋 Sve rezervacije</h2>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Datum</th>
                        <th>Lokacija</th>
                        <th>Ime</th>
                        <th>Mobitel</th>
                        <th>Email</th>
                        <th>Osoba</th>
                        <th>Cijena</th>
                        <th>Status</th>
                        <th>Akcije</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($r = $rez->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $r['id_rezervacije']; ?></td>
                        <td><?php echo date('d.m.Y.', strtotime($r['datum_rezervacije'])); ?></td>
                        <td><?php echo $r['lokacija']; ?></td>
                        <td><?php echo $r['ime_prezime']; ?></td>
                        <td><?php echo $r['broj_mobitela']; ?></td>
                        <td><?php echo $r['email'] ?: '-'; ?></td>
                        <td><?php echo $r['broj_osoba']; ?></td>
                        <td><?php echo $r['broj_osoba'] * $r['cijena_po_osobi']; ?>€</td>
                        <td>
                            <span class="badge badge-<?php 
                                echo $r['status'] == 'potvrđeno' ? 'success' : 
                                    ($r['status'] == 'na čekanju' ? 'warning' : 'danger'); 
                            ?>">
                                <?php echo $r['status']; ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($r['status'] == 'na čekanju'): ?>
                                <a href="?promijeni=1&id=<?php echo $r['id_rezervacije']; ?>&status=potvrđeno" 
                                   class="badge badge-success">✅ Potvrdi</a>
                                <a href="?promijeni=1&id=<?php echo $r['id_rezervacije']; ?>&status=otkazano" 
                                   class="badge badge-danger">❌ Otkaži</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>