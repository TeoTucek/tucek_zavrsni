<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin__login.php");
    exit();
}
require_once '../spoji.php';

// CSRF token za admin akcije
if (empty($_SESSION['admin_csrf'])) {
    $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
}
$admin_csrf = $_SESSION['admin_csrf'];

// Pomoćna funkcija - provjerava CSRF token pri svakoj izmjeni
function provjeriCSRF() {
    $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['admin_csrf'] ?? '', $token)) {
        http_response_code(403);
        die('CSRF token nevažeći. Vrati se na <a href="admin_rezervacije.php">pregled</a>.');
    }
}

// ===== UKLJUČI PHPMailer NA VRHU =====
require_once '../src/PHPMailer.php';
require_once '../src/SMTP.php';
require_once '../src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ===== FUNKCIJA ZA SLANJE EMAILA NAKON POTVRDE (SA RAZLOGOM) =====
function posaljiEmailPotvrda($mysqli, $id_rezervacije, $status, $razlog = '') {
    // Dohvati podatke o rezervaciji
    $rez = $mysqli->query("
        SELECT r.*, l.naziv AS lokacija 
        FROM rezervacije r
        JOIN lokacije l ON r.id_lokacije = l.id_lokacije 
        WHERE r.id_rezervacije = $id_rezervacije
    ");
    
    if (!$rez || $rez->num_rows == 0) {
        return false;
    }
    $r = $rez->fetch_assoc();
    
    if (empty($r['email'])) {
        return false;
    }
    
    // Dohvati tip ulaznice
    $naziv_tipa = "R23";
    if (!empty($r['id_tipa_ulaznice'])) {
        $tip = $mysqli->query("SELECT naziv FROM tipovi_ulaznica WHERE id_tipa = " . $r['id_tipa_ulaznice']);
        if ($tip && $tip->num_rows > 0) {
            $naziv_tipa = $tip->fetch_assoc()['naziv'];
        }
    }
    
    // Dohvati dodatne usluge za ukupnu cijenu u emailu
    $usluge_sql = $mysqli->query("SELECT SUM(kolicina * cijena_po_komadu) as ukupno FROM stavke_usluga WHERE id_rezervacije = $id_rezervacije");
    $ukupno_usluge = 0;
    if ($usluge_sql && $usluge_sql->num_rows > 0) {
        $usluge_row = $usluge_sql->fetch_assoc();
        $ukupno_usluge = $usluge_row['ukupno'] ?? 0;
    }

    // Dohvati cijenu noćnog ribolova (ako postoji)
    $cijena_nocni = 0;
    $nocni_naziv = '';
    if (!empty($r['id_paketa_nocni'])) {
        $noc = $mysqli->query("SELECT naziv, cijena FROM nocni_ribolov WHERE id_paketa = " . (int)$r['id_paketa_nocni']);
        if ($noc && $noc->num_rows > 0) {
            $noc_row = $noc->fetch_assoc();
            $cijena_nocni = $noc_row['cijena'];
            $nocni_naziv = $noc_row['naziv'];
        }
    }

    $ukupno = ($r['broj_osoba'] * $r['cijena_po_osobi']) + $cijena_nocni + $ukupno_usluge;
    
    if ($status == 'potvrđeno') {
        $subject = "✅ REZERVACIJA POTVRĐENA - Ribnjačarstvo Končanica";
        $boja = "#27ae60";
        $poruka_status = "POTVRĐENA";
        $tekst = "Vaša rezervacija je potvrđena! Veselimo se vašem dolasku.";
        $dodatno = "<p><strong>📌 Napomena:</strong> Molimo vas da dođete 15 minuta prije termina.</p>";
        $razlog_html = "";
    } else {
        $subject = "❌ REZERVACIJA OTKAZANA - Ribnjačarstvo Končanica";
        $boja = "#e74c3c";
        $poruka_status = "OTKAZANA";
        $tekst = "Vaša rezervacija je otkazana.";
        $dodatno = "";
        
        // DODAJ RAZLOG AKO POSTOJI
        if (!empty($razlog)) {
            $razlog_html = "
            <div style='background: #f8d7da; padding: 15px; border-radius: 10px; margin: 15px 0; border-left: 4px solid #e74c3c;'>
                <strong>📝 Razlog otkazivanja:</strong><br>
                " . htmlspecialchars($razlog) . "
            </div>";
        } else {
            $razlog_html = "";
        }
    }
    
    $message = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; }
            .container { max-width: 600px; margin: 0 auto; }
            .header { background: #0a3b4b; color: white; padding: 20px; text-align: center; }
            .content { background: #f8f9fa; padding: 20px; }
            .details { background: white; padding: 15px; border-left: 4px solid $boja; margin: 15px 0; }
            .status { font-size: 24px; color: $boja; font-weight: bold; text-align: center; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>🎣 Ribnjačarstvo Končanica</h2>
                <p>Najstarije ribnjačarstvo u Hrvatskoj od 1900.</p>
            </div>
            <div class='content'>
                <h3>Poštovani/a {$r['ime_prezime']},</h3>
                <p>$tekst</p>
                
                <div class='status'>
                    STATUS: $poruka_status
                </div>
                
                $razlog_html
                
                <div class='details'>
                    <h4>📋 Detalji rezervacije:</h4>
                    <p><strong>Broj rezervacije:</strong> #{$r['id_rezervacije']}</p>
                    <p><strong>📅 Datum:</strong> " . date('d.m.Y.', strtotime($r['datum_rezervacije'])) . "</p>
                    <p><strong>📍 Lokacija:</strong> {$r['lokacija']}</p>
                    <p><strong>🎫 Tip ulaznice:</strong> $naziv_tipa</p>
                    <p><strong>👥 Broj osoba:</strong> {$r['broj_osoba']}</p>"
                    . ($nocni_naziv ? "<p><strong>🌙 Noćni ribolov:</strong> " . htmlspecialchars($nocni_naziv) . " (" . number_format($cijena_nocni, 2) . " €)</p>" : "") .
                    "<p><strong>💰 Ukupno za platiti:</strong> " . number_format($ukupno, 2) . " €</p>
                </div>
                
                $dodatno
                
                <div style='background: #e8f4f8; padding: 15px; border-radius: 10px; margin: 15px 0;'>
                    <strong>📞 Važni kontakti:</strong><br>
                    Telefon: <strong>+385 91 139 9709</strong><br>
                    Email: info@ribnjacarstvo-koncanica.hr<br>
                    Radno vrijeme: Pon - Ned: 06:00 - 20:00
                </div>
                
                <p>🎣 <strong>Sretan ribolov!</strong></p>
                <p>Ribnjačarstvo Končanica d.d.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;

        $mail->setFrom(SMTP_USER, SMTP_FROM_NAME);
        $mail->addAddress($r['email'], $r['ime_prezime']);
        
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->AltBody = strip_tags($message);
        
        $mail->send();
        
        // Log uspjeha
        file_put_contents('../email_log.txt', date('Y-m-d H:i:s') . " - Email potvrde poslan za rezervaciju #$id_rezervacije na {$r['email']}\n", FILE_APPEND);
        
        return true;
    } catch (Exception $e) {
        // Log greške
        file_put_contents('../email_error.txt', date('Y-m-d H:i:s') . " - Greška: " . $mail->ErrorInfo . "\n", FILE_APPEND);
        return false;
    }
}

// ===== BRISANJE REZERVACIJE =====
if (isset($_POST['obrisi'])) {
    provjeriCSRF();
    $id = (int)$_POST['obrisi'];

    // Brisanje pravim redoslijedom - FK constrainti se poštuju
    $stmt = $mysqli->prepare("DELETE FROM povijest_statusa WHERE id_rezervacije = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $stmt = $mysqli->prepare("DELETE FROM stavke_usluga WHERE id_rezervacije = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $stmt = $mysqli->prepare("DELETE FROM stavke_mamci WHERE id_rezervacije = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $stmt = $mysqli->prepare("DELETE FROM rezervacije WHERE id_rezervacije = ?");
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();

    if ($result) {
        header("Location: admin_rezervacije.php?msg=obrisano");
    } else {
        header("Location: admin_rezervacije.php?msg=greska");
    }
    exit();
}

// ===== PROMJENA STATUSA =====
if (isset($_POST['promijeni']) && isset($_POST['id']) && isset($_POST['status'])) {
    provjeriCSRF();
    $id = (int)$_POST['id'];
    $status = $_POST['status'];
    $admin = $_SESSION['admin_ime'];

    // Bijela lista dozvoljenih statusa - sprječava nevažeće vrijednosti
    if (!in_array($status, ['potvrđeno', 'otkazano', 'na čekanju'], true)) {
        header("Location: admin_rezervacije.php?msg=greska");
        exit();
    }

    // DOHVATI RAZLOG AKO POSTOJI
    $razlog = '';
    if ($status == 'otkazano' && isset($_POST['razlog'])) {
        $razlog = trim($_POST['razlog']);
    }
    
    $old = $mysqli->query("SELECT status FROM rezervacije WHERE id_rezervacije = $id");
    $stari_status = $old->fetch_assoc()['status'];
    
    // AŽURIRAJ STATUS I RAZLOG (ako je otkazano)
    if ($status == 'otkazano') {
        $stmt = $mysqli->prepare("UPDATE rezervacije SET status = ?, razlog_otkazivanja = ? WHERE id_rezervacije = ?");
        $stmt->bind_param("ssi", $status, $razlog, $id);
    } else {
        $stmt = $mysqli->prepare("UPDATE rezervacije SET status = ? WHERE id_rezervacije = ?");
        $stmt->bind_param("si", $status, $id);
    }
    $stmt->execute();
    
    $stmt2 = $mysqli->prepare("INSERT INTO povijest_statusa (id_rezervacije, stari_status, novi_status, promijenio) 
                               VALUES (?, ?, ?, ?)");
    $stmt2->bind_param("isss", $id, $stari_status, $status, $admin);
    $stmt2->execute();
    
    // POŠALJI EMAIL - PROSLIJEDI RAZLOG!
    if ($status == 'potvrđeno' || $status == 'otkazano') {
        posaljiEmailPotvrda($mysqli, $id, $status, $razlog);
    }
    
    header("Location: admin_rezervacije.php");
    exit();
}

// ===== FILTERI =====
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$filter_lokacija = isset($_GET['lokacija']) ? (int)$_GET['lokacija'] : '';
$filter_datum_od = isset($_GET['datum_od']) ? $_GET['datum_od'] : '';
$filter_datum_do = isset($_GET['datum_do']) ? $_GET['datum_do'] : '';

// Dohvati sve lokacije za filter
$sve_lokacije = $mysqli->query("SELECT id_lokacije, naziv FROM lokacije WHERE aktivno = 1 ORDER BY naziv");

// Gradi SQL upit s filterima - DODAJ razlog_otkazivanja i noćni paket
$sql = "SELECT r.*, l.naziv AS lokacija_naziv, r.razlog_otkazivanja,
               n.naziv AS nocni_naziv, n.cijena AS nocni_cijena
        FROM rezervacije r
        JOIN lokacije l ON r.id_lokacije = l.id_lokacije
        LEFT JOIN nocni_ribolov n ON r.id_paketa_nocni = n.id_paketa
        WHERE 1=1";
$params = [];
$types = "";

if (!empty($filter_status)) {
    $sql .= " AND r.status = ?";
    $params[] = $filter_status;
    $types .= "s";
}
if (!empty($filter_lokacija)) {
    $sql .= " AND r.id_lokacije = ?";
    $params[] = $filter_lokacija;
    $types .= "i";
}
if (!empty($filter_datum_od)) {
    $sql .= " AND r.datum_rezervacije >= ?";
    $params[] = $filter_datum_od;
    $types .= "s";
}
if (!empty($filter_datum_do)) {
    $sql .= " AND r.datum_rezervacije <= ?";
    $params[] = $filter_datum_do;
    $types .= "s";
}

$sql .= " ORDER BY r.datum_rezervacije DESC, r.id_rezervacije DESC";

// Izvrši upit
if (!empty($params)) {
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $rezervacije = $stmt->get_result();
} else {
    $rezervacije = $mysqli->query($sql);
}

// Statistike s filterima (prepared statement - bez SQL injectiona)
$stats_sql = "SELECT
    COUNT(*) AS ukupno,
    SUM(CASE WHEN status = 'na čekanju' THEN 1 ELSE 0 END) AS cekanje,
    SUM(CASE WHEN status = 'potvrđeno' THEN 1 ELSE 0 END) AS potvrdeno,
    SUM(CASE WHEN status = 'otkazano' THEN 1 ELSE 0 END) AS otkazano
FROM rezervacije r
JOIN lokacije l ON r.id_lokacije = l.id_lokacije
WHERE 1=1";
$stats_params = [];
$stats_types = "";
if (!empty($filter_lokacija)) {
    $stats_sql .= " AND r.id_lokacije = ?";
    $stats_params[] = $filter_lokacija;
    $stats_types .= "i";
}
if (!empty($filter_datum_od)) {
    $stats_sql .= " AND r.datum_rezervacije >= ?";
    $stats_params[] = $filter_datum_od;
    $stats_types .= "s";
}
if (!empty($filter_datum_do)) {
    $stats_sql .= " AND r.datum_rezervacije <= ?";
    $stats_params[] = $filter_datum_do;
    $stats_types .= "s";
}

if (!empty($stats_params)) {
    $stmt_stats = $mysqli->prepare($stats_sql);
    $stmt_stats->bind_param($stats_types, ...$stats_params);
    $stmt_stats->execute();
    $stats = $stmt_stats->get_result()->fetch_assoc();
} else {
    $stats = $mysqli->query($stats_sql)->fetch_assoc();
}

// Poruka
$poruka = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'obrisano') {
        $poruka = '<div class="alert success">✅ Rezervacija je uspješno obrisana!</div>';
    } else if ($_GET['msg'] == 'greska') {
        $error = isset($_GET['error']) ? $_GET['error'] : 'Nepoznata greška';
        $poruka = '<div class="alert error">❌ Greška: ' . htmlspecialchars($error) . '</div>';
    }
}

?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rezervacije - Admin</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        body {
            background: linear-gradient(135deg, #0a3b4b, #1a5f6e);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        .header-actions h1 {
            color: white;
            font-size: 28px;
        }
        .btn-back {
            background: #e67e22;
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-back:hover {
            background: #d35400;
        }
        
        .filter-box {
            background: white;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .filter-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        .filter-group label {
            font-size: 12px;
            font-weight: 600;
            color: #7f8c8d;
            margin-bottom: 5px;
        }
        .filter-group select, .filter-group input {
            padding: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s;
        }
        .filter-group select:focus, .filter-group input:focus {
            border-color: #e67e22;
            outline: none;
        }
        .filter-buttons {
            display: flex;
            gap: 10px;
            align-items: flex-end;
        }
        .btn-filter {
            background: #e67e22;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-filter:hover {
            background: #d35400;
        }
        .btn-reset {
            background: #7f8c8d;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }
        .btn-reset:hover {
            background: #636e72;
        }
        
        .stats-mini {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .stat-mini-card {
            background: white;
            border-radius: 12px;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .stat-mini-card .count {
            font-size: 24px;
            font-weight: bold;
        }
        .stat-mini-card .label {
            font-size: 12px;
            color: #7f8c8d;
        }
        .stat-mini-card.cekanje .count { color: #f39c12; }
        .stat-mini-card.potvrdeno .count { color: #27ae60; }
        .stat-mini-card.otkazano .count { color: #e74c3c; }
        .stat-mini-card.ukupno .count { color: #3498db; }
        
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .alert.success { background: #d4edda; color: #155724; border-left: 4px solid #27ae60; }
        .alert.error { background: #f8d7da; color: #721c24; border-left: 4px solid #e74c3c; }
        
        .table-container {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow-x: auto;
        }
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }
        .admin-table th {
            background: #0a3b4b;
            color: white;
            padding: 15px;
            text-align: left;
        }
        .admin-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #ecf0f1;
        }
        .admin-table tr:hover {
            background: #f8f9fa;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-cekanju { background: #fff3cd; color: #856404; }
        .status-potvrdeno { background: #d4edda; color: #155724; }
        .status-otkazano { background: #f8d7da; color: #721c24; }
        
        /* Tooltip za razlog otkazivanja */
        .status-cell {
            position: relative;
            cursor: pointer;
        }
        
        .status-cell .tooltip-text {
            visibility: hidden;
            background-color: #2c3e50;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 8px 12px;
            position: absolute;
            z-index: 100;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
            font-size: 12px;
            font-weight: normal;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            opacity: 0;
            transition: opacity 0.3s;
            pointer-events: none;
        }
        
        .status-cell .tooltip-text::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #2c3e50 transparent transparent transparent;
        }
        
        .status-cell:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }
        
        /* Ako je otkazano, dodatni stil */
        .status-otkazano {
            position: relative;
            cursor: help;
            border-bottom: 1px dotted #e74c3c;
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .btn-approve, .btn-cancel, .btn-delete { padding: 5px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; border: none; cursor: pointer; color: white; font-family: inherit; }
        .btn-approve { background: #27ae60; }
        .btn-cancel { background: #e74c3c; }
        .btn-delete { background: #7f8c8d; }
        .btn-pdf {
            background: #e74c3c;
            color: white;
            padding: 5px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 12px;
            transition: all 0.3s;
            display: inline-block;
        }
        .btn-pdf:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }
        .empty-row td { text-align: center; padding: 50px; color: #7f8c8d; }
        @media (max-width: 768px) {
            .filter-grid { grid-template-columns: 1fr; }
            .filter-buttons { justify-content: stretch; }
            .filter-buttons a, .filter-buttons button { flex: 1; text-align: center; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-actions">
            <h1>📋 Pregled rezervacija</h1>
            <a href="admin_dashboard.php" class="btn-back">← Nazad na dashboard</a>
        </div>
        
        <?php echo $poruka; ?>
        
        <!-- FILTERI -->
        <div class="filter-box">
            <div class="filter-title">🔍 Filtriraj rezervacije</div>
            <form method="GET" action="">
                <div class="filter-grid">
                    <div class="filter-group">
                        <label>📌 Status</label>
                        <select name="status">
                            <option value="">-- Svi statusi --</option>
                            <option value="na čekanju" <?php echo $filter_status == 'na čekanju' ? 'selected' : ''; ?>>⏳ Na čekanju</option>
                            <option value="potvrđeno" <?php echo $filter_status == 'potvrđeno' ? 'selected' : ''; ?>>✅ Potvrđeno</option>
                            <option value="otkazano" <?php echo $filter_status == 'otkazano' ? 'selected' : ''; ?>>❌ Otkazano</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>📍 Lokacija</label>
                        <select name="lokacija">
                            <option value="">-- Sve lokacije --</option>
                            <optgroup label="🎣 R23 pozicije">
                                <?php 
                                $sve_lokacije = $mysqli->query("SELECT id_lokacije, naziv FROM lokacije WHERE tip = 'R23 pozicija' AND aktivno = 1 ORDER BY naziv");
                                while ($l = $sve_lokacije->fetch_assoc()): 
                                ?>
                                <option value="<?php echo $l['id_lokacije']; ?>" <?php echo $filter_lokacija == $l['id_lokacije'] ? 'selected' : ''; ?>>
                                    <?php echo $l['naziv']; ?>
                                </option>
                                <?php endwhile; ?>
                            </optgroup>
                            <optgroup label="🏝️ Otok">
                                <?php 
                                $otok = $mysqli->query("SELECT id_lokacije, naziv FROM lokacije WHERE tip = 'C&R Otok' AND aktivno = 1");
                                while ($o = $otok->fetch_assoc()): 
                                ?>
                                <option value="<?php echo $o['id_lokacije']; ?>" <?php echo $filter_lokacija == $o['id_lokacije'] ? 'selected' : ''; ?>>
                                    <?php echo $o['naziv']; ?>
                                </option>
                                <?php endwhile; ?>
                            </optgroup>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>📅 Datum od</label>
                        <input type="date" name="datum_od" value="<?php echo $filter_datum_od; ?>">
                    </div>
                    <div class="filter-group">
                        <label>📅 Datum do</label>
                        <input type="date" name="datum_do" value="<?php echo $filter_datum_do; ?>">
                    </div>
                    <div class="filter-buttons">
                        <button type="submit" class="btn-filter">🔍 Filtriraj</button>
                        <a href="admin_rezervacije.php" class="btn-reset">🔄 Resetiraj filtere</a>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- STATISTIKE -->
        <div class="stats-mini">
            <div class="stat-mini-card ukupno">
                <span class="count"><?php echo $stats['ukupno']; ?></span>
                <span class="label">📋 Ukupno</span>
            </div>
            <div class="stat-mini-card cekanje">
                <span class="count"><?php echo $stats['cekanje']; ?></span>
                <span class="label">⏳ Na čekanju</span>
            </div>
            <div class="stat-mini-card potvrdeno">
                <span class="count"><?php echo $stats['potvrdeno']; ?></span>
                <span class="label">✅ Potvrđeno</span>
            </div>
            <div class="stat-mini-card otkazano">
                <span class="count"><?php echo $stats['otkazano']; ?></span>
                <span class="label">❌ Otkazano</span>
            </div>
        </div>
        
        <!-- TABLICA REZERVACIJA -->
        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Datum</th>
                        <th>Lokacija</th>
                        <th>Ime i prezime</th>
                        <th>Mobitel</th>
                        <th>Email</th>
                        <th>Osoba</th>
                        <th>Cijena</th>
                        <th>Status</th>
                        <th>Akcije</th>
                     </thead>
                <tbody>
                    <?php if (!$rezervacije || $rezervacije->num_rows == 0): ?>
                        <tr class="empty-row">
                            <td colspan="10">📭 Nema rezervacija za prikaz</td>
                        </tr>
                    <?php else: ?>
                        <?php while ($r = $rezervacije->fetch_assoc()):
                            // Dohvati dodatne usluge za UKUPNU CIJENU
                            $usluge_sql = $mysqli->query("SELECT SUM(kolicina * cijena_po_komadu) as ukupno FROM stavke_usluga WHERE id_rezervacije = " . $r['id_rezervacije']);
                            $ukupno_usluge = 0;
                            if ($usluge_sql && $usluge_sql->num_rows > 0) {
                                $usluge_row = $usluge_sql->fetch_assoc();
                                $ukupno_usluge = $usluge_row['ukupno'] ?? 0;
                            }
                            $cijena_nocni = $r['nocni_cijena'] ?? 0;
                            $ukupna_cijena = ($r['broj_osoba'] * $r['cijena_po_osobi']) + $cijena_nocni + $ukupno_usluge;
                        ?>
                        <tr>
                            <td>#<?php echo $r['id_rezervacije']; ?></td>
                            <td><?php echo date('d.m.Y.', strtotime($r['datum_rezervacije'])); ?></td>
                            <td><?php echo htmlspecialchars($r['lokacija_naziv']); ?></td>
                            <td><?php echo htmlspecialchars($r['ime_prezime']); ?></td>
                            <td><?php echo htmlspecialchars($r['broj_mobitela']); ?></td>
                            <td><?php echo htmlspecialchars($r['email'] ?: '-'); ?></td>
                            <td><?php echo $r['broj_osoba']; ?></td>
                            <td><strong><?php echo number_format($ukupna_cijena, 2); ?> €</strong></td>
                            <td class="status-cell">
                                <span class="status-badge status-<?php 
                                    echo $r['status'] == 'na čekanju' ? 'cekanju' : 
                                        ($r['status'] == 'potvrđeno' ? 'potvrdeno' : 'otkazano'); 
                                ?>">
                                    <?php echo $r['status']; ?>
                                </span>
                                <?php if ($r['status'] == 'otkazano' && !empty($r['razlog_otkazivanja'])): ?>
                                    <span class="tooltip-text">
                                        📝 <strong>Razlog:</strong> <?php echo htmlspecialchars($r['razlog_otkazivanja']); ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="action-buttons">
                                <?php if ($r['status'] == 'na čekanju'): ?>
                                    <form method="POST" style="display:inline" onsubmit="return confirm('Potvrdi rezervaciju #<?php echo $r['id_rezervacije']; ?>?')">
                                        <input type="hidden" name="csrf_token" value="<?php echo $admin_csrf; ?>">
                                        <input type="hidden" name="promijeni" value="1">
                                        <input type="hidden" name="id" value="<?php echo $r['id_rezervacije']; ?>">
                                        <input type="hidden" name="status" value="potvrđeno">
                                        <button type="submit" class="btn-approve">✅ Potvrdi</button>
                                    </form>
                                    <form method="POST" style="display:inline" onsubmit="return otkaziRezervaciju(this, <?php echo $r['id_rezervacije']; ?>, '<?php echo htmlspecialchars(addslashes($r['ime_prezime']), ENT_QUOTES); ?>')">
                                        <input type="hidden" name="csrf_token" value="<?php echo $admin_csrf; ?>">
                                        <input type="hidden" name="promijeni" value="1">
                                        <input type="hidden" name="id" value="<?php echo $r['id_rezervacije']; ?>">
                                        <input type="hidden" name="status" value="otkazano">
                                        <input type="hidden" name="razlog" value="">
                                        <button type="submit" class="btn-cancel">❌ Otkaži</button>
                                    </form>
                                <?php endif; ?>
                                <form method="POST" style="display:inline" onsubmit="return confirm('⚠️ SIGURNO? Obrisati rezervaciju #<?php echo $r['id_rezervacije']; ?>?')">
                                    <input type="hidden" name="csrf_token" value="<?php echo $admin_csrf; ?>">
                                    <input type="hidden" name="obrisi" value="<?php echo $r['id_rezervacije']; ?>">
                                    <button type="submit" class="btn-delete">🗑️ Obriši</button>
                                </form>
                                <a href="../pdf_generator.php?id=<?php echo $r['id_rezervacije']; ?>"
                                   class="btn-pdf" target="_blank">
                                   📄 PDF
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
    function otkaziRezervaciju(form, id, ime) {
        var razlog = prompt("❌ Otkazivanje rezervacije #" + id + "\nKorisnik: " + ime + "\n\nUnesite razlog otkazivanja:");
        if (razlog === null) return false;
        if (razlog.trim() === "") {
            alert("Morate unijeti razlog otkazivanja!");
            return false;
        }
        form.razlog.value = razlog;
        return true;
    }
    </script>
</body>
</html>