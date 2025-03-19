<?php
class Database {
    private $host = "localhost";
    private $db_name = "finance_db";
    private $username = "root";
    private $password = "";
    private $port = 3306;
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $e) {
            echo "Erro na conexão: " . $e->getMessage();
        }

        return $this->conn;
    }
}
?>
