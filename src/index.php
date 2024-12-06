<?php
require_once './lib/constants.php';
require_once './clases/Database.php';
require_once './clases/login.php';

/**
 * Si la sesión no se ha iniciado, se inicia
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Si el usuario ya está logueado lo redirige a main.php
 */
if (isset($_SESSION['user'])) {
    header('Location: main.php');
    exit;
}

/**
 * Inicializa la base de datos y el Login
 */
$database = new Database();
$dbConnection = $database->getConnection();
$login = new Login($dbConnection);

$message = ''; 


$success_message = '';

/**
 * Si $_SESSION['registration_succes'] existe le da el valor
 * de registro exitoso, proveniente de register.php
 */
if (isset($_SESSION['registration_success'])) {
    $success_message = $_SESSION['registration_success'];
    unset($_SESSION['registration_success']);
}

/**
 *  Si $_POST['usuario'] y $_POST['contraseña'] existen
 * se les da el valor a $username y $password.
 * Con el objeto de la clase Login se llama a la función login()
 * que devuelve true si el login es exitoso, si es exitoso la 
 * superglobal $_SESSION['user'] obtiene el valor de $username
 * y redirige la página a main.php
 */
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
