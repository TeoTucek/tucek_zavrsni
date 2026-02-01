<?php
// navbar.php - zajednička navigacija za sve stranice
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-fish"></i> Ribnjak Končanica
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" 
                       href="index.php">Početna</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'cjenik.php' ? 'active' : ''; ?>" 
                       href="cjenik.php">Cjenik</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'pravila.php' ? 'active' : ''; ?>" 
                       href="pravila.php">Pravila</a>
                </li>
                
                <?php if(isset($_SESSION['djelatnik_id'])): ?>
                    <?php if(is_admin()): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'clanovi.php' ? 'active' : ''; ?>" 
                               href="clanovi.php">Kupci</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'djelatnici.php' ? 'active' : ''; ?>" 
                               href="djelatnici.php">Djelatnici</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'karte.php' ? 'active' : ''; ?>" 
                           href="karte.php">Prodaja karata</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'izlovi.php' ? 'active' : ''; ?>" 
                           href="izlovi.php">Izlovi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'statistike.php' ? 'active' : ''; ?>" 
                           href="statistike.php">Statistike</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" 
                           data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <?php echo $_SESSION['djelatnik_ime']; ?>
                        </a>
                        <ul class="dropdown-menu">
                            <?php if(is_admin()): ?>
                                <li><a class="dropdown-item" href="djelatnici.php">Upravljanje djelatnicima</a></li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item text-danger" href="logout.php">Odjava</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : ''; ?>" 
                           href="login.php">Prijava djelatnika</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>