<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo json_encode(['success' => false, 'error' => 'ID inválido.']);
    exit;
}

$stmt = $conn->prepare("SELECT id, titulo, descripcion, imagen, categoria, created_at FROM portafolio WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();
$stmt->close();

if (!$item) {
    echo json_encode(['success' => false, 'error' => 'Sesión no encontrada.']);
    exit;
}

$item['imagenes'] = [];
$item['colaboradores'] = [];

if ($conn->query("SHOW TABLES LIKE 'portafolio_imagenes'")->num_rows > 0) {
    $stmtImgs = $conn->prepare("SELECT imagen FROM portafolio_imagenes WHERE portafolio_id = ? ORDER BY orden ASC");
    $stmtImgs->bind_param("i", $id);
    $stmtImgs->execute();
    $resultImgs = $stmtImgs->get_result();
    while ($rowImg = $resultImgs->fetch_assoc()) {
        $item['imagenes'][] = $rowImg;
    }
    $stmtImgs->close();
}

if ($conn->query("SHOW TABLES LIKE 'portafolio_colaboradores'")->num_rows > 0) {
    $stmtCollabs = $conn->prepare("SELECT nombre, rol FROM portafolio_colaboradores WHERE portafolio_id = ? ORDER BY id ASC");
    $stmtCollabs->bind_param("i", $id);
    $stmtCollabs->execute();
    $resultCollabs = $stmtCollabs->get_result();
    while ($rowCollab = $resultCollabs->fetch_assoc()) {
        $item['colaboradores'][] = $rowCollab;
    }
    $stmtCollabs->close();
}

echo json_encode(['success' => true, 'item' => $item]);
