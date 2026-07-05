<?php
require_once '../../config/database.php';
require_once '../../includes/auth.php';

requireLogin();

$id = intval($_GET['id']);
$error = '';
$success = '';

// Obtener item actual
$stmt = $conn->prepare("SELECT * FROM portafolio WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();
$stmt->close();

if (!$item) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $categoria = trim($_POST['categoria']);
    $imagen = $item['imagen'];
    
    // Manejo de imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $imagen = time() . '_' . $_FILES['imagen']['name'];
        move_uploaded_file($_FILES['imagen']['tmp_name'], '../../images/' . $imagen);
    }
    
    if (empty($titulo)) {
        $error = 'El título es obligatorio.';
    } else {
        $stmt = $conn->prepare("UPDATE portafolio SET titulo = ?, descripcion = ?, imagen = ?, categoria = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $titulo, $descripcion, $imagen, $categoria, $id);
        
        if ($stmt->execute()) {
            $success = 'Item actualizado exitosamente.';
            $stmt->close();
            header("Location: index.php");
            exit();
        } else {
            $error = 'Error al actualizar el item.';
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
    <title>Editar Portafolio - W-Style Admin</title>
    <link rel="stylesheet" href="../../css/admin-style.css">
</head>
<body>
    <div class="admin-container">
        <?php include '../sidebar.php'; ?>
        
        <div class="main-content">
            <header class="admin-header">
                <h1>Editar Portafolio</h1>
                <a href="index.php" class="btn-secondary">Volver</a>
            </header>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="form-container">
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="titulo">Título *</label>
                        <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($item['titulo']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" rows="4"><?php echo htmlspecialchars($item['descripcion']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="categoria">Categoría</label>
                        <input type="text" id="categoria" name="categoria" value="<?php echo htmlspecialchars($item['categoria']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="imagen">Imagen</label>
                        <input type="file" id="imagen" name="imagen" accept="image/*">
                        <?php if ($item['imagen']): ?>
                            <p>Imagen actual: <img src="../../images/<?php echo htmlspecialchars($item['imagen']); ?>" alt="" class="thumbnail"></p>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" class="btn-primary">Actualizar</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
