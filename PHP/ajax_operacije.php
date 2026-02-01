<?php
session_start();
require_once 'db_connection.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$response = ['success' => false, 'message' => ''];

switch ($action) {
    // Članovi
    case 'dodaj_clana':
        if (!isset($_SESSION['djelatnik']) || $_SESSION['djelatnik']['uloga'] !== 'admin') {
            $response['message'] = 'Nemate dozvolu za ovu operaciju.';
            break;
        }
        
        $ime = mysqli_real_escape_string($conn, $_POST['ime']);
        $prezime = mysqli_real_escape_string($conn, $_POST['prezime']);
        $jmbg = mysqli_real_escape_string($conn, $_POST['jmbg']);
        $adresa = mysqli_real_escape_string($conn, $_POST['adresa']);
        $telefon = mysqli_real_escape_string($conn, $_POST['telefon']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        $godisnja_ulaznica = intval($_POST['godisnja_ulaznica']);
        
        // Provjeri JMBG
        $check_query = "SELECT id_Clan FROM clan WHERE jmbg = '$jmbg'";
        if (mysqli_num_rows(mysqli_query($conn, $check_query)) > 0) {
            $response['message'] = 'Član s tim JMBG-om već postoji!';
        } else {
            $query = "INSERT INTO clan (ime, prezime, jmbg, adresa, telefon, email, status, godisnja_ulaznica, datum_uclanjenja) 
                     VALUES ('$ime', '$prezime', '$jmbg', '$adresa', '$telefon', '$email', '$status', '$godisnja_ulaznica', CURDATE())";
            
            if (mysqli_query($conn, $query)) {
                $response['success'] = true;
                $response['message'] = 'Član uspješno dodan!';
            } else {
                $response['message'] = 'Greška: ' . mysqli_error($conn);
            }
        }
        break;
        
    case 'obrisi_clana':
        if (!isset($_SESSION['djelatnik']) || $_SESSION['djelatnik']['uloga'] !== 'admin') {
            $response['message'] = 'Nemate dozvolu za ovu operaciju.';
            break;
        }
        
        $id = intval($_POST['id']);
        $query = "DELETE FROM clan WHERE id_Clan = $id";
        
        if (mysqli_query($conn, $query)) {
            $response['success'] = true;
            $response['message'] = 'Član uspješno obrisan!';
        } else {
            $response['message'] = 'Greška: ' . mysqli_error($conn);
        }
        break;
        
    // Izlovi
    case 'zakazi_izlov':
        if (!isset($_SESSION['djelatnik'])) {
            $response['message'] = 'Morate biti prijavljeni.';
            break;
        }
        
        $clan_id = intval($_POST['clan_id']);
        $ribnjak_id = intval($_POST['ribnjak_id']);
        $datum = mysqli_real_escape_string($conn, $_POST['datum']);
        $vrijeme = mysqli_real_escape_string($conn, $_POST['vrijeme']);
        $trajanje = intval($_POST['trajanje']);
        
        $query = "INSERT INTO izlov (clan_id, ribnjak_id, datum, vrijeme, trajanje, cijena, placeno) 
                 VALUES ($clan_id, $ribnjak_id, '$datum', '$vrijeme', $trajanje, $trajanje * 10, 0)";
        
        if (mysqli_query($conn, $query)) {
            $response['success'] = true;
            $response['message'] = 'Izlov uspješno zakazan!';
        } else {
            $response['message'] = 'Greška: ' . mysqli_error($conn);
        }
        break;
        
    case 'dodaj_izlov':
        if (!isset($_SESSION['djelatnik'])) {
            $response['message'] = 'Morate biti prijavljeni.';
            break;
        }
        
        $clan_id = intval($_POST['clan_id']);
        $ribnjak_id = intval($_POST['ribnjak_id']);
        $datum = mysqli_real_escape_string($conn, $_POST['datum']);
        $vrijeme = mysqli_real_escape_string($conn, $_POST['vrijeme']);
        $trajanje = intval($_POST['trajanje']);
        $tezina_kg = floatval($_POST['tezina_kg']);
        $cijena = floatval($_POST['cijena']);
        
        $query = "INSERT INTO izlov (clan_id, ribnjak_id, datum, vrijeme, trajanje, tezina_kg, cijena, placeno) 
                 VALUES ($clan_id, $ribnjak_id, '$datum', '$vrijeme', $trajanje, $tezina_kg, $cijena, 0)";
        
        if (mysqli_query($conn, $query)) {
            $response['success'] = true;
            $response['message'] = 'Izlov uspješno dodan!';
        } else {
            $response['message'] = 'Greška: ' . mysqli_error($conn);
        }
        break;
        
    case 'oznaci_placeno':
        if (!isset($_SESSION['djelatnik'])) {
            $response['message'] = 'Morate biti prijavljeni.';
            break;
        }
        
        $id = intval($_POST['id']);
        $query = "UPDATE izlov SET placeno = 1 WHERE id_izlov = $id";
        
        if (mysqli_query($conn, $query)) {
            $response['success'] = true;
            $response['message'] = 'Izlov označen kao plaćen!';
        } else {
            $response['message'] = 'Greška: ' . mysqli_error($conn);
        }
        break;
        
    case 'obrisi_izlov':
        if (!isset($_SESSION['djelatnik'])) {
            $response['message'] = 'Morate biti prijavljeni.';
            break;
        }
        
        $id = intval($_POST['id']);
        $query = "DELETE FROM izlov WHERE id_izlov = $id";
        
        if (mysqli_query($conn, $query)) {
            $response['success'] = true;
            $response['message'] = 'Izlov uspješno obrisan!';
        } else {
            $response['message'] = 'Greška: ' . mysqli_error($conn);
        }
        break;
        
    // Djelatnici
    case 'dodaj_djelatnika':
        if (!isset($_SESSION['djelatnik']) || $_SESSION['djelatnik']['uloga'] !== 'admin') {
            $response['message'] = 'Nemate dozvolu za ovu operaciju.';
            break;
        }
        
        $ime = mysqli_real_escape_string($conn, $_POST['ime']);
        $prezime = mysqli_real_escape_string($conn, $_POST['prezime']);
        $korisnicko_ime = mysqli_real_escape_string($conn, $_POST['korisnicko_ime']);
        $lozinka = password_hash($_POST['lozinka'], PASSWORD_DEFAULT);
        $uloga = mysqli_real_escape_string($conn, $_POST['uloga']);
        $aktivan = intval($_POST['aktivan']);
        
        // Provjeri korisničko ime
        $check_query = "SELECT id_djelatnik FROM djelatnici WHERE korisnicko_ime = '$korisnicko_ime'";
        if (mysqli_num_rows(mysqli_query($conn, $check_query)) > 0) {
            $response['message'] = 'Korisničko ime već postoji!';
        } else {
            $query = "INSERT INTO djelatnici (korisnicko_ime, lozinka, ime, prezime, uloga, aktivan) 
                     VALUES ('$korisnicko_ime', '$lozinka', '$ime', '$prezime', '$uloga', $aktivan)";
            
            if (mysqli_query($conn, $query)) {
                $response['success'] = true;
                $response['message'] = 'Djelatnik uspješno dodan!';
            } else {
                $response['message'] = 'Greška: ' . mysqli_error($conn);
            }
        }
        break;
        
    case 'obrisi_djelatnika':
        if (!isset($_SESSION['djelatnik']) || $_SESSION['djelatnik']['uloga'] !== 'admin') {
            $response['message'] = 'Nemate dozvolu za ovu operaciju.';
            break;
        }
        
        $id = intval($_POST['id']);
        // Ne dozvoli brisanje sebe samog
        if ($id == $_SESSION['djelatnik']['id_djelatnik']) {
            $response['message'] = 'Ne možete obrisati samog sebe!';
            break;
        }
        
        $query = "DELETE FROM djelatnici WHERE id_djelatnik = $id";
        
        if (mysqli_query($conn, $query)) {
            $response['success'] = true;
            $response['message'] = 'Djelatnik uspješno obrisan!';
        } else {
            $response['message'] = 'Greška: ' . mysqli_error($conn);
        }
        break;
        
    // Podaci za grafikone
    case 'get_chart_data':
        if (!isset($_SESSION['djelatnik'])) {
            break;
        }
        
        // Podaci za ribnjake
        $query = "SELECT r.naziv, COUNT(i.id_izlov) as broj_izlova
                 FROM ribnjaci r
                 LEFT JOIN izlov i ON r.id_ribnjaci = i.ribnjak_id
                 WHERE r.aktivan = 1
                 GROUP BY r.id_ribnjaci";
        $result = mysqli_query($conn, $query);
        $ribnjaci = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $ribnjaci[] = $row;
        }
        
        // Podaci za mjesečnu zaradu
        $zarada = array_fill(0, 12, 0);
        $query = "SELECT MONTH(datum) as mjesec, SUM(cijena) as zarada
                 FROM izlov 
                 WHERE YEAR(datum) = YEAR(CURDATE()) AND placeno = 1
                 GROUP BY MONTH(datum)";
        $result = mysqli_query($conn, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            $zarada[$row['mjesec'] - 1] = floatval($row['zarada']);
        }
        
        $response['success'] = true;
        $response['ribnjaci'] = $ribnjaci;
        $response['zarada'] = $zarada;
        break;
        
    default:
        $response['message'] = 'Nepoznata akcija';
}

