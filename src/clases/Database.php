<?php
class Database {
    private $connection;

    public function __construct() {
        $this->connection = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);

        if ($this->connection->connect_error) {
            die("Error de conexión: " . $this->connection->connect_error);
        }
    }

    public function getConnection() {
        return $this->connection;
    }

    public function __destruct() {
        $this->connection->close();
    }
}
?>
