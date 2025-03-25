<?php
require_once '../config/database.php';
require_once '../models/Transacao.php';
require_once '../models/Categoria.php';
require_once '../utils/Validator.php';
require_once '../controllers/log_controller.php';

class TransacaoController {
    private $db;
    private $transacao;
    private $categoria;
    private $logger;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->transacao = new Transacao($this->db);
        $this->categoria = new Categoria($this->db);
        $this->logger = new LogController();
    }

    public function create($usuario_id, $data) {
        // Sanitiza e valida os dados recebidos
        $data = Validator::sanitizar($data);
        $erros = Validator::validarTransacao($data);
        
        // Se houver erros, retorna falso
        if (!empty($erros)) {
            return ['success' => false, 'errors' => $erros];
        }
        
        $this->transacao->usuario_id = $usuario_id;
        $this->transacao->categoria_id = $data['categoria_id'];
        $this->transacao->descricao = $data['descricao'];
        $this->transacao->valor = Validator::formatarMoeda($data['valor']);
        $this->transacao->data = $data['data'];
        $this->transacao->tipo = $data['tipo'];

        $resultado = $this->transacao->create();
        
        // Registrar a criação da transação nos logs
        if ($resultado) {
            $transacao_id = $this->db->lastInsertId();
            $this->logger->registrarTransacaoCriada(
                $usuario_id,
                $transacao_id,
                $data['tipo'],
                $this->transacao->valor
            );
        }
        
        return ['success' => $resultado];
    }
    
    public function delete($id, $usuario_id) {
        // Buscar a transação antes de excluir para registrar no log
        $transacao = $this->transacao->getById($id, $usuario_id);
        
        if (!$transacao) {
            return false;
        }
        
        $resultado = $this->transacao->delete($id, $usuario_id);
        
        // Registrar a exclusão da transação nos logs
        if ($resultado) {
            $this->logger->registrarTransacaoExcluida(
                $usuario_id,
                $id,
                $transacao['tipo'],
                $transacao['valor']
            );
        }
        
        return $resultado;
    }

    public function getTransacoes($usuario_id, $tipo = null) {
        // Valida o tipo se fornecido
        if ($tipo && !in_array($tipo, ['receita', 'despesa'])) {
            return [];
        }
        
        return $this->transacao->read($usuario_id, $tipo);
    }

    public function getTotal($usuario_id, $tipo) {
        // Valida o tipo
        if (!in_array($tipo, ['receita', 'despesa'])) {
            return 0;
        }
        
        return $this->transacao->getTotal($usuario_id, $tipo);
    }

    public function getCategorias($tipo = null) {
        // Valida o tipo se fornecido
        if ($tipo && !in_array($tipo, ['receita', 'despesa'])) {
            return [];
        }
        
        return $this->categoria->read($tipo);
    }
}
?> 