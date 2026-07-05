<?php
require_once '../../config/database.php';
require_once '../../includes/auth.php';

requireLogin();

if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $stmt = $conn->prepare("DELETE FROM contactos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit();
}

if (isset($_GET['marcar_leido'])) {
    $id = intval($_GET['marcar_leido']);
    $stmt = $conn->prepare("UPDATE contactos SET leido = TRUE WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit();
}

$result = $conn->query("SELECT * FROM contactos ORDER BY fecha DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contactos - W-Style Admin</title>
    <link rel="stylesheet" href="../../css/admin-style.css">
</head>
<body>
    <div class="admin-container">
        <?php include '../sidebar.php'; ?>
        
        <div class="main-content">
            <header class="admin-header">
                <h1>Mensajes de Contacto</h1>
            </header>
            
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Mensaje</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="<?php echo $row['leido'] ? '' : 'unread'; ?>">
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['telefono']); ?></td>
                            <td><?php echo htmlspecialchars(substr($row['mensaje'], 0, 50)) . '...'; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($row['fecha'])); ?></td>
                            <td>
                                <?php if ($row['leido']): ?>
                                    <span class="badge badge-read">Leído</span>
                                <?php else: ?>
                                    <span class="badge badge-unread">No leído</span>
                                <?php endif; ?>
                            </td>
                            <td class="actions">
                                <?php if (!$row['leido']): ?>
                                    <a href="index.php?marcar_leido=<?php echo $row['id']; ?>" class="btn-read">Marcar leído</a>
                                <?php endif; ?>
                                <a href="index.php?eliminar=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('¿Estás seguro de eliminar este mensaje?');">Eliminar</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
