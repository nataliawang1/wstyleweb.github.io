<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

$result = $conn->query("SELECT id, titulo, descripcion, imagen, categoria FROM portafolio ORDER BY created_at DESC");
$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}
echo json_encode(['success' => true, 'items' => $items]);
