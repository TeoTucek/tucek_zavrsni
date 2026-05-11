# Izvještaj o pronađenim greškama i ispravcima


## Uvod

Pri pregledu tvoje aplikacije naišla sam na ozbiljan funkcionalni nedostatak koji si i sam primijetio — cijena prikazana korisniku pri rezervaciji ne odgovara cijeni koju vidi administrator. Krenula sam tražiti uzrok tog problema, no pri tome sam pronašla i niz drugih grešaka — neke logičke, neke sigurnosne — koje se moraju ispraviti prije nego što aplikacija bude prezentirana.

Ovaj dokument prolazi kroz **sve** pronađene probleme po prioritetu, objašnjava **što je bilo krivo**, **zašto je to problem** i **kako je popravljeno**. Cilj je da razumiješ što je u kodu falilo, a ne samo da prepišeš ispravak koda.

---

## 1. Cijena u korisničkom prikazu ne odgovara cijeni u admin sučelju

**Težina:** kritično (osnovna funkcionalnost)

### Što se događalo

Kad korisnik na frontendu odabere R23 ulaznicu + Trodnevni noćni ribolov (45 €), JavaScript funkcija `izracunajCijenu()` u `index.php` ispravno računa:

```
ukupno = (cijena_tipa × broj_osoba) + cijena_nocni + cijena_usluge
```

Isto računa i `spremi.php` na liniji 84 te tu cijenu šalje u potvrdni email korisniku. Međutim, u tablici `rezervacije` ti si stupce `id_tipa_ulaznice` i `id_paketa_nocni` napravio u shemi, ali ih u `INSERT` upit nikad nisi uključio. Pogledajte original (`spremi.php`, prije ispravka):

```php
$stmt = $mysqli->prepare("INSERT INTO rezervacije
    (id_lokacije, datum_rezervacije, ime_prezime, broj_mobitela, email,
     broj_osoba, cijena_po_osobi, napomena)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
```

Posljedica: svaka rezervacija u bazi ima `id_tipa_ulaznice = NULL` i `id_paketa_nocni = NULL`. Što znači da kasnije, kad admin otvara pregled, ne postoji način da sazna je li korisnik plaćao noćni ribolov ili samo dnevnu kartu. U `admin/admin_rezervacije.php` ukupna cijena računa se kao:

```php
$ukupna_cijena = ($r['broj_osoba'] * $r['cijena_po_osobi']) + $ukupno_usluge;
```

**Noćni ribolov je potpuno ispao iz računice.** Ako je korisnik platio R23 + Trodnevni noćni paket, admin vidi 45 € manje nego što je korisnik vidio u potvrdi.

### Kako je popravljeno

1. U `spremi.php` `INSERT` proširen je s dva stupca:

   ```php
   $stmt = $mysqli->prepare("INSERT INTO rezervacije
       (id_lokacije, id_tipa_ulaznice, id_paketa_nocni, datum_rezervacije,
        ime_prezime, broj_mobitela, email, broj_osoba, cijena_po_osobi, napomena)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
   $stmt->bind_param("iiissssids", $id_lokacije, $id_tipa_ulaznice,
       $id_paketa_nocni, $datum, $ime, $mobitel, $email, $broj_osoba,
       $cijena_tipa, $napomena);
   ```

2. U `admin/admin_rezervacije.php` glavni `SELECT` proširen je s `LEFT JOIN` na tablicu `nocni_ribolov` pa se cijena noćnog dohvaća zajedno s ostatkom retka:

   ```sql
   LEFT JOIN nocni_ribolov n ON r.id_paketa_nocni = n.id_paketa
   ```

   Ukupna cijena je sada:
   ```php
   $cijena_nocni = $r['nocni_cijena'] ?? 0;
   $ukupna_cijena = ($r['broj_osoba'] * $r['cijena_po_osobi']) + $cijena_nocni + $ukupno_usluge;
   ```

