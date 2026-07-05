<?php
require_once 'config/database.php';
$activePage = 'portafolio';
$portafolio = $conn->query("SELECT * FROM portafolio ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>W-Style | Portafolio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <main>
        <section class="page-header">
            <h1>Portafolio W</h1>
            <p>Nuestras mejores creaciones</p>
        </section>

        <section class="portfolio-grid">
            <?php while ($item = $portafolio->fetch_assoc()): ?>
            <div class="portfolio-item">
                <img src="images/<?php echo htmlspecialchars($item['imagen']); ?>" alt="<?php echo htmlspecialchars($item['titulo']); ?>">
                <div class="portfolio-overlay">
                    <h3><?php echo htmlspecialchars($item['titulo']); ?></h3>
                    <p><?php echo htmlspecialchars($item['descripcion']); ?></p>
                </div>
            </div>
            <?php endwhile; ?>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
