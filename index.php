<?php
require_once 'config/database.php';
$activePage = 'index';

$mensajeEstado = '';
$tipoMensaje = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');

    if (empty($nombre) || empty($email) || empty($mensaje)) {
        $mensajeEstado = 'Por favor completa todos los campos.';
        $tipoMensaje = 'error';
    } else {
        $stmt = $conn->prepare("INSERT INTO contactos (nombre, email, mensaje) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nombre, $email, $mensaje);
        if ($stmt->execute()) {
            $mensajeEstado = '¡Mensaje enviado! Te contactaremos pronto.';
            $tipoMensaje = 'success';
        } else {
            $mensajeEstado = 'Hubo un error al enviar tu mensaje. Intenta de nuevo.';
            $tipoMensaje = 'error';
        }
        $stmt->close();
    }
}

$portafolio = $conn->query("SELECT * FROM portafolio ORDER BY created_at DESC LIMIT 6");
$testimonios = $conn->query("SELECT * FROM clientes WHERE testimonio IS NOT NULL AND testimonio != '' ORDER BY created_at ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>W-Style | Wang Style</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <main>
        <section class="hero">
            <div class="hero-content">
                <h1>Wang Style</h1>
                <p>Estilo único. Elegancia sin límites.</p>
                <a href="portafolio.php" class="btn btn-shop">Ver más</a>
            </div>
        </section>

        <section class="wclub-preview">
            <h2>W Club</h2>
            <p>Únete a nuestra comunidad exclusiva de moda y estilo.</p>
            <a href="wclub.php" class="btn btn-shop">Ver más</a>
            <div class="gallery-item">
                <img src="images/wclub.jpg" alt="W Club">
            </div>
        </section>

        <section class="portfolio-preview">
            <h2>Portafolio</h2>
            <div class="portfolio-grid">
                <?php while ($item = $portafolio->fetch_assoc()): ?>
                <div class="portfolio-item">
                    <img src="images/<?php echo htmlspecialchars($item['imagen']); ?>" alt="<?php echo htmlspecialchars($item['titulo']); ?>">
                    <div class="portfolio-overlay">
                        <h3><?php echo htmlspecialchars($item['titulo']); ?></h3>
                        <p><?php echo htmlspecialchars($item['descripcion']); ?></p>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </section>

        <section class="clients-preview">
            <h2>Clientes</h2>
            <div class="testimonials-carousel">
                <button class="carousel-btn prev" onclick="moveCarousel(-1)">&#10094;</button>
                <div class="testimonials-track">
                    <?php while ($testimonio = $testimonios->fetch_assoc()): ?>
                    <div class="testimonial-item">
                        <p>"<?php echo htmlspecialchars($testimonio['testimonio']); ?>"</p>
                        <cite>- <?php echo htmlspecialchars($testimonio['nombre']); ?></cite>
                    </div>
                    <?php endwhile; ?>
                </div>
                <button class="carousel-btn next" onclick="moveCarousel(1)">&#10095;</button>
            </div>
        </section>

        <section class="contact-preview">
            <h2>Contacto</h2>
            <p>¿Listo para trabajar juntos?</p>
            <?php if ($mensajeEstado): ?>
                <p class="form-message <?php echo $tipoMensaje; ?>"><?php echo htmlspecialchars($mensajeEstado); ?></p>
            <?php endif; ?>
            <form class="contact-form-inline" method="POST" action="index.php">
                <input type="text" name="nombre" placeholder="Nombre" required>
                <input type="email" name="email" placeholder="Email" required>
                <textarea name="mensaje" placeholder="Mensaje" rows="3" required></textarea>
                <button type="submit" class="btn">SEND</button>
            </form>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script>
        let currentIndex = 0;
        const itemsPerView = 3;
        const track = document.querySelector('.testimonials-track');
        const totalItems = document.querySelectorAll('.testimonial-item').length;

        function moveCarousel(direction) {
            if (totalItems <= itemsPerView) return;
            const maxIndex = totalItems - itemsPerView;
            currentIndex += direction;

            if (currentIndex < 0) {
                currentIndex = maxIndex;
            } else if (currentIndex > maxIndex) {
                currentIndex = 0;
            }

            const itemWidth = track.children[0].offsetWidth + 30; // 30 is gap
            track.style.transform = `translateX(-${currentIndex * itemWidth}px)`;
        }

        // Auto-play carousel
        setInterval(() => {
            moveCarousel(1);
        }, 5000);
    </script>
</body>
</html>
