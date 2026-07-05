<?php
require_once '../../config/database.php';
require_once '../../includes/auth.php';

requireLogin();

if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $stmt = $conn->prepare("DELETE FROM wclub_miembros WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit();
}

$result = $conn->query("SELECT * FROM wclub_miembros ORDER BY fecha_registro DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>W Club - W-Style Admin</title>
    <link rel="stylesheet" href="../../css/admin-style.css">
</head>
<body>
    <div class="admin-container">
        <?php include '../sidebar.php'; ?>
        
        <div class="main-content">
            <header class="admin-header">
                <h1>W Club - Miembros</h1>
            </header>
            
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Fecha Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['telefono']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($row['fecha_registro'])); ?></td>
                            <td class="actions">
                                <a href="editar.php?id=<?php echo $row['id']; ?>" class="btn-edit">Editar</a>
                                <a href="index.php?eliminar=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('¿Estás seguro de eliminar este miembro?');">Eliminar</a>
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
