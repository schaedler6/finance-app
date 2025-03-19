<?php
require_once '../config/database.php';
require_once '../models/Transacao.php';
require_once '../models/Categoria.php';

class TransacaoController {
    private $db;
    private $transacao;
    private $categoria;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->transacao = new Transacao($this->db);
        $this->categoria = new Categoria($this->db);
    }

    public function create($usuario_id, $data) {
        $this->transacao->usuario_id = $usuario_id;
        $this->transacao->categoria_id = $data['categoria_id'];
        $this->transacao->descricao = $data['descricao'];
        $this->transacao->valor = str_replace(',', '.', $data['valor']);
        $this->transacao->data = $data['data'];
        $this->transacao->tipo = $data['tipo'];

        return $this->transacao->create();
    }

    public function getTransacoes($usuario_id, $tipo = null) {
        return $this->transacao->read($usuario_id, $tipo);
    }

    public function getTotal($usuario_id, $tipo) {
        return $this->transacao->getTotal($usuario_id, $tipo);
    }

    public function getCategorias($tipo = null) {
        return $this->categoria->read($tipo);
    }
}
?> 