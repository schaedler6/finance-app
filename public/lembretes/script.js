const { createApp } = Vue

createApp({
    data() {
        return {
            showForm: false,
            form: {
                id: null,
                descricao: '',
                valor: '',
                data_vencimento: new Date().toISOString().split('T')[0],
                status: 'pendente'
            },
            errors: {},
            lembretes: [],
            lembretesAtrasados: [],
            lembretesProximos: []
        }
    },
    methods: {
        formatCurrency(value) {
            return parseFloat(value).toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            })
        },
        formatDate(date) {
            return new Date(date).toLocaleDateString('pt-BR')
        },
        async loadData() {
            try {
                // Carregar todos os lembretes
                const lembretesResponse = await fetch('../api/lembretes.php')
                const lembretesData = await lembretesResponse.json()
                if (lembretesData.success) {
                    this.lembretes = lembretesData.data
                }

                // Carregar lembretes atrasados
                const atrasadosResponse = await fetch('../api/lembretes.php?atrasados=true')
                const atrasadosData = await atrasadosResponse.json()
                if (atrasadosData.success) {
                    this.lembretesAtrasados = atrasadosData.data
                }

                // Carregar próximos lembretes
                const proximosResponse = await fetch('../api/lembretes.php?proximos=true')
                const proximosData = await proximosResponse.json()
                if (proximosData.success) {
                    this.lembretesProximos = proximosData.data
                }
            } catch (error) {
                console.error('Erro ao carregar dados:', error)
                alert('Erro ao carregar dados. Tente novamente.')
            }
        },
        validarFormulario() {
            this.errors = {}
            let isValid = true
            
            // Validar descrição
            if (!this.form.descricao || this.form.descricao.trim().length < 3) {
                this.errors.descricao = 'A descrição deve ter pelo menos 3 caracteres'
                isValid = false
            } else if (this.form.descricao.trim().length > 255) {
                this.errors.descricao = 'A descrição deve ter no máximo 255 caracteres'
                isValid = false
            }
            
            // Validar valor
            if (!this.form.valor) {
                this.errors.valor = 'Digite um valor'
                isValid = false
            } else {
                // Remover formatação e verificar se é número válido
                const valorLimpo = this.form.valor.replace(/\./g, '').replace(',', '.')
                if (isNaN(valorLimpo) || parseFloat(valorLimpo) <= 0) {
                    this.errors.valor = 'Digite um valor válido maior que zero'
                    isValid = false
                }
            }
            
            // Validar data
            if (!this.form.data_vencimento) {
                this.errors.data_vencimento = 'Selecione uma data de vencimento'
                isValid = false
            }
            
            // Validar status
            if (!this.form.status) {
                this.errors.status = 'Selecione um status'
                isValid = false
            }
            
            return isValid
        },
        async handleSubmit() {
            if (!this.validarFormulario()) {
                return
            }
            
            try {
                const action = this.form.id ? 'update' : 'create'
                const requestData = {
                    action: action,
                    ...this.form
                }
                
                const response = await fetch('../api/lembretes.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(requestData)
                })

                const data = await response.json()

                if (data.success) {
                    this.showForm = false
                    this.resetForm()
                    this.loadData()
                    alert(this.form.id ? 'Lembrete atualizado com sucesso!' : 'Lembrete cadastrado com sucesso!')
                } else {
                    if (data.errors) {
                        // Exibir erros de validação do servidor
                        this.errors = data.errors
                    } else {
                        alert('Erro ao processar lembrete: ' + data.message)
                    }
                }
            } catch (error) {
                console.error('Erro ao processar lembrete:', error)
                alert('Erro ao processar lembrete. Tente novamente.')
            }
        },
        formatarValorInput(e) {
            let valor = e.target.value
            
            // Remove tudo que não for número
            valor = valor.replace(/\D/g, '')
            
            // Converte para número e divide por 100 para obter o formato com centavos
            if (valor !== '') {
                valor = (parseInt(valor) / 100).toFixed(2)
                
                // Formata para o padrão brasileiro
                valor = valor.replace('.', ',')
                
                // Adiciona pontos para milhares
                if (valor.length > 6) {
                    let parteInteira = valor.split(',')[0]
                    const parteDecimal = valor.split(',')[1]
                    
                    // Formata com pontos a cada 3 dígitos
                    parteInteira = parteInteira.replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                    
                    valor = parteInteira + ',' + parteDecimal
                }
            }
            
            this.form.valor = valor
        },
        resetForm() {
            this.form = {
                id: null,
                descricao: '',
                valor: '',
                data_vencimento: new Date().toISOString().split('T')[0],
                status: 'pendente'
            }
            this.errors = {}
        },
        cancelarForm() {
            this.showForm = false
            this.resetForm()
        },
        editarLembrete(lembrete) {
            this.form = {
                id: lembrete.id,
                descricao: lembrete.descricao,
                valor: lembrete.valor.toString().replace('.', ','),
                data_vencimento: lembrete.data_vencimento,
                status: lembrete.status
            }
            this.showForm = true
            window.scrollTo(0, 0)
        },
        async excluirLembrete(id) {
            if (!confirm('Tem certeza que deseja excluir este lembrete?')) {
                return
            }

            try {
                const response = await fetch('../api/lembretes.php', {
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
                    alert('Lembrete excluído com sucesso!')
                } else {
                    alert('Erro ao excluir lembrete: ' + data.message)
                }
            } catch (error) {
                console.error('Erro ao excluir lembrete:', error)
                alert('Erro ao excluir lembrete. Tente novamente.')
            }
        },
        async marcarComoPago(lembrete) {
            try {
                const response = await fetch('../api/lembretes.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'marcar_como_pago',
                        id: lembrete.id
                    })
                })

                const data = await response.json()

                if (data.success) {
                    this.loadData()
                    alert('Lembrete marcado como pago com sucesso!')
                } else {
                    alert('Erro ao marcar lembrete como pago: ' + data.message)
                }
            } catch (error) {
                console.error('Erro ao marcar lembrete como pago:', error)
                alert('Erro ao marcar lembrete como pago. Tente novamente.')
            }
        },
        statusTexto(status) {
            switch(status) {
                case 'pendente': return 'Pendente'
                case 'pago': return 'Pago'
                case 'atrasado': return 'Atrasado'
                default: return status
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