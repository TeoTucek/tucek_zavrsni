// =====================================================
// SCRIPT.JS - POTPUNO ISPRAVLJEN (ZA ZAUZETOST PO POZICIJI)
// =====================================================

$(document).ready(function() {

    // =============================================
    // 0. GLOBALNE VARIJABLE
    // =============================================
    var zauzetiDatumi = [];  // OVDJE ĆE BITI ZAUZETI DATUMI ZA ODABRANU POZICIJU

    // =============================================
    // 0.1 FUNKCIJA ZA DOHVAT ZAUZETIH DATUMA ZA ODABRANU POZICIJU
    // =============================================
    function ucitajZauzeteDatume(id_lokacije) {
        if (!id_lokacije) {
            zauzetiDatumi = [];
            if ($("#datum").datepicker("instance")) {
                $("#datum").datepicker("refresh");
            }
            return;
        }
        
        $.ajax({
            url: 'zauzeti_datumi.php',
            type: 'POST',
            data: { id_lokacije: id_lokacije },
            dataType: 'json',
            success: function(data) {
                zauzetiDatumi = data;
                // Osvježi kalendar
                if ($("#datum").datepicker("instance")) {
                    $("#datum").datepicker("refresh");
                }
            },
            error: function() {
                console.log("Greška pri dohvaćanju zauzetih datuma");
            }
        });
    }
// =============================================
// 1. KALENDAR (SA ZAUZETOŠĆU PO POZICIJI) - ISPRAVLJEN!
// =============================================
if ($("#datum").length) {

    $("#datum").datepicker({
        dateFormat: "yy-mm-dd",
        minDate: 0,
        maxDate: "+3M",
        beforeShowDay: function(date) {
            var dateString = $.datepicker.formatDate('yy-mm-dd', date);

            // 1. PROVJERA BLOKIRANIH DATUMA (cijeli ribnjak zatvoren)
            if (typeof blokiraniDatumi !== 'undefined' && blokiraniDatumi.indexOf(dateString) !== -1) {
                return [false, "blokirano", "⛔ Ribnjak zatvoren"];
            }

            // 2. PROVJERA ZAUZETOSTI ZA ODABRANU POZICIJU
            if (zauzetiDatumi.indexOf(dateString) !== -1) {
                return [false, "zauzet", "❌ Termin zauzet za ovu poziciju!"];
            }

            return [true, "slobodan", "✅ Slobodno"];
        },
        onSelect: function(dateText, inst) {
            // SPRJEČAVA SKOK NA VRH STRANICE
            event.preventDefault();
            
            $("#infoDatum").html("✅ Odabrano: " + dateText).css("color", "#27ae60");
            provjeriNajavu();
            izracunajCijenu();
            
            return false;
        }
    });
}

  // =============================================
// 2. KAD SE PROMIJENI LOKACIJA - DOHVAĆAMO ZAUZETE DATUME I AŽURIRAMO TIPOVE
// =============================================
$("#lokacija").off('change').on('change', function() {
    var id_lokacije = $(this).val();
    var naziv_lokacije = $(this).find("option:selected").text();
    
    // Učitaj zauzete datume za ovu poziciju
    ucitajZauzeteDatume(id_lokacije);
    
    // AŽURIRAJ TIPOVE ULAZNICA PREMA LOKACIJI (OTOK ILI R23)
    if (typeof azurirajPremaLokaciji !== 'undefined') {
        azurirajPremaLokaciji(id_lokacije, naziv_lokacije);
    }
    
    // Kapacitet
    var kap = $(this).find("option:selected").data("kapacitet");
    if (kap) {
        $("#broj_osoba").attr("max", kap);
        $("#infoKapacitet").html("🎣 Max " + kap + " osoba").css("color", "#27ae60");
    } else {
        $("#infoKapacitet").html("");
    }

    izracunajCijenu();
});

    // =============================================
    // 3. EVENTI
    // =============================================
    $("#tipUlaznice, #nocniRibolov").change(izracunajCijenu);
    $("#broj_osoba").on("input", izracunajCijenu);

    $(".usluga-checkbox").change(function() {
        var input = $(this).closest(".usluga-item").find(".usluga-kolicina");

        input.prop("disabled", !$(this).is(":checked"));

        if (!$(this).is(":checked")) {
            input.val(0);
        }

        izracunajUsluge();
        provjeriNajavu();
        izracunajCijenu();
    });

    $(".usluga-kolicina").on("input", function() {
        izracunajUsluge();
        izracunajCijenu();
    });

    // =============================================
    // 4. DODATNE USLUGE TABLICA
    // =============================================
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

                var row = `<tr>
                    <td>${naziv}</td>
                    <td>${cijena.toFixed(2)} €</td>
                    <td>${kolicina}</td>
                    <td style="color: #27ae60; font-weight: bold;">${suma.toFixed(2)} €</td>
                </tr>`;
                tbody.append(row);
            }
        });

        if (imaUsluga) {
            var totalRow = `<tr class="ukupno-red">
                <td colspan="3"><strong>UKUPNO DODATNE USLUGE:</strong></td>
                <td style="color: #e67e22; font-weight: bold; font-size: 1.1rem;">${ukupno.toFixed(2)} €</td>
            </tr>`;
            tbody.append(totalRow);
        }

        return ukupno;
    }

    // =============================================
    // 5. NAJAVA (PRIHRANA)
    // =============================================
    function provjeriNajavu() {
        var potrebnaNajava = false;
        
        $(".usluga-checkbox").each(function() {
            if ($(this).is(":checked") && $(this).data("najava") == 1) {
                potrebnaNajava = true;
            }
        });
        
        if (potrebnaNajava) {
            var datum = $("#datum").val();
            if (datum) {
                var danas = new Date();
                var odabrani = new Date(datum);
                var razlika = Math.ceil((odabrani - danas) / (1000 * 60 * 60 * 24));
                
                if (razlika < 2) {
                    $("#infoDatum").html("⚠️ Prihrana treba 2 dana najave! Odaberi kasniji datum.").css("color", "#e74c3c");
                } else {
                    if ($("#infoDatum").text().includes("⚠️")) {
                        $("#infoDatum").html("✅ Odabrano: " + datum).css("color", "#27ae60");
                    }
                }
            }
        }
    }

    // =============================================
    // 6. UKUPNA CIJENA
    // =============================================
    function izracunajCijenu() {
        var ukupno = 0;
        var brOsoba = parseInt($("#broj_osoba").val()) || 0;

        // TIP ULAZNICE
        var tip = $("#tipUlaznice option:selected");
        var cijenaTip = parseFloat(tip.data("cijena")) || 0;
        ukupno += cijenaTip * brOsoba;

        // NOĆNI RIBOLOV
        var nocni = $("#nocniRibolov option:selected");
        var cijenaNocni = parseFloat(nocni.data("cijena")) || 0;
        ukupno += cijenaNocni;

        // DODATNE USLUGE
        ukupno += izracunajUsluge();

        $("#ukupnaCijena").text(ukupno.toFixed(2));
    }

    // =============================================
    // 7. VALIDACIJA FORME
    // =============================================
    $("#rezervacijaForma").submit(function(e) {
        var lokacija = $("#lokacija").val();
        var datum = $("#datum").val();
        var tip = $("#tipUlaznice").val();
        var ime = $("input[name='ime']").val();
        var mobitel = $("input[name='mobitel']").val();
        
        if (!lokacija || !datum || !tip || !ime || !mobitel) {
            alert("❌ Molimo popunite sva obavezna polja!");
            e.preventDefault();
            return false;
        }
        
        // Provjera najave za prihranu
        var prihranaOdabrana = false;
        $(".usluga-checkbox").each(function() {
            if ($(this).is(":checked") && $(this).data("najava") == 1) {
                prihranaOdabrana = true;
            }
        });
        
        if (prihranaOdabrana) {
            var datumOdabrani = new Date(datum);
            var danas = new Date();
            var razlika = Math.ceil((datumOdabrani - danas) / (1000 * 60 * 60 * 24));
            if (razlika < 2) {
                alert("⚠️ Prihranu je potrebno naručiti 2 dana prije ribolova!\nMolimo odaberite kasniji datum.");
                e.preventDefault();
                return false;
            }
        }
        
        var poruka = "🎣 POTVRDA REZERVACIJE 🎣\n\n";
        poruka += "Lokacija: " + $("#lokacija option:selected").text() + "\n";
        poruka += "Tip ulaznice: " + $("#tipUlaznice option:selected").text() + "\n";
        poruka += "Datum: " + datum + "\n";
        poruka += "Broj osoba: " + $("#broj_osoba").val() + "\n";
        poruka += "Ukupno: " + $("#ukupnaCijena").text() + " €\n\n";
        poruka += "Potvrdi rezervaciju?";
        
        return confirm(poruka);
    });

    // =============================================
    // 8. INIT - AKO JE LOKACIJA VEĆ ODABRANA
    // =============================================
    if ($("#lokacija").val()) {
        ucitajZauzeteDatume($("#lokacija").val());
    }
    
    izracunajUsluge();
    izracunajCijenu();

    console.log("✅ SCRIPT RADI! Kalendar prikazuje zauzetost po poziciji!");
});


// ===== 3D BUBBLE EFEKT NA KLIK =====
$(document).on('click', function(e) {
    // Stvori element
    var bubble = document.createElement('div');
    bubble.innerHTML = ['🐟', '🐠', '🐡', '💧', '🌊', '🎣'][Math.floor(Math.random() * 6)];
    bubble.style.position = 'fixed';
    bubble.style.left = e.pageX - 15 + 'px';
    bubble.style.top = e.pageY - 15 + 'px';
    bubble.style.fontSize = '30px';
    bubble.style.pointerEvents = 'none';
    bubble.style.zIndex = '9999';
    bubble.style.opacity = '1';
    bubble.style.transition = 'all 1s ease-out';
    bubble.style.transform = 'scale(0)';
    
    document.body.appendChild(bubble);
    
    // Animacija
    setTimeout(function() {
        bubble.style.transform = 'scale(1.5)';
        bubble.style.opacity = '0';
        bubble.style.marginTop = '-50px';
    }, 10);
    
    // Ukloni nakon animacije
    setTimeout(function() {
        bubble.remove();
    }, 1000);
});