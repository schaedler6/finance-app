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
    <title>Lembretes de Contas - Controle Financeiro</title>
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
                    <a href="../receitas/receitas.php">
                        <i class='bx bx-trending-up'></i>
                        Receitas
                    </a>
                </li>
                <li>
                    <a href="../despesas/despesas.php">
                        <i class='bx bx-trending-down'></i>
                        Despesas
                    </a>
                </li>
                <li>
                    <a href="../lembretes/lembretes.php" class="active">
                        <i class='bx bx-bell'></i>
                        Lembretes
                    </a>
                </li>
                <li>
                    <a href="../limite_gastos/limite_gastos.php">
                        <i class='bx bx-dollar-circle'></i>
                        Limite de Gastos
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
                <h1>Lembretes de Contas a Pagar</h1>
                
                <button class="btn btn-success" @click="showForm = true" v-if="!showForm">
                    <i class='bx bx-plus'></i>
                    Novo Lembrete
                </button>
            </div>
            
            <div class="card" v-if="showForm">
                <h2>{{ form.id ? 'Editar' : 'Novo' }} Lembrete</h2>
                
                <form @submit.prevent="handleSubmit" class="form">
                    <div class="form-group">
                        <label for="descricao">Descrição</label>
                        <input 
                            type="text" 
                            id="descricao" 
                            v-model="form.descricao" 
                            class="form-control" 
                            :class="{'error': errors.descricao}"
                            required
                        >
                        <div class="error-message" v-if="errors.descricao">
                            {{ errors.descricao }}
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="valor">Valor (R$)</label>
                        <input 
                            type="text" 
                            id="valor" 
                            v-model="form.valor" 
                            class="form-control" 
                            @input="formatarValorInput"
                            :class="{'error': errors.valor}"
                            placeholder="0,00" 
                            required
                        >
                        <div class="error-message" v-if="errors.valor">
                            {{ errors.valor }}
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="data_vencimento">Data de Vencimento</label>
                        <input 
                            type="date" 
                            id="data_vencimento" 
                            v-model="form.data_vencimento" 
                            class="form-control" 
                            :class="{'error': errors.data_vencimento}"
                            required
                        >
                        <div class="error-message" v-if="errors.data_vencimento">
                            {{ errors.data_vencimento }}
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select 
                            id="status" 
                            v-model="form.status" 
                            class="form-control" 
                            :class="{'error': errors.status}"
                            required
                        >
                            <option value="pendente">Pendente</option>
                            <option value="pago">Pago</option>
                            <option value="atrasado">Atrasado</option>
                        </select>
                        <div class="error-message" v-if="errors.status">
                            {{ errors.status }}
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" @click="cancelarForm">
                            <i class='bx bx-x'></i>
                            Cancelar
                        </button>
                        
                        <button type="submit" class="btn btn-success">
                            <i class='bx bx-save'></i>
                            {{ form.id ? 'Atualizar' : 'Salvar' }}
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="card">
                <h2>Lembretes Atrasados</h2>
                
                <div v-if="lembretesAtrasados.length === 0" class="empty-message">
                    <i class='bx bx-check-circle'></i>
                    <p>Não há contas atrasadas.</p>
                </div>
                
                <div class="table-responsive" v-else>
                    <table>
                        <thead>
                            <tr>
                                <th>Descrição</th>
                                <th>Valor</th>
                                <th>Vencimento</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="lembrete in lembretesAtrasados" :key="lembrete.id">
                                <td>{{ lembrete.descricao }}</td>
                                <td>R$ {{ formatCurrency(lembrete.valor) }}</td>
                                <td>{{ formatDate(lembrete.data_vencimento) }}</td>
                                <td>
                                    <span :class="'status status-' + lembrete.status">
                                        {{ statusTexto(lembrete.status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="actions">
                                        <button class="btn-icon" @click="marcarComoPago(lembrete)" title="Marcar como pago">
                                            <i class='bx bx-check'></i>
                                        </button>
                                        <button class="btn-icon" @click="editarLembrete(lembrete)" title="Editar">
                                            <i class='bx bx-edit'></i>
                                        </button>
                                        <button class="btn-icon" @click="excluirLembrete(lembrete.id)" title="Excluir">
                                            <i class='bx bx-trash'></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="card">
                <h2>Próximos Vencimentos</h2>
                
                <div v-if="lembretesProximos.length === 0" class="empty-message">
                    <i class='bx bx-calendar-check'></i>
                    <p>Não há contas a vencer nos próximos dias.</p>
                </div>
                
                <div class="table-responsive" v-else>
                    <table>
                        <thead>
                            <tr>
                                <th>Descrição</th>
                                <th>Valor</th>
                                <th>Vencimento</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="lembrete in lembretesProximos" :key="lembrete.id">
                                <td>{{ lembrete.descricao }}</td>
                                <td>R$ {{ formatCurrency(lembrete.valor) }}</td>
                                <td>{{ formatDate(lembrete.data_vencimento) }}</td>
                                <td>
                                    <span :class="'status status-' + lembrete.status">
                                        {{ statusTexto(lembrete.status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="actions">
                                        <button class="btn-icon" @click="marcarComoPago(lembrete)" title="Marcar como pago">
                                            <i class='bx bx-check'></i>
                                        </button>
                                        <button class="btn-icon" @click="editarLembrete(lembrete)" title="Editar">
                                            <i class='bx bx-edit'></i>
                                        </button>
                                        <button class="btn-icon" @click="excluirLembrete(lembrete.id)" title="Excluir">
                                            <i class='bx bx-trash'></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="card">
                <h2>Todos os Lembretes</h2>
                
                <div v-if="lembretes.length === 0" class="empty-message">
                    <i class='bx bx-info-circle'></i>
                    <p>Nenhum lembrete cadastrado.</p>
                </div>
                
                <div class="table-responsive" v-else>
                    <table>
                        <thead>
                            <tr>
                                <th>Descrição</th>
                                <th>Valor</th>
                                <th>Vencimento</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="lembrete in lembretes" :key="lembrete.id">
                                <td>{{ lembrete.descricao }}</td>
                                <td>R$ {{ formatCurrency(lembrete.valor) }}</td>
                                <td>{{ formatDate(lembrete.data_vencimento) }}</td>
                                <td>
                                    <span :class="'status status-' + lembrete.status">
                                        {{ statusTexto(lembrete.status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="actions">
                                        <button v-if="lembrete.status !== 'pago'" class="btn-icon" @click="marcarComoPago(lembrete)" title="Marcar como pago">
                                            <i class='bx bx-check'></i>
                                        </button>
                                        <button class="btn-icon" @click="editarLembrete(lembrete)" title="Editar">
                                            <i class='bx bx-edit'></i>
                                        </button>
                                        <button class="btn-icon" @click="excluirLembrete(lembrete.id)" title="Excluir">
                                            <i class='bx bx-trash'></i>
                                        </button>
                                    </div>
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