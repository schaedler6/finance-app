<?php
$host = "localhost";
$db_name = "finance_db";
$username = "root";
$password = "";
$port = 3306;

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$db_name",
        $username,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("set names utf8");
} catch(PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
    exit;
}
?> 