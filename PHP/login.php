<?php
require_once 'config.php';

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jmbg = clean_input($_POST['jmbg']);
    
    $stmt = $pdo->prepare("SELECT * FROM clan WHERE jmbg = ?");
    $stmt->execute([$jmbg]);
    $user = $stmt->fetch();
    
    if($user) {
        $_SESSION['user_id'] = $user['id_Clan'];
        $_SESSION['user_name'] = $user['ime'] . ' ' . $user['prezime'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['is_admin'] = ($user['status'] == 'admin');
        
        $success = "Uspješno prijavljeni!";
        header("Refresh: 2; url=profil.php");
    } else {
        $error = "Pogrešan JMBG!";
    }
}
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prijava - Ribolovni Klub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header i {
            font-size: 3rem;
            color: #2c7873;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <i class="fas fa-sign-in-alt"></i>
                <h2>Prijava</h2>
                <p class="text-muted">Prijavite se svojim JMBG-om</p>
            </div>
            
            <?php if($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="jmbg" class="form-label">JMBG</label>
                    <input type="text" class="form-control" id="jmbg" name="jmbg" required 
                           pattern="[0-9]{13}" title="Unesite 13 znamenki">
                </div>
                
                <button type="submit" class="btn btn-primary w-100">Prijava</button>
            </form>
            
            <hr class="my-4">
            
            <div class="text-center">
                <p class="mb-2">Niste član?</p>
                <a href="clanovi.php?action=new" class="btn btn-outline-primary">Registrirajte se</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
<?php
// login.php
require_once 'config.php';

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $korisnicko_ime = clean_input($_POST['korisnicko_ime']);
    $lozinka = clean_input($_POST['lozinka']);
    
    $stmt = $pdo->prepare("SELECT * FROM djelatnici WHERE korisnicko_ime = ? AND aktivan = 1");
    $stmt->execute([$korisnicko_ime]);
    $djelatnik = $stmt->fetch();
    
    if($djelatnik && password_verify($lozinka, $djelatnik['lozinka'])) {
        $_SESSION['djelatnik_id'] = $djelatnik['id'];
        $_SESSION['djelatnik_ime'] = $djelatnik['ime'] . ' ' . $djelatnik['prezime'];
        $_SESSION['djelatnik_uloga'] = $djelatnik['uloga'];
        
        $success = "Uspješno prijavljeni!";
        header("Refresh: 1; url=index.php");
    } else {
        $error = "Pogrešno korisničko ime ili lozinka!";
    }
}
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prijava djelatnika - Ribnjak Končanica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            background-color: white;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header i {
            font-size: 3rem;
            color: #2c7873;
        }
        body {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <i class="fas fa-sign-in-alt"></i>
                <h2>Prijava djelatnika</h2>
                <p class="text-muted">Ribnjak Končanica</p>
            </div>
            
            <?php if($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="korisnicko_ime" class="form-label">Korisničko ime</label>
                    <input type="text" class="form-control" id="korisnicko_ime" name="korisnicko_ime" required>
                </div>
                
                <div class="mb-3">
                    <label for="lozinka" class="form-label">Lozinka</label>
                    <input type="password" class="form-control" id="lozinka" name="lozinka" required>
                </div>
                
                <button type="submit" class="btn btn-primary w-100">Prijava</button>
            </form>
            
            <hr class="my-4">
            
            <div class="text-center">
                <p class="mb-2">Povratak na početnu stranicu</p>
                <a href="index.php" class="btn btn-outline-secondary">Početna</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>