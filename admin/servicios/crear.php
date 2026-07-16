<?php
require_once '../../config/database.php';
require_once '../../includes/auth.php';

requireLogin();

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $imagen = '';
    
    if (empty($titulo)) {
        $error = 'El título es obligatorio.';
    } else {
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
            $imagen = time() . '_' . basename($_FILES['imagen']['name']);
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], '../../images/' . $imagen)) {
                $stmt = $conn->prepare("INSERT INTO servicios (titulo, descripcion, imagen) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $titulo, $descripcion, $imagen);
                
                if ($stmt->execute()) {
                    $stmt->close();
                    header("Location: index.php");
                    exit();
                } else {
                    $error = 'Error al agregar el servicio.';
                }
                $stmt->close();
            } else {
                $error = 'Error al subir la imagen.';
            }
        } else {
            $error = 'La imagen es obligatoria.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Servicio - W-Style Admin</title>
    <link rel="stylesheet" href="../../css/admin-style.css">
</head>
<body>
    <div class="admin-container">
        <?php include '../sidebar.php'; ?>
        
        <div class="main-content">
            <header class="admin-header">
                <h1>Agregar Servicio</h1>
                <a href="index.php" class="btn-secondary">Volver</a>
            </header>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="form-container">
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="titulo">Título *</label>
                        <input type="text" id="titulo" name="titulo" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" rows="4"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="imagen">Imagen *</label>
                        <input type="file" id="imagen" name="imagen" accept="image/*" required>
                    </div>
                    
                    <button type="submit" class="btn-primary">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
