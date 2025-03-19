<?php
class Categoria {
    private $conn;
    private $table_name = "categorias";

    public $id;
    public $nome;
    public $tipo;
    public $cor;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read($tipo = null) {
        $query = "SELECT * FROM " . $this->table_name;
        
        if($tipo) {
            $query .= " WHERE tipo = ?";
        }
        
        $query .= " ORDER BY nome";
        
        $stmt = $this->conn->prepare($query);
        
        if($tipo) {
            $stmt->execute([$tipo]);
        } else {
            $stmt->execute();
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (nome, tipo, cor) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$this->nome, $this->tipo, $this->cor]);
    }
}
?> 