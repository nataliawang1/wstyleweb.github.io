<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: /W-Style/admin/login.php");
        exit();
    }
}

function requireGuest() {
    if (isLoggedIn()) {
        header("Location: /W-Style/admin/dashboard.php");
        exit();
    }
}

function logout() {
    session_unset();
    session_destroy();
    header("Location: /W-Style/admin/login.php");
    exit();
}
?>