3. Email-funkcija `posaljiEmailPotvrda()` također dohvaća cijenu noćnog i prikazuje retku „Noćni ribolov" u tijelu poruke.

4. `pdf_generator.php` ažuriran je na isti način — i u stavkama i u ukupnoj cijeni.

### Pouka

Kad imaš podatak koji prikazuješ korisniku, **mora završiti u bazi**. Ako ga ne spremiš, izgubljen je zauvijek. Provjerite svaki put nakon `INSERT`-a podudaraju li se stupci na koje ciljaš i podaci koje šalješ — jednostavni `SELECT *` u phpMyAdmin nakon prve rezervacije bi ovaj problem otkrio odmah.

---

## 2. Otkazani termini se nisu mogli ponovno rezervirati

**Težina:** kritično (osnovna funkcionalnost)

### Što se događalo

U `install.php` (i u dump-u) na tablici `rezervacije` definiran je:

```sql
UNIQUE KEY unique_rez (id_lokacije, datum_rezervacije)
```

`UNIQUE` index znači da za danu kombinaciju lokacije i datuma može postojati **samo jedan** redak — bez obzira na status. Aplikacijska logika je drugačija: u `spremi.php` provjerava se zauzetost samo za rezervacije sa statusom različitim od `'otkazano'`. Te dvije pretpostavke su u sukobu.

Scenarij:

1. Mihael rezervira poziciju 1 za 15.04. → status `na čekanju`.
2. Admin otkazuje rezervaciju → status `otkazano`, redak ostaje u tablici.
3. Pero pokušava rezervirati istu poziciju za 15.04.
4. PHP provjera prolazi (jer u upitu stoji `status != 'otkazano'`).
5. `INSERT` puca jer redak već postoji u UNIQUE indeksu.
6. `$stmt->execute()` vraća `false`, korisnik se preusmjerava na `?status=greska`.
7. Pero vidi „Greška pri spremanju" bez ikakvog objašnjenja, i ne može rezervirati taj termin **nikad** dok admin ručno ne obriše stari redak.

### Kako je popravljeno

UNIQUE index je zamijenjen običnim indeksom. SQL koji je izvršen:

```sql
ALTER TABLE rezervacije ADD INDEX idx_lokacije (id_lokacije);
ALTER TABLE rezervacije DROP INDEX unique_rez;
ALTER TABLE rezervacije ADD INDEX idx_lokacija_datum (id_lokacije, datum_rezervacije);
```

Prvi `ADD INDEX` je nužan jer je `unique_rez` koristio strani ključ `rezervacije_ibfk_1` (FK na `lokacije.id_lokacije`); MariaDB ne dopušta brisanje indeksa potrebnog FK-u. Treći indeks je običan, ne-jedinstveni, pa ne ograničava ponovno rezerviranje — služi samo za brže pretrage.

Provjera zauzetosti je sada isključivo aplikacijska, kako je i bila zamišljena.

### Pouka

Pravilo poslovne logike („termin smije biti zauzet samo jednom") nije isto što i pravilo na razini sheme („kombinacija stupaca smije postojati samo jednom"). Ako odlučiš statuse držati u bazi, ne smiješ paralelno imati i UNIQUE constraint nad istim stupcima — biraj jedno ili drugo. U ovom slučaju aplikacijska provjera je dovoljna (postoji race-condition prozor od par milisekundi između `SELECT` i `INSERT`, što je za ovu domenu prihvatljivo).

---

## 3. SQL injection u admin filterima

**Težina:** kritično (sigurnost)

### Što se događalo

U `admin/admin_rezervacije.php`, statistički upit gradio se konkatenacijom GET parametara:

```php
if (!empty($filter_lokacija)) $stats_sql .= " AND r.id_lokacije = $filter_lokacija";
if (!empty($filter_datum_od)) $stats_sql .= " AND r.datum_rezervacije >= '$filter_datum_od'";
if (!empty($filter_datum_do)) $stats_sql .= " AND r.datum_rezervacije <= '$filter_datum_do'";
```

