<?php
require_once '../controllers/AuthController.php';
require_once '../controllers/TransacaoController.php';

header('Content-Type: application/json');

$auth = new AuthController();
$transacao = new TransacaoController();

if (!$auth->isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

$user_id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : null;
    echo json_encode(['success' => true, 'data' => $transacao->getTransacoes($user_id, $tipo)]);
}

else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['action'])) {
        switch ($data['action']) {
            case 'create':
                if (isset($data['categoria_id']) && isset($data['descricao']) && 
                    isset($data['valor']) && isset($data['data']) && isset($data['tipo'])) {
                    
                    $result = $transacao->create($user_id, $data);
                    
                    if ($result['success']) {
                        echo json_encode(['success' => true, 'message' => 'Transação criada com sucesso']);
                    } else {
                        http_response_code(400);
                        if (isset($result['errors'])) {
                            echo json_encode([
                                'success' => false, 
                                'message' => 'Erro de validação', 
                                'errors' => $result['errors']
                            ]);
                        } else {
                            echo json_encode(['success' => false, 'message' => 'Erro ao criar transação']);
                        }
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
                }
                break;
                
            case 'totals':
                $receitas = $transacao->getTotal($user_id, 'receita');
                $despesas = $transacao->getTotal($user_id, 'despesa');
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'receitas' => $receitas,
                        'despesas' => $despesas,
                        'saldo' => $receitas - $despesas
                    ]
                ]);
                break;
                
            case 'categorias':
                $tipo = isset($data['tipo']) ? $data['tipo'] : null;
                echo json_encode(['success' => true, 'data' => $transacao->getCategorias($tipo)]);
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