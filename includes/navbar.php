<?php $activePage = $activePage ?? ''; ?>
<header>
    <nav class="navbar">
        <div class="logo">
            <a href="index.php">
                <img src="images/LOGO WANG RED.jpg" alt="W-Style Logo">
            </a>
        </div>
        <ul class="nav-links left-links">
            <li><a href="index.php" <?php if ($activePage == 'index') echo 'class="active"'; ?>>Wang</a></li>
            <li><a href="portafolio.php" <?php if ($activePage == 'portafolio') echo 'class="active"'; ?>>Portafolio</a></li>
            <li><a href="servicios.php" <?php if ($activePage == 'servicios') echo 'class="active"'; ?>>Servicios</a></li>
        </ul>
        <ul class="nav-links right-links">
            <li><a href="clientes.php" <?php if ($activePage == 'clientes') echo 'class="active"'; ?>>Clientes</a></li>
            <li><a href="wclub.php" <?php if ($activePage == 'wclub') echo 'class="active"'; ?>>W-Club</a></li>
            <li><a href="contacto.php" <?php if ($activePage == 'contacto') echo 'class="active"'; ?>>Contacto</a></li>
        </ul>
    </nav>
</header>