`$filter_lokacija` je već bila castan u `int`, ali `$filter_datum_od` i `$filter_datum_do` dolazili su direktno iz URL-a, bez sanitacije. Napadač s admin pristupom (ili kroz CSRF, vidi sljedeću točku) je tako mogao kroz URL parametar `?datum_od=' OR 1=1 UNION SELECT...` izvršiti proizvoljan SQL i pročitati npr. cijelu tablicu `korisnici` (uključujući bcrypt hash admin lozinke).

### Kako je popravljeno

Prepisan je u prepared statement s parametrima:

```php
$stats_params = [];
$stats_types = "";
if (!empty($filter_lokacija)) {
    $stats_sql .= " AND r.id_lokacije = ?";
    $stats_params[] = $filter_lokacija;
    $stats_types .= "i";
}
// ... isto za datume

if (!empty($stats_params)) {
    $stmt_stats = $mysqli->prepare($stats_sql);
    $stmt_stats->bind_param($stats_types, ...$stats_params);
    $stmt_stats->execute();
    $stats = $stmt_stats->get_result()->fetch_assoc();
}
```

### Pouka

Pravilo: **svaki** put kad u SQL ide vrijednost koja dolazi izvana — i kad si „provjerio" da je broj, i kad si „samo" datum — koristi prepared statement. Cast u `int` je sigurna prečica, ali nije dosljedna; sve ostalo (datumi, stringovi, enumi) mora biti parametar. Kad imaš dva filtra na istoj stranici od kojih je jedan parametriran a drugi nije, to je crveni alarm — netko će to prije ili kasnije iskoristiti.

---

## 4. Bez CSRF zaštite na admin akcijama

**Težina:** kritično (sigurnost)

### Što se događalo

Sve admin akcije — potvrda, otkazivanje, brisanje rezervacije — bile su `GET` zahtjevi:

```html
<a href="?promijeni=1&id=15&status=otkazano">Otkaži</a>
<a href="?obrisi=15">Obriši</a>
```

Bez CSRF tokena. Što to znači u praksi: ako bi napadač poslao adminu mail s `<img src="https://tvoj-server/admin/admin_rezervacije.php?obrisi=15">`, i admin bi imao otvorenu sesiju u istom browseru, slika bi se učitala i rezervacija obrisana **bez ikakve admin akcije**. Isto vrijedi za bilo koji drugi sajt koji bi admin posjetio dok je logiran.

### Kako je popravljeno

1. Pri prvom dolasku admina na stranicu generira se CSRF token i pohranjuje u sesiju:
   ```php
   if (empty($_SESSION['admin_csrf'])) {
       $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
   }
   ```

2. Svaka akcijska obrada prvo zove `provjeriCSRF()` koja uspoređuje poslani token sa sesijskim koristeći `hash_equals()` (otporno na timing napade).

3. Sva tri akcijska gumba (potvrda, otkazivanje, brisanje) pretvorena su iz `<a>` linkova u `<form method="POST">` s hidden inputom za token:
   ```html
   <form method="POST" onsubmit="...">
       <input type="hidden" name="csrf_token" value="<?php echo $admin_csrf; ?>">
       <input type="hidden" name="obrisi" value="15">
       <button type="submit" class="btn-delete">🗑️ Obriši</button>
   </form>
   ```

4. Status koji dolazi iz POST-a je dodatno bijela-listan (samo `potvrđeno`, `otkazano`, `na čekanju`) prije korištenja — ako klijent pošalje bilo što drugo, zahtjev se odbija.

### Pouka

Pravilo: **radnje za promjenu stanja nikad ne idu kroz GET**. GET je za čitanje (ponavljanje istog zahtjeva ne mijenja stanje). Sve što mijenja podatke ide kroz POST + CSRF token. Browseri, search-engine boti, mail klijenti i preview-alati svi „klikaju" GET-ove automatski; ne smiju ti pri tome obrisati pola baze.

