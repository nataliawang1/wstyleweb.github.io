<?php
require_once '../../config/database.php';
require_once '../../includes/auth.php';

requireLogin();

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $marca = trim($_POST['marca']);
    $descripcion = trim($_POST['descripcion']);
    $testimonio = trim($_POST['testimonio']);
    $logo = '';
    
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $logo = time() . '_' . $_FILES['logo']['name'];
        move_uploaded_file($_FILES['logo']['tmp_name'], '../../images/' . $logo);
    }
    
    if (empty($nombre)) {
        $error = 'El nombre es obligatorio.';
    } else {
        $stmt = $conn->prepare("INSERT INTO clientes (nombre, marca, descripcion, logo, testimonio) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nombre, $marca, $descripcion, $logo, $testimonio);
        
        if ($stmt->execute()) {
            $stmt->close();
            header("Location: index.php");
            exit();
        } else {
            $error = 'Error al agregar el cliente.';
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
    <title>Crear Cliente - W-Style Admin</title>
    <link rel="stylesheet" href="../../css/admin-style.css">
</head>
<body>
    <div class="admin-container">
        <?php include '../sidebar.php'; ?>
        
        <div class="main-content">
            <header class="admin-header">
                <h1>Agregar Cliente</h1>
                <a href="index.php" class="btn-secondary">Volver</a>
            </header>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="form-container">
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nombre">Nombre *</label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="marca">Marca</label>
                        <input type="text" id="marca" name="marca">
                    </div>
                    
                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="testimonio">Testimonio</label>
                        <textarea id="testimonio" name="testimonio" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="logo">Logo</label>
                        <input type="file" id="logo" name="logo" accept="image/*">
                    </div>
                    
                    <button type="submit" class="btn-primary">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
