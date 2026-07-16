<?php
require_once '../../config/database.php';
require_once '../../includes/auth.php';

requireLogin();

$id = intval($_GET['id']);
$error = '';
$success = '';

$stmt = $conn->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();
$stmt->close();

if (!$item) {
    header("Location: index.php");
    exit();
}


$galery_result = $conn->query("SELECT * FROM cliente_galeria WHERE cliente_id = $id ORDER BY orden");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'update_client') {
        $nombre = trim($_POST['nombre']);
        $marca = trim($_POST['marca']);
        $descripcion = trim($_POST['descripcion']);
        $testimonio = trim($_POST['testimonio']);
        $logo = $item['logo'];
        
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
            $logo = time() . '_' . $_FILES['logo']['name'];
            move_uploaded_file($_FILES['logo']['tmp_name'], '../../images/' . $logo);
        }
        
        if (empty($nombre)) {
            $error = 'El nombre es obligatorio.';
        } else {
            $stmt = $conn->prepare("UPDATE clientes SET nombre = ?, marca = ?, descripcion = ?, logo = ?, testimonio = ? WHERE id = ?");
            $stmt->bind_param("sssssi", $nombre, $marca, $descripcion, $logo, $testimonio, $id);
            
            if ($stmt->execute()) {
                $item['nombre'] = $nombre;
                $item['marca'] = $marca;
                $item['descripcion'] = $descripcion;
                $item['testimonio'] = $testimonio;
                $item['logo'] = $logo;
                $success = 'Cliente actualizado exitosamente.';
                $stmt->close();
            } else {
                $error = 'Error al actualizar el cliente.';
                $stmt->close();
            }
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'add_gallery') {
        $added = false;
        
        if (isset($_FILES['galeria_fotos'])) {
            foreach ($_FILES['galeria_fotos']['name'] as $key => $filename) {
                if (!empty($filename) && $_FILES['galeria_fotos']['error'][$key] == 0) {
                    $nombre_foto = trim($_POST['nombre_foto'][$key] ?? '');
                    $descripcion_foto = trim($_POST['descripcion_foto'][$key] ?? '');
                    $nuevo_nombre = time() . '_' . $key . '_' . basename($filename);
                    
                    if (move_uploaded_file($_FILES['galeria_fotos']['tmp_name'][$key], '../../images/' . $nuevo_nombre)) {
                        $result = $conn->query("SELECT COALESCE(MAX(orden), 0) + 1 as next_orden FROM cliente_galeria WHERE cliente_id = $id");
                        $row = $result->fetch_assoc();
                        $next_orden = $row['next_orden'];
                        
                        $stmt = $conn->prepare("INSERT INTO cliente_galeria (cliente_id, nombre, descripcion, archivo, tipo, orden) VALUES (?, ?, ?, ?, 'foto', ?)");
                        $stmt->bind_param("isssi", $id, $nombre_foto, $descripcion_foto, $nuevo_nombre, $next_orden);
                        if ($stmt->execute()) {
                            $added = true;
                        }
                        $stmt->close();
                    }
                }
            }
        }
        
        if (isset($_POST['video_urls'])) {
            foreach ($_POST['video_urls'] as $key => $video_url) {
                $video_url = trim($video_url);
                if (!empty($video_url)) {
                    $nombre_video = trim($_POST['nombre_video'][$key] ?? '');
                    $descripcion_video = trim($_POST['descripcion_video'][$key] ?? '');
                    
                    $result = $conn->query("SELECT COALESCE(MAX(orden), 0) + 1 as next_orden FROM cliente_galeria WHERE cliente_id = $id");
                    $row = $result->fetch_assoc();
                    $next_orden = $row['next_orden'];
                    
                    $stmt = $conn->prepare("INSERT INTO cliente_galeria (cliente_id, nombre, descripcion, archivo, tipo, url_video, orden) VALUES (?, ?, ?, 'video', 'video', ?, ?)");
                    $stmt->bind_param("isssi", $id, $nombre_video, $descripcion_video, $video_url, $next_orden);
                    if ($stmt->execute()) {
                        $added = true;
                    }
                    $stmt->close();
                }
            }
        }
        
        if ($added) {
            $success = 'Contenido agregado a la galería.';
            $galery_result = $conn->query("SELECT * FROM cliente_galeria WHERE cliente_id = $id ORDER BY orden");
        } else {
            $error = 'No se agregó ningún contenido.';
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'delete_gallery_item') {
        $item_id = intval($_POST['item_id']);
        $stmt = $conn->prepare("DELETE FROM cliente_galeria WHERE id = ? AND cliente_id = ?");
        $stmt->bind_param("ii", $item_id, $id);
        if ($stmt->execute()) {
            $success = 'Elemento de galería eliminado.';
            $galery_result = $conn->query("SELECT * FROM cliente_galeria WHERE cliente_id = $id ORDER BY orden");
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
    <title>Editar Cliente - W-Style Admin</title>
    <link rel="stylesheet" href="../../css/admin-style.css">
    <style>
        fieldset {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        legend {
            padding: 0 10px;
            font-weight: 600;
            color: #000;
        }
        .gallery-section-form {
            margin-bottom: 30px;
        }
        .gallery-section-form h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 16px;
        }
        .gallery-item-form {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            border: 1px solid #e0e0e0;
        }
        .gallery-preview {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        .gallery-preview-item {
            position: relative;
            background: white;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .gallery-preview-item img,
        .gallery-preview-item video {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        .gallery-preview-item .delete-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background: #e63946;
            color: white;
            border: none;
            border-radius: 4px;
            width: 30px;
            height: 30px;
            cursor: pointer;
            font-size: 18px;
        }
        .remove-btn {
            background-color: #e63946;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include '../sidebar.php'; ?>
        
        <div class="main-content">
            <header class="admin-header">
                <h1>Editar Cliente: <?php echo htmlspecialchars($item['nombre']); ?></h1>
                <a href="index.php" class="btn-secondary">Volver</a>
            </header>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="form-container">
                <!-- Client Update Form -->
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update_client">
                    
                    <fieldset>
                        <legend>Información del Cliente</legend>
                        
                        <div class="form-group">
                            <label for="nombre">Nombre del Cliente *</label>
                            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($item['nombre']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="marca">Marca/Empresa</label>
                            <input type="text" id="marca" name="marca" value="<?php echo htmlspecialchars($item['marca']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="descripcion">Descripción General</label>
                            <textarea id="descripcion" name="descripcion" rows="3"><?php echo htmlspecialchars($item['descripcion']); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="testimonio">Testimonio</label>
                            <textarea id="testimonio" name="testimonio" rows="3"><?php echo htmlspecialchars($item['testimonio']); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="logo">Logo</label>
                            <input type="file" id="logo" name="logo" accept="image/*">
                            <?php if ($item['logo']): ?>
                                <small>Logo actual: <?php echo htmlspecialchars($item['logo']); ?></small>
                            <?php endif; ?>
                        </div>
                    </fieldset>
                    
                    <button type="submit" class="btn-primary">Actualizar Cliente</button>
                </form>

                <hr style="margin: 40px 0; border: none; border-top: 1px solid #ddd;">
                
                <!-- Gallery Preview -->
                <?php if ($galery_result && $galery_result->num_rows > 0): ?>
                    <fieldset>
                        <legend>Galería Actual (BTS)</legend>
                        <div class="gallery-preview">
                            <?php while ($gallery_item = $galery_result->fetch_assoc()): ?>
                                <div class="gallery-preview-item">
                                    <?php if ($gallery_item['tipo'] === 'foto'): ?>
                                        <img src="../../images/<?php echo htmlspecialchars($gallery_item['archivo']); ?>" alt="<?php echo htmlspecialchars($gallery_item['nombre']); ?>">
                                    <?php else: ?>
                                        <div style="background: #333; height: 150px; display: flex; align-items: center; justify-content: center; color: white;">
                                            <span>Video</span>
                                        </div>
                                    <?php endif; ?>
                                    <form method="POST" action="" style="display: inline;">
                                        <input type="hidden" name="action" value="delete_gallery_item">
                                        <input type="hidden" name="item_id" value="<?php echo $gallery_item['id']; ?>">
                                        <button type="submit" class="delete-btn" title="Eliminar" onclick="return confirm('¿Eliminar este elemento?');">×</button>
                                    </form>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </fieldset>
                <?php endif; ?>
                
                <!-- Add Gallery Form -->
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_gallery">
                    
                    <fieldset>
                        <legend>Agregar Fotos y Videos (BTS)</legend>
                        
                        <div class="gallery-section-form">
                            <h3>Fotos</h3>
                            <div id="fotos-container">
                                <div class="gallery-item-form">
                                    <div class="form-group">
                                        <label>Foto</label>
                                        <input type="file" name="galeria_fotos[]" accept="image/*">
                                    </div>
                                    <div class="form-group">
                                        <label>Título/Descripción</label>
                                        <input type="text" name="nombre_foto[]" placeholder="Ej: Sesión de fotos">
                                    </div>
                                    <div class="form-group">
                                        <label>Descripción detallada</label>
                                        <textarea name="descripcion_foto[]" rows="2" placeholder="Detalles de la foto"></textarea>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn-secondary" onclick="agregarFoto()">+ Agregar Foto</button>
                        </div>
                        
                        <div class="gallery-section-form">
                            <h3>Videos</h3>
                            <div id="videos-container">
                                <div class="gallery-item-form">
                                    <div class="form-group">
                                        <label>URL de Video (YouTube/Vimeo)</label>
                                        <input type="url" name="video_urls[]" placeholder="https://youtube.com/watch?v=...">
                                    </div>
                                    <div class="form-group">
                                        <label>Título/Descripción</label>
                                        <input type="text" name="nombre_video[]" placeholder="Ej: BTS del evento">
                                    </div>
                                    <div class="form-group">
                                        <label>Descripción detallada</label>
                                        <textarea name="descripcion_video[]" rows="2" placeholder="Detalles del video"></textarea>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn-secondary" onclick="agregarVideo()">+ Agregar Video</button>
                        </div>
                    </fieldset>
                    
                    <button type="submit" class="btn-primary">Agregar a Galería</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function agregarFoto() {
            const container = document.getElementById('fotos-container');
            const item = document.createElement('div');
            item.className = 'gallery-item-form';
            item.innerHTML = `
                <div class="form-group">
                    <label>Foto</label>
                    <input type="file" name="galeria_fotos[]" accept="image/*">
                </div>
                <div class="form-group">
                    <label>Título/Descripción</label>
                    <input type="text" name="nombre_foto[]" placeholder="Ej: Sesión de fotos">
                </div>
                <div class="form-group">
                    <label>Descripción detallada</label>
                    <textarea name="descripcion_foto[]" rows="2" placeholder="Detalles de la foto"></textarea>
                </div>
                <button type="button" class="remove-btn" onclick="this.parentElement.remove()">Eliminar</button>
            `;
            container.appendChild(item);
        }

        function agregarVideo() {
            const container = document.getElementById('videos-container');
            const item = document.createElement('div');
            item.className = 'gallery-item-form';
            item.innerHTML = `
                <div class="form-group">
                    <label>URL de Video (YouTube/Vimeo)</label>
                    <input type="url" name="video_urls[]" placeholder="https://youtube.com/watch?v=...">
                </div>
                <div class="form-group">
                    <label>Título/Descripción</label>
                    <input type="text" name="nombre_video[]" placeholder="Ej: BTS del evento">
                </div>
                <div class="form-group">
                    <label>Descripción detallada</label>
                    <textarea name="descripcion_video[]" rows="2" placeholder="Detalles del video"></textarea>
                </div>
                <button type="button" class="remove-btn" onclick="this.parentElement.remove()">Eliminar</button>
            `;
            container.appendChild(item);
        }
    </script>
</body>
</html>
