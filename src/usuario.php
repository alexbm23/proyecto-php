<?php

require_once "./clases/Summoner.php";

if (!empty($_GET)) {
    $gameName = str_replace(" ", "%20", htmlspecialchars($_GET['gameName']));
    $tag = htmlspecialchars($_GET['tag']);

    // Validar que no estén vacíos
    if (empty($gameName) || empty($tag)) {
        echo "<h2>Error: Nombre de usuario o tag vacío.</h2>";
        exit;
    }

    require_once "./lib/sacarPuuid.php";

    // Inicializamos el summoner con sacarPuuid.php
    if (isset($summoner) && $summoner instanceof Summoner) {
        echo "<h2>Detalles del Usuario:</h2>";
        $summoner->pintarCard($dbConnection);
        echo "<br><br>";
        $summoner->procesarIdPartidas();
        echo '<div class="History-Container ">';
        $summoner->pintarHistorial();
        echo '</div>';
    } else {
        echo "<h2>Cuenta no encontrada. Verifique los datos ingresados o intente nuevamente más tarde.</h2>";
    }
} else {
    echo "<h2>No se ha proporcionado ningún usuario</h2>";
}
?>