---

## 5. `pdf_generator.php` curi privatne podatke svih klijenata

**Težina:** kritično (GDPR / privatnost)

### Što se događalo

```php
$id_rezervacije = (int)$_GET['id'];
$rez = $mysqli->query("SELECT r.*, l.naziv ... WHERE r.id_rezervacije = $id_rezervacije");
```

Bez sesijske provjere, bez vlasništva. Bilo tko može pisati `pdf_generator.php?id=1`, `?id=2`, `?id=3` i u jednoj minuti skinuti puno ime, mobitel, email i lokaciju **svakog** klijenta. To je klasično IDOR ranjivost (Insecure Direct Object Reference) i pod GDPR-om — kazna.

### Kako je popravljeno

Na vrhu fajla dodana je sesijska provjera s dvije dozvoljene putanje:

```php
session_start();
// ...
$je_admin = !empty($_SESSION['admin_id']);
$vlastita = !empty($_SESSION['moje_rezervacije'])
            && in_array($id_rezervacije, $_SESSION['moje_rezervacije']);

if (!$je_admin && !$vlastita) {
    http_response_code(403);
    die("Nemate pravo pristupa ovoj rezervaciji.");
}
```

Logika: PDF smije vidjeti ili admin (logiran) ili korisnik koji je tu rezervaciju upravo napravio (njen ID je u sesiji `moje_rezervacije`, koji `spremi.php` puni nakon uspješnog `INSERT`-a).

Usput su i `SELECT`-ovi prebačeni na prepared statemente jer i dalje ima smisla — defense in depth.

### Pouka

Pravilo: **autentifikacija ≠ autorizacija**. „Je li korisnik logiran" je autentifikacija. „Smije li ovaj logirani korisnik vidjeti ovaj konkretan podatak" je autorizacija. Svaki endpoint koji čita ili mijenja podatak vezan za korisnika mora odgovoriti na oba pitanja. Ako odgovaraš samo na prvo, dobivaš IDOR. Ako ne odgovaraš ni na jedno, što je ovdje bio slučaj, dobivaš javnu CSV bazu kontakta.

---

## 6. Lozinke i tajne u repozitoriju

**Težina:** kritično (sigurnost)

### Što se događalo

Tri ozbiljna kvara:

1. Gmail app-password `hodslnmvpearyjbw` pisao je direktno u `spremi.php` i `admin/admin_rezervacije.php`, hardkodiran u source.
2. U folderu `lozinke/New Tekstni dokument.txt` ležao je tekstualni fajl s istom Gmail lozinkom i admin pristupom — committan u git.
3. `install.php` je bio dostupan na produkcijskom putu, i pokretanjem bi obrisao bazu (`DROP DATABASE IF EXISTS`) i resetirao na default `admin / admin123`. Login forme su čak imale te kredencijale kao defaultne `value` u inputima.

Posljedice: tko god je ikad imao pristup git history-ju (a to je svatko tko klonira repo) ima Gmail lozinku zauvijek, čak i ako je sad obrišeš iz koda.

### Kako je popravljeno

1. Kreiran `config.php` (izvan git-a) koji drži sve tajne kao PHP konstante: `DB_*`, `SMTP_*`, `SMTP_FROM_NAME`.

2. Kreiran `config.example.php` (u git-u) koji je template s placeholderima — novi developer kopira u `config.php` i popuni.

3. `spoji.php` čita iz `config.php` i baca jasnu grešku ako fajl ne postoji. Slanje mailova u `spremi.php` i `admin/admin_rezervacije.php` koristi konstante umjesto hardkodiranih vrijednosti.

4. Kreiran `.gitignore` koji isključuje:
   - `config.php`
   - `lozinke/`
   - log fajlove (`email_log.txt`, `email_error.txt`)
   - `email_potvrde/`

