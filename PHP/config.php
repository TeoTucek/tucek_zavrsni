<?php
// config.php
session_start();

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'ribolovni_koncanica';
$port = 3306;
$charset = 'utf8mb4';

// PDO konekcija
try {
    $dsn = "mysql:host=$host;dbname=$database;port=$port;charset=$charset";
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Greška pri spajanju na bazu: " . $e->getMessage());
}

// Provjera je li funkcija već definirana (zbog db_connection.php)
if (!function_exists('clean_input')) {
    // Funkcija za sigurnost
    function clean_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
}

// Provjera prijave djelatnika
function check_djelatnik_login() {
    if(!isset($_SESSION['djelatnik_id'])) {
        header("Location: login.php");
        exit();
    }
}

// Provjera admin privilegija
function is_admin() {
    return isset($_SESSION['djelatnik_uloga']) && $_SESSION['djelatnik_uloga'] == 'admin';
}

// Dobivanje korisničkih podataka
function get_djelatnik_data($pdo, $djelatnik_id) {
    $stmt = $pdo->prepare("SELECT * FROM djelatnici WHERE id = ?");
    $stmt->execute([$djelatnik_id]);
    return $stmt->fetch();
}
?>