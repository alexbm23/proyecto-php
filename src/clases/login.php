<?php
class Login {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

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

    public function isAuthenticated() {
        return isset($_SESSION['user']);
    }

    public function logout() {
        session_unset();
        session_destroy();
    }

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
