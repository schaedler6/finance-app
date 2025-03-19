// Funções de API
const api = {
    baseUrl: '/api',
    
    async login(email, senha) {
        const response = await fetch(`${this.baseUrl}/auth.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'login',
                email,
                senha
            })
        });
        return await response.json();
    },
    
    async logout() {
        const response = await fetch(`${this.baseUrl}/auth.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'logout'
            })
        });
        return await response.json();
    },
    
    async checkAuth() {
        const response = await fetch(`${this.baseUrl}/auth.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'check'
            })
        });
        return await response.json();
    },
    
    async getTransacoes(tipo = null) {
        const url = tipo ? `${this.baseUrl}/transacoes.php?tipo=${tipo}` : `${this.baseUrl}/transacoes.php`;
        const response = await fetch(url);
        return await response.json();
    },
    
    async createTransacao(data) {
        const response = await fetch(`${this.baseUrl}/transacoes.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'create',
                ...data
            })
        });
        return await response.json();
    },
    
    async getTotals() {
        const response = await fetch(`${this.baseUrl}/transacoes.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'totals'
            })
        });
        return await response.json();
    },
    
    async getCategorias(tipo = null) {
        const response = await fetch(`${this.baseUrl}/transacoes.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'categorias',
                tipo
            })
        });
        return await response.json();
    }
};

// Funções de Formatação
const format = {
    currency(value) {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(value);
    },
    
    date(date) {
        return new Date(date).toLocaleDateString('pt-BR');
    }
};

