<?php
require_once(__DIR__ . '\..\config\database.php');
require_once(__DIR__ . '\..\models\Usuario.php');

class AuthController {
    private $db;
    private $usuario;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->usuario = new Usuario($this->db);
    }

    public function login($email, $senha) {
        $user = $this->usuario->login($email, $senha);
        
        if($user) {
            session_start();
            $_SESSION['id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['nome'] = $user['nome'];
            return true;
        }
        return false;
    }

    public function logout() {
        session_start();
        session_destroy();
        return true;
    }

    public function isLoggedIn() {
        session_start();
        return isset($_SESSION['id']);
    }
}
?> 