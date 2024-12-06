<?php
/**
 * La clase Database construye un objeto mysqli con la 
 * información de mi base de datos
 */
class Database {
    private mysqli $connection;


    /**
     * Constructor de la clase Database, esta inicializa un
     * objeto de la clase mysqli con las contantes que 
     * contienen la información de mi base de datos.
     * Intenta hacer la conexión, si no conecta salta un error.
     */
    public function __construct() {
        $this->connection = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);

        if ($this->connection->connect_error) {
            die("Error de conexión: " . $this->connection->connect_error);
        }
    }

    /**
     * Getter de $conection
     *
     * @return void
     */
    public function getConnection():mysqli {
        return $this->connection;
    }

    /**
     * Funcion para cerrar la conexión
     */
    public function __destruct() {
        $this->connection->close();
    }
}
?>
