<?php
session_start();
require_once 'spoji.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$poruka = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'uspjeh') {
        $id_prikaz = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $poruka = '<div class="alert success">✅ Rezervacija uspješna! ID: #' . $id_prikaz . '</div>';
    } else if ($_GET['status'] == 'zauzeto') {
        $poruka = '<div class="alert error">❌ Termin je zauzet! Odaberi drugi datum.</div>';
    } else if ($_GET['status'] == 'blokirano') {
        $poruka = '<div class="alert error">❌ Ribnjak ne radi taj dan! Odaberi drugi datum.</div>';
    } else if ($_GET['status'] == 'greska') {
        $poruka = '<div class="alert error">❌ Greška pri spremanju. Pokušaj ponovno.</div>';
    }
}

// Dohvati sve podatke iz baze
$lokacije = $mysqli->query("SELECT * FROM lokacije WHERE aktivno = 1 ORDER BY tip, naziv");
$tipovi = $mysqli->query("SELECT * FROM tipovi_ulaznica WHERE aktivan = 1");
$nocni = $mysqli->query("SELECT * FROM nocni_ribolov WHERE aktivan = 1");

// Zauzeti datumi - za početni prikaz (prazan, jer će se učitati preko AJAX)
$zauzeti_datumi = [];

// Blokirani datumi
$blokirani = $mysqli->query("SELECT datum_od, datum_do FROM blokirani_datumi");
$blokirani_datumi = [];
while ($row = $blokirani->fetch_assoc()) {
    $start = new DateTime($row['datum_od']);
    $end = new DateTime($row['datum_do']);
    $end->modify('+1 day');
    $interval = new DateInterval('P1D');
    $period = new DatePeriod($start, $interval, $end);
    foreach ($period as $date) {
        $blokirani_datumi[] = $date->format('Y-m-d');
    }
}
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ribnjačarstvo Končanica - Rezervacije</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    
    <style>
        .bubble-effect {
            position: fixed;
            pointer-events: none;
            z-index: 9999;
            font-size: 30px;
            transition: all 1s ease-out;
            user-select: none;
        }
        .loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(10, 59, 75, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s;
        }
        .loader.hide {
            opacity: 0;
            visibility: hidden;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #fff;
            border-top-color: #e67e22;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="loader" id="loader">
        <div class="spinner"></div>
    </div>

    <header>
        <div class="header-content">
            <div class="logo">
                <h1>RIBNJAČARSTVO KONČANICA</h1>
                <span>od 1900.</span>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php" class="active">Rezervacija</a></li>
                    <li><a href="admin/admin__login.php">Admin</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h2>REZERVIRAJ SVOJU POZICIJU</h2>
            <p>R23 • R23 PLUS • C&R Otok • Noćni ribolov</p>
        </div>
    </section>

    <main class="container">
        <?php echo $poruka; ?>

        <div class="grid-2">
            <!-- FORMA ZA REZERVACIJU -->
            <div class="card">
                <h2>📝 Rezerviraj termin</h2>
                
                <form method="POST" action="spremi.php" id="rezervacijaForma">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    
                    <div class="form-group">
                        <label>📍 Lokacija:</label>
                        <select name="id_lokacije" id="lokacija" required>
                            <option value="">-- Odaberi lokaciju --</option>
                            <?php while ($l = $lokacije->fetch_assoc()): ?>
                            <option value="<?php echo $l['id_lokacije']; ?>" data-kapacitet="<?php echo $l['kapacitet']; ?>">
                                <?php echo htmlspecialchars($l['naziv']); ?>
                                <?php echo $l['ima_struju'] ? '⚡' : ''; ?>
                                <?php echo $l['ima_sjenicu'] ? '🏠' : ''; ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>🎫 Tip ulaznice:</label>
                        <select name="id_tipa_ulaznice" id="tipUlaznice" required>
                            <option value="">-- Odaberi tip --</option>
                            <?php while ($t = $tipovi->fetch_assoc()): ?>
                            <option value="<?php echo $t['id_tipa']; ?>" data-cijena="<?php echo $t['cijena']; ?>">
                                <?php echo htmlspecialchars($t['naziv']); ?> - <?php echo $t['cijena']; ?>€
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>📅 Datum:</label>
                        <input type="text" name="datum" id="datum" class="form-control" required readonly>
                        <div id="infoDatum" class="info-text"></div>
                    </div>

                    <div class="form-group">
                        <label>🌙 Noćni ribolov:</label>
                        <select name="id_paketa_nocni" id="nocniRibolov">
                            <option value="">-- Bez noćnog ribolova --</option>
                            <?php while ($n = $nocni->fetch_assoc()): ?>
                            <option value="<?php echo $n['id_paketa']; ?>" data-cijena="<?php echo $n['cijena']; ?>">
                                <?php echo htmlspecialchars($n['naziv']); ?> (<?php echo $n['dani']; ?> dana) - <?php echo $n['cijena']; ?>€
                            </option>
                            <?php endwhile; ?>
                        </select>
                        <small class="info-text">Noćni ribolov dozvoljen samo određene vikende</small>
                    </div>

                    <div class="form-group">
                        <label>👤 Ime i prezime:</label>
                        <input type="text" name="ime" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>📱 Broj mobitela:</label>
                        <input type="tel" name="mobitel" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>📧 Email (za potvrdu):</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>👥 Broj osoba:</label>
                        <input type="number" name="broj_osoba" id="broj_osoba" class="form-control" min="1" value="2" required>
                        <div id="infoKapacitet" class="info-text"></div>
                    </div>

                    <div class="usluge-box">
                        <h3>🎣 Dodatne usluge</h3>
                        
                        <div class="usluga-item">
                            <input type="checkbox" class="usluga-checkbox" data-id="1" data-cijena="3.90" data-najava="0">
                            <span class="usluga-naziv">🎣 Šaranska kadica</span>
                            <span class="usluga-cijena">3.90 € (kom)</span>
                            <input type="number" class="usluga-kolicina" name="usluga_1" min="0" max="10" value="0" disabled>
                        </div>
                        
                        <div class="usluga-item">
                            <input type="checkbox" class="usluga-checkbox" data-id="2" data-cijena="4.90" data-najava="1">
                            <span class="usluga-naziv">🌽 Prihrana (hranidbeni tretman)</span>
                            <span class="usluga-cijena">4.90 € (3kg)</span>
                            <input type="number" class="usluga-kolicina" name="usluga_2" min="0" max="10" value="0" disabled>
                        </div>
                        
                        <div class="najava-info">
                            ⚠️ Prihranu je obavezno naručiti 2 dana prije ribolova!<br>
                            Kontakt: <strong>091 139 9709</strong>
                        </div>
                        
                        <div class="usluga-item">
                            <input type="checkbox" class="usluga-checkbox" data-id="3" data-cijena="2.00" data-najava="0">
                            <span class="usluga-naziv">🔴 Pelet 5mm</span>
                            <span class="usluga-cijena">2.00 € (po kg)</span>
                            <input type="number" class="usluga-kolicina" name="usluga_3" min="0" max="10" value="0" disabled>
                        </div>
                        
                        <div id="ukupnoUslugeContainer" class="usluge-ukupno-container">
                            <table class="usluge-tablica">
                                <thead><tr><th>Usluga</th><th>Cijena</th><th>Količina</th><th>Ukupno</th> </thead>
                                <tbody id="uslugeTbody"></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>📝 Napomena:</label>
                        <textarea name="napomena" class="form-control" rows="3" placeholder="Dolazim kasnije, trebam struju, roštilj..."></textarea>
                    </div>

                    <div class="ukupno-box">
                        <h3>💰 UKUPNO ZA PLATITI: <span id="ukupnaCijena">0.00</span> €</h3>
                    </div>

                    <button type="submit" class="btn" style="width: 100%;">🎣 REZERVIRAJ</button>
                </form>
            </div>

            <!-- DESNA STRANA - CIJENE -->
            <div>
                <div class="card info-r23">
                    <h2>🎫 Dnevne karte (R23)</h2>
                    <?php 
                    $tipovi_pregled = $mysqli->query("SELECT * FROM tipovi_ulaznica WHERE naziv NOT LIKE '%Otok%' AND aktivan = 1");
                    while ($t = $tipovi_pregled->fetch_assoc()): 
                    ?>
                    <p><strong><?php echo $t['naziv']; ?></strong> - <?php echo $t['cijena']; ?> €</p>
                    <?php endwhile; ?>
                </div>

                <div class="card info-r23">
                    <h2>🌙 Noćni ribolov (R23)</h2>
                    <?php 
                    $nocni_pregled = $mysqli->query("SELECT * FROM nocni_ribolov WHERE naziv NOT LIKE '%Otok%' AND aktivan = 1 ORDER BY dani");
                    while ($n = $nocni_pregled->fetch_assoc()): 
                    ?>
                    <p><strong><?php echo $n['naziv']; ?></strong> (<?php echo $n['dani']; ?> dana) - <?php echo $n['cijena']; ?> €</p>
                    <?php endwhile; ?>
                    <small>Noćni ribolov dozvoljen samo određene vikende.</small>
                </div>

                <div class="card otok-card info-otok" style="display: none;">
                    <h2>🏝️ C&R OTOK</h2>
                    <p><strong>Dnevne karte:</strong></p>
                    <?php 
                    $otok_tipovi = $mysqli->query("SELECT * FROM tipovi_ulaznica WHERE naziv LIKE '%Otok%' AND aktivan = 1");
                    while ($t = $otok_tipovi->fetch_assoc()): 
                    ?>
                    <p>• <?php echo $t['naziv']; ?> - <strong><?php echo $t['cijena']; ?> €</strong></p>
                    <?php endwhile; ?>
                    <p><strong>🌙 Noćni ribolov:</strong></p>
                    <?php 
                    $otok_nocni = $mysqli->query("SELECT * FROM nocni_ribolov WHERE naziv LIKE '%Otok%' AND aktivan = 1 ORDER BY dani");
                    while ($n = $otok_nocni->fetch_assoc()): 
                    ?>
                    <p>• <?php echo $n['naziv']; ?> (<?php echo $n['dani']; ?> dana) - <strong><?php echo $n['cijena']; ?> €</strong></p>
                    <?php endwhile; ?>
                    <p>🏠 Sjenica • 🔥 Roštilj • ⚡ Struja • 🚤 Prijevoz barkom</p>
                    <small>Sve ulaznice uključuju C&R režim</small>
                </div>

                <div class="card">
                    <h2>🎣 Dodatne usluge</h2>
                    <table class="cjenik-tablica">
                        <tr><td>🎣 Šaranska kadica</td><td><strong>3.90 €</strong></td><td>(po kom)</td></tr>
                        <tr><td>🌽 Prihrana (hranidbeni tretman)</td><td><strong>4.90 €</strong></td><td>(po tretmanu)</td></tr>
                        <tr><td>🔴 Pelet 5mm</td><td><strong>2.00 €</strong></td><td>(po kg)</td></tr>
                    </table>
                    <div class="najava-info" style="margin-top: 10px;">
                        ⚠️ Prihranu je obavezno naručiti 2 dana prije ribolova!<br>
                        📞 <strong>091 139 9709</strong>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="footer-content">
            <div>
                <h3>Ribnjačarstvo Končanica</h3>
                <p>Najstarije ribnjačarstvo u Hrvatskoj</p>
                <p>od 1900.</p>
            </div>
            <div>
                <h3>Kontakt</h3>
                <p>📞 +385 91 139 9709</p>
                <p>⏰ Pon - Ned: 06:00 - 20:00</p>
                <p>📧 info@ribnjacarstvo-koncanica.hr</p>
            </div>
            <div>
                <h3>Adresa</h3>
                <p>Končanica, Hrvatska</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 - Ribnjačarstvo Končanica | Najstarije ribnjačarstvo u Hrvatskoj od 1900.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        var zauzetiDatumi = [];
        var blokiraniDatumi = <?php echo json_encode($blokirani_datumi); ?>;
        
        var sviTipovi = <?php 
            $sviTipovi = $mysqli->query("SELECT * FROM tipovi_ulaznica WHERE aktivan = 1");
            $tipovi_array = [];
            while ($t = $sviTipovi->fetch_assoc()) {
                $tipovi_array[] = $t;
            }
            echo json_encode($tipovi_array);
        ?>;
        
        var sviNocni = <?php 
            $sviNocni = $mysqli->query("SELECT * FROM nocni_ribolov WHERE aktivan = 1");
            $nocni_array = [];
            while ($n = $sviNocni->fetch_assoc()) {
                $nocni_array[] = $n;
            }
            echo json_encode($nocni_array);
        ?>;
        
        function azurirajPremaLokaciji(id_lokacije, naziv_lokacije) {
            var jeOtok = naziv_lokacije && naziv_lokacije.toLowerCase().includes("otok");
            
            var filtriraniTipovi = sviTipovi.filter(function(tip) {
                if (jeOtok) return tip.naziv.toLowerCase().includes("otok");
                else return !tip.naziv.toLowerCase().includes("otok");
            });
            
            var filtriraniNocni = sviNocni.filter(function(nocni) {
                if (jeOtok) return nocni.naziv.toLowerCase().includes("otok");
                else return !nocni.naziv.toLowerCase().includes("otok");
            });
            
            var tipSelect = $("#tipUlaznice");
            tipSelect.empty();
            tipSelect.append('<option value="">-- Odaberi tip --</option>');
            filtriraniTipovi.forEach(function(tip) {
                tipSelect.append('<option value="' + tip.id_tipa + '" data-cijena="' + tip.cijena + '">' + tip.naziv + ' - ' + tip.cijena + '€</option>');
            });
            
            var nocniSelect = $("#nocniRibolov");
            nocniSelect.empty();
            nocniSelect.append('<option value="">-- Bez noćnog ribolova --</option>');
            filtriraniNocni.forEach(function(nocni) {
                nocniSelect.append('<option value="' + nocni.id_paketa + '" data-cijena="' + nocni.cijena + '">' + nocni.naziv + ' (' + nocni.dani + ' dana) - ' + nocni.cijena + '€</option>');
            });
            
            tipSelect.val("");
            nocniSelect.val("");
            
            if (jeOtok) {
                $(".info-r23").hide();
                $(".info-otok").show();
            } else {
                $(".info-r23").show();
                $(".info-otok").hide();
            }
            
            izracunajCijenu();
        }
        
        function ucitajZauzeteDatume(id_lokacije) {
            if (!id_lokacije) {
                zauzetiDatumi = [];
                if ($("#datum").datepicker("instance")) $("#datum").datepicker("refresh");
                return;
            }
            $.ajax({
                url: 'zauzeti_datumi.php',
                type: 'POST',
                data: { id_lokacije: id_lokacije },
                dataType: 'json',
                success: function(data) {
                    zauzetiDatumi = data;
                    if ($("#datum").datepicker("instance")) $("#datum").datepicker("refresh");
                }
            });
        }
        
        function izracunajUsluge() {
            var ukupno = 0;
            var tbody = $("#uslugeTbody");
            tbody.empty();
            var imaUsluga = false;
            
            $(".usluga-item").each(function() {
                var cb = $(this).find(".usluga-checkbox");
                var kol = $(this).find(".usluga-kolicina");
                if (cb.is(":checked") && kol.val() > 0) {
                    var naziv = $(this).find(".usluga-naziv").text();
                    var cijena = parseFloat(cb.data("cijena")) || 0;
                    var kolicina = parseInt(kol.val()) || 0;
                    var suma = cijena * kolicina;
                    ukupno += suma;
                    imaUsluga = true;
                    tbody.append('<tr><td>' + naziv + '</td><td>' + cijena.toFixed(2) + ' €</td><td>' + kolicina + '</td><td style="color:#27ae60;">' + suma.toFixed(2) + ' €</td></tr>');
                }
            });
            
            if (imaUsluga) {
                tbody.append('<tr class="ukupno-red"><td colspan="3"><strong>UKUPNO:</strong></td><td style="color:#e67e22;font-weight:bold;">' + ukupno.toFixed(2) + ' €</td></tr>');
            }
            return ukupno;
        }
        
        function izracunajCijenu() {
            var ukupno = 0;
            var brOsoba = parseInt($("#broj_osoba").val()) || 0;
            var tip = $("#tipUlaznice option:selected");
            var cijenaTip = parseFloat(tip.data("cijena")) || 0;
            ukupno += cijenaTip * brOsoba;
            var nocni = $("#nocniRibolov option:selected");
            var cijenaNocni = parseFloat(nocni.data("cijena")) || 0;
            ukupno += cijenaNocni;
            ukupno += izracunajUsluge();
            $("#ukupnaCijena").text(ukupno.toFixed(2));
        }
        
        function provjeriNajavu() {
            var datum = $("#datum").val();
            if (!datum) return;
            var potrebnaNajava = false;
            $(".usluga-checkbox").each(function() {
                if ($(this).is(":checked") && $(this).data("najava") == 1) potrebnaNajava = true;
            });
            if (potrebnaNajava) {
                var danas = new Date();
                var odabrani = new Date(datum);
                var razlika = Math.ceil((odabrani - danas) / (1000 * 60 * 60 * 24));
                if (razlika < 2) {
                    $("#infoDatum").html("⚠️ Prihrana treba 2 dana najave!").css("color", "#e74c3c");
                }
            }
        }
        
        $(document).ready(function() {
            // Sakrij loader
            setTimeout(function() {
                $("#loader").addClass("hide");
            }, 500);
            
            if ($("#lokacija").val()) {
                ucitajZauzeteDatume($("#lokacija").val());
                azurirajPremaLokaciji($("#lokacija").val(), $("#lokacija option:selected").text());
            }
            
            $("#lokacija").off('change').on('change', function() {
                var id = $(this).val();
                var naziv = $(this).find("option:selected").text();
                ucitajZauzeteDatume(id);
                azurirajPremaLokaciji(id, naziv);
                var kap = $(this).find("option:selected").data("kapacitet");
                if (kap) {
                    $("#broj_osoba").attr("max", kap);
                    $("#infoKapacitet").html("🎣 Max " + kap + " osoba").css("color", "#27ae60");
                }
                izracunajCijenu();
            });
            
            $("#datum").datepicker({
                dateFormat: "yy-mm-dd",
                minDate: 0,
                maxDate: "+3M",
                beforeShowDay: function(date) {
                    var ds = $.datepicker.formatDate('yy-mm-dd', date);
                    if (blokiraniDatumi.indexOf(ds) !== -1) return [false, "blokirano", "⛔ Zatvoreno"];
                    if (zauzetiDatumi.indexOf(ds) !== -1) return [false, "zauzet", "❌ Zauzeto"];
                    return [true, "slobodan", "✅ Slobodno"];
                },
                onSelect: function(dateText) {
                    $("#infoDatum").html("✅ Odabrano: " + dateText).css("color", "#27ae60");
                    provjeriNajavu();
                    izracunajCijenu();
                }
            });
            
            $("#tipUlaznice, #nocniRibolov").change(izracunajCijenu);
            $("#broj_osoba").on("input", izracunajCijenu);
            
            $(".usluga-checkbox").change(function() {
                var input = $(this).closest(".usluga-item").find(".usluga-kolicina");
                input.prop("disabled", !$(this).is(":checked"));
                if (!$(this).is(":checked")) input.val(0);
                izracunajUsluge();
                provjeriNajavu();
                izracunajCijenu();
            });
            
            $(".usluga-kolicina").on("input", function() {
                izracunajUsluge();
                izracunajCijenu();
            });
            
            $("#rezervacijaForma").submit(function(e) {
                var greska = "";
                
                if (!$("#lokacija").val()) {
                    greska += "❌ Odaberi lokaciju\n";
                }
                if (!$("#tipUlaznice").val()) {
                    greska += "❌ Odaberi tip ulaznice\n";
                }
                if (!$("#datum").val()) {
                    greska += "❌ Odaberi datum\n";
                }
                if (!$("input[name='ime']").val().trim()) {
                    greska += "❌ Unesi ime i prezime\n";
                }
                if (!$("input[name='mobitel']").val().trim()) {
                    greska += "❌ Unesi broj mobitela\n";
                }
                if (!$("input[name='email']").val().trim()) {
                    greska += "❌ Unesi email\n";
                }
                if (!$("#broj_osoba").val() || $("#broj_osoba").val() <= 0) {
                    greska += "❌ Unesi broj osoba\n";
                }
                
                if (greska !== "") {
                    alert(greska);
                    e.preventDefault();
                    return false;
                }
                
                return confirm("Potvrdi rezervaciju?\nUkupno: " + $("#ukupnaCijena").text() + " €");
            });
            
            izracunajUsluge();
            izracunajCijenu();
        });
    </script>
</body>
</html>