5. Folder `lozinke/`, fajl `install.php`, `kreiraj_admin.php`, i pomoćne test skripte `test_direct.php` i `test_rezervacija.php` su obrisani.

### Što još moraš napraviti ručno

- **Rotirati Gmail app password.** Stari je u git history-ju zauvijek — i ako fajl izbacišiz repa, on i dalje postoji u prethodnim commitima. Idi u Google račun → Security → App passwords → ukloni stari, generiraj novi, upiši ga u `config.php` na serveru.
- **Promijeniti admin lozinku** sa `admin123` na nešto pristojno (16+ znakova, miks). `password_hash()` je već u kodu, samo treba reset pokrenuti ručno kroz phpMyAdmin: `UPDATE korisnici SET lozinka_hash = '...' WHERE email = 'admin@ribnjacstvo.hr'`.
- **Obrisati `default value` u admin login formi** (`admin/admin__login.php` linije 325, 333) — i dalje predlaže `admin@ribnjacstvo.hr / admin123` kao default. Daj prazne inpute.

### Pouka

Pravilo: **tajne ne idu u git**. Nikad, ni jednom, ni kao komentar, ni kao testna vrijednost. Sve ide u `.gitignore`-an config-fajl ili u environment varijable, a u repu ide `.example` template. Pokušaj zamisliti: ako bih sutra otpustila projekt na GitHub i pošla na godišnji, što bi bilo? Sad zamisli da si umjesto Gmail lozinke imala produkcijski Stripe ključ.

---

## 7. Kapacitet pozicije se nije validirao na serveru

**Težina:** ozbiljno

### Što se događalo

HTML input `name="broj_osoba"` ima `max=<kapacitet>` koji JS dodaje pri promjeni lokacije, ali u `spremi.php` jedino je stajalo:

```php
$broj_osoba = (int)$_POST['broj_osoba'];
```

Korisnik koji preko Postmana ili devtoola pošalje `broj_osoba=999` za poziciju kapaciteta 2 — rezervacija prolazi, baza prihvaća, admin vidi nemoguć broj osoba.

### Kako je popravljeno

U `spremi.php` lokacija se dohvaća zajedno s kapacitetom, i odmah validira:

```php
$stmt = $mysqli->prepare("SELECT naziv, kapacitet FROM lokacije
                          WHERE id_lokacije = ? AND aktivno = 1");
$stmt->bind_param("i", $id_lokacije);
// ...
$kapacitet = (int)$lok_row['kapacitet'];

if ($broj_osoba < 1 || $broj_osoba > $kapacitet) {
    header("Location: index.php?status=greska");
    exit();
}
```

### Važno

Pravilo: **client-side validacija je za UX, server-side je za sigurnost**. HTML `max`, `required`, JS `alert` — sve to postoji da korisniku odmah pokaže grešku. Ali što god se može poslati kroz formu, može se poslati i kroz `curl`. Server mora ponovno provjeriti sve, baš sve.

---

## 8. Blokirani datumi se nisu provjeravali u `spremi.php`

**Težina:** ozbiljno

### Što se događalo

U adminu si imao mehanizam za zatvaranje datuma (Božić, Nova godina, praznik rada) kroz tablicu `blokirani_datumi`. Frontend `index.php` te datume crta crveno u datepickeru. Ali `spremi.php` nije imao **niti jednu liniju** koja bi to provjerio. Korisnik koji ručno upiše blokirani datum (ili koji posve preskoči JS) može rezervirati u zatvoreni dan.

### Kako je popravljeno

U `spremi.php` dodana je provjera prije nego što se uopće dohvaća cijena:

```php
$stmt = $mysqli->prepare("SELECT id_blokade FROM blokirani_datumi
                          WHERE (id_lokacije = ? OR id_lokacije IS NULL)
                          AND ? BETWEEN datum_od AND datum_do");
$stmt->bind_param("is", $id_lokacije, $datum);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    header("Location: index.php?status=blokirano");
    exit();
}
```

