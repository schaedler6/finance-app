<?php
require_once '../config/database.php';
require_once '../models/Lembrete.php';
require_once '../utils/Validator.php';
require_once '../controllers/log_controller.php';

class LembreteController {
    private $db;
    private $lembrete;
    private $logger;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->lembrete = new Lembrete($this->db);
        $this->logger = new LogController();
    }

    public function create($usuario_id, $data) {
        // Sanitiza e valida os dados recebidos
        $data = Validator::sanitizar($data);
        $erros = $this->validarLembrete($data);
        
        // Se houver erros, retorna falso
        if (!empty($erros)) {
            return ['success' => false, 'errors' => $erros];
        }
        
        $this->lembrete->usuario_id = $usuario_id;
        $this->lembrete->descricao = $data['descricao'];
        $this->lembrete->valor = Validator::formatarMoeda($data['valor']);
        $this->lembrete->data_vencimento = $data['data_vencimento'];
        $this->lembrete->status = $data['status'] ?? 'pendente';

        $resultado = $this->lembrete->create();
        
        // Registrar a criação do lembrete nos logs
        if ($resultado) {
            $lembrete_id = $this->db->lastInsertId();
            $this->logger->registrarErro(
                $usuario_id,
                "Lembrete de conta criado: {$data['descricao']}",
                ['lembrete_id' => $lembrete_id, 'valor' => $this->lembrete->valor]
            );
        }
        
        return ['success' => $resultado];
    }
    
    public function update($id, $usuario_id, $data) {
        // Sanitiza e valida os dados recebidos
        $data = Validator::sanitizar($data);
        $erros = $this->validarLembrete($data);
        
        // Se houver erros, retorna falso
        if (!empty($erros)) {
            return ['success' => false, 'errors' => $erros];
        }
        
        // Verifica se o lembrete existe e pertence ao usuário
        $lembrete_atual = $this->lembrete->getById($id, $usuario_id);
        if (!$lembrete_atual) {
            return ['success' => false, 'message' => 'Lembrete não encontrado'];
        }
        
        $this->lembrete->id = $id;
        $this->lembrete->usuario_id = $usuario_id;
        $this->lembrete->descricao = $data['descricao'];
        $this->lembrete->valor = Validator::formatarMoeda($data['valor']);
        $this->lembrete->data_vencimento = $data['data_vencimento'];
        $this->lembrete->status = $data['status'] ?? $lembrete_atual['status'];

        $resultado = $this->lembrete->update();
        
        // Registrar a atualização do lembrete nos logs
        if ($resultado) {
            $this->logger->registrarErro(
                $usuario_id,
                "Lembrete de conta atualizado: {$data['descricao']}",
                ['lembrete_id' => $id, 'valor' => $this->lembrete->valor]
            );
        }
        
        return ['success' => $resultado];
    }
    
    public function delete($id, $usuario_id) {
        // Verifica se o lembrete existe e pertence ao usuário
        $lembrete = $this->lembrete->getById($id, $usuario_id);
        if (!$lembrete) {
            return ['success' => false, 'message' => 'Lembrete não encontrado'];
        }
        
        $resultado = $this->lembrete->delete($id, $usuario_id);
        
        // Registrar a exclusão do lembrete nos logs
        if ($resultado) {
            $this->logger->registrarErro(
                $usuario_id,
                "Lembrete de conta excluído: {$lembrete['descricao']}",
                ['lembrete_id' => $id, 'valor' => $lembrete['valor']]
            );
        }
        
        return ['success' => $resultado];
    }
    
    public function getLembretes($usuario_id, $status = null) {
        // Atualiza status de lembretes atrasados
        $this->lembrete->atualizarStatusAtrasados($usuario_id);
        
        return $this->lembrete->read($usuario_id, $status);
    }
    
    public function getLembretesProximos($usuario_id, $dias = 7) {
        // Atualiza status de lembretes atrasados
        $this->lembrete->atualizarStatusAtrasados($usuario_id);
        
        return $this->lembrete->getLembretesProximos($usuario_id, $dias);
    }
    
    public function getLembretesAtrasados($usuario_id) {
        // Atualiza status de lembretes atrasados
        $this->lembrete->atualizarStatusAtrasados($usuario_id);
        
        return $this->lembrete->getLembretesAtrasados($usuario_id);
    }
    
    public function marcarComoPago($id, $usuario_id) {
        // Verifica se o lembrete existe e pertence ao usuário
        $lembrete = $this->lembrete->getById($id, $usuario_id);
        if (!$lembrete) {
            return ['success' => false, 'message' => 'Lembrete não encontrado'];
        }
        
        $resultado = $this->lembrete->marcarComoPago($id, $usuario_id);
        
        // Registrar a marcação como pago nos logs
        if ($resultado) {
            $this->logger->registrarErro(
                $usuario_id,
                "Lembrete de conta marcado como pago: {$lembrete['descricao']}",
                ['lembrete_id' => $id, 'valor' => $lembrete['valor']]
            );
        }
        
        return ['success' => $resultado];
    }
    
    private function validarLembrete($dados) {
        $erros = [];
        
        // Valida descrição
        if (!isset($dados['descricao']) || !Validator::validarTexto($dados['descricao'], 3, 255)) {
            $erros['descricao'] = 'A descrição deve ter entre 3 e 255 caracteres';
        }
        
        // Valida valor
        if (!isset($dados['valor']) || !Validator::validarMoeda($dados['valor'])) {
            $erros['valor'] = 'Valor inválido. Use o formato 0,00 ou 0.00';
        }
        
        // Valida data de vencimento
        if (!isset($dados['data_vencimento']) || !Validator::validarData($dados['data_vencimento'])) {
            $erros['data_vencimento'] = 'Data de vencimento inválida. Use o formato YYYY-MM-DD';
        }
        
        // Valida status (se fornecido)
        if (isset($dados['status']) && !in_array($dados['status'], ['pendente', 'pago', 'atrasado'])) {
            $erros['status'] = 'Status inválido. Deve ser "pendente", "pago" ou "atrasado"';
        }
        
        return $erros;
    }
}
?> 