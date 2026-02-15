// =====================================================
// DATOTEKA: script.js
// OPIS: Svi JavaScripti za interaktivnost
// =====================================================

$(document).ready(function() {
    // =================================================
    // 1. DATUM PICKER - Odabir datuma s UI-ja
    // =================================================
    $("#datum").datepicker({
        dateFormat: "yy-mm-dd",
        minDate: 0, // ne može se odabrati prošli datum
        maxDate: "+3M", // maksimalno 3 mjeseca unaprijed
        beforeShowDay: function(date) {
            // Ovdje ćemo kasnije implementirati provjeru zauzetosti
            return [true, ""];
        },
        onSelect: function(dateText) {
            provjeriZauzetost();
        }
    });

    // =================================================
    // 2. PROVJERA ZAUZETOSTI LOKACIJA (AJAX)
    // =================================================
    $("#lokacija").change(function() {
        provjeriZauzetost();
        azurirajKapacitet();
    });

    function provjeriZauzetost() {
        var lokacijaId = $("#lokacija").val();
        var datum = $("#datum").val();
        
        if (lokacijaId && datum) {
            $.ajax({
                url: 'provjera_rezervacije.php',
                type: 'POST',
                data: {
                    id_lokacije: lokacijaId,
                    datum: datum
                },
                success: function(response) {
                    if (response == 'slobodno') {
                        $("#datumInfo").html('✅ Termin je slobodan!').css('color', '#27ae60');
                        $("#submitBtn").prop('disabled', false);
                    } else {
                        $("#datumInfo").html('❌ Termin je zauzet! Odaberi drugi datum.').css('color', '#e74c3c');
                        $("#submitBtn").prop('disabled', true);
                    }
                }
            });
        }
    }

    // =================================================
    // 3. KAPACITET - Ograničenje broja osoba
    // =================================================
    function azurirajKapacitet() {
        var lokacijaId = $("#lokacija").val();
        if (lokacijaId) {
            var odabrana = lokacije.find(l => l.id_lokacije == lokacijaId);
            if (odabrana) {
                $("#broj_osoba").attr('max', odabrana.kapacitet);
                $("#kapacitetInfo").html('Maksimalno ' + odabrana.kapacitet + ' osoba na ovoj lokaciji');
                
                // Ako je C&R Otok, omogući više osoba
                if (odabrana.tip == 'C&R Otok') {
                    $("#broj_osoba").attr('max', 10);
                }
            }
        }
    }

    // =================================================
    // 4. KUKURUZ I PELET - Izračun cijene
    // =================================================
    function izracunajMamac() {
        var ukupno = 0;
        
        // Kukuruz: 10kg = 6€
        if ($("#kukuruz").is(":checked")) {
            var kolicinaKukuruza = parseInt($("#kukuruz_kolicina").val()) || 0;
            ukupno += kolicinaKukuruza * 6;
        }
        
        // Pelet: 1kg = 2€
        if ($("#pelet").is(":checked")) {
            var kolicinaPeleta = parseInt($("#pelet_kolicina").val()) || 0;
            ukupno += kolicinaPeleta * 2;
        }
        
        $("#ukupnoMamac").text(ukupno.toFixed(2));
    }

    // Checkbox eventi za mamac
    $("#kukuruz").change(function() {
        $("#kukuruz_kolicina").prop('disabled', !$(this).is(":checked"));
        if (!$(this).is(":checked")) {
            $("#kukuruz_kolicina").val(0);
        }
        izracunajMamac();
    });

    $("#pelet").change(function() {
        $("#pelet_kolicina").prop('disabled', !$(this).is(":checked"));
        if (!$(this).is(":checked")) {
            $("#pelet_kolicina").val(0);
        }
        izracunajMamac();
    });

    // Količine eventi
    $("#kukuruz_kolicina, #pelet_kolicina").change(izracunajMamac);
    $("#kukuruz_kolicina, #pelet_kolicina").keyup(izracunajMamac);

    // =================================================
    // 5. AUTOMATSKI ODABIR LOKACIJE IZ URL-a
    // =================================================
    const urlParams = new URLSearchParams(window.location.search);
    const tip = urlParams.get('tip');
    
    if (tip == 'OTOK') {
        // Pronađi ID otoka i odaberi ga
        var otok = lokacije.find(l => l.tip == 'C&R Otok');
        if (otok) {
            $("#lokacija").val(otok.id_lokacije);
            azurirajKapacitet();
        }
    }

    // =================================================
    // 6. VALIDACIJA FORME PRIJE SLANJA
    // =================================================
    $("#rezervacijaForma").submit(function(e) {
        var lokacija = $("#lokacija").val();
        var datum = $("#datum").val();
        var ime = $("#ime").val();
        var mobitel = $("#mobitel").val();
        var brojOsoba = $("#broj_osoba").val();
        
        if (!lokacija || !datum || !ime || !mobitel) {
            alert("Molimo popunite sva obavezna polja!");
            e.preventDefault();
            return false;
        }
        
        // Provjera mobitela (osnovna)
        if (mobitel.length < 9) {
            alert("Unesite ispravan broj mobitela!");
            e.preventDefault();
            return false;
        }
        
        // Ako je sve OK, pitaj za potvrdu
        var poruka = "Potvrdi rezervaciju?\n\n";
        poruka += "Lokacija: " + $("#lokacija option:selected").text() + "\n";
        poruka += "Datum: " + datum + "\n";
        poruka += "Osobe: " + brojOsoba + "\n";
        
        // Dodaj mamac info
        if ($("#kukuruz").is(":checked") && $("#kukuruz_kolicina").val() > 0) {
            poruka += "Kukuruz: " + $("#kukuruz_kolicina").val() + " × 10kg\n";
        }
        if ($("#pelet").is(":checked") && $("#pelet_kolicina").val() > 0) {
            poruka += "Pelet: " + $("#pelet_kolicina").val() + " × 1kg\n";
        }
        
        return confirm(poruka);
    });

    // =================================================
    // 7. FUNKCIJA ZA ODABIR LOKACIJE IZ POPISA
    // =================================================
    window.odaberiLokaciju = function(id) {
        $("#lokacija").val(id).trigger('change');
        azurirajKapacitet();
        
        // Scroll do forme
        $('html, body').animate({
            scrollTop: $("#rezervacijaForma").offset().top - 100
        }, 500);
        
        // Efekt pulse na formi
        $("#rezervacijaForma").addClass('pulse');
        setTimeout(function() {
            $("#rezervacijaForma").removeClass('pulse');
        }, 1000);
    }

    // =================================================
    // 8. ANIMACIJE I EFEKTI
    // =================================================
    $(window).scroll(function() {
        $(".fade-in").each(function() {
            var elementTop = $(this).offset().top;
            var elementBottom = elementTop + $(this).outerHeight();
            var viewportTop = $(window).scrollTop();
            var viewportBottom = viewportTop + $(window).height();
            
            if (elementBottom > viewportTop && elementTop < viewportBottom) {
                $(this).addClass("visible");
            }
        });
    }).scroll();

    // Inicijalno pokreni provjeru kapaciteta ako je lokacija već odabrana
    if ($("#lokacija").val()) {
        azurirajKapacitet();
    }
});