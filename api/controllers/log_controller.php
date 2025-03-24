<?php
require_once '../config/database.php';
require_once '../utils/Logger.php';

class LogController {
    private $db;
    private $logger;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->logger = new Logger($this->db);
    }
    
    /**
     * Registra um login de usuário
     */
    public function registrarLogin($usuario_id) {
        return $this->logger->registrarLogin($usuario_id);
    }
    
    /**
     * Registra um logout de usuário
     */
    public function registrarLogout($usuario_id) {
        return $this->logger->registrarLogout($usuario_id);
    }
    
    /**
     * Registra criação de transação
     */
    public function registrarTransacaoCriada($usuario_id, $transacao_id, $tipo, $valor) {
        return $this->logger->registrarTransacaoCriada($usuario_id, $transacao_id, $tipo, $valor);
    }
    
    /**
     * Registra exclusão de transação
     */
    public function registrarTransacaoExcluida($usuario_id, $transacao_id, $tipo, $valor) {
        return $this->logger->registrarTransacaoExcluida($usuario_id, $transacao_id, $tipo, $valor);
    }
    
    /**
     * Registra um erro no sistema
     */
    public function registrarErro($usuario_id, $mensagem, $detalhes = null) {
        return $this->logger->registrarErro($usuario_id, $mensagem, $detalhes);
    }
    
    /**
     * Retorna os logs de um usuário
     */
    public function getLogsPorUsuario($usuario_id, $limite = 100) {
        return $this->logger->getLogsPorUsuario($usuario_id, $limite);
    }
    
    /**
     * Retorna os logs por tipo
     */
    public function getLogsPorTipo($tipo, $limite = 100) {
        return $this->logger->getLogsPorTipo($tipo, $limite);
    }
} 