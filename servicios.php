<?php
require_once 'config/database.php';
$activePage = 'servicios';
$servicios = $conn->query("SELECT * FROM servicios ORDER BY created_at ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>W-Style | Servicios</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <main>
        <section class="page-header">
            <h1>Servicios</h1>
            <p>Todo lo que necesitas para tu estilo</p>
        </section>

        <section class="hero-image">
            <img src="images/servicios-hero.jpg" alt="Servicios">
        </section>

        <section class="services-list">
            <?php while ($servicio = $servicios->fetch_assoc()): ?>
            <div class="service-item">
                <div class="service-icon"><?php echo htmlspecialchars($servicio['icono']); ?></div>
                <div class="service-content">
                    <h3><?php echo htmlspecialchars($servicio['titulo']); ?></h3>
                    <p><?php echo htmlspecialchars($servicio['descripcion']); ?></p>
                </div>
                <a href="contacto.php" class="service-btn">VER MÁS</a>
            </div>
            <?php endwhile; ?>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
