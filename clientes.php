<?php
require_once 'config/database.php';
$activePage = 'clientes';
$clientes = $conn->query("SELECT * FROM clientes ORDER BY created_at ASC");
$testimonios = $conn->query("SELECT * FROM clientes WHERE testimonio IS NOT NULL AND testimonio != '' ORDER BY created_at ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>W-Style | Clientes</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <main>
        <section class="page-header">
            <h1>Nuestros Clientes</h1>
            <p>Marcas que confían en nosotros</p>
        </section>

        <section class="clients-grid">
            <?php while ($cliente = $clientes->fetch_assoc()): ?>
            <div class="client-item">
                <div class="client-logo">
                    <?php if ($cliente['logo']): ?>
                        <img src="images/<?php echo htmlspecialchars($cliente['logo']); ?>" alt="<?php echo htmlspecialchars($cliente['marca']); ?>">
                    <?php else: ?>
                        <span><?php echo htmlspecialchars($cliente['marca']); ?></span>
                    <?php endif; ?>
                </div>
                <p><?php echo htmlspecialchars($cliente['descripcion']); ?></p>
            </div>
            <?php endwhile; ?>
        </section>

        <section class="testimonials">
            <h2>Testimonios</h2>
            <?php while ($testimonio = $testimonios->fetch_assoc()): ?>
            <div class="testimonial-item">
                <p>"<?php echo htmlspecialchars($testimonio['testimonio']); ?>"</p>
                <cite>- <?php echo htmlspecialchars($testimonio['nombre']); ?>, <?php echo htmlspecialchars($testimonio['marca']); ?></cite>
            </div>
            <?php endwhile; ?>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
