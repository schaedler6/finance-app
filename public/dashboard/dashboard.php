<?php
session_start();
require 'db.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['id'];
$stmt = $pdo->prepare("SELECT nome FROM usuarios WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT SUM(valor) as total FROM transacoes WHERE usuario_id = ? AND tipo = 'receita'");
$stmt->execute([$user_id]);
$receitas = $stmt->fetch(PDO::FETCH_ASSOC);
$total_receitas = $receitas['total'] ?? 0;

$stmt = $pdo->prepare("SELECT SUM(valor) as total FROM transacoes WHERE usuario_id = ? AND tipo = 'despesa'");
$stmt->execute([$user_id]);
$despesas = $stmt->fetch(PDO::FETCH_ASSOC);
$total_despesas = $despesas['total'] ?? 0;

$stmt = $pdo->prepare("SELECT limite_gastos FROM usuarios WHERE id = ?");
$stmt->execute([$user_id]);
$limite = $stmt->fetch(PDO::FETCH_ASSOC);
$limite_gastos = $limite['limite_gastos'] ?? 0;

$stmt = $pdo->prepare("SELECT limite_gastos FROM usuarios WHERE id = ?");
$stmt->execute([$user_id]);
$limite = $stmt->fetch(PDO::FETCH_ASSOC);
$limite_gastos = $limite['limite_gastos'] ?? 0;

$saldo = $total_receitas - $total_despesas;

