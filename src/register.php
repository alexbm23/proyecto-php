<?php
require_once './lib/constants.php';
require_once './clases/Database.php';
require_once './clases/login.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user'])) {
    header('Location: main.php');
    exit;
}

$database = new Database();
$dbConnection = $database->getConnection();
$login = new Login($dbConnection);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['usuario'] ?? '';
    $password = $_POST['contraseña'] ?? '';
    $_SESSION['registration_success'] = "¡Registro completado! Ahora puedes iniciar sesión.";
    // Intentar registrar al usuario
    $message = $login->register($username, $password);

    // Si el registro es exitoso, redirigir a index.php
    if ($message === "Registro exitoso.") {
        header('Location: index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="./css/register.css">
</head>
<body>
    <section class="container">
        <div class="login-container">
            <div class="form-container">
                <h1 class="opacity">REGISTER</h1>

                <!-- Mensaje de error o éxito -->
                <?php if (!empty($message)): ?>
                    <p style="color: <?= $message === "Registro exitoso." ? 'green' : 'red'; ?>;">
                        <?= htmlspecialchars($message); ?>
                    </p>
                <?php endif; ?>

                <form method="post" action="register.php">
                    <input type="text" placeholder="USERNAME" id="usuario" name="usuario" required />
                    <input type="password" placeholder="PASSWORD" id="contraseña" name="contraseña" required />
                    <button class="opacity" type="submit">SUBMIT</button>
                </form>
                <div class="register-forget opacity">
                    <a href="index.php">LOGIN</a>
                </div>
            </div>
        </div>
        <div class="theme-btn-container"></div>
    </section>
</body>
</html>
