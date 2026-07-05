<?php
session_start();

// Verificar si el usuario está logueado
function isLoggedIn() {
    return isset($_SESSION['admin_id']);
}

// Verificar si el usuario está logueado, si no redirigir al login
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: ../admin/login.php");
        exit();
    }
}

// Redirigir si ya está logueado
function requireGuest() {
    if (isLoggedIn()) {
        header("Location: ../admin/dashboard.php");
        exit();
    }
}

// Cerrar sesión
function logout() {
    session_unset();
    session_destroy();
    header("Location: ../admin/login.php");
    exit();
}
?>
