<?php
class Transacao {
    private $conn;
    private $table_name = "transacoes";

    public $id;
    public $usuario_id;
    public $categoria_id;
    public $descricao;
    public $valor;
    public $data;
    public $tipo;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                 (usuario_id, categoria_id, descricao, valor, data, tipo) 
                 VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            $this->usuario_id,
            $this->categoria_id,
            $this->descricao,
            $this->valor,
            $this->data,
            $this->tipo
        ]);
    }

    public function read($usuario_id, $tipo = null) {
        $query = "SELECT t.*, c.nome as categoria, c.cor 
                 FROM " . $this->table_name . " t
                 JOIN categorias c ON t.categoria_id = c.id
                 WHERE t.usuario_id = ?";
        
        if($tipo) {
            $query .= " AND t.tipo = ?";
        }
        
        $query .= " ORDER BY t.data DESC";
        
        $stmt = $this->conn->prepare($query);
        
        if($tipo) {
            $stmt->execute([$usuario_id, $tipo]);
        } else {
            $stmt->execute([$usuario_id]);
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotal($usuario_id, $tipo) {
        $query = "SELECT SUM(valor) as total 
                 FROM " . $this->table_name . " 
                 WHERE usuario_id = ? AND tipo = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$usuario_id, $tipo]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total'] ?? 0;
    }
}
?> 