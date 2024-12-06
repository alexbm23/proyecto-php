<?php
require_once './clases/Database.php';
require_once './clases/summoner.php';
require_once './lib/constants.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$userLoged = $_GET['userLoged'] ?? null;
if (!$userLoged) {
    echo "Error: No se ha especificado un usuario logueado.";
    exit;
}

// Conexión a la base de datos
$database = new Database();
$dbConnection = $database->getConnection();
/**
 * Prueba si está inicializada la base de datos
 * Código añadido por que me estaban dando fallos 
 * al inicializar :( */

 if (!$dbConnection || !$dbConnection instanceof mysqli) {
    die('Error: Conexión a la base de datos no válida.');
}


// Manejo del botón de quitar de favoritos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['removeFav'])) {
    $puuid = $_POST['puuid'];

    // Eliminar de la base de datos
    $stmt = $dbConnection->prepare("DELETE FROM summoner WHERE puuid = ? AND idUser = (SELECT id FROM users WHERE username = ?)");
    $stmt->bind_param("ss", $puuid, $_SESSION['user']);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<p>Summoner eliminado de favoritos correctamente.</p>";
    } else {
        echo "<p>Error al eliminar el summoner de favoritos.</p>";
    }
    $stmt->close();
}

/**
 * Prueba si está inicializada la base de datos
 * Código añadido por que me estaban dando fallos 
 * al inicializar :( */

 if (!$dbConnection || !$dbConnection instanceof mysqli) {
    die('Error: Conexión a la base de datos no válida.');
}


// Consulta para obtener los summoners vinculados al usuario logueado
$stmt = $dbConnection->prepare("
    SELECT s.puuid, s.username, s.tag, s.foto 
    FROM summoner s
    INNER JOIN users u ON s.idUser = u.id
    WHERE u.username = ?
");
$stmt->bind_param("s", $userLoged);
$stmt->execute();
$result = $stmt->get_result();

$summoners = [];
while ($row = $result->fetch_assoc()) {
    $summoners[] = new Summoner($row['puuid'], $row['username'], $row['tag']);
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Summoners de <?= htmlspecialchars($userLoged) ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Summoners vinculados a <?= htmlspecialchars($userLoged) ?></h1>
    <form action="main.php">
                    <button type="submit">Volver a main</button>
                </form>
    <?php if (!empty($summoners)): ?>
    <div class="summoners-container">
        <?php foreach ($summoners as $summoner): ?>
            <div class="summoner-card">
                <?php $summoner->pintarCard($dbConnection); ?>
                <!-- Botón para ver perfil completo -->
                <form method="GET" action="main.php">
                    <input type="hidden" name="puuid" value="<?= htmlspecialchars($summoner->getPuuid()) ?>">
                    <input type="hidden" name="gameName" value="<?= htmlspecialchars($summoner->getGameName()) ?>">
                    <input type="hidden" name="tag" value="<?= htmlspecialchars($summoner->getTag()) ?>">
                    <button type="submit" class="view-profile-button">Ver perfil completo</button>
                </form>
                <!-- Botón para quitar de favoritos -->
                <form method="POST" action="">
                    <input type="hidden" name="puuid" value="<?= htmlspecialchars($summoner->getPuuid()) ?>">
                    <button type="submit" name="removeFav" class="remove-fav-button">Quitar de favoritos</button>
                </form>
                
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>No hay summoners vinculados a este usuario.</p>
<?php endif; ?>
</body>
</html>
