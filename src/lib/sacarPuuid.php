<?php
require_once "./lib/constants.php";
require_once "./clases/Summoner.php";

/**
 * HACE EL PRIMER FETCH, COMPRUEBA SI EXISTE EL SUMMONER, SI EXISTE
 * INICIALIZA UN OBJETO DE CLASE SUMMONER, SI NO DEVUELVE "CUENTA NO ENCONTRADA"
 * Hace 3 retries ya que hay veces que riot no deja hacer peticiones
 * ya que hay un máximo de peticiones por tiempo
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
   
    error_log("Error: No se pudo obtener una respuesta válida de la API tras $maxRetries intentos.");
    $summoner = null;
} else {
    
    $data = json_decode($response, true);

    
    if (isset($data['puuid'])) {
        $summoner = new Summoner($data["puuid"], $data["gameName"], $data["tagLine"]);
    } else {
        error_log("Error: Respuesta inválida de la API. Datos recibidos: " . $response);
        $summoner = null;
    }
}
?>
