<?php



// Función para hacer un fetch con curl, se le pasa por parámetro
// la url a la que hace el fetc
function fetchCurl($url)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    curl_close($ch);

    return $response;
    
   

}
?>