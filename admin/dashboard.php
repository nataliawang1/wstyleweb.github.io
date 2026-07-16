<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

requireLogin();

$stats = [];

$result = $conn->query("SELECT COUNT(*) as count FROM portafolio");
$stats['portafolio'] = $result->fetch_assoc()['count'];

$result = $conn->query("SELECT COUNT(*) as count FROM servicios");
$stats['servicios'] = $result->fetch_assoc()['count'];

$result = $conn->query("SELECT COUNT(*) as count FROM clientes");
$stats['clientes'] = $result->fetch_assoc()['count'];

$result = $conn->query("SELECT COUNT(*) as count FROM wclub_miembros");
$stats['wclub'] = $result->fetch_assoc()['count'];

$result = $conn->query("SELECT COUNT(*) as count FROM contactos WHERE leido = FALSE");
$stats['mensajes'] = $result->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - W-Style Admin</title>
    <link rel="stylesheet" href="../css/admin-style.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        
        <div class="main-content">
            <header class="admin-header">
                <h1>Panel de Administración</h1>
                <div class="user-info">
                    <span>Bienvenido, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                    <a href="/W-Style/admin/logout.php" class="btn-logout">Cerrar Sesión</a>
                </div>
            </header>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"></div>
                    <div class="stat-info">
                        <h3>Portafolio</h3>
                        <p class="stat-number"><?php echo $stats['portafolio']; ?></p>
                    </div>
                    <a href="portafolio/index.php" class="stat-link">Ver todo</a>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon"></div>
                    <div class="stat-info">
                        <h3>Servicios</h3>
                        <p class="stat-number"><?php echo $stats['servicios']; ?></p>
                    </div>
                    <a href="servicios/index.php" class="stat-link">Ver todo</a>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon"></div>
                    <div class="stat-info">
                        <h3>Clientes</h3>
                        <p class="stat-number"><?php echo $stats['clientes']; ?></p>
                    </div>
                    <a href="clientes/index.php" class="stat-link">Ver todo</a>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon"></div>
                    <div class="stat-info">
                        <h3>W Club</h3>
                        <p class="stat-number"><?php echo $stats['wclub']; ?></p>
                    </div>
                    <a href="wclub/index.php" class="stat-link">Ver todo</a>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon"></div>
                    <div class="stat-info">
                        <h3>Mensajes</h3>
                        <p class="stat-number"><?php echo $stats['mensajes']; ?></p>
                    </div>
                    <a href="contactos/index.php" class="stat-link">Ver todo</a>
                </div>
            </div>
            
            <div class="recent-activity">
                <h2>Acciones Rápidas</h2>
                <div class="quick-actions">
                    <a href="portafolio/crear.php" class="btn-action">+ Agregar al Portafolio</a>
                    <a href="servicios/crear.php" class="btn-action">+ Agregar Servicio</a>
                    <a href="clientes/crear.php" class="btn-action">+ Agregar Cliente</a>
                    <a href="contactos/index.php" class="btn-action">Ver Mensajes</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
