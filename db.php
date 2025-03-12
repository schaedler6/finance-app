<?php 
$host = "localhost";
$user = "root";
$pass = "";
$db = "finance_db";
$port = 3306;

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8",$user,$pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Erro ao conectar: ".$e->getMessage());
}
?>