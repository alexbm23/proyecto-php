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

// Inicializa la base de datos y la clase Login
$database = new Database();
$dbConnection = $database->getConnection();
$login = new Login($dbConnection);

$message = ''; // Mensaje para el estado del login

// Mensaje de éxito de registro
$success_message = '';
if (isset($_SESSION['registration_success'])) {
    $success_message = $_SESSION['registration_success'];
    unset($_SESSION['registration_success']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['usuario'] ?? '';
    $password = $_POST['contraseña'] ?? '';

    if ($login->login($username, $password)) {
        $_SESSION['user'] = $username;
        header('Location: main.php');
        exit;
    } else {
        $message = 'Credenciales incorrectas. Inténtalo de nuevo.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./css/login.css">
</head>
<body>
    <section class="container">
        <div class="login-container">
            <div class="form-container">
                <h1 class="opacity">LOGIN</h1>

                <!-- Mensaje de éxito -->
                <?php if (!empty($success_message)): ?>
                    <p style="color: green;"><?= htmlspecialchars($success_message); ?></p>
                <?php endif; ?>

                <!-- Mensaje de error -->
                <?php if (!empty($message)): ?>
                    <p style="color: red;"><?= htmlspecialchars($message); ?></p>
                <?php endif; ?>

                <form method="post" action="index.php">
                    <input type="text" placeholder="USERNAME" id="usuario" name="usuario" required />
                    <input type="password" placeholder="PASSWORD" id="contraseña" name="contraseña" required />
                    <button class="opacity" type="submit">SUBMIT</button>
                </form>
                <div class="register-forget opacity">
                    <a href="register.php">REGISTER</a>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
