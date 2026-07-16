<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.html');
    exit();
}

$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$mensaje = trim($_POST['mensaje'] ?? '');
$error = '';

if (empty($nombre) || empty($email) || empty($mensaje)) {
    $error = 'Nombre, email y mensaje son obligatorios.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Por favor ingresa un correo válido.';
}

if ($error) {
    header('Location: ../contacto.html?error=' . urlencode($error));
    exit();
}

$stmt = $conn->prepare("INSERT INTO contactos (nombre, email, telefono, mensaje) VALUES (?, ?, ?, ?)");
$stmt->bind_param('ssss', $nombre, $email, $telefono, $mensaje);
$stmt->execute();
$stmt->close();

header('Location: ../contacto.html?success=1');
exit();
