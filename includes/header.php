<?php
$currentPage = basename($_SERVER['PHP_SELF']);

function isActive($page) {
    global $currentPage;
    return $currentPage === $page ? 'active' : '';
}
?>
<header>
    <nav class="navbar">
        <div class="logo">
            <a href="index.php">
                <img src="images/LOGO WANG RED.jpg" alt="W-Style Logo">
            </a>
        </div>
        <ul class="nav-links">
            <li><a href="index.php" class="<?php echo isActive('index.php'); ?>">Wang</a></li>
            <li><a href="portafolio.php" class="<?php echo isActive('portafolio.php'); ?>">Portafolio</a></li>
            <li><a href="servicios.php" class="<?php echo isActive('servicios.php'); ?>">Servicios</a></li>
            <li><a href="clientes.php" class="<?php echo isActive('clientes.php'); ?>">Clientes</a></li>
            <li><a href="wclub.php" class="<?php echo isActive('wclub.php'); ?>">W-Club</a></li>
            <li><a href="contacto.php" class="<?php echo isActive('contacto.php'); ?>">Contacto</a></li>
        </ul>
    </nav>
</header>
