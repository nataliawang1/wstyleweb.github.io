<nav class="sidebar">
    <div class="sidebar-header">
        <h2>W-Style</h2>
        <p>Admin Panel</p>
    </div>
    
    <ul class="sidebar-menu">
        <li><a href="/W-Style/admin/dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a></li>
        <li><a href="/W-Style/admin/portafolio/index.php" class="<?php echo strpos($_SERVER['PHP_SELF'], 'portafolio') !== false ? 'active' : ''; ?>">Portafolio</a></li>
        <li><a href="/W-Style/admin/servicios/index.php" class="<?php echo strpos($_SERVER['PHP_SELF'], 'servicios') !== false ? 'active' : ''; ?>">Servicios</a></li>
        <li><a href="/W-Style/admin/clientes/index.php" class="<?php echo strpos($_SERVER['PHP_SELF'], 'clientes') !== false ? 'active' : ''; ?>">Clientes</a></li>
        <li><a href="/W-Style/admin/wclub/index.php" class="<?php echo strpos($_SERVER['PHP_SELF'], 'wclub') !== false ? 'active' : ''; ?>">W Club</a></li>
        <li><a href="/W-Style/admin/contactos/index.php" class="<?php echo strpos($_SERVER['PHP_SELF'], 'contactos') !== false ? 'active' : ''; ?>">Contactos</a></li>
    </ul>
    
    <div class="sidebar-footer">
        <a href="/W-Style/index.html" target="_blank">Ver Sitio Web</a>
    </div>
</nav>
