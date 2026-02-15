<?php
// =====================================================
// DATOTEKA: rezervacija.php
// OPIS: Glavna stranica za rezervacije - povezana s bazom
// =====================================================

require_once 'spoji.php';

// Inicijalizacija varijabli
$poruka = '';
$tip_filtar = isset($_GET['tip']) ? $_GET['tip'] : '';

// Dohvati sve aktivne lokacije iz baze
$upit = "SELECT * FROM aktivne_lokacije";
if ($tip_filtar == 'R23') {
    $upit .= " WHERE tip = 'R23 pozicija'";
} elseif ($tip_filtar == 'OTOK') {
    $upit .= " WHERE tip = 'C&R Otok'";
} elseif ($tip_filtar == 'CR') {
    // Ako treba poseban filter za Crnaju
    $upit .= " WHERE tip = 'R23 pozicija'"; // Prilagodi po potrebi
}

$rezultat = $mysqli->query($upit);
$lokacije = [];
if ($rezultat) {
    while ($row = $rezultat->fetch_assoc()) {
        $lokacije[] = $row;
    }
}

// Provjera slobodnih termina - za JavaScript
$zauzeti_datumi = [];
$upit_zauzeti = "SELECT id_lokacije, datum_rezervacije FROM rezervacije WHERE status != 'otkazano' AND datum_rezervacije >= CURDATE()";
$rez_zauzeti = $mysqli->query($upit_zauzeti);
if ($rez_zauzeti) {
    while ($row = $rez_zauzeti->fetch_assoc()) {
        $zauzeti_datumi[$row['id_lokacije']][] = $row['datum_rezervacije'];
    }
}
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rezervacija termina - Ribnjačarstvo Končanica</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
</head>
<body>
    <!-- HEADER -->
    <header>
        <div class="header-content">
            <div class="logo">
                <h1>RIBNJAČARSTVO KONČANICA</h1>
                <span>od 1900.</span>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Početna</a></li>
                    <li><a href="rezervacija.php" class="active">Rezerviraj</a></li>
                    <li><a href="index.php#o-nama">O nama</a></li>
                    <li><a href="index.php#kontakt">Kontakt</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- GLAVNI SADRŽAJ -->
    <main class="container" style="margin: 2rem auto;">
        <h1 class="text-center" style="font-size: 2.5rem; color: white; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">Rezervacija termina</h1>
        
        <?php if ($poruka): ?>
            <div class="alert alert-success"><?php echo $poruka; ?></div>
        <?php endif; ?>

        <div class="grid-2" style="gap: 2rem;">
            <!-- LIJEVA STRANA - FORMA ZA REZERVACIJU -->
            <div class="card fade-in">
                <h2 style="color: #2c3e50; margin-bottom: 1.5rem;">📝 Unesi podatke</h2>
                
                <form id="rezervacijaForma" method="POST" action="spremi_rezervaciju.php">
                    <!-- Odabir lokacije -->
                    <div class="form-group">
                        <label for="lokacija">Odaberi lokaciju:</label>
                        <select class="form-control" id="lokacija" name="id_lokacije" required>
                            <option value="">-- Odaberi poziciju --</option>
                            <?php foreach ($lokacije as $lok): ?>
                                <option value="<?php echo $lok['id_lokacije']; ?>" 
                                        data-tip="<?php echo $lok['tip']; ?>"
                                        data-kapacitet="<?php echo $lok['kapacitet']; ?>">
                                    <?php 
                                    echo htmlspecialchars($lok['naziv']); 
                                    echo $lok['tip'] == 'C&R Otok' ? ' (OTOK - ' . $lok['kapacitet'] . ' osoba)' : '';
                                    ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Odabir datuma -->
                    <div class="form-group">
                        <label for="datum">Odaberi datum:</label>
                        <input type="text" class="form-control" id="datum" name="datum" placeholder="Odaberi datum..." required readonly>
                        <small id="datumInfo" style="color: #666; display: block; margin-top: 0.5rem;"></small>
                    </div>

                    <!-- Osobni podaci -->
                    <div class="form-group">
                        <label for="ime">Ime i prezime:</label>
                        <input type="text" class="form-control" id="ime" name="ime" placeholder="npr. Ivan Horvat" required>
                    </div>

                    <div class="form-group">
                        <label for="mobitel">Broj mobitela:</label>
                        <input type="tel" class="form-control" id="mobitel" name="mobitel" placeholder="npr. 091 234 5678" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email (opcionalno):</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="npr. ivan@email.hr">
                    </div>

                    <!-- Broj osoba -->
                    <div class="form-group">
                        <label for="broj_osoba">Broj osoba:</label>
                        <input type="number" class="form-control" id="broj_osoba" name="broj_osoba" min="1" max="10" value="2" required>
                        <small id="kapacitetInfo" style="color: #27ae60;"></small>
                    </div>

                    <!-- ===== NOVO: KUKURUZ I PELET ===== -->
                    <div class="mamac-opcije">
                        <h3 style="color: #2c3e50; margin-bottom: 1rem;">🌽 Dodatna mamac oprema</h3>
                        
                        <!-- KUKURUZ - 10kg po 6€ -->
                        <div class="mamac-item">
                            <input type="checkbox" id="kukuruz" name="kukuruz" value="1">
                            <div class="mamac-info">
                                <span class="mamac-naziv">Kukuruz (10kg)</span>
                                <span class="mamac-cijena">6 €</span>
                            </div>
                            <input type="number" class="mamac-kolicina" id="kukuruz_kolicina" name="kukuruz_kolicina" min="0" max="5" value="0" disabled>
                        </div>

                        <!-- PELET 5mm - 1kg po 2€ -->
                        <div class="mamac-item">
                            <input type="checkbox" id="pelet" name="pelet" value="1">
                            <div class="mamac-info">
                                <span class="mamac-naziv">Pelet 5mm (1kg)</span>
                                <span class="mamac-cijena">2 €</span>
                            </div>
                            <input type="number" class="mamac-kolicina" id="pelet_kolicina" name="pelet_kolicina" min="0" max="10" value="0" disabled>
                        </div>

                        <!-- Ukupna cijena mamaca -->
                        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed #3498db;">
                            <p style="font-size: 1.2rem; font-weight: bold;">
                                UKUPNO MAMAC: <span id="ukupnoMamac">0.00</span> €
                            </p>
                        </div>
                    </div>

                    <!-- Napomena -->
                    <div class="form-group">
                        <label for="napomena">Dodatna napomena:</label>
                        <textarea class="form-control" id="napomena" name="napomena" placeholder="Npr. dolazim kasnije, trebam struju, imam svoj roštilj..."></textarea>
                    </div>

                    <!-- Gumb za slanje -->
                    <button type="submit" class="btn btn-secondary" style="width: 100%; padding: 1.2rem; font-size: 1.2rem;" id="submitBtn">
                        📅 REZERVIRAJ TERMIN
                    </button>
                </form>
            </div>

            <!-- DESNA STRANA - PREGLED LOKACIJA -->
            <div class="fade-in">
                <h2 style="color: white; margin-bottom: 1rem;">📍 Dostupne lokacije</h2>
                
                <!-- R23 Pozicije -->
                <div class="card" style="margin-bottom: 1.5rem;">
                    <h3 style="color: #2ecc71; margin-bottom: 1rem;">🎣 R23 Sportski ribnjak</h3>
                    <div style="max-height: 300px; overflow-y: auto;">
                        <?php foreach ($lokacije as $lok): ?>
                            <?php if ($lok['tip'] == 'R23 pozicija'): ?>
                                <div class="lokacija-item" style="padding: 0.8rem; border-bottom: 1px solid #ecf0f1; cursor: pointer;" onclick="odaberiLokaciju(<?php echo $lok['id_lokacije']; ?>)">
                                    <strong><?php echo $lok['naziv']; ?></strong><br>
                                    <small><?php echo $lok['opis']; ?></small><br>
                                    <small>📊 Kapacitet: <?php echo $lok['kapacitet']; ?> osobe | ⚡ Struja: <?php echo $lok['ima_struju'] ? 'DA' : 'NE'; ?></small>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- C&R Otok -->
                <?php foreach ($lokacije as $lok): ?>
                    <?php if ($lok['tip'] == 'C&R Otok'): ?>
                        <div class="card otok-card" style="background: linear-gradient(135deg, #f1c40f, #e67e22); color: white; text-align: center;">
                            <h3 style="font-size: 2rem;">🏝️ <?php echo $lok['naziv']; ?></h3>
                            <p style="font-size: 1.2rem; margin: 1rem 0;"><?php echo $lok['opis']; ?></p>
                            <p><strong>Kapacitet:</strong> do <?php echo $lok['kapacitet']; ?> osoba</p>
                            <p><strong>Cijena:</strong> <?php echo $lok['cijena_po_osobi']; ?> € po osobi</p>
                            <button class="btn" style="background: white; color: #e67e22; margin-top: 1rem;" onclick="odaberiLokaciju(<?php echo $lok['id_lokacije']; ?>)">
                                Rezerviraj otok
                            </button>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <!-- FOOTER -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>Ribnjačarstvo Končanica</h3>
                <p>Najstarije ribnjačarstvo u Hrvatskoj</p>
                <p>Osnovano 1900. godine</p>
            </div>
            <div class="footer-section">
                <h3>Kontakt za rezervacije</h3>
                <p>📞 +385 91 139 9709</p>
                <p>⏰ Pon - Ned: 06:00 - 20:00</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 Ribnjačarstvo Končanica</p>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        // Podaci iz PHP-a za JavaScript
        var zauzetiDatumi = <?php echo json_encode($zauzeti_datumi); ?>;
        var lokacije = <?php echo json_encode($lokacije); ?>;
    </script>
    <script src="script.js"></script>
</body>
</html>