<?php
// footer.php
?>
<footer class="bg-dark text-white py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5>Ribnjak Končanica</h5>
                <p class="mb-2">R23 & Ribnjak Crnaja C&R</p>
                <p class="mb-1"><i class="fas fa-map-marker-alt me-2"></i> Končanica, Hrvatska</p>
                <p class="mb-1"><i class="fas fa-phone me-2"></i> 091 139 9970</p>
            </div>
            <div class="col-md-4">
                <h5>Radno vrijeme</h5>
                <p class="mb-1">Radnim danima: 7:00 - 19:00</p>
                <p class="mb-1">Vikendom: 6:00 - 20:00</p>
                <p class="mb-1">Otok: 24/7 uz rezervaciju</p>
            </div>
            <div class="col-md-4">
                <h5>Korisni linkovi</h5>
                <ul class="list-unstyled">
                    <li><a href="index.php" class="text-white text-decoration-none">Početna</a></li>
                    <li><a href="cjenik.php" class="text-white text-decoration-none">Cjenik</a></li>
                    <li><a href="pravila.php" class="text-white text-decoration-none">Pravila ribolova</a></li>
                    <?php if(isset($_SESSION['djelatnik_id'])): ?>
                        <li><a href="logout.php" class="text-white text-decoration-none">Odjava djelatnika</a></li>
                    <?php else: ?>
                        <li><a href="login.php" class="text-white text-decoration-none">Prijava djelatnika</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <hr class="bg-white my-3">
        <div class="text-center">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Ribnjak Končanica. Sva prava pridržana.</p>
        </div>
    </div>
</footer>