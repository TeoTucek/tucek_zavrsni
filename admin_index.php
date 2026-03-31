<?php
// =====================================================
// ADMIN LOGIN - sa password_hash i password_verify
// =====================================================
session_start();
require_once '../spoji.php';

// Ako je već prijavljen
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $lozinka = $_POST['lozinka'];
    
    // Prepared statement!
    $stmt = $mysqli->prepare("SELECT id_korisnika, ime, prezime, lozinka_hash, role 
                              FROM korisnici WHERE email = ? AND role = 'admin'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $rez = $stmt->get_result();
    
    if ($row = $rez->fetch_assoc()) {
        // password_verify za provjeru hasha!
        if (password_verify($lozinka, $row['lozinka_hash'])) {
            $_SESSION['admin_id'] = $row['id_korisnika'];
            $_SESSION['admin_ime'] = $row['ime'] . ' ' . $row['prezime'];
            header("Location: dashboard.php");
            exit();
        }
    }
    $error = "Pogrešan email ili lozinka!";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin login</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body style="background: linear-gradient(135deg, #0a3b4b, #1a5f6e);">
    <div class="container" style="max-width: 400px; margin-top: 100px;">
        <div class="card">
            <h2 style="text-align: center; color: #e67e22;">🔐 ADMIN LOGIN</h2>
            
            <?php if ($error): ?>
                <div class="alert error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Lozinka:</label>
                    <input type="password" name="lozinka" class="form-control" required>
                </div>
                
                <button type="submit" class="btn" style="width: 100%;">Prijavi se</button>
            </form>
            
            <p style="margin-top: 20px; text-align: center; color: #666;">
                (admin@ribnjacstvo.hr / admin123)
            </p>
        </div>
    </div>
</body>
</html>