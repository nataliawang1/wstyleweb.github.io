<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../wclub.html');
    exit();
}

$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$error = '';

if (empty($nombre) || empty($email) || empty($telefono)) {
    $error = 'Todos los campos son obligatorios.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Por favor ingresa un correo válido.';
}

if ($error) {
    header('Location: ../wclub.html?error=' . urlencode($error));
    exit();
}

$stmt = $conn->prepare("SELECT id FROM wclub_miembros WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $error = 'Este correo electrónico ya está registrado en el club.';
    header('Location: ../wclub.html?error=' . urlencode($error));
    exit();
}

$stmt->close();

$stmt = $conn->prepare("INSERT INTO wclub_miembros (nombre, email, telefono) VALUES (?, ?, ?)");
$stmt->bind_param('sss', $nombre, $email, $telefono);
$stmt->execute();
$stmt->close();

header('Location: ../wclub.html?success=1');
exit();
