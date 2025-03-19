<?php
require_once '../../api/controllers/auth_controller.php';

$auth = new AuthController();

if (!$auth->isLoggedIn()) {
    header("Location: ../login/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Controle Financeiro</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div id="app">
        <div class="sidebar">
            <div class="sidebar-header">
                <i class='bx bx-wallet'></i>
                <h2>Controle Financeiro</h2>
            </div>
            <ul class="sidebar-menu">
                <li>
                    <a href="dashboard.php" class="active">
                        <i class='bx bx-home'></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="../receitas/receitas.php">
                        <i class='bx bx-plus-circle'></i>
                        Receitas
                    </a>
                </li>
                <li>
                    <a href="../despesas/despesas.php">
                        <i class='bx bx-minus-circle'></i>
                        Despesas
                    </a>
                </li>
                <li>
                    <a href="#" @click.prevent="logout">
                        <i class='bx bx-log-out'></i>
                        Sair
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="content">
            <div class="dashboard-header">
                <h1>Dashboard</h1>
                <div class="user-info">
                    <p>Bem-vindo, <strong><?php echo htmlspecialchars($_SESSION['nome']); ?></strong></p>
                    <p>{{ currentDate }}</p>
                </div>
            </div>
            
            <div class="cards">
                <div class="card">
                    <div class="card-icon">
                        <i class='bx bx-plus-circle'></i>
                    </div>
                    <div class="card-info">
                        <h3>Receitas</h3>
                        <p class="value receitas">R$ {{ formatCurrency(totals.receitas) }}</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-icon">
                        <i class='bx bx-minus-circle'></i>
                    </div>
                    <div class="card-info">
                        <h3>Despesas</h3>
                        <p class="value despesas">R$ {{ formatCurrency(totals.despesas) }}</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-icon">
                        <i class='bx bx-wallet'></i>
                    </div>
                    <div class="card-info">
                        <h3>Saldo</h3>
                        <p class="value" :class="{ 'positive': totals.saldo >= 0, 'negative': totals.saldo < 0 }">
                            R$ {{ formatCurrency(totals.saldo) }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="chart-container">
                <h2>Distribuição de Gastos</h2>
                <canvas ref="despesasChart"></canvas>
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
                            <tr v-for="receita in ultimasReceitas" :key="receita.id">
                                <td>{{ formatDate(receita.data) }}</td>
                                <td>{{ receita.categoria }}</td>
                                <td>{{ receita.descricao }}</td>
                                <td>R$ {{ formatCurrency(receita.valor) }}</td>
                            </tr>
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
                            <tr v-for="despesa in ultimasDespesas" :key="despesa.id">
                                <td>{{ formatDate(despesa.data) }}</td>
                                <td>{{ despesa.categoria }}</td>
                                <td>{{ despesa.descricao }}</td>
                                <td>R$ {{ formatCurrency(despesa.valor) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html> 