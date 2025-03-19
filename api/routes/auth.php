<?php
require_once '../controllers/AuthController.php';

header('Content-Type: application/json');

$auth = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['action'])) {
        switch ($data['action']) {
            case 'login':
                if (isset($data['email']) && isset($data['senha'])) {
                    if ($auth->login($data['email'], $data['senha'])) {
                        echo json_encode(['success' => true, 'message' => 'Login realizado com sucesso']);
                    } else {
                        http_response_code(401);
                        echo json_encode(['success' => false, 'message' => 'Email ou senha inválidos']);
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Email e senha são obrigatórios']);
                }
                break;
                
            case 'logout':
                if ($auth->logout()) {
                    echo json_encode(['success' => true, 'message' => 'Logout realizado com sucesso']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Erro ao fazer logout']);
                }
                break;
                
            case 'check':
                echo json_encode(['success' => true, 'logged_in' => $auth->isLoggedIn()]);
                break;
                
            default:
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Ação inválida']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ação não especificada']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
}
?> 