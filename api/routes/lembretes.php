<?php
require_once '../controllers/AuthController.php';
require_once '../controllers/LembreteController.php';

header('Content-Type: application/json');

$auth = new AuthController();
$lembrete = new LembreteController();

if (!$auth->isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

$user_id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $status = isset($_GET['status']) ? $_GET['status'] : null;
    $proximos = isset($_GET['proximos']) && $_GET['proximos'] === 'true';
    $atrasados = isset($_GET['atrasados']) && $_GET['atrasados'] === 'true';
    $dias = isset($_GET['dias']) ? intval($_GET['dias']) : 7;
    
    if ($proximos) {
        echo json_encode(['success' => true, 'data' => $lembrete->getLembretesProximos($user_id, $dias)]);
    } else if ($atrasados) {
        echo json_encode(['success' => true, 'data' => $lembrete->getLembretesAtrasados($user_id)]);
    } else {
        echo json_encode(['success' => true, 'data' => $lembrete->getLembretes($user_id, $status)]);
    }
}

else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['action'])) {
        switch ($data['action']) {
            case 'create':
                $result = $lembrete->create($user_id, $data);
                
                if ($result['success']) {
                    echo json_encode(['success' => true, 'message' => 'Lembrete criado com sucesso']);
                } else {
                    http_response_code(400);
                    if (isset($result['errors'])) {
                        echo json_encode([
                            'success' => false, 
                            'message' => 'Erro de validação', 
                            'errors' => $result['errors']
                        ]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Erro ao criar lembrete']);
                    }
                }
                break;
                
            case 'update':
                if (!isset($data['id'])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'ID do lembrete não fornecido']);
                    break;
                }
                
                $result = $lembrete->update($data['id'], $user_id, $data);
                
                if ($result['success']) {
                    echo json_encode(['success' => true, 'message' => 'Lembrete atualizado com sucesso']);
                } else {
                    http_response_code(400);
                    if (isset($result['errors'])) {
                        echo json_encode([
                            'success' => false, 
                            'message' => 'Erro de validação', 
                            'errors' => $result['errors']
                        ]);
                    } else {
                        echo json_encode(['success' => false, 'message' => $result['message'] ?? 'Erro ao atualizar lembrete']);
                    }
                }
                break;
                
            case 'delete':
                if (!isset($data['id'])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'ID do lembrete não fornecido']);
                    break;
                }
                
                $result = $lembrete->delete($data['id'], $user_id);
                
                if ($result['success']) {
                    echo json_encode(['success' => true, 'message' => 'Lembrete excluído com sucesso']);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => $result['message'] ?? 'Erro ao excluir lembrete']);
                }
                break;
                
            case 'marcar_como_pago':
                if (!isset($data['id'])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'ID do lembrete não fornecido']);
                    break;
                }
                
                $result = $lembrete->marcarComoPago($data['id'], $user_id);
                
                if ($result['success']) {
                    echo json_encode(['success' => true, 'message' => 'Lembrete marcado como pago com sucesso']);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => $result['message'] ?? 'Erro ao marcar lembrete como pago']);
                }
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