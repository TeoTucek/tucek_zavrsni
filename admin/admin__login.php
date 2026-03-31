<?php
session_start();
require_once '../spoji.php';

// Ako je već prijavljen, idi na dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $lozinka = $_POST['lozinka'];
    
    // Dohvati admina iz baze
    $stmt = $mysqli->prepare("SELECT id_korisnika, ime, prezime, lozinka_hash, role 
                              FROM korisnici WHERE email = ? AND role = 'admin'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $rezultat = $stmt->get_result();
    
    if ($row = $rezultat->fetch_assoc()) {
        // Provjeri lozinku
        if (password_verify($lozinka, $row['lozinka_hash'])) {
            $_SESSION['admin_id'] = $row['id_korisnika'];
            $_SESSION['admin_ime'] = $row['ime'] . ' ' . $row['prezime'];
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Pogrešna lozinka!";
        }
    } else {
        $error = "Korisnik ne postoji ili nema admin prava!";
    }
}
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin login - Ribnjačarstvo Končanica</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #0a3b4b 0%, #1a5f6e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow: hidden;
        }
        
        /* Dekorativni elementi */
        body::before {
            content: '🎣';
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 60px;
            opacity: 0.1;
            transform: rotate(-15deg);
        }
        
        body::after {
            content: '🐟';
            position: absolute;
            bottom: 20px;
            right: 20px;
            font-size: 80px;
            opacity: 0.1;
            transform: rotate(10deg);
        }
        
        .login-box {
            max-width: 450px;
            width: 100%;
            margin: 20px;
            animation: fadeInUp 0.6s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(230, 126, 34, 0.2);
            position: relative;
            backdrop-filter: blur(10px);
        }
        
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #e67e22, #f39c12, #e67e22);
            border-radius: 20px 20px 0 0;
        }
        
        .logo-icon {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .logo-icon span {
            font-size: 48px;
            background: linear-gradient(135deg, #e67e22, #f39c12);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        h2 {
            text-align: center;
            color: #2c3e50;
            font-size: 28px;
            margin-bottom: 8px;
            font-weight: 700;
        }
        
        .subtitle {
            text-align: center;
            color: #7f8c8d;
            font-size: 14px;
            margin-bottom: 30px;
            border-bottom: 1px solid #ecf0f1;
            padding-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
            font-size: 14px;
        }
        
        .form-group label i {
            margin-right: 8px;
            color: #e67e22;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #e67e22;
            background: white;
            box-shadow: 0 0 0 3px rgba(230, 126, 34, 0.1);
        }
        
        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #e67e22, #f39c12);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(230, 126, 34, 0.3);
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(230, 126, 34, 0.4);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .alert {
            padding: 12px 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 14px;
            border-left: 4px solid;
            animation: shake 0.5s ease;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        .alert.error {
            background: #fee2e2;
            color: #991b1b;
            border-left-color: #dc2626;
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: #e67e22;
            text-decoration: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
        }
        
        .back-link a:hover {
            color: #d35400;
            transform: translateX(-3px);
        }
        
        hr {
            margin: 25px 0 20px;
            border: none;
            border-top: 1px solid #ecf0f1;
        }
        
        .demo-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 12px;
            text-align: center;
        }
        
        .demo-info p {
            color: #7f8c8d;
            font-size: 12px;
            margin: 0;
        }
        
        .demo-info strong {
            color: #e67e22;
        }
        
        .demo-info .demo-credentials {
            font-family: monospace;
            background: white;
            padding: 8px;
            border-radius: 8px;
            margin-top: 8px;
            font-size: 13px;
            border: 1px solid #e0e0e0;
        }
        
        /* Responzivnost */
        @media (max-width: 480px) {
            .card {
                padding: 25px;
            }
            
            h2 {
                font-size: 24px;
            }
            
            .logo-icon span {
                font-size: 36px;
            }
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="card">
            <div class="logo-icon">
                <span>🎣</span>
            </div>
            
            <h2>Admin panel</h2>
            <div class="subtitle">
                Ribnjačarstvo Končanica
            </div>
            
            <?php if ($error): ?>
                <div class="alert error">
                    ⚠️ <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>
                        <i>📧</i> Email adresa
                    </label>
                    <input type="email" name="email" class="form-control" required 
                           placeholder="admin@ribnjacstvo.hr" value="admin@ribnjacstvo.hr">
                </div>
                
                <div class="form-group">
                    <label>
                        <i>🔒</i> Lozinka
                    </label>
                    <input type="password" name="lozinka" class="form-control" required 
                           placeholder="••••••••" value="admin123">
                </div>
                
                <button type="submit" class="btn">
                    🔓 Prijavi se
                </button>
            </form>
            
            <div class="back-link">
                <a href="../index.php">
                    ← Povratak na početnu stranicu
                </a>
            </div>
            
            <hr>
            
            <div class="demo-info">
                <p>📋 <strong>Demo podaci za prijavu</strong></p>
                <div class="demo-credentials">
                    📧 admin@ribnjacstvo.hr<br>
                    🔑 admin123
                </div>
                <p style="margin-top: 8px; font-size: 11px;">
                    Ovo su testni podaci, promijenite lozinku nakon prve prijave.
                </p>
            </div>
        </div>
    </div>
</body>
</html>