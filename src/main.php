<?php
require_once "./lib/constants.php";
require_once "./lib/fetchApi.php";
require_once "./clases/version.php";
require_once "./clases/summoner.php";
require_once "./clases/login.php";
require_once "./clases/Database.php";
require_once "./lib/controlVersion.php";

/**
 * Si la sesión no ha sido iniciada la inicia
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Si el usuario no está logueado redirige la página a index.php
 * que es la página de login
 */
if (!isset($_SESSION['user'])) {
    header('Location: /index.php');
    exit;
}

/**
 * Conexión con la base de datos, crea un nuevo 
 * objeto de la clase Database
 */
$database = new Database();
$dbConnection = $database->getConnection();


/**
 * Prueba si está inicializada la base de datos
 * Código añadido por que me estaban dando fallos 
 * al inicializar :( */

if (!$dbConnection || !$dbConnection instanceof mysqli) {
    die('Error: Conexión a la base de datos no válida.');
}

// Manejo del botón de favoritos
/**
 * Este trozo de código maneja el botón de favoritos, comprueba si está seteado 
 * $_POST['toggleFav'] y si es así inicializa las variables necesarias.
 * Hace una consulta a la base de datos para verificar si la cuenta ya está en favoritos, preguntando si existe una fila que su idUser sea igual al usuario que está logueado y que el $puuid almacenado anteriormente sea igual al puuid de la tabla.
 * Si la respuesta tiene alguna fila significa que este usuario ya está en favoritos por lo tanto lo elimina y si no tiene ninguna línea lo añade a favoritos
 * 
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggleFav'])) {
    $puuid = $_POST['puuid'];
    $username = $_POST['username'];
    $tag = $_POST['tag'];
    $iconoPerfil = $_POST['iconoPerfil'];

    // Verificar si el summoner ya está en favoritos
    $stmt = $dbConnection->prepare("SELECT idUser FROM summoner WHERE puuid = ? AND idUser = (SELECT id FROM users WHERE username = ?)");
    $stmt->bind_param("ss", $puuid, $_SESSION['user']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        /** Elimina de favoritos */
        $deleteStmt = $dbConnection->prepare("DELETE FROM summoner WHERE puuid = ? AND idUser = (SELECT id FROM users WHERE username = ?)");
        $deleteStmt->bind_param("ss", $puuid, $_SESSION['user']);
        $deleteStmt->execute();
        $deleteStmt->close();
    } else {
        /** Añade a favoritos */
        $insertStmt = $dbConnection->prepare("INSERT INTO summoner (puuid, username, tag, foto, idUser) VALUES (?, ?, ?, ?, (SELECT id FROM users WHERE username = ?))");
        $insertStmt->bind_param("sssss", $puuid, $username, $tag, $iconoPerfil, $_SESSION['user']);
        $insertStmt->execute();
        $insertStmt->close();
    }

    $stmt->close();
}


/**
 * Inicializa la clase de Login para el LogOut desde el
 * botón de log out
 */
$database = new Database();
$dbConnection = $database->getConnection();
$login = new Login($dbConnection);

/**
 * Prueba si está inicializada la base de datos
 * Código añadido por que me estaban dando fallos 
 * al inicializar :()
 */

if (!$dbConnection || !$dbConnection instanceof mysqli) {
    die('Error: Conexión a la base de datos no válida.');
}

/**
 * Si existe $_POST['logout'] ejecuta la función logout() de la
 * clase login y redirige a index.php, que es la página de login
 * 
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    $login->logout();
    header('Location: /');
    exit;
}

/**
 * Si existe $puuid en $_GET hace una consulta a la base
 * de datos a la tabla summoner, trayendose las filas donde
 * el campo puuid sea igual a $puuid, si hay alguna fila crea
 * un objeto de la clase summoner con los datos obtenidos, si no dice Error, summoner no encontrado
 */
$selectedSummoner = null;
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['puuid'])) {
    $puuid = $_GET['puuid'];

    // Consulta a la base de datos para obtener el summoner por PUUID
    $stmt = $dbConnection->prepare("SELECT * FROM summoner WHERE puuid = ?");
    $stmt->bind_param("s", $puuid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $summonerData = $result->fetch_assoc();

        // Crear un objeto Summoner con los datos obtenidos
        $selectedSummoner = new Summoner(
            $summonerData['puuid'],
            $summonerData['username'],
            $summonerData['tag']
        );

        

    } else {
        echo "<p>Error: Summoner no encontrado.</p>";
    }

    $stmt->close();
}

/**
 * Esto controla si el formulario se ha enviado y verifica si 
 * los parámetros ya están en la url para evitar bucles
 * Código añadido por que me daba fallo y me hacía un bucle en todas las peticiones :(
 */
if (isset($_GET['gameName']) && isset($_GET['tag'])) {
    $gameName = htmlspecialchars($_GET['gameName']);
    $tag = htmlspecialchars($_GET['tag']);

    // Verificar si los parámetros ya están en la URL para evitar bucles
    if (!isset($_GET['redirected'])) {
        header("Location: /main.php/?gameName=$gameName&tag=$tag&redirected=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/style.css">
    <title>Proyecto</title>
</head>

<body>
    <div class="mainContainer">
        <span>Las busquedas pueden fallar por que la API tiene un número limitado de solicitudes, <br>solo espera un minuto y vuelve a buscar o recargar la página</span>
        <div class="mainContainer__userInfo">
            <form action="/userLoged.php" method="get">
                <input type="submit" name="userLoged" value="<?= htmlspecialchars($_SESSION['user']); ?>">
            </form>
            <form method="post" action="">
                <button type="submit" name="logout">Log out</button>
            </form>

        </div>

        <div class="content-container">

            <?php
            /**
             * Si existe el summoner inicialia $POST con el 
             * gameName y el tag del usuario
             */
            if (isset($selectedSummoner) && $selectedSummoner instanceof Summoner) {
                $_POST  = $selectedSummoner->getGameName();
                $_POST = $selectedSummoner->getTag();
            }
            
            /**
             * Si están seteados $gamename y $tag, sacados desde el formulario,  se hace la petición y si aún no se ha buscado ninguno dice que no se ha
             * buscado ningún usuario
             */
            if (isset($gameName) && isset($tag)) {
                require_once "usuario.php";
            } else {
                echo "<p>No se ha buscado ningún usuario.</p>";
            }
            ?>

        </div>

        <form action="/main.php" method="GET" class="formBuscarUser">
            <input type="text" name="gameName" id="gameName" placeholder="Usuario" required>
            <input type="text" name="tag" id="tag" placeholder="tag" required>
            <br>
            <input type="submit" value="Buscar">
        </form>




    </div>








</body>

</html>