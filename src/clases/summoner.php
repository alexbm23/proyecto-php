<?php

require_once "./clases/champion.php";
require_once "./clases/game.php";
require_once "./lib/fetchApi.php";
// Clase summoner
if (!class_exists('Summoner')) {
class summoner
{

   private $puuid;
   public $gameName;
   public $tag;
   public $iconoPerfil;
   public $nivelCuenta;
   public $masteryChampions;
   public $rangoSoloDuo = "No jugado";
   public $rangoFlex = "No jugado";
   public $idPartidas = [];
   public $historial = [];
   public $favourite = false;


   /**
    * Constructor de la clase summoner
    *
    * @param [type] $puuid
    * @param [type] $gameName
    * @param [type] $tag
    */
   public function __construct($puuid, $gameName, $tag)
   {
      $this->puuid = $puuid;
      $this->gameName = $gameName;
      $this->tag = $tag;
      $this->infoSummoner();
      $this->sacarChampionMastery();
      $this->setIdPartidas();
   }


   public function setRangoSoloDuo($rango)
   {
      $this->rangoSoloDuo = $rango;
   }

   public function setRangoFlex($rango)
   {
      $this->rangoFlex = $rango;
   }

   public function getPuuid()
   {
      return $this->puuid;
   }

   public function getGameName()
   {
      return $this->gameName;
   }

   public function getTag()
   {
      return $this->tag;
   }

   public function getIconoPerfil()
   {
      return $this->iconoPerfil;
   }

   public function setIconoPerfil($iconoPerfil)
   {
      $this->iconoPerfil = $iconoPerfil;
   }

   public function setNivelCuenta($nivelCuenta)
   {
      $this->nivelCuenta = $nivelCuenta;
   }

   public function setMasteryChampion($masteryChampions)
   {
      $this->masteryChampions = $masteryChampions;
   }

   public function getMasteryChampion()
   {
      return $this->masteryChampions;
   }

   public function getNivelCuenta()
   {
      return $this->nivelCuenta;
   }

   public function getRangoSoloDuo()
   {
      return $this->rangoSoloDuo;
   }

   public function getRangoFlex()
   {
      return $this->rangoFlex;
   }






   /**
    * Esta función hace un echo de la imagen del icono de perfil a través de la
    * propiedad $iconoPerfil
    * @return void
    */
   public function pintarIconoPerfil()
   {
      $urlIcono = "https://ddragon.leagueoflegends.com/cdn/14.23.1/img/profileicon/{$this->getIconoPerfil()}.png";
      echo '<img src="' . $urlIcono . '"> </img>';
   }



   /**
    * Hace un fetch a la API por cada ID de partida que contenga el array 
    * de la propiedad idPartidas, saca las propiedades necesarias para crear un
    * nuevo objeto de la clase game y lo inicializa.
    *
    * @return void
    */
   public function procesarIdPartidas()
   {
      foreach ($this->getIdPartidas() as $key) {
         $url = "https://europe.api.riotgames.com/lol/match/v5/matches/{$key}?api_key=" . TOKEN;
         $response = fetchCurl($url);
         $data = json_decode($response);

         // Validar si $data tiene contenido y la propiedad 'info' existe
         if (is_object($data) && isset($data->info)) {
            $info = $data->info;

            // INFORMACIÓN GENERAL DE LA PARTIDA
            $duracion = $info->gameDuration ?? 0;
            $modoJuego = $info->gameMode ?? 'Desconocido';
            $queueId = $info->queueId ?? 0;

            // BUSQUEDA DEL JUGADOR POR PUUID
            $participantes = $info->participants ?? [];
            $datosJugador = null;

            foreach ($participantes as $participante) {
               if ($participante->puuid == $this->getPuuid()) {
                  $datosJugador = $participante;
               }
            }

            // Validar que se encontró al jugador
            if ($datosJugador) {
               $resultado = $datosJugador->win ? "Victoria" : "Derrota";
               $campeonJugado = $datosJugador->championName == "MonkeyKing" ? "Wukong" : $datosJugador->championName;
               $rolJugado = $datosJugador->teamPosition ?? 'Sin rol';
               $kills = $datosJugador->kills ?? 0;
               $deaths = $datosJugador->deaths ?? 0;
               $assists = $datosJugador->assists ?? 0;
               $visionScore = $datosJugador->visionScore ?? 0;
               $damageDealt = $datosJugador->totalDamageDealtToChampions ?? 0;
               $cs = ($datosJugador->totalMinionsKilled ?? 0) + ($datosJugador->neutralMinionsKilled ?? 0);
               $equipoJugador = $datosJugador->teamId ?? 0;

               // Fecha del inicio de la partida
               $fecha = $info->gameStartTimestamp ?? time() * 1000;
               $fecha = round($fecha / 1000);
               $fecha = date("Y/m/d", $fecha);

               $campeonContrario = "Desconocido";
               foreach ($participantes as $participante) {
                  if ($participante->teamId !== $equipoJugador && $participante->teamPosition === $rolJugado) {
                     $campeonContrario = $participante->championName == "MonkeyKing" ? "Wukong" : $participante->championName;
                  }
               }

               // EXTRAER EL TIPO DE COLA
               $colas = [
                  420 => "Clasificatorias Solo/Dúo",
                  440 => "Clasificatorias Flex",
                  450 => "ARAM",
                  400 => "Normales (Selección)",
                  430 => "Normales (Blind Pick)",
               ];

               $tipoCola = $colas[$queueId] ?? "Cola Desconocida";
               $duracion = gmdate("H:i:s", $duracion);

               // Crear objeto Game y añadir al historial
               array_push($this->historial, new game($key, $campeonJugado, $rolJugado, $duracion, $modoJuego, $tipoCola, $resultado, $kills, $deaths, $assists, $visionScore, $damageDealt, $cs, $campeonContrario, $fecha));
            }
         } else {
            // Si no se puede procesar, loguear el error o manejarlo
            error_log("Error al procesar partida con ID: {$key}. Respuesta: " . $response);
         }
      }
   }

   /**
    * Esta función recorre todo el array de la propiedad historial
    * y utiliza la función pintarcard() de la clase game para pintar todas
    * las partidas
    *
    * @return void
    */
   public function pintarHistorial()
   {

      foreach ($this->historial as $partida) {
         $partida->pintarCard();
      }
   }




   /**
    * Hace un fetch que devuelve un array con los ID de las 20 últimas partidas Jugadas
    *
    * @return void
    */
   public function setIdPartidas()
   {

      $url = "https://europe.api.riotgames.com/lol/match/v5/matches/by-puuid/{$this->getPuuid()}/ids?start=0&count=20&api_key=" . TOKEN;

      $idPartidas = json_decode(fetchCurl($url));

      $this->idPartidas = $idPartidas;
   }

   public function getIdPartidas()
   {
      return $this->idPartidas;
   }

   /**
    * Esta funcion saca información del summoner y establece el icono de perfil
    * y el nivel de la cuenta haciendo un fetch a la API de riot
    *
    * @return void
    */
   public function infoSummoner()
   {
      $urlSumm = "https://euw1.api.riotgames.com/lol/summoner/v4/summoners/by-puuid/{$this->getPuuid()}?api_key=" . TOKEN;



      $response = fetchCurl($urlSumm);
      $data = json_decode($response, true);
      $this->setIconoPerfil($data["profileIconId"]);
      $this->setNivelCuenta($data["summonerLevel"]);



      $urlRanked = "https://euw1.api.riotgames.com/lol/league/v4/entries/by-summoner/{$data['id']}?api_key=" . TOKEN;

      $responseRanked = fetchCurl($urlRanked);
      $dataRanked = json_decode($responseRanked, true);

      foreach ($dataRanked as $entry) {
         if ($entry['queueType'] === "RANKED_SOLO_5x5") {
            $this->setRangoSoloDuo($entry['tier'] . " " . $entry['rank'] . " (" . $entry['leaguePoints'] . "LP)");
         }

         if ($entry['queueType'] === "RANKED_FLEX_SR") {
            $this->setRangoFlex($entry['tier'] . " " . $entry['rank'] . " (" . $entry['leaguePoints'] . "LP)");
         }
      }
   }
   /**
    * Hace un fetch a la api de Riot que devuelve un array con los
    * campeones con más maestría, los convierte en objeto clase champion y utiliza el constructor de la clase para instanciar el championId, el championPoints y el championLevel
    *
    * @return void
    */
   public function sacarChampionMastery()
   {



      $championMasteryCount = 5;
      $urlMastery = "https://euw1.api.riotgames.com/lol/champion-mastery/v4/champion-masteries/by-puuid/{$this->getPuuid()}/top?count={$championMasteryCount}&api_key=" . TOKEN;





      $response = fetchCurl($urlMastery);
      $data = json_decode($response);


      $maestrias = [];


      foreach ($data as $objeto) {
         array_push($maestrias, new champion($objeto->championId, $objeto->championPoints, $objeto->championLevel));
      };

      $this->setMasteryChampion($maestrias);
   }



   /**
    * Devuelve una variable que contiene información sobre los campeones con
    * más maestria del summoner
    *
    * @return void
    */
   public function stringMasteryChampion()
   {

      $respuesta = "";


      $respuesta .= "MASTERY CHAMPIONS: <br>";
      foreach ($this->getMasteryChampion() as $key) {
         $respuesta .= "NEW CHAMPION: <br>";
         $respuesta .= "ID: " . $key->getId() . "<br>";
         $respuesta .= "NOMBRE: " . $key->getNombre() . "<br>";
         $respuesta .= "MASTERY POINTS: " . $key->getMasteryPoints() . "<br>";
         $respuesta .= "MASTERY LEVEL: " . $key->getMasteryLevel() . "<br>";
         $respuesta .= $key->pintarFotoLoading() . "<br>";
      }

      return $respuesta;
   }

   /**
    * toString de la clase summoner
    *
    * @return string
    */
   public function __toString()
   {
      $respuesta = "";
      ob_start();

      $respuesta =  "GAMENAME: $this->gameName <br> TAG: $this->tag <br> ICONOPERFIL: $this->iconoPerfil <br> NIVEL CUENTA: $this->nivelCuenta <br> Rango Solo/Dúo: $this->rangoSoloDuo <br> Rango Flex: $this->rangoFlex<br>";
      $respuesta .= $this->stringMasteryChampion();




      return $respuesta;
   }

   /**
    * Esta función transforma el rango y saca el 
    * primer segmento de la propiedad rango
    * para facilitar la búsqueda de la imagen del rango
    *
    * @param [type] $rango
    * @return void
    */
   public function transformarRango($rango)
   {

      $partes = explode(" ", $rango);
      $rango = $partes[0];

      return $rango;
   }

   /**
    * Esta función muestra por pantalla la información principal del summoner en forma de card
    * También verifica si el usuario está en favoritos de
    * la cuenta logueada, hace una petición a la base de datos
    * , a la tabla summoner y si existe esa cuenta y el userId
    * pertenece al usuario logueado la variable isFavourite se pone en
    * True. A través de esta variable se controla si es o no favorita
    * @return void
    */
    public function pintarCard($dbConnection)
    {
        $puuid = $this->getPuuid();
        $currentFile = basename($_SERVER['PHP_SELF']);

        // Verificar si el summoner está en favoritos
        $stmt = $dbConnection->prepare("SELECT COUNT(*) as count FROM summoner WHERE puuid = ? AND idUser = (SELECT id FROM users WHERE username = ?)");
        $stmt->bind_param("ss", $puuid, $_SESSION['user']);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $isFavorite = $data['count'] > 0;
        $stmt->close();

        $buttonText = $isFavorite ? "Quitar de favoritos" : "Añadir a favoritos";
        $buttonClass = $isFavorite ? "favAccountButton fav-active" : "favAccountButton";

        $urlIcono = "https://ddragon.leagueoflegends.com/cdn/14.23.1/img/profileicon/{$this->getIconoPerfil()}.png";
        $urlRangoSolo = "https://opgg-static.akamaized.net/images/medals_new/{$this->transformarRango($this->getRangoSoloDuo())}.png?image=q_auto:good,f_webp,w_144&v=1729058249";
        $urlRangoFlex = "https://opgg-static.akamaized.net/images/medals_new/{$this->transformarRango($this->getRangoFlex())}.png?image=q_auto:good,f_webp,w_144&v=1729058249";

        echo '
        <div class="Summoner-Container">
            <form method="POST" action="">';

            if ($_SERVER['REQUEST_URI'] == "main.php")  {
               echo' <button class="' . $buttonClass . '" type="submit" name="toggleFav">' . $buttonText . '</button>';
           } 
            echo '
               
                <input type="hidden" name="puuid" value="' . $puuid . '">
                <input type="hidden" name="username" value="' . $this->getGameName() . '">
                <input type="hidden" name="tag" value="' . $this->getTag() . '">
                <input type="hidden" name="iconoPerfil" value="' . $this->getIconoPerfil() . '">
            </form>
            <img src="' . $urlIcono . '" class="Summoner-Icon" alt="Icono del perfil"></img>
            <div class="Summoner-Info">
                <h3>' . $this->getGameName() . '</h3> 
                <h4>#' . $this->getTag() . '</h4>
                <br>
                <br>
                <h3>SoloDuo:</h3> ' . trim($this->getRangoSoloDuo());

        if ($this->getRangoSoloDuo() != "No jugado") {
            echo ' <img src="' . $urlRangoSolo . '" class="Summoner-Rank-Icon" alt="Rango Solo"></img>';
        }

        echo '
                <br>
                <br>
                <h3>Flexible: </h3>' . trim($this->getRangoFlex());

        if (trim($this->getRangoFlex()) != "No jugado") {
            echo ' <img src="' . $urlRangoFlex . '" class="Summoner-Rank-Icon" alt="Rango Flex"></img>';
        }

        echo '    
            </div>

            <div class="Summoner-Level">' . $this->getNivelCuenta() . '</div>

            <h2 class="Summoner-Mastery-Title">Campeones con más maestria</h2>
            <div class="Summoner-Mastery-Container">
        ';

        foreach ($this->getMasteryChampion() as $champion) {
            echo '
            <div class="Summoner-Mastery-Champion" style="background-image: url(\'' . $champion->getFotoLoading() . '\'); background-size:cover; background-position:top;">
                    <span>' . $champion->getNombre() . '</span>
                    <span>' . $champion->getMasteryPoints() . 'p</span>
                </div>
            ';
        }

        echo '
            </div>
        </div>
        ';
    }
}
}