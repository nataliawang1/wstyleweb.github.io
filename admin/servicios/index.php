<?php
require_once '../../config/database.php';
require_once '../../includes/auth.php';

requireLogin();

if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $stmt = $conn->prepare("DELETE FROM servicios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit();
}

$result = $conn->query("SELECT * FROM servicios ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios - W-Style Admin</title>
    <link rel="stylesheet" href="../../css/admin-style.css">
</head>
<body>
    <div class="admin-container">
        <?php include '../sidebar.php'; ?>
        
        <div class="main-content">
            <header class="admin-header">
                <h1>Servicios</h1>
                <a href="crear.php" class="btn-primary">+ Agregar Nuevo</a>
            </header>
            
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Icono</th>
                            <th>Título</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['icono']); ?></td>
                            <td><?php echo htmlspecialchars($row['titulo']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                            <td class="actions">
                                <a href="editar.php?id=<?php echo $row['id']; ?>" class="btn-edit">Editar</a>
                                <a href="index.php?eliminar=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('¿Estás seguro de eliminar este servicio?');">Eliminar</a>
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
