<?php
class Lembrete {
    private $conn;
    private $table_name = "lembretes";

    public $id;
    public $usuario_id;
    public $descricao;
    public $valor;
    public $data_vencimento;
    public $status; // 'pendente', 'pago', 'atrasado'
    public $data_criacao;

    public function __construct($db) {
        $this->conn = $db;
        $this->criarTabelaSeNaoExistir();
    }
    
    private function criarTabelaSeNaoExistir() {
        $query = "CREATE TABLE IF NOT EXISTS " . $this->table_name . " (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT(11) NOT NULL,
            descricao VARCHAR(255) NOT NULL,
            valor DECIMAL(10,2) NOT NULL,
            data_vencimento DATE NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'pendente',
            data_criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
        )";
        
        $this->conn->exec($query);
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                 (usuario_id, descricao, valor, data_vencimento, status) 
                 VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            $this->usuario_id,
            $this->descricao,
            $this->valor,
            $this->data_vencimento,
            $this->status ?? 'pendente'
        ]);
    }
    
    public function read($usuario_id, $status = null) {
        $query = "SELECT * FROM " . $this->table_name . " 
                 WHERE usuario_id = ?";
        
        if($status) {
            $query .= " AND status = ?";
        }
        
        $query .= " ORDER BY data_vencimento";
        
        $stmt = $this->conn->prepare($query);
        
        if($status) {
            $stmt->execute([$usuario_id, $status]);
        } else {
            $stmt->execute([$usuario_id]);
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                 SET descricao = ?, valor = ?, data_vencimento = ?, status = ? 
                 WHERE id = ? AND usuario_id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            $this->descricao,
            $this->valor,
            $this->data_vencimento,
            $this->status,
            $this->id,
            $this->usuario_id
        ]);
    }
    
    public function delete($id, $usuario_id) {
        $query = "DELETE FROM " . $this->table_name . " 
                 WHERE id = ? AND usuario_id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([$id, $usuario_id]);
    }
    
    public function getById($id, $usuario_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                 WHERE id = ? AND usuario_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id, $usuario_id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getLembretesProximos($usuario_id, $dias = 7) {
        $query = "SELECT * FROM " . $this->table_name . " 
                 WHERE usuario_id = ? AND status = 'pendente' 
                 AND data_vencimento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY) 
                 ORDER BY data_vencimento";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$usuario_id, $dias]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getLembretesAtrasados($usuario_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                 WHERE usuario_id = ? AND status = 'pendente' 
                 AND data_vencimento < CURDATE() 
                 ORDER BY data_vencimento";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$usuario_id]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function marcarComoPago($id, $usuario_id) {
        $query = "UPDATE " . $this->table_name . " 
                 SET status = 'pago' 
                 WHERE id = ? AND usuario_id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([$id, $usuario_id]);
    }
    
    public function atualizarStatusAtrasados($usuario_id) {
        $query = "UPDATE " . $this->table_name . " 
                 SET status = 'atrasado' 
                 WHERE usuario_id = ? AND status = 'pendente' 
                 AND data_vencimento < CURDATE()";
        
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([$usuario_id]);
    }
}
?> 