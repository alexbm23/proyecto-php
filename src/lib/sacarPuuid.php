<?php
require_once "./lib/constants.php";
require_once "./clases/Summoner.php";

/**
 * HACE EL PRIMER FETCH, COMPRUEBA SI EXISTE EL SUMMONER, SI EXISTE
 * INICIALIZA UN OBJETO DE CLASE SUMMONER, SI NO DEVUELVE "CUENTA NO ENCONTRADA"
 */
$urlAcc = "https://europe.api.riotgames.com/riot/account/v1/accounts/by-riot-id/{$gameName}/{$tag}?api_key=" . TOKEN;

// Número máximo de reintentos
$maxRetries = 3;
$retry = 0;
$response = false;

do {
    $response = fetchCurl($urlAcc);
    if ($response === false) {
        error_log("Error: No se pudo conectar con la API en el intento $retry.");
        $retry++;
        sleep(1); // Esperar 1 segundo antes de reintentar
    } else {
        break; // Si la respuesta es válida, salir del bucle
    }
} while ($retry < $maxRetries);

if ($response === false) {
    // Después de los reintentos, si no hay respuesta válida, registrar error
    error_log("Error: No se pudo obtener una respuesta válida de la API tras $maxRetries intentos.");
    $summoner = null;
} else {
    // Decodificamos la respuesta JSON
    $data = json_decode($response, true);

    // Validamos si la respuesta contiene el campo 'puuid'
    if (isset($data['puuid'])) {
        $summoner = new Summoner($data["puuid"], $data["gameName"], $data["tagLine"]);
    } else {
        error_log("Error: Respuesta inválida de la API. Datos recibidos: " . $response);
        $summoner = null;
    }
}
?>