$stmt = $pdo->prepare("
    SELECT t.*, c.nome as categoria, c.cor
    FROM transacoes t
    JOIN categorias c ON t.categoria_id = c.id
    WHERE t.usuario_id = ?
    ORDER BY t.data DESC
    LIMIT 5
");
$stmt->execute([$user_id]);
$ultimos_lancamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle Financeiro - Dashboard</title>
    <style>
        .notification-container {
            position: relative;
            margin-left: auto;
        }

        .notification-icon {
            background: #007bff;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }

        .notification-icon.active {
            background: #dc3545;
            animation: pulse 1s infinite;
        }

        .notification-icon span {
            color: white;
            font-weight: bold;
        }

        .notification-dropdown {
            display: none;
            position: absolute;
            right: 0;
            top: 45px;
            background: white;
            min-width: 300px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            padding: 15px;
            z-index: 100;
        }

        .notification-dropdown.show {
            display: block;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .limite { color: #ffc107; }
        .btn-warning { background: #ffc107; }
        
        
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
        
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .user-info {
            text-align: right;
        }
        
        .cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        @media (max-width: 1200px) {
            .cards {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .cards {
                grid-template-columns: 1fr;
            }
        }
        
        .card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            flex: 1;
        }
        
        .card h3 {
            margin-top: 0;
            color: #555;
        }
        
        .card p.value {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .receitas { color: #28a745; }
        .despesas { color: #dc3545; }
        .saldo { color: #007bff; }
        
        .tables {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .table-container {
            flex: 1;
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
        
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background: #007bff;
            color: white;
            border-radius: 4px;
            text-decoration: none;
        }
        
        .btn-success {
            background: #28a745;
        }
        
        .btn-danger {
            background: #dc3545;
        }
        
        .chart-container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .categoria-indicador {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 5px;
        }
    </style>
    <!-- Chart.js para os gráficos -->
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
            <li><a href="limite_gastos.php">Limite de Gastos</a></li>
            <li><a href="logout.php">Sair</a></li>
        </ul>
    </div>
    
    <div class="content">
        <div class="dashboard-header">
            <h1>Dashboard</h1>
            <div class="user-info">
                <div class="notification-container">
                    <div class="notification-icon <?php echo ($limite_gastos > 0 && $total_despesas >= $limite_gastos) ? 'active' : ''; ?>" id="notificationIcon">
                        <span>!</span>
                        <div class="notification-dropdown" id="notificationDropdown">
                            <?php if ($limite_gastos > 0 && $total_despesas >= $limite_gastos): ?>
                                <div class="alert alert-danger">
                                    ⚠️ Atenção! Seus gastos atingiram o limite definido de 
                                    R$ <?php echo number_format($limite_gastos, 2, ',', '.'); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <p>Bem-vindo, <strong><?php echo htmlspecialchars($user['nome']); ?></strong></p>
                <p><?php echo date('d/m/Y'); ?></p>
            </div>
        </div>
        
        <div class="cards">
            <div class="card">
                <h3>Receitas</h3>
                <p class="value receitas">R$ <?php echo number_format($total_receitas, 2, ',', '.'); ?></p>
                <a href="receitas.php" class="btn btn-success">Adicionar Receita</a>
            </div>
            
            <div class="card">
                <h3>Despesas</h3>
                <p class="value despesas">R$ <?php echo number_format($total_despesas, 2, ',', '.'); ?></p>
                <a href="despesas.php" class="btn btn-danger">Adicionar Despesa</a>
            </div>

            <div class="card">
                <h3>Limite de Gastos</h3>
                <p class="value limite">R$ <?php echo number_format($limite_gastos, 2, ',', '.'); ?></p>
                <a href="limite_gastos.php" class="btn btn-warning">Definir Limite</a>
            </div>
            
            <div class="card">
                <h3>Saldo</h3>
                <p class="value saldo">R$ <?php echo number_format($saldo, 2, ',', '.'); ?></p>
            </div>
        </div>
        
        <div class="chart-container">
            <h2>Distribuição de Gastos</h2>
            <canvas id="despesasChart"></canvas>
        </div>
        
        <div class="tables">
            <div class="table-container">
                <h2>Últimas Receitas</h2>
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
                        <?php foreach ($ultimos_lancamentos as $lancamento): ?>
                            <?php if ($lancamento['tipo'] == 'receita'): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($lancamento['data'])); ?></td>
                                    <td>
                                        <span class="categoria-indicador" style="background-color: <?php echo $lancamento['cor']; ?>"></span>
                                        <?php echo htmlspecialchars($lancamento['categoria']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($lancamento['descricao']); ?></td>
                                    <td class="receitas">R$ <?php echo number_format($lancamento['valor'], 2, ',', '.'); ?></td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="table-container">
                <h2>Últimas Despesas</h2>
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
                        <?php foreach ($ultimos_lancamentos as $lancamento): ?>
                            <?php if ($lancamento['tipo'] == 'despesa'): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($lancamento['data'])); ?></td>
                                    <td>
                                        <span class="categoria-indicador" style="background-color: <?php echo $lancamento['cor']; ?>"></span>
                                        <?php echo htmlspecialchars($lancamento['categoria']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($lancamento['descricao']); ?></td>
                                    <td class="despesas">R$ <?php echo number_format($lancamento['valor'], 2, ',', '.'); ?></td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script>
    
        document.addEventListener('DOMContentLoaded', function() {
        
            const ctx = document.getElementById('despesasChart').getContext('2d');
            
        
            const despesasChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Alimentação', 'Transporte', 'Moradia', 'Lazer', 'Outros'],
                    datasets: [{
                        data: [30, 20, 40, 10, 5],
                        backgroundColor: [
                            '#dc3545',
                            '#fd7e14',
                            '#6f42c1',
                            '#20c997',
                            '#6c757d'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });
        });
    </script>

    <script>
        // Notificações
        const notificationIcon = document.getElementById('notificationIcon');
        const notificationDropdown = document.getElementById('notificationDropdown');

        notificationIcon.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationDropdown.classList.toggle('show');
        
            // Remover estado ativo ao clicar
            if(notificationIcon.classList.contains('active')) {
                notificationIcon.classList.remove('active');
            }
        });

        // Fechar notificação ao clicar fora
        document.addEventListener('click', function(e) {
            if(!notificationIcon.contains(e.target)) {
                notificationDropdown.classList.remove('show');
            }
        });
    </script>

</body>
</html> 