header('Content-Type: application/json');
echo json_encode($response);
case 'rezerviraj_uslugu':
    if (!isset($_SESSION['djelatnik'])) {
        $response['message'] = 'Morate biti prijavljeni.';
        break;
    }
    
    $usluga_id = intval($_POST['usluga_id']);
    $clan_id = intval($_POST['clan_id']);
    $datum = mysqli_real_escape_string($conn, $_POST['datum']);
    $kolicina = intval($_POST['kolicina']);
    
    // Dohvati podatke o usluzi
    $query = "SELECT * FROM cjenik WHERE id_cjenik = $usluga_id";
    $result = mysqli_query($conn, $query);
    $usluga = mysqli_fetch_assoc($result);
    
    if (!$usluga) {
        $response['message'] = 'Usluga ne postoji.';
        break;
    }
    
    $ukupna_cijena = $usluga['cijena'] * $kolicina;
    
    // Kreiraj rezervaciju
    $query = "INSERT INTO izlov (clan_id, ribnjak_id, datum, cijena, placeno) 
             VALUES ($clan_id, {$usluga['ribnjak_id']}, '$datum', $ukupna_cijena, 0)";
    
    if (mysqli_query($conn, $query)) {
        $response['success'] = true;
        $response['message'] = 'Usluga uspješno rezervirana!';
    } else {
        $response['message'] = 'Greška: ' . mysqli_error($conn);
    }
    break;
?>