Logika: pretražuje se i specifična blokada (`id_lokacije = ?`) i globalna (`id_lokacije IS NULL` — vrijedi za sve lokacije). Ako bilo koja pokriva traženi datum — odbij.

Uz to, dodana je i validacija formata datuma (`DateTime::createFromFormat('Y-m-d', $datum)`) i provjera da datum nije u prošlosti.

---

## 9. XSS u admin pregledu i index porukama

**Težina:** ozbiljno

### Što se događalo

Ime, prezime, mobitel i email korisnika prikazivali su se u admin tablici i u dashboardu **bez** `htmlspecialchars()`:

```php
<td><?php echo $r['broj_mobitela']; ?></td>
<td><?php echo $r['email'] ?: '-'; ?></td>
```

Ime/prezime/email dolaze iz javne forme — napadač može upisati `<script>alert(document.cookie)</script>` kao prezime i kad admin sljedeći put otvori pregled, skripta se izvrši u admin sesiji. Krađa sesijskog kolačića, brisanje rezervacija, što god napadač želi.

Slično u `index.php`:
```php
$poruka = '... ID: #' . $_GET['id'] . '</div>';
```

GET parametar `id` ispisivao se direktno u HTML.

### Kako je popravljeno

- Svi outputi iz baze u admin tablici i dashboardu prošli su kroz `htmlspecialchars()`.
- U `index.php` parametar `$_GET['id']` se prije ispisa cast-a u `int` (jer i tako je samo broj rezervacije).

### Pouka

Pravilo: **sve što ide u HTML mora proći kroz `htmlspecialchars()`**, osim ako si 100% sigurna da je hardkodirana konstanta. Čak i podaci iz baze — jer u bazu su došli iz forme. Pravilo „iz baze je sigurno" ne postoji.

---

## 10. Mrtvi i duplicirani fajlovi

**Težina:** ozbiljno (kvari preglednost, povećava napadnu površinu)

### Što je obrisano i zašto

| Fajl | Zašto |
|---|---|
| `admin_rezervacije.php` (root) | Stara verzija koja je linkala na nepostojeći `rezervacije.php`. Pokazivala je krivu cijenu (`broj_osoba × cijena_po_osobi`, bez ičega drugog) i nije imala `htmlspecialchars`. Zbunjuje. |
| `admin_dashboard.php` (root) | Duplikat radnog `admin/admin_dashboard.php`-a, ali linka na nepostojeće `rezervacije.php`, `lokacije.php`, `mamci.php`, `blokirani.php`. |
| `admin/admin.php` | Treća verzija login forme. Postoje već `admin/admin__login.php` i (sada obrisani) `admin_index.php`. |
| `admin_index.php` | Druga verzija login forme. |
| `includes_spremi-rezervaciju.php` | Zove funkciju `getCijenaLokacije()` koja ne postoji nigdje — fajl je nefunkcionalan. |
| `kreiraj_admin.php` | Pomoćna skripta za jednokratnu inicijalizaciju — ne smije ostati na produkciji jer briše i pravi admina. |
| `install.php` | `DROP DATABASE IF EXISTS` — apsolutno ne smije biti dostupan. Tko god ode na URL pokreće reset cijele baze. |
| `lozinke/` | Plaintext lozinke u repu. |
| `test_direct.php`, `test_rezervacija.php` | Test skripte koje koriste **raw string interpolaciju** u SQL-u; očito ostaci debuga. |

---

## Što sve nije ispravljeno (preporuke za sljedeći korak)

Nisam dirala stvari koje rade, ali su mogle biti bolje. Razmisli o njima kao o domaćoj zadaći:

