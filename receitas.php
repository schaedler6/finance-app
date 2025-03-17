<?php
session_start();
require 'db.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['id'];
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $categoria_id = $_POST['categoria_id'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $valor = str_replace(',', '.', $_POST['valor'] ?? '');
    $data = $_POST['data'] ?? '';
    

    if (empty($categoria_id) || empty($descricao) || empty($valor) || empty($data)) {
        $mensagem = '<div class="alert alert-danger">Preencha todos os campos obrigatórios.</div>';
    } else {
    
        $stmt = $pdo->prepare("
            INSERT INTO transacoes (usuario_id, categoria_id, descricao, valor, data, tipo)
            VALUES (?, ?, ?, ?, ?, 'receita')
        ");
        
        if ($stmt->execute([$user_id, $categoria_id, $descricao, $valor, $data])) {
            $mensagem = '<div class="alert alert-success">Receita cadastrada com sucesso!</div>';
        } else {
            $mensagem = '<div class="alert alert-danger">Erro ao cadastrar receita. Tente novamente.</div>';
        }
    }
}

$stmt = $pdo->prepare("SELECT id, nome FROM categorias WHERE tipo = 'receita'");
$stmt->execute();
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    SELECT t.*, c.nome as categoria, c.cor
    FROM transacoes t
    JOIN categorias c ON t.categoria_id = c.id
    WHERE t.usuario_id = ? AND t.tipo = 'receita'
    ORDER BY t.data DESC
");
$stmt->execute([$user_id]);
$receitas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle Financeiro - Receitas</title>
    <style>
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
        
        .btn-danger {
            background: #dc3545;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        table th {
            background: #f5f5f5;
        }
        
        .receitas { color: #28a745; }
        
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
        
        .categoria-indicador {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 5px;
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
            <li><a href="logout.php">Sair</a></li>
        </ul>
    </div>
    
    <div class="content">
        <h1>Gerenciar Receitas</h1>
        
        <?php echo $mensagem; ?>
        
        <div class="card">
            <h2>Nova Receita</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="categoria_id">Categoria</label>
                    <select id="categoria_id" name="categoria_id" class="form-control" required>
                        <option value="">Selecione uma categoria</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?php echo $categoria['id']; ?>">
                                <?php echo htmlspecialchars($categoria['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="descricao">Descrição</label>
                    <input type="text" id="descricao" name="descricao" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="valor">Valor (R$)</label>
                    <input type="text" id="valor" name="valor" class="form-control" placeholder="0,00" required>
                </div>
                
                <div class="form-group">
                    <label for="data">Data</label>
                    <input type="date" id="data" name="data" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-success">Cadastrar Receita</button>
            </form>
        </div>
        
        <div class="card">
            <h2>Minhas Receitas</h2>
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Categoria</th>
                        <th>Descrição</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($receitas) > 0): ?>
                        <?php foreach ($receitas as $receita): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($receita['data'])); ?></td>
                                <td>
                                    <span class="categoria-indicador" style="background-color: <?php echo $receita['cor']; ?>"></span>
                                    <?php echo htmlspecialchars($receita['categoria']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($receita['descricao']); ?></td>
                                <td class="receitas">R$ <?php echo number_format($receita['valor'], 2, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center;">Nenhuma receita cadastrada.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html> 