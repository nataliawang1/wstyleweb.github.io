<?php
require_once '../../config/database.php';
require_once '../../includes/auth.php';

requireLogin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $categoria = trim($_POST['categoria']);

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
        $coverImage = $uploadedImages[0] ?? '';
        $stmt = $conn->prepare("INSERT INTO portafolio (titulo, descripcion, imagen, categoria) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $titulo, $descripcion, $coverImage, $categoria);

        if ($stmt->execute()) {
            $portafolioId = $stmt->insert_id;
            $stmt->close();

            foreach ($uploadedImages as $index => $imagen) {
                $stmtImg = $conn->prepare("INSERT INTO portafolio_imagenes (portafolio_id, imagen, orden) VALUES (?, ?, ?)");
                $orden = $index;
                $stmtImg->bind_param("isi", $portafolioId, $imagen, $orden);
                $stmtImg->execute();
                $stmtImg->close();
            }

            foreach ($colaboradores as $colaborador) {
                $stmtCollab = $conn->prepare("INSERT INTO portafolio_colaboradores (portafolio_id, nombre, rol) VALUES (?, ?, ?)");
                $stmtCollab->bind_param("iss", $portafolioId, $colaborador['nombre'], $colaborador['rol']);
                $stmtCollab->execute();
                $stmtCollab->close();
            }

            $success = 'Item agregado exitosamente.';
            header("Location: index.php");
            exit();
        } else {
            $error = 'Error al agregar el item.';
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
    <title>Crear Portafolio - W-Style Admin</title>
    <link rel="stylesheet" href="../../css/admin-style.css">
</head>
<body>
    <div class="admin-container">
        <?php include '../sidebar.php'; ?>
        
        <div class="main-content">
            <header class="admin-header">
                <h1>Agregar al Portafolio</h1>
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
                        <input type="text" id="titulo" name="titulo" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" rows="4"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="categoria">Categoría</label>
                        <input type="text" id="categoria" name="categoria">
                    </div>

                    <div class="form-group">
                        <label for="imagenes">Imágenes de la sesión</label>
                        <input type="file" id="imagenes" name="imagenes[]" accept="image/*" multiple>
                        <small class="form-note">La primera imagen se usará como portada en el portafolio.</small>
                    </div>

                    <div class="form-group">
                        <label>Colaboradores</label>
                        <div id="collaborators-wrapper">
                            <div class="collaborator-row">
                                <input type="text" name="colaborador_nombre[]" placeholder="Nombre">
                                <input type="text" name="colaborador_rol[]" placeholder="Rol (maquillaje, fotografía, etc.)">
                                <button type="button" class="btn-secondary remove-collaborator">Eliminar</button>
                            </div>
                        </div>
                        <button type="button" id="add-collaborator" class="btn-ghost">+ Agregar colaborador</button>
                    </div>
                    
                    <button type="submit" class="btn-primary">Guardar</button>
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
