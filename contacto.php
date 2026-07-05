<?php
require_once 'config/database.php';
$activePage = 'contacto';

$mensajeEstado = '';
$tipoMensaje = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');

    if (empty($nombre) || empty($email) || empty($mensaje)) {
        $mensajeEstado = 'Por favor completa todos los campos obligatorios.';
        $tipoMensaje = 'error';
    } else {
        $stmt = $conn->prepare("INSERT INTO contactos (nombre, email, telefono, mensaje) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nombre, $email, $telefono, $mensaje);
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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>W-Style | Contacto</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <main>
        <section class="page-header">
            <h1>Contacto</h1>
            <p>¿Listo para trabajar juntos?</p>
        </section>

        <section class="contact-container">
            <div class="contact-info">
                <h2>Información</h2>
                <div class="info-item">
                    <strong>Dirección:</strong>
                    <p>Av. Principal #123, Ciudad de México</p>
                </div>
                <div class="info-item">
                    <strong>Teléfono:</strong>
                    <p>+52 55 1234 5678</p>
                </div>
                <div class="info-item">
                    <strong>Email:</strong>
                    <p>info@w-style.com</p>
                </div>
                <div class="info-item">
                    <strong>Horario:</strong>
                    <p>Lunes - Viernes: 9:00 - 18:00</p>
                </div>
            </div>

            <div class="contact-form">
                <h2>Envíanos un mensaje</h2>
                <?php if ($mensajeEstado): ?>
                    <p class="form-message <?php echo $tipoMensaje; ?>"><?php echo htmlspecialchars($mensajeEstado); ?></p>
                <?php endif; ?>
                <form method="POST" action="contacto.php">
                    <input type="text" name="nombre" placeholder="Nombre completo" required>
                    <input type="email" name="email" placeholder="Correo electrónico" required>
                    <input type="tel" name="telefono" placeholder="Teléfono">
                    <textarea name="mensaje" placeholder="Tu mensaje" rows="5" required></textarea>
                    <button type="submit" class="btn">Enviar</button>
                </form>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
