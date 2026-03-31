<?php
require_once 'spoji.php';

// Generiraj ispravan hash za lozinku 'admin123'
$hash = password_hash('admin123', PASSWORD_DEFAULT);

// Obriši starog admina
$mysqli->query("DELETE FROM korisnici WHERE email = 'admin@ribnjacstvo.hr'");

// Dodaj novog admina
$stmt = $mysqli->prepare("INSERT INTO korisnici (ime, prezime, email, lozinka_hash, role) VALUES (?, ?, ?, ?, ?)");
$ime = 'Admin';
$prezime = 'Administrator';
$email = 'admin@ribnjacstvo.hr';
$role = 'admin';

$stmt->bind_param("sssss", $ime, $prezime, $email, $hash, $role);

if ($stmt->execute()) {
    echo "<h2 style='color: green;'>✅ ADMIN KREIRAN!</h2>";
    echo "<p><strong>Email:</strong> admin@ribnjacstvo.hr</p>";
    echo "<p><strong>Lozinka:</strong> admin123</p>";
    echo "<p><strong>Hash u bazi:</strong> " . $hash . "</p>";
    echo "<hr>";
    echo "<a href='admin/admin_login.php' style='display: inline-block; background: #e67e22; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Idi na admin login →</a>";
} else {
    echo "<h2 style='color: red;'>❌ Greška: " . $mysqli->error . "</h2>";
}

// Provjeri
$check = $mysqli->query("SELECT id_korisnika, email, role FROM korisnici WHERE email = 'admin@ribnjacstvo.hr'");
if ($check->num_rows > 0) {
    $row = $check->fetch_assoc();
    echo "<p>✅ Admin u bazi: ID=" . $row['id_korisnika'] . ", Email=" . $row['email'] . "</p>";
}
?>