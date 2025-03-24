<?php
/**
 * Logger.php
 * Classe responsável por registrar ações importantes do sistema
 */
class Logger {
    private $conn;
    private $table_name = "logs";

    public function __construct($db) {
        $this->conn = $db;
        $this->criarTabelaSeNaoExistir();
    }
    
    /**
     * Cria a tabela de logs caso não exista
     */
    private function criarTabelaSeNaoExistir() {
        $query = "CREATE TABLE IF NOT EXISTS " . $this->table_name . " (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT(11),
            tipo VARCHAR(50) NOT NULL,
            descricao TEXT NOT NULL,
            data_hora DATETIME NOT NULL,
            ip VARCHAR(45),
            user_agent TEXT,
            detalhes TEXT
        )";
        
        $this->conn->exec($query);
    }
    
    /**
     * Registra uma ação no sistema
     * 
     * @param int $usuario_id ID do usuário que realizou a ação
     * @param string $tipo Tipo de ação (login, logout, criar, editar, excluir)
     * @param string $descricao Descrição da ação
     * @param array $detalhes Detalhes adicionais da ação (opcional)
     * @return bool Sucesso ou falha
     */
    public function registrar($usuario_id, $tipo, $descricao, $detalhes = null) {
        $query = "INSERT INTO " . $this->table_name . " 
                 (usuario_id, tipo, descricao, data_hora, ip, user_agent, detalhes) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        
        $data_hora = date('Y-m-d H:i:s');
        $ip = $this->getIpCliente();
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $detalhes_json = $detalhes ? json_encode($detalhes) : null;
        
        return $stmt->execute([
            $usuario_id,
            $tipo,
            $descricao,
            $data_hora,
            $ip,
            $user_agent,
            $detalhes_json
        ]);
    }
    
    /**
     * Registra um login de usuário
     */
    public function registrarLogin($usuario_id) {
        return $this->registrar($usuario_id, 'login', 'Usuário fez login no sistema');
    }
    
    /**
     * Registra um logout de usuário
     */
    public function registrarLogout($usuario_id) {
        return $this->registrar($usuario_id, 'logout', 'Usuário fez logout do sistema');
    }
    
    /**
     * Registra uma criação de transação
     */
    public function registrarTransacaoCriada($usuario_id, $transacao_id, $tipo, $valor) {
        $descricao = "Usuário registrou uma nova " . strtolower($tipo) . " no valor de R$ " . number_format($valor, 2, ',', '.');
        $detalhes = [
            'transacao_id' => $transacao_id,
            'tipo' => $tipo,
            'valor' => $valor
        ];
        
        return $this->registrar($usuario_id, 'criar_' . strtolower($tipo), $descricao, $detalhes);
    }
    
    /**
     * Registra uma exclusão de transação
     */
    public function registrarTransacaoExcluida($usuario_id, $transacao_id, $tipo, $valor) {
        $descricao = "Usuário excluiu uma " . strtolower($tipo) . " no valor de R$ " . number_format($valor, 2, ',', '.');
        $detalhes = [
            'transacao_id' => $transacao_id,
            'tipo' => $tipo,
            'valor' => $valor
        ];
        
        return $this->registrar($usuario_id, 'excluir_' . strtolower($tipo), $descricao, $detalhes);
    }
    
    /**
     * Registra um erro no sistema
     */
    public function registrarErro($usuario_id, $mensagem, $detalhes = null) {
        return $this->registrar($usuario_id, 'erro', $mensagem, $detalhes);
    }
    
    /**
     * Retorna os logs de um usuário
     */
    public function getLogsPorUsuario($usuario_id, $limite = 100) {
        $query = "SELECT * FROM " . $this->table_name . " 
                 WHERE usuario_id = ? 
                 ORDER BY data_hora DESC 
                 LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$usuario_id, $limite]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Retorna os logs por tipo
     */
    public function getLogsPorTipo($tipo, $limite = 100) {
        $query = "SELECT * FROM " . $this->table_name . " 
                 WHERE tipo = ? 
                 ORDER BY data_hora DESC 
                 LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$tipo, $limite]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Retorna o IP do cliente
     */
    private function getIpCliente() {
        $ip = '';
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        return $ip;
    }
} 