<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../controllers/log_controller.php';

class AuthController {
    private $db;
    private $usuario;
    private $logger;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->usuario = new Usuario($this->db);
        $this->logger = new LogController();
    }

    public function login($email, $senha) {
        $user = $this->usuario->login($email, $senha);
        
        if($user) {
            session_start();
            $_SESSION['id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['nome'] = $user['nome'];
            
            // Registrar o login nos logs
            $this->logger->registrarLogin($user['id']);
            
            return true;
        }
        return false;
    }

    public function logout() {
        session_start();
        
        // Registrar o logout nos logs se o usuário estiver logado
        if(isset($_SESSION['id'])) {
            $this->logger->registrarLogout($_SESSION['id']);
        }
        
        session_destroy();
        return true;
    }

    public function isLoggedIn() {
        session_start();
        return isset($_SESSION['id']);
    }
}
?> 