1. **Cijena ulaznice se sprema u trenutku rezervacije (`cijena_po_osobi` u retku rezervacije)** — što je ispravno jer kasnije promjene u `tipovi_ulaznica` ne smiju retroaktivno mijenjati ono što je korisnik platio. Ali za noćni ribolov i usluge to si već riješila. Razmisli treba li i `nocni_cijena_snapshot` stupac (da admin uvijek vidi koliko je *tada* koštalo, čak i ako u međuvremenu izmijeniš cjenik).

2. **Tip ulaznice ne provjerava se protiv lokacije.** Korisnik može poslati R23 ulaznicu (13.90 €) za C&R Otok lokaciju (koji traži 29.90 €). HTML filter to skriva, ali server ne. Dodaj provjeru: `tipovi_ulaznica.naziv LIKE '%Otok%'` mora se podudarati s `lokacije.tip = 'C&R Otok'`.

3. **Funkcije u `spoji.php` su mrtve.** `getCijenaUlaznice()` i sl. čitaju nepostojeću kolonu `cijena_eur` (u shemi se zove `cijena`). Nitko ih ne zove. Obriši ih ili popravi.

4. **`error_reporting(E_ALL)` + `display_errors=1`** ostalo je u `admin/admin_rezervacije.php`. Na razvojnoj okolini super, na produkciji svaka greška curi putanje, SQL upite, ime baze. Premjesti u `config.php` kao `define('DEBUG', true)` i grupiraj.

5. **CSRF token nije rotiran nakon `logout`-a.** Sitno, ali za potpunost bi pri odjavi sesiju trebalo regenerirati (`session_regenerate_id(true)`), a ne samo destroy.

6. **`zauzeti_datumi.php`** prima samo POST, dobro je, ali nema CSRF — to je u redu jer otkriva samo javne datume, nije osjetljiv. Spominjem da znaš zašto nije popravljen.

7. **Logiranje akcija.** Tablica `povijest_statusa` postoji i radi, dobro. Ali nigdje se ne loga tko je obrisao rezervaciju (delete je jedini permanentni gubitak podataka). Razmisli o `audit_log` tablici.

---

## Sažetak

| # | Problem | Težina | Status |
|---|---|---|---|
| 1 | Cijena noćnog ribolova se gubi između frontenda i admina | Kritično | Ispravljeno |
| 2 | UNIQUE constraint blokira ponovnu rezervaciju otkazanog termina | Kritično | Ispravljeno |
| 3 | SQL injection u admin filterima (datum_od/do) | Kritično | Ispravljeno |
| 4 | Bez CSRF tokena na admin akcijama | Kritično | Ispravljeno |
| 5 | `pdf_generator.php` dostupan svima — IDOR | Kritično | Ispravljeno |
| 6 | Lozinke u kodu i repu | Kritično | Ispravljeno + rotiraj Gmail ručno |
| 7 | Kapacitet se ne validira na serveru | Ozbiljno | Ispravljeno |
| 8 | Blokirani datumi se ne provjeravaju u `spremi.php` | Ozbiljno | Ispravljeno |
| 9 | XSS u admin pregledu | Ozbiljno | Ispravljeno |
| 10 | Mrtvi i duplicirani fajlovi | Ozbiljno | Ispravljeno |

**Promijenjeni fajlovi:**
- `spremi.php`
- `spoji.php`
- `index.php`
- `pdf_generator.php`
- `admin/admin_rezervacije.php`
- `admin/admin_dashboard.php`

**Novi fajlovi:**
- `config.php` (lokalni, ne ide u git)
- `config.example.php`
- `.gitignore`

**Obrisani fajlovi:**
- `admin_rezervacije.php` (root), `admin_dashboard.php` (root)
- `admin/admin.php`, `admin_index.php`
- `includes_spremi-rezervaciju.php`
- `install.php`, `kreiraj_admin.php`
- `test_direct.php`, `test_rezervacija.php`
- `lozinke/`

**Shema baze:**
- `rezervacije`: maknut `UNIQUE unique_rez`, dodani obični indeksi `idx_lokacije` i `idx_lokacija_datum`.
