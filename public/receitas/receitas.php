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
    <title>Receitas - Controle Financeiro</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
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
                    <a href="../dashboard/dashboard.php">
                        <i class='bx bx-home'></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="receitas.php" class="active">
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
            <div class="page-header">
                <h1>Gerenciar Receitas</h1>
                <button class="btn btn-success" @click="showForm = !showForm">
                    <i class='bx bx-plus'></i>
                    Nova Receita
                </button>
            </div>
            
            <div v-if="showForm" class="card">
                <h2>Nova Receita</h2>
                <form @submit.prevent="handleSubmit" class="form">
                    <div class="form-group">
                        <label for="categoria_id">Categoria</label>
                        <select 
                            id="categoria_id" 
                            v-model="form.categoria_id" 
                            class="form-control" 
                            required
                        >
                            <option value="">Selecione uma categoria</option>
                            <option 
                                v-for="categoria in categorias" 
                                :key="categoria.id" 
                                :value="categoria.id"
                            >
                                {{ categoria.nome }}
                            </option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="descricao">Descrição</label>
                        <input 
                            type="text" 
                            id="descricao" 
                            v-model="form.descricao" 
                            class="form-control" 
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="valor">Valor (R$)</label>
                        <input 
                            type="text" 
                            id="valor" 
                            v-model="form.valor" 
                            class="form-control" 
                            placeholder="0,00" 
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="data">Data</label>
                        <input 
                            type="date" 
                            id="data" 
                            v-model="form.data" 
                            class="form-control" 
                            required
                        >
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-success">
                            <i class='bx bx-save'></i>
                            Salvar
                        </button>
                        <button 
                            type="button" 
                            class="btn btn-secondary" 
                            @click="showForm = false"
                        >
                            <i class='bx bx-x'></i>
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="card">
                <h2>Minhas Receitas</h2>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Categoria</th>
                                <th>Descrição</th>
                                <th>Valor</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="receita in receitas" :key="receita.id">
                                <td>{{ formatDate(receita.data) }}</td>
                                <td>{{ receita.categoria }}</td>
                                <td>{{ receita.descricao }}</td>
                                <td>R$ {{ formatCurrency(receita.valor) }}</td>
                                <td>
                                    <button 
                                        class="btn-icon" 
                                        @click="deleteReceita(receita.id)"
                                    >
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </td>
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