// Funções de UI
const ui = {
    showAlert(message, type = 'success') {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.textContent = message;
        document.querySelector('.content').insertBefore(alert, document.querySelector('.content').firstChild);
        
        setTimeout(() => {
            alert.remove();
        }, 3000);
    },
    
    async loadDashboard() {
        try {
            const [totals, transacoes] = await Promise.all([
                api.getTotals(),
                api.getTransacoes()
            ]);
            
            if (totals.success) {
                document.querySelector('.receitas').textContent = format.currency(totals.data.receitas);
                document.querySelector('.despesas').textContent = format.currency(totals.data.despesas);
                document.querySelector('.saldo').textContent = format.currency(totals.data.saldo);
            }
            
            if (transacoes.success) {
                const ultimasReceitas = document.querySelector('.ultimas-receitas tbody');
                const ultimasDespesas = document.querySelector('.ultimas-despesas tbody');
                
                ultimasReceitas.innerHTML = '';
                ultimasDespesas.innerHTML = '';
                
                transacoes.data.forEach(transacao => {
                    const row = `
                        <tr>
                            <td>${format.date(transacao.data)}</td>
                            <td>
                                <span class="categoria-indicador" style="background-color: ${transacao.cor}"></span>
                                ${transacao.categoria}
                            </td>
                            <td>${transacao.descricao}</td>
                            <td class="${transacao.tipo}">${format.currency(transacao.valor)}</td>
                        </tr>
                    `;
                    
                    if (transacao.tipo === 'receita') {
                        ultimasReceitas.innerHTML += row;
                    } else {
                        ultimasDespesas.innerHTML += row;
                    }
                });
            }
        } catch (error) {
            console.error('Erro ao carregar dashboard:', error);
            ui.showAlert('Erro ao carregar dados', 'danger');
        }
    },
    
    async loadReceitas() {
        try {
            const [categorias, transacoes] = await Promise.all([
                api.getCategorias('receita'),
                api.getTransacoes('receita')
            ]);
            
            if (categorias.success) {
                const select = document.querySelector('#categoria_id');
                select.innerHTML = '<option value="">Selecione uma categoria</option>';
                categorias.data.forEach(categoria => {
                    select.innerHTML += `<option value="${categoria.id}">${categoria.nome}</option>`;
                });
            }
            
            if (transacoes.success) {
                const tbody = document.querySelector('table tbody');
                tbody.innerHTML = '';
                
                transacoes.data.forEach(transacao => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${format.date(transacao.data)}</td>
                            <td>
                                <span class="categoria-indicador" style="background-color: ${transacao.cor}"></span>
                                ${transacao.categoria}
                            </td>
                            <td>${transacao.descricao}</td>
                            <td class="receitas">${format.currency(transacao.valor)}</td>
                        </tr>
                    `;
                });
            }
        } catch (error) {
            console.error('Erro ao carregar receitas:', error);
            ui.showAlert('Erro ao carregar dados', 'danger');
        }
    },
    
    async loadDespesas() {
        try {
            const [categorias, transacoes] = await Promise.all([
                api.getCategorias('despesa'),
                api.getTransacoes('despesa')
            ]);
            
            if (categorias.success) {
                const select = document.querySelector('#categoria_id');
                select.innerHTML = '<option value="">Selecione uma categoria</option>';
                categorias.data.forEach(categoria => {
                    select.innerHTML += `<option value="${categoria.id}">${categoria.nome}</option>`;
                });
            }
            
            if (transacoes.success) {
                const tbody = document.querySelector('table tbody');
                tbody.innerHTML = '';
                
                transacoes.data.forEach(transacao => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${format.date(transacao.data)}</td>
                            <td>
                                <span class="categoria-indicador" style="background-color: ${transacao.cor}"></span>
                                ${transacao.categoria}
                            </td>
                            <td>${transacao.descricao}</td>
                            <td class="despesas">${format.currency(transacao.valor)}</td>
                        </tr>
                    `;
                });
            }
        } catch (error) {
            console.error('Erro ao carregar despesas:', error);
            ui.showAlert('Erro ao carregar dados', 'danger');
        }
    }
};

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    // Login Form
    const loginForm = document.querySelector('#loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.querySelector('#email').value;
            const senha = document.querySelector('#password').value;
            
            try {
                const response = await api.login(email, senha);
                if (response.success) {
                    window.location.href = '/dashboard.php';
                } else {
                    ui.showAlert(response.message, 'danger');
                }
            } catch (error) {
                console.error('Erro no login:', error);
                ui.showAlert('Erro ao fazer login', 'danger');
            }
        });
    }
    
    // Logout Button
    const logoutBtn = document.querySelector('#logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            try {
                const response = await api.logout();
                if (response.success) {
                    window.location.href = '/login.php';
                }
            } catch (error) {
                console.error('Erro no logout:', error);
            }
        });
    }
    
    // Dashboard
    if (document.querySelector('.dashboard')) {
        ui.loadDashboard();
    }
    
    // Receitas
    if (document.querySelector('#receitaForm')) {
        ui.loadReceitas();
        
        document.querySelector('#receitaForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = {
                categoria_id: formData.get('categoria_id'),
                descricao: formData.get('descricao'),
                valor: formData.get('valor'),
                data: formData.get('data'),
                tipo: 'receita'
            };
            
            try {
                const response = await api.createTransacao(data);
                if (response.success) {
                    ui.showAlert('Receita cadastrada com sucesso!');
                    e.target.reset();
                    ui.loadReceitas();
                } else {
                    ui.showAlert(response.message, 'danger');
                }
            } catch (error) {
                console.error('Erro ao cadastrar receita:', error);
                ui.showAlert('Erro ao cadastrar receita', 'danger');
            }
        });
    }
    
    // Despesas
    if (document.querySelector('#despesaForm')) {
        ui.loadDespesas();
        
        document.querySelector('#despesaForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = {
                categoria_id: formData.get('categoria_id'),
                descricao: formData.get('descricao'),
                valor: formData.get('valor'),
                data: formData.get('data'),
                tipo: 'despesa'
            };
            
            try {
                const response = await api.createTransacao(data);
                if (response.success) {
                    ui.showAlert('Despesa cadastrada com sucesso!');
                    e.target.reset();
                    ui.loadDespesas();
                } else {
                    ui.showAlert(response.message, 'danger');
                }
            } catch (error) {
                console.error('Erro ao cadastrar despesa:', error);
                ui.showAlert('Erro ao cadastrar despesa', 'danger');
            }
        });
    }
}); 