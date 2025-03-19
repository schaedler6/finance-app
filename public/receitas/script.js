const { createApp } = Vue

createApp({
    data() {
        return {
            showForm: false,
            form: {
                categoria_id: '',
                descricao: '',
                valor: '',
                data: new Date().toISOString().split('T')[0],
                tipo: 'receita'
            },
            categorias: [],
            receitas: []
        }
    },
    methods: {
        formatCurrency(value) {
            return value.toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            })
        },
        formatDate(date) {
            return new Date(date).toLocaleDateString('pt-BR')
        },
        async loadData() {
            try {
                // Carregar categorias
                const categoriasResponse = await fetch('../api/transacoes.php?action=categorias&tipo=receita')
                const categoriasData = await categoriasResponse.json()
                this.categorias = categoriasData

                // Carregar receitas
                const receitasResponse = await fetch('../api/transacoes.php?tipo=receita')
                const receitasData = await receitasResponse.json()
                this.receitas = receitasData
            } catch (error) {
                console.error('Erro ao carregar dados:', error)
                alert('Erro ao carregar dados. Tente novamente.')
            }
        },
        async handleSubmit() {
            try {
                const response = await fetch('../api/transacoes.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'create',
                        ...this.form
                    })
                })

                const data = await response.json()

                if (data.success) {
                    this.showForm = false
                    this.form = {
                        categoria_id: '',
                        descricao: '',
                        valor: '',
                        data: new Date().toISOString().split('T')[0],
                        tipo: 'receita'
                    }
                    this.loadData()
                    alert('Receita cadastrada com sucesso!')
                } else {
                    alert('Erro ao cadastrar receita. Tente novamente.')
                }
            } catch (error) {
                console.error('Erro ao cadastrar receita:', error)
                alert('Erro ao cadastrar receita. Tente novamente.')
            }
        },
        async deleteReceita(id) {
            if (!confirm('Tem certeza que deseja excluir esta receita?')) {
                return
            }

            try {
                const response = await fetch('../api/transacoes.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'delete',
                        id: id
                    })
                })

                const data = await response.json()

                if (data.success) {
                    this.loadData()
                    alert('Receita excluída com sucesso!')
                } else {
                    alert('Erro ao excluir receita. Tente novamente.')
                }
            } catch (error) {
                console.error('Erro ao excluir receita:', error)
                alert('Erro ao excluir receita. Tente novamente.')
            }
        },
        async logout() {
            try {
                const response = await fetch('../api/auth.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'logout'
                    })
                })

                const data = await response.json()

                if (data.success) {
                    window.location.href = '../login/login.php'
                } else {
                    alert('Erro ao fazer logout. Tente novamente.')
                }
            } catch (error) {
                console.error('Erro ao fazer logout:', error)
                alert('Erro ao fazer logout. Tente novamente.')
            }
        }
    },
    mounted() {
        this.loadData()
    }
}).mount('#app') 