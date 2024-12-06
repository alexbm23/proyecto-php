<?php

require_once './clases/Database.php';

/**
 * Esta clase se utiliza para controlar la versión de la tabla
 * champions de mi base de datos, si esta no está a la misma
 * vesión que la última del juego, se actualiza
 */

class version
{


    public $ultimaVersion;
    public $versionBBDD;
    public $tablaVacia;


    public function inicializarVersion()
    {
        $this->setVersionBBDD();
        $this->setUltimaVersion();
        $this->comprobarVersionBBDD();
        $this->comprobarChampionsTable();
    }



    /**
     * Esta funcion actualiza la propiedad $versionBBDD
     * Hace un select de la tabla api_version de la base de datos y 
     * guarda el valor de esa fila en la propiedad $versionBBDD
     *
     * @return void
     */
    public function setVersionBBDD()
    {
        // SACAR VERSIÓN DEL JUEGO DESDE LA BASE DE DATOS

        $database = new Database();
        $conn = $database->getConnection();
        if (!$conn || !$conn instanceof mysqli) {
            die('Error: Conexión a la base de datos no válida.');
        }


        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Fetch the API version
        $sql = "SELECT version FROM api_version";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $this->versionBBDD = $row["version"];
    }

    /**
     * Inicializa la propiedad $ultimaVersion
     * Hace un fetch a la api de versiones y saca el primer elemento del array
     * guarda el valor de este elemento en la propiedad $ultimaVersion
     *
     * @return void
     */
    public function setUltimaVersion()
    {

        $urlVersion = "https://ddragon.leagueoflegends.com/api/versions.json";

        $this->ultimaVersion = json_decode(fetchCurl($urlVersion))[0];
    }

    /**
     * Comprueba que la version de la BBDD y la última version sean las mismas,
     * si es así devuelve un echo de que está actualizada, si no es así
     * actualiza la versión en la base de datos y actualiza la tabla champions
     *
     * @return void
     */
    public function comprobarVersionBBDD()
    {

        if ($this->ultimaVersion != $this->versionBBDD) {


            $sql = "UPDATE api_version SET version = ?, last_update = NOW() WHERE id = 1";



            $database = new Database();
            $conn = $database->getConnection();
            if (!$conn || !$conn instanceof mysqli) {
                die('Error: Conexión a la base de datos no válida.');
            }            // Preparar el statement
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $this->ultimaVersion);

            // Ejecutar el statement
            if ($stmt->execute()) {

                $this->actualizarTablaChampions();
                $this->setVersionBBDD();
            }
        }
    }






    /**
     * Esta funcion comprueba si en la tabla champions de mi Base de Datos
     * existe algún valor, si no existe ningún valor se ejecuta la funcion 
     * actualizarTablaChampions()
     *
     * @return void
     */
    public function comprobarChampionsTable()
    {

        $database = new Database();
        $conn = $database->getConnection();
        if (!$conn || !$conn instanceof mysqli) {
            die('Error: Conexión a la base de datos no válida.');
        }
        // Preparar la consulta
        $sql = "SELECT EXISTS (SELECT 1 FROM champions)";
        $result = $conn->query($sql);

        // Evaluar el resultado
        if ($result->num_rows == 0) {
            $this->actualizarTablaChampions();
        }
    }

    /**
     * Esta funcion actualiza la tabla champions de mi BBDD
     * Borra todos los datos de la tabla y a continuación filtra un JSON
     * con todos los campeones, saca el campo "id" y "key" de cada uno y 
     * hace el insert en la tabla
     *
     * @return void
     */
    public function actualizarTablaChampions()
    {

        $database = new Database();
        $conn = $database->getConnection();
        if (!$conn || !$conn instanceof mysqli) {
            die('Error: Conexión a la base de datos no válida.');
        }

        // Comprobar conexión
        if ($conn->connect_error) {
            die("Error de conexión: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("TRUNCATE TABLE champions");
        $stmt->execute();

        // Obtener los datos de la API
        $url = "https://ddragon.leagueoflegends.com/cdn/{$this->ultimaVersion}/data/es_ES/champion.json";
        $data = file_get_contents($url);
        $champions = json_decode($data, true);

        // Preparar la sentencia INSERT (corregida)
        $sql = "INSERT INTO champions (id, `key`) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);



        //Insertar cada campeón
        foreach ($champions['data'] as $champion) {
            $id = $champion['id'];
            $key = $champion['key'];
            $stmt->bind_param("ss", $id, $key);
            $stmt->execute();
        }
    }
}
