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
                tipo: 'despesa'
            },
            categorias: [],
            despesas: []
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
                const categoriasResponse = await fetch('../api/transacoes.php?action=categorias&tipo=despesa')
                const categoriasData = await categoriasResponse.json()
                this.categorias = categoriasData

                // Carregar despesas
                const despesasResponse = await fetch('../api/transacoes.php?tipo=despesa')
                const despesasData = await despesasResponse.json()
                this.despesas = despesasData
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
                        tipo: 'despesa'
                    }
                    this.loadData()
                    alert('Despesa cadastrada com sucesso!')
                } else {
                    alert('Erro ao cadastrar despesa. Tente novamente.')
                }
            } catch (error) {
                console.error('Erro ao cadastrar despesa:', error)
                alert('Erro ao cadastrar despesa. Tente novamente.')
            }
        },
        async deleteDespesa(id) {
            if (!confirm('Tem certeza que deseja excluir esta despesa?')) {
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
                    alert('Despesa excluída com sucesso!')
                } else {
                    alert('Erro ao excluir despesa. Tente novamente.')
                }
            } catch (error) {
                console.error('Erro ao excluir despesa:', error)
                alert('Erro ao excluir despesa. Tente novamente.')
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