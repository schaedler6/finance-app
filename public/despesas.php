<?php
require_once '../api/controllers/AuthController.php';

$auth = new AuthController();

if (!$auth->isLoggedIn()) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Despesas - Controle Financeiro</title>
    <link rel="stylesheet" href="css/style.css">
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
            <li><a href="#" id="logoutBtn">Sair</a></li>
        </ul>
    </div>
    
    <div class="content">
        <h1>Gerenciar Despesas</h1>
        
        <div class="card">
            <h2>Nova Despesa</h2>
            <form id="despesaForm">
                <div class="form-group">
                    <label for="categoria_id">Categoria</label>
                    <select id="categoria_id" name="categoria_id" class="form-control" required>
                        <option value="">Selecione uma categoria</option>
                        <!-- Preenchido via JavaScript -->
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
                
                <button type="submit" class="btn btn-danger">Cadastrar Despesa</button>
            </form>
        </div>
        
        <div class="card">
            <h2>Minhas Despesas</h2>
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
                    <!-- Preenchido via JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <script src="js/main.js"></script>
</body>
</html> 