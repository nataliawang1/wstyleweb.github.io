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

$colaboradoresExistentes = [];
if ($conn->query("SHOW TABLES LIKE 'portafolio_colaboradores'")->num_rows > 0) {
    $stmt = $conn->prepare("SELECT nombre, rol FROM portafolio_colaboradores WHERE portafolio_id = ? ORDER BY id ASC");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $colaboradoresExistentes[] = $row;
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $categoria = trim($_POST['categoria']);
    $imagen = $item['imagen'];

    $uploadedImages = [];
    if (!empty($_FILES['imagenes']) && is_array($_FILES['imagenes']['name'])) {
        for ($i = 0; $i < count($_FILES['imagenes']['name']); $i++) {
            if ($_FILES['imagenes']['error'][$i] === UPLOAD_ERR_OK) {
                $filename = time() . '_' . basename($_FILES['imagenes']['name'][$i]);
                if (move_uploaded_file($_FILES['imagenes']['tmp_name'][$i], '../../images/' . $filename)) {
                    $uploadedImages[] = $filename;
                }
            }
        }
    }

    $colaboradores = [];
    if (!empty($_POST['colaborador_nombre']) && is_array($_POST['colaborador_nombre'])) {
        foreach ($_POST['colaborador_nombre'] as $index => $nombre) {
            $nombre = trim($nombre);
            $rol = trim($_POST['colaborador_rol'][$index] ?? '');
            if ($nombre !== '' && $rol !== '') {
                $colaboradores[] = ['nombre' => $nombre, 'rol' => $rol];
            }
        }
    }

    if (empty($titulo)) {
        $error = 'El título es obligatorio.';
    } else {
        if (!$imagen && !empty($uploadedImages)) {
            $imagen = $uploadedImages[0];
        }

        $stmt = $conn->prepare("UPDATE portafolio SET titulo = ?, descripcion = ?, imagen = ?, categoria = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $titulo, $descripcion, $imagen, $categoria, $id);

        if ($stmt->execute()) {
            $stmt->close();
            if (!empty($uploadedImages) && $conn->query("SHOW TABLES LIKE 'portafolio_imagenes'")->num_rows > 0) {
                $stmtOrder = $conn->prepare("SELECT IFNULL(MAX(orden), -1) AS max_order FROM portafolio_imagenes WHERE portafolio_id = ?");
                $stmtOrder->bind_param("i", $id);
                $stmtOrder->execute();
                $resultOrder = $stmtOrder->get_result();
                $maxOrder = ($resultOrder->fetch_assoc()['max_order'] ?? -1) + 1;
                $stmtOrder->close();

                foreach ($uploadedImages as $index => $imagenFile) {
                    $stmtImg = $conn->prepare("INSERT INTO portafolio_imagenes (portafolio_id, imagen, orden) VALUES (?, ?, ?)");
                    $orden = $maxOrder + $index;
                    $stmtImg->bind_param("isi", $id, $imagenFile, $orden);
                    $stmtImg->execute();
                    $stmtImg->close();
                }
            }

            if ($conn->query("SHOW TABLES LIKE 'portafolio_colaboradores'")->num_rows > 0) {
                $deleteStmt = $conn->prepare("DELETE FROM portafolio_colaboradores WHERE portafolio_id = ?");
                $deleteStmt->bind_param("i", $id);
                $deleteStmt->execute();
                $deleteStmt->close();

                foreach ($colaboradores as $colaborador) {
                    $stmtCollab = $conn->prepare("INSERT INTO portafolio_colaboradores (portafolio_id, nombre, rol) VALUES (?, ?, ?)");
                    $stmtCollab->bind_param("iss", $id, $colaborador['nombre'], $colaborador['rol']);
                    $stmtCollab->execute();
                    $stmtCollab->close();
                }
            }

            $success = 'Item actualizado exitosamente.';
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
                        <label for="imagenes">Agregar imágenes adicionales</label>
                        <input type="file" id="imagenes" name="imagenes[]" accept="image/*" multiple>
                        <small class="form-note">La primera imagen subida se toma como portada si no existe imagen de portada.</small>
                    </div>

                    <?php if ($item['imagen']): ?>
                        <div class="form-group">
                            <label>Portada actual</label>
                            <p><img src="../../images/<?php echo htmlspecialchars($item['imagen']); ?>" alt="" class="thumbnail"></p>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Colaboradores</label>
                        <div id="collaborators-wrapper">
                            <?php if (!empty($colaboradoresExistentes)): ?>
                                <?php foreach ($colaboradoresExistentes as $collab): ?>
                                    <div class="collaborator-row">
                                        <input type="text" name="colaborador_nombre[]" placeholder="Nombre" value="<?php echo htmlspecialchars($collab['nombre']); ?>">
                                        <input type="text" name="colaborador_rol[]" placeholder="Rol (maquillaje, fotografía, etc.)" value="<?php echo htmlspecialchars($collab['rol']); ?>">
                                        <button type="button" class="btn-secondary remove-collaborator">Eliminar</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="collaborator-row">
                                    <input type="text" name="colaborador_nombre[]" placeholder="Nombre">
                                    <input type="text" name="colaborador_rol[]" placeholder="Rol (maquillaje, fotografía, etc.)">
                                    <button type="button" class="btn-secondary remove-collaborator">Eliminar</button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" id="add-collaborator" class="btn-ghost">+ Agregar colaborador</button>
                    </div>
                    
                    <button type="submit" class="btn-primary">Actualizar</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const collaboratorWrapper = document.getElementById('collaborators-wrapper');
        document.getElementById('add-collaborator').addEventListener('click', () => {
            const row = document.createElement('div');
            row.className = 'collaborator-row';
            row.innerHTML = `
                <input type="text" name="colaborador_nombre[]" placeholder="Nombre">
                <input type="text" name="colaborador_rol[]" placeholder="Rol (maquillaje, fotografía, etc.)">
                <button type="button" class="btn-secondary remove-collaborator">Eliminar</button>
            `;
            collaboratorWrapper.appendChild(row);
        });

        collaboratorWrapper.addEventListener('click', (event) => {
            if (event.target.classList.contains('remove-collaborator')) {
                const row = event.target.closest('.collaborator-row');
                if (row) row.remove();
            }
        });
    </script>
</body>
</html>
