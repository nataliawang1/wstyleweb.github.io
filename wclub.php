<?php
require_once 'config/database.php';
$activePage = 'wclub';

$mensaje = '';
$tipoMensaje = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');

    if (empty($nombre) || empty($email)) {
        $mensaje = 'El nombre y el correo son obligatorios.';
        $tipoMensaje = 'error';
    } else {
        $stmt = $conn->prepare("INSERT INTO wclub_miembros (nombre, email, telefono) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nombre, $email, $telefono);
        if ($stmt->execute()) {
            $mensaje = '¡Bienvenido al W Club! Tu registro fue exitoso.';
            $tipoMensaje = 'success';
        } else {
            $mensaje = 'Este correo ya está registrado o hubo un error. Intenta de nuevo.';
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
    <title>W-Style | W Club</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <main>
        <section class="page-header">
            <h1>W Club</h1>
            <p>Únete a nuestra comunidad exclusiva</p>
        </section>

        <section class="hero-image">
            <img src="images/wclub-hero.jpg" alt="W Club">
        </section>

        <section class="mission-vision">
            <div class="mission">
                <h3>Misión</h3>
                <p>Crear una comunidad de personas apasionadas por la moda y el estilo, donde cada miembro pueda expresar su individualidad a través de nuestras colecciones exclusivas y experiencias personalizadas.</p>
            </div>
            <div class="vision">
                <h3>Visión</h3>
                <p>Ser el referente mundial en moda exclusiva, conectando a personas con un estilo de vida sofisticado y único, trascendiendo las tendencias para crear un legado atemporal.</p>
            </div>
        </section>

        <section class="club-benefits">
            <h2>Beneficios del Club</h2>
            <div class="benefits-grid">
                <div class="benefit-card">
                    <h3>Acceso Exclusivo</h3>
                    <p>Colecciones limitadas solo para miembros</p>
                </div>
                <div class="benefit-card">
                    <h3>Descuentos Especiales</h3>
                    <p>20% de descuento en todas las compras</p>
                </div>
                <div class="benefit-card">
                    <h3>Eventos VIP</h3>
                    <p>Invitaciones a desfiles y eventos exclusivos</p>
                </div>
                <div class="benefit-card">
                    <h3>Asesoría Personal</h3>
                    <p>Consultas de estilo personalizadas</p>
                </div>
            </div>
        </section>

        <section class="club-join">
            <h2>Únete Ahora</h2>
            <?php if ($mensaje): ?>
                <p class="form-message <?php echo $tipoMensaje; ?>"><?php echo htmlspecialchars($mensaje); ?></p>
            <?php endif; ?>
            <form class="join-form" method="POST" action="wclub.php">
                <input type="text" name="nombre" placeholder="Nombre completo" required>
                <input type="email" name="email" placeholder="Correo electrónico" required>
                <input type="tel" name="telefono" placeholder="Teléfono" required>
                <button type="submit" class="btn">Registrarse</button>
            </form>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
