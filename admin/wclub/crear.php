<?php
require_once '../../config/database.php';
require_once '../../includes/auth.php';

requireLogin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    
    if (empty($nombre) || empty($email)) {
        $error = 'El nombre y email son obligatorios.';
    } else {
        $stmt = $conn->prepare("INSERT INTO wclub_miembros (nombre, email, telefono) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nombre, $email, $telefono);
        
        if ($stmt->execute()) {
            $success = 'Miembro agregado exitosamente.';
            $stmt->close();
            header("Location: index.php");
            exit();
        } else {
            $error = 'Error al agregar el miembro. El email podría ya estar registrado.';
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
    <title>Crear Miembro W Club - W-Style Admin</title>
    <link rel="stylesheet" href="../../css/admin-style.css">
</head>
<body>
    <div class="admin-container">
        <?php include '../sidebar.php'; ?>
        
        <div class="main-content">
            <header class="admin-header">
                <h1>Agregar Miembro W Club</h1>
                <a href="index.php" class="btn-secondary">Volver</a>
            </header>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="form-container">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="nombre">Nombre *</label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="text" id="telefono" name="telefono">
                    </div>
                    
                    <button type="submit" class="btn">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
