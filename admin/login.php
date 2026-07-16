```php
<?php
session_start();
require_once '../config/database.php';

if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password FROM administradores WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $admin = $result->fetch_assoc();

        if (password_verify($password, $admin['password'])) {

            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];

            header("Location: dashboard.php");
            exit();

        } else {

            $error = "Contraseña incorrecta.";

        }

    } else {

        $error = "Usuario no encontrado.";

    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Panel de Administración | W-Style</title>

    <link rel="stylesheet" href="../css/admin-style.css?v=<?php echo time(); ?>">

</head>

<body class="login-page">

<div class="login-container">

    <div class="login-box">

        <div class="logo">

            <img src="../images/LOGO WANG RED.jpg" alt="W-Style Logo">

            <p>Panel de Administración</p>

        </div>

        <?php if (!empty($error)): ?>

            <div class="alert alert-danger">

                <?php echo htmlspecialchars($error); ?>

            </div>

        <?php endif; ?>

        <form method="POST">

            <input
                type="text"
                name="username"
                placeholder="Usuario"
                required
                autocomplete="username">

            <input
                type="password"
                name="password"
                placeholder="Contraseña"
                required
                autocomplete="current-password">

            <button type="submit" class="btn">
                Iniciar sesión
            </button>

        </form>

    </div>

</div>

</body>

</html>
```
