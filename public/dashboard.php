<?php
require_once '../api/controllers/auth_controller.php';

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
    <title>Dashboard - Controle Financeiro</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        <div class="dashboard-header">
            <h1>Dashboard</h1>
            <div class="user-info">
                <p>Bem-vindo, <strong><?php echo htmlspecialchars($_SESSION['nome']); ?></strong></p>
                <p><?php echo date('d/m/Y'); ?></p>
            </div>
        </div>
        
        <div class="cards">
            <div class="card">
                <h3>Receitas</h3>
                <p class="value receitas">R$ 0,00</p>
                <a href="receitas.php" class="btn btn-success">Adicionar Receita</a>
            </div>
            
            <div class="card">
                <h3>Despesas</h3>
                <p class="value despesas">R$ 0,00</p>
                <a href="despesas.php" class="btn btn-danger">Adicionar Despesa</a>
            </div>
            
            <div class="card">
                <h3>Saldo</h3>
                <p class="value saldo">R$ 0,00</p>
            </div>
        </div>
        
        <div class="chart-container">
            <h2>Distribuição de Gastos</h2>
            <canvas id="despesasChart"></canvas>
        </div>
        
        <div class="tables">
            <div class="table-container">
                <h2>Últimas Receitas</h2>
                <table class="ultimas-receitas">
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
            
            <div class="table-container">
                <h2>Últimas Despesas</h2>
                <table class="ultimas-despesas">
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
    </div>

    <script src="js/main.js"></script>
</body>
</html> 