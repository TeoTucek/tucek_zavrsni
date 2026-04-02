<?php
session_start();
require_once 'spoji.php';

// PHPMailer
require 'src/PHPMailer.php';
require 'src/SMTP.php';
require 'src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
if (
    empty($_POST['id_lokacije']) ||
    empty($_POST['id_tipa_ulaznice']) ||
    empty($_POST['datum']) ||
    empty($_POST['ime']) ||
    empty($_POST['mobitel']) ||
    empty($_POST['email']) ||
    empty($_POST['broj_osoba'])
) {
    header("Location: index.php?status=greska");
    exit();
}

// CSRF provjera
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("CSRF napad!");
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: index.php");
    exit();
}

// Dohvati podatke
$id_lokacije = (int)$_POST['id_lokacije'];
$id_tipa_ulaznice = (int)$_POST['id_tipa_ulaznice'];
$id_paketa_nocni = !empty($_POST['id_paketa_nocni']) ? (int)$_POST['id_paketa_nocni'] : null;
$datum = $_POST['datum'];
$ime = $_POST['ime'];
$mobitel = $_POST['mobitel'];
$email = $_POST['email'] ?? '';
$broj_osoba = (int)$_POST['broj_osoba'];
$napomena = $_POST['napomena'] ?? '';

// Dohvati naziv lokacije
$lok = $mysqli->query("SELECT naziv FROM lokacije WHERE id_lokacije = $id_lokacije");
$naziv_lokacije = $lok->fetch_assoc()['naziv'];

// Dohvati tip ulaznice i cijenu
$tip = $mysqli->query("SELECT naziv, cijena FROM tipovi_ulaznica WHERE id_tipa = $id_tipa_ulaznice");
$tip_podaci = $tip->fetch_assoc();
$naziv_tipa = $tip_podaci['naziv'];
$cijena_tipa = $tip_podaci['cijena'];

// Dohvati noćni paket ako postoji
$nocni_tekst = "";
$cijena_nocni = 0;
if ($id_paketa_nocni) {
    $noc = $mysqli->query("SELECT naziv, cijena FROM nocni_ribolov WHERE id_paketa = $id_paketa_nocni");
    if ($noc && $noc->num_rows > 0) {
        $noc_podaci = $noc->fetch_assoc();
        $nocni_tekst = $noc_podaci['naziv'];
        $cijena_nocni = $noc_podaci['cijena'];
    }
}

// Dohvati dodatne usluge (samo za email, ne sprema se ovdje)
$usluge_lista = [];
$cijena_usluge = 0;
$usluge = $mysqli->query("SELECT * FROM dodatne_usluge WHERE aktivan = 1");
if ($usluge) {
    while ($u = $usluge->fetch_assoc()) {
        $kolicina = isset($_POST['usluga_' . $u['id_usluge']]) ? (int)$_POST['usluga_' . $u['id_usluge']] : 0;
        if ($kolicina > 0) {
            $ukupno_usluga = $kolicina * $u['cijena'];
            $cijena_usluge += $ukupno_usluga;
            $usluge_lista[] = $u['naziv'] . " x" . $kolicina . " = " . number_format($ukupno_usluga, 2) . " €";
        }
    }
}

// Ukupna cijena
$ukupno = ($cijena_tipa * $broj_osoba) + $cijena_nocni + $cijena_usluge;

// Provjera zauzetosti
$stmt = $mysqli->prepare("SELECT id_rezervacije FROM rezervacije 
                          WHERE id_lokacije = ? AND datum_rezervacije = ? 
                          AND status != 'otkazano'");
$stmt->bind_param("is", $id_lokacije, $datum);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    header("Location: index.php?status=zauzeto");
    exit();
}

