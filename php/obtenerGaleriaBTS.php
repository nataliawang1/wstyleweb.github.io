<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

$result = $conn->query("SELECT cg.id, cg.cliente_id, cg.nombre, cg.descripcion, cg.archivo, cg.tipo, cg.url_video FROM cliente_galeria cg ORDER BY cg.cliente_id, cg.orden");
$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}
echo json_encode(['success' => true, 'items' => $items]);
