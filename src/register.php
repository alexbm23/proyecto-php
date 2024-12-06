<?php
require_once './lib/constants.php';
require_once './clases/Database.php';
require_once './clases/login.php';

/**
 * Si la sesión no ha sido iniciada la inicia
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Comprueba si ya se ha iniciado sesión, si es así 
 * redirige la página a main.php
 */
if (isset($_SESSION['user'])) {
    header('Location: main.php');
    exit;
}


/**
 * Establece un objeto de clase Database y comprueba si hay conexión
 * Establece un nuevo objeto de la clase Login.
 */
$database = new Database();
$dbConnection = $database->getConnection();
$login = new Login($dbConnection);

$message = '';

/**
 * Si existe el método POST:
 * Si $_POST['usuario'] y $_POST['contraseña'] existen
 * se les da el valor a $username y $password.
 * Se le da un valor de registro comprobado a $_SESSION['registration_succes']
 * A $message se le da el valor devuelto por la función register
 * de la clase login, si este es "Registro exitoso" se redirige
 * la página a index.php que es la página de login, si no
 * se mostrará el mensaje de error
 */
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