// Spremi rezervaciju
$stmt = $mysqli->prepare("INSERT INTO rezervacije 
    (id_lokacije, datum_rezervacije, ime_prezime, broj_mobitela, email, broj_osoba, cijena_po_osobi, napomena) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("issssids", 
    $id_lokacije, $datum, $ime, $mobitel, $email, $broj_osoba, $cijena_tipa, $napomena
);

if ($stmt->execute()) {
    $id_rezervacije = $mysqli->insert_id;
    
    // ===== ===== ===== ===== ===== ===== ===== ===== =====
    // ===== SPREMANJE DODATNIH USLUGA U BAZU =====
    // ===== ===== ===== ===== ===== ===== ===== ===== =====
    
    $usluge = $mysqli->query("SELECT * FROM dodatne_usluge WHERE aktivan = 1");
    if ($usluge) {
        while ($u = $usluge->fetch_assoc()) {
            $kolicina = isset($_POST['usluga_' . $u['id_usluge']]) ? (int)$_POST['usluga_' . $u['id_usluge']] : 0;
            if ($kolicina > 0) {
                $stmt_usluge = $mysqli->prepare("INSERT INTO stavke_usluga (id_rezervacije, id_usluge, kolicina, cijena_po_komadu) VALUES (?, ?, ?, ?)");
                $stmt_usluge->bind_param("iiid", $id_rezervacije, $u['id_usluge'], $kolicina, $u['cijena']);
                $stmt_usluge->execute();
            }
        }
    }
    
    // ===== SLANJE EMAILA =====
    if (!empty($email)) {
        
        $tvoj_email = "rezervacije.koncanica@gmail.com";
        $tvoja_lozinka = "hodslnmvpearyjbw";  // BEZ RAZMAKA!
        
        $mail = new PHPMailer(true);
        
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $tvoj_email;
            $mail->Password = $tvoja_lozinka;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            
            $mail->setFrom($tvoj_email, 'Ribnjacarstvo Koncanica');
            $mail->addAddress($email, $ime);
            
            $mail->isHTML(true);
            $mail->Subject = "🎣 Potvrda rezervacije - Ribnjacarstvo Koncanica";
            
            $mail->Body = "
            <html>
            <body style='font-family: Arial, sans-serif;'>
                <div style='max-width: 600px; margin: 0 auto;'>
                    <div style='background: #0a3b4b; color: white; padding: 20px; text-align: center;'>
                        <h2>🎣 Ribnjačarstvo Končanica</h2>
                        <p>Najstarije ribnjačarstvo u Hrvatskoj od 1900.</p>
                    </div>
                    <div style='background: #f8f9fa; padding: 20px;'>
                        <h3>Poštovani/a $ime,</h3>
                        <p><strong>✅ Uspješno ste rezervirali poziciju!</strong></p>
                        
                        <div style='background: white; padding: 15px; border-left: 4px solid #e67e22; margin: 15px 0;'>
                            <h4>📋 Detalji vaše rezervacije:</h4>
                            <p><strong>Broj rezervacije:</strong> #$id_rezervacije</p>
                            <p><strong>📅 Datum:</strong> " . date('d.m.Y.', strtotime($datum)) . "</p>
                            <p><strong>📍 Lokacija:</strong> $naziv_lokacije</p>
                            <p><strong>🎫 Tip ulaznice:</strong> $naziv_tipa (" . number_format($cijena_tipa, 2) . " €)</p>
                            <p><strong>👥 Broj osoba:</strong> $broj_osoba</p>";
            
            if ($nocni_tekst) {
                $mail->Body .= "<p><strong>🌙 Noćni ribolov:</strong> $nocni_tekst (" . number_format($cijena_nocni, 2) . " €)</p>";
            }
            
            if (!empty($usluge_lista)) {
                $mail->Body .= "<p><strong>🎣 Dodatne usluge:</strong><br>";
                foreach ($usluge_lista as $usl) {
                    $mail->Body .= "• $usl<br>";
                }
                $mail->Body .= "</p>";
            }
            
            $mail->Body .= "
                            <hr>
                            <p style='font-size: 20px; color: #e67e22; font-weight: bold;'>💰 UKUPNO: " . number_format($ukupno, 2) . " €</p>
                        </div>
                        
                        <p><strong>📌 Status:</strong> <span style='color: #f39c12;'>⏳ NA ČEKANJU</span></p>
                        <p>Nakon potvrde administratora, dobit ćete dodatnu obavijest.</p>
                        
                        <div style='background: #e8f4f8; padding: 15px; border-radius: 10px; margin: 15px 0;'>
                            <strong>📞 Kontakt:</strong><br>
                            Telefon: <strong>+385 91 139 9709</strong><br>
                            Email: info@ribnjacarstvo-koncanica.hr<br>
                            Radno vrijeme: Pon - Ned: 06:00 - 20:00
                        </div>
                        
                        <p>🎣 <strong>Sretan ribolov!</strong></p>
                        <p>Ribnjačarstvo Končanica d.d.</p>
                    </div>
                    <div style='text-align: center; font-size: 12px; color: #666; padding: 20px;'>
                        <p>Ova poruka je automatski generirana. Molimo ne odgovarajte na ovaj email.</p>
                    </div>
                </div>
            </body>
            </html>
            ";
            
            $mail->AltBody = "Rezervacija uspješna: $datum, $naziv_lokacije, $broj_osoba osoba, " . number_format($ukupno, 2) . " €";
            
            $mail->send();
            
        } catch (Exception $e) {
            // Ako email ne prođe, spremi u datoteku
            $folder = "email_potvrde/";
            if (!file_exists($folder)) mkdir($folder, 0777, true);
            file_put_contents($folder . "rezervacija_" . $id_rezervacije . ".html", $mail->Body);
        }
    }
    
    header("Location: index.php?status=uspjeh&id=$id_rezervacije");
    
} else {
    header("Location: index.php?status=greska");
}
?>