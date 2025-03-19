<?php
session_start();
require 'db.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['id'];
$mensagem = '';

// Processar formulário de limite de gastos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $limite_gastos = str_replace(',', '.', $_POST['limite_gastos'] ?? '');
    
    if (!is_numeric($limite_gastos)) {
        $mensagem = '<div class="alert alert-danger">Valor inválido para o limite de gastos.</div>';
    } else {
        $limite_gastos = (float)$limite_gastos;
        $stmt = $pdo->prepare("UPDATE usuarios SET limite_gastos = ? WHERE id = ?");
        
        if ($stmt->execute([$limite_gastos, $user_id])) {
            $mensagem = '<div class="alert alert-success">Limite de gastos atualizado com sucesso!</div>';
        } else {
            $mensagem = '<div class="alert alert-danger">Erro ao atualizar o limite de gastos.</div>';
        }
    }
}

// Obter limite atual
$stmt = $pdo->prepare("SELECT limite_gastos FROM usuarios WHERE id = ?");
$stmt->execute([$user_id]);
$limite_atual = $stmt->fetchColumn() ?? 0;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle Financeiro - Limite de Gastos</title>
    <style>
        /* Manter mesmo estilo do receitas.php */
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            margin: 0;
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background: #333;
            color: #fff;
            padding: 20px;
            height: 100vh;
        }
        
        .sidebar h2 {
            margin-top: 0;
            border-bottom: 1px solid #444;
            padding-bottom: 10px;
        }
        
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        
        .sidebar ul li {
            padding: 10px 0;
        }
        
        .sidebar ul li a {
            color: #fff;
            text-decoration: none;
            display: block;
        }
        
        .sidebar ul li a:hover {
            color: #ccc;
        }
        
        .content {
            flex-grow: 1;
            padding: 20px;
            background: #f5f5f5;
        }
        
        .card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background: #007bff;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
        
        .btn-success {
            background: #28a745;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Controle Financeiro</h2>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="receitas.php">Receitas</a></li>
            <li><a href="despesas.php">Despesas</a></li>
            <li><a href="categorias.php">Categorias</a></li>
            <li><a href="relatorios.php">Relatórios</a></li>
            <li><a href="limite_gastos.php">Limite de Gastos</a></li>
            <li><a href="logout.php">Sair</a></li>
        </ul>
    </div>
    
    <div class="content">
        <h1>Limite de Gastos</h1>
        
        <?php echo $mensagem; ?>
        
        <div class="card">
            <h2>Definir Limite Mensal</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="limite_gastos">Valor do Limite (R$)</label>
                    <input type="text" 
                        id="limite_gastos" 
                        name="limite_gastos" 
                        class="form-control" 
                        placeholder="0,00"
                        value="<?php echo number_format($limite_atual, 2, ',', '.'); ?>"
                        required>
                </div>
                
                <button type="submit" class="btn btn-success">Salvar Limite</button>
            </form>
        </div>

        <div class="card">
            <h2>Seu Limite Atual</h2>
            <p style="font-size: 1.5em; color: #28a745;">
                R$ <?php echo number_format($limite_atual, 2, ',', '.'); ?>
            </p>
        </div>
    </div>
</body>
</html>
