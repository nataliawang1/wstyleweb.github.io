<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

$countResult = $conn->query("SELECT COUNT(*) as count FROM wclub_miembros");
$count = $countResult ? intval($countResult->fetch_assoc()['count']) : 0;

$result = $conn->query("SELECT id, nombre, email, telefono, fecha_registro FROM wclub_miembros ORDER BY fecha_registro DESC LIMIT 6");
$members = [];
while ($row = $result->fetch_assoc()) {
    $members[] = $row;
}

echo json_encode(['success' => true, 'count' => $count, 'members' => $members]);
