<?php
// =====================================================
// pdf_generator.php - Jednostavna verzija (ne treba TCPDF)
// =====================================================

require_once 'spoji.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Nedostaje ID rezervacije!");
}

$id_rezervacije = (int)$_GET['id'];

// Dohvati podatke o rezervaciji
$rez = $mysqli->query("
    SELECT r.*, l.naziv AS lokacija_naziv 
    FROM rezervacije r
    JOIN lokacije l ON r.id_lokacije = l.id_lokacije
    WHERE r.id_rezervacije = $id_rezervacije
");

// Provjeri da li rezervacija postoji
if (!$rez || $rez->num_rows == 0) {
    die("Rezervacija #$id_rezervacije ne postoji!");
}

$r = $rez->fetch_assoc();

// Dohvati tip ulaznice (ako postoji)
$naziv_tipa = "R23";
if (!empty($r['id_tipa_ulaznice'])) {
    $tip = $mysqli->query("SELECT naziv FROM tipovi_ulaznica WHERE id_tipa = " . $r['id_tipa_ulaznice']);
    if ($tip && $tip->num_rows > 0) {
        $naziv_tipa = $tip->fetch_assoc()['naziv'];
    }
}

// Dohvati noćni ribolov (ako postoji)
$nocni_tekst = "";
if (!empty($r['id_paketa_nocni'])) {
    $noc = $mysqli->query("SELECT naziv FROM nocni_ribolov WHERE id_paketa = " . $r['id_paketa_nocni']);
    if ($noc && $noc->num_rows > 0) {
        $nocni_tekst = $noc->fetch_assoc()['naziv'];
    }
}

// Dohvati dodatne usluge
$usluge_lista = [];
$ukupno_usluge = 0;
$usluge = $mysqli->query("
    SELECT u.naziv, s.kolicina, s.cijena_po_komadu
    FROM stavke_usluga s
    JOIN dodatne_usluge u ON s.id_usluge = u.id_usluge
    WHERE s.id_rezervacije = $id_rezervacije
");

if ($usluge && $usluge->num_rows > 0) {
    while ($u = $usluge->fetch_assoc()) {
        $ukupno = $u['kolicina'] * $u['cijena_po_komadu'];
        $ukupno_usluge += $ukupno;
        $usluge_lista[] = $u['naziv'] . " x" . $u['kolicina'] . " = " . number_format($ukupno, 2) . " €";
    }
}

// Ukupna cijena
$ukupno = ($r['broj_osoba'] * $r['cijena_po_osobi']) + $ukupno_usluge;

// =====================================================
// GENERIRANJE HTML ZA PRINT/PDF
// =====================================================
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Potvrda rezervacije #<?php echo $id_rezervacije; ?></title>
    <style>
        @media print {
            body { margin: 0; padding: 20px; }
            .no-print { display: none; }
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            color: #333;
        }
        .header {
            background: #0a3b4b;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0;
            opacity: 0.8;
        }
        .status {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: bold;
            font-size: 18px;
        }
        .details {
            background: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #e67e22;
            margin: 15px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ecf0f1;
        }
        .total {
            font-size: 20px;
            font-weight: bold;
            color: #e67e22;
            text-align: right;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px solid #e67e22;
        }
        .contact {
            background: #e8f4f8;
            padding: 15px;
            border-radius: 10px;
            margin: 15px 0;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ecf0f1;
        }
        .btn-print {
            background: #e67e22;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-bottom: 20px;
        }
        .btn-print:hover {
            background: #d35400;
        }
    </style>
</head>
<body>
    <button class="btn-print no-print" onclick="window.print();">🖨️ Ispiši / Spremi kao PDF</button>
    
    <div class="header">
        <h1>🎣 Ribnjačarstvo Končanica</h1>
        <p>Najstarije ribnjačarstvo u Hrvatskoj od 1900.</p>
    </div>
    
    <div class="status">
        ✅ REZERVACIJA <?php echo strtoupper($r['status'] ?? 'NA ČEKANJU'); ?>
    </div>
    
    <h3>Poštovani/a <?php echo htmlspecialchars($r['ime_prezime'] ?? 'Korisnik'); ?>,</h3>
    <p>Potvrđujemo vašu rezervaciju. U nastavku su detalji:</p>
    
    <div class="details">
        <h4>📋 Detalji rezervacije</h4>
        <table>
            <tr><td><strong>Broj rezervacije:</strong></td><td>#<?php echo $id_rezervacije; ?></td></tr>
            <tr><td><strong>Datum:</strong></td><td><?php echo date('d.m.Y.', strtotime($r['datum_rezervacije'] ?? date('Y-m-d'))); ?></td></tr>
            <tr><td><strong>Lokacija:</strong></td><td><?php echo htmlspecialchars($r['lokacija_naziv'] ?? 'Nepoznato'); ?></td></tr>
            <tr><td><strong>Tip ulaznice:</strong></td><td><?php echo $naziv_tipa; ?> (<?php echo number_format($r['cijena_po_osobi'] ?? 0, 2); ?> €/osobi)</td></tr>
            <tr><td><strong>Broj osoba:</strong></td><td><?php echo $r['broj_osoba'] ?? 1; ?></td></tr>
            <?php if ($nocni_tekst): ?>
            <tr><td><strong>Noćni ribolov:</strong></td><td><?php echo $nocni_tekst; ?></td></tr>
            <?php endif; ?>
        </table>
        
        <?php if (!empty($usluge_lista)): ?>
        <h4>🎣 Dodatne usluge</h4>
        <table>
            <?php foreach ($usluge_lista as $usl): ?>
            <tr><td><?php echo $usl; ?></td></tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
        
        <div class="total">
            UKUPNO ZA PLATITI: <?php echo number_format($ukupno, 2); ?> €
        </div>
    </div>
    
    <div class="contact">
        <strong>📞 Važni kontakti:</strong><br>
        Telefon: <strong>+385 91 139 9709</strong><br>
        Email: info@ribnjacarstvo-koncanica.hr<br>
        Radno vrijeme: Pon - Ned: 06:00 - 20:00
    </div>
    
    <p>🎣 <strong>Sretan ribolov i ugodan boravak!</strong></p>
    <p>Ribnjačarstvo Končanica d.d.</p>
    
    <div class="footer">
        <p>Ova potvrda je generirana automatski. Molimo sačuvajte za evidenciju.</p>
        <p>© 2026 Ribnjačarstvo Končanica - Najstarije ribnjačarstvo u Hrvatskoj</p>
    </div>
</body>
</html>