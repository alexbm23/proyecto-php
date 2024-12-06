<?php

/**
 * La clase Login controla el Login a mi página, guardando
 * en sesión el username. Además de controlar el Login también
 * controla el register
 */
class Login {
    private $db;

    /**
     * Constructor de la clase Login, recibe por parámetro $db
     * el cual usa para hacer todas las consultas a la 
     * base de datos.
     *
     * @param [type] $dbConnection
     */
    public function __construct($dbConnection) {
        $this->db = $dbConnection;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }


    /**
     * Lo primero que hace es llamar a la función userExist con el 
     * username para comprobar si ya existe, en ese caso devuelve
     * que el usuario ya existe. Si no existe el usuario, hashea la contraseña
     * y hace un insert a la tabla users con el nuevo usuario y la contraseña,
     * si esto sale bien el registro sería exitoso
     *
     * @param [type] $username
     * @param [type] $password
     * @return void
     */
    public function register($username, $password) {
        if ($this->userExists($username)) {
            return "El usuario ya existe.";
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $username, $hashedPassword);

        if ($stmt->execute()) {
            return "Registro exitoso.";
        } else {
            return "Error al registrar el usuario: " . $stmt->error;
        }
    }

    /**
     * Esta es la función principal del Login, hace una petición
     * a la base de datos, en la que se trae toda la información
     * del usuario con el mismo id que $username pasado por el 
     * parámetro de la función, si la sentencia obtiene 1 solo
     * resultado esta lo almacena en resultado como un array
     * asociativo y establece $_SESSION['user'] con el valor
     * del usuario traido con la base de datos en caso de que la
     * contraseña sea correcta
     *
     * @param [type] $username
     * @param [type] $password
     * @return void
     */
    public function login($username, $password) {
        $query = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user['username'];
                return true;
            }
        }
        return false;
    }

    /**
     * Esta función devuelve True si existe el $_SESSION[user],
     * para verificar que está logueado, si no devuelve false
     *
     * @return boolean
     */
    public function isAuthenticated() {
        return isset($_SESSION['user']);
    }


    /**
     * Esta función le hace unset a la sesión y la destruye,
     * así eliminando de $_SESSION el usuario logueado
     *
     * @return void
     */
    public function logout() {
        session_unset();
        session_destroy();
    }


    /**
     * Esta función devuelve True si el usuario ya existe en
     * la base de datos, si no existe devuelve False.
     * Comprueba en la base de datos haciendo una sentencia que 
     * devuelve los useres que tenga el mismo id que el $username
     * que se le pase por parámetro a la función
     *
     * @param [type] $username
     * @return void
     */
    private function userExists($username) {
        $query = "SELECT id FROM users WHERE username = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }
}
?>
