<?php
require_once '../../config/database.php';
require_once '../../includes/auth.php';

requireLogin();

$id = intval($_GET['id']);
$error = '';
$success = '';

// Obtener datos del miembro
$stmt = $conn->prepare("SELECT * FROM wclub_miembros WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$miembro = $result->fetch_assoc();
$stmt->close();

if (!$miembro) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    
    if (empty($nombre) || empty($email)) {
        $error = 'El nombre y email son obligatorios.';
    } else {
        $stmt = $conn->prepare("UPDATE wclub_miembros SET nombre = ?, email = ?, telefono = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nombre, $email, $telefono, $id);
        
        if ($stmt->execute()) {
            $success = 'Miembro actualizado exitosamente.';
            $stmt->close();
            header("Location: index.php");
            exit();
        } else {
            $error = 'Error al actualizar el miembro. El email podría ya estar registrado.';
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
    <title>Editar Miembro W Club - W-Style Admin</title>
    <link rel="stylesheet" href="../../css/admin-style.css">
</head>
<body>
    <div class="admin-container">
        <?php include '../sidebar.php'; ?>
        
        <div class="main-content">
            <header class="admin-header">
                <h1>Editar Miembro W Club</h1>
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
                        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($miembro['nombre']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($miembro['email']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($miembro['telefono']); ?>">
                    </div>
                    
                    <button type="submit" class="btn">Actualizar</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
