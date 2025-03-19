const { createApp } = Vue

createApp({
    data() {
        return {
            currentDate: new Date().toLocaleDateString('pt-BR'),
            totals: {
                receitas: 0,
                despesas: 0,
                saldo: 0
            },
            ultimasReceitas: [],
            ultimasDespesas: [],
            despesasChart: null
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
                // Carregar totais
                const totalsResponse = await fetch('../api/transacoes.php?action=totals')
                const totalsData = await totalsResponse.json()
                this.totals = totalsData

                // Carregar últimas receitas
                const receitasResponse = await fetch('../api/transacoes.php?tipo=receita&limit=5')
                const receitasData = await receitasResponse.json()
                this.ultimasReceitas = receitasData

                // Carregar últimas despesas
                const despesasResponse = await fetch('../api/transacoes.php?tipo=despesa&limit=5')
                const despesasData = await despesasResponse.json()
                this.ultimasDespesas = despesasData

                // Carregar dados do gráfico
                const chartResponse = await fetch('../api/transacoes.php?action=categorias&tipo=despesa')
                const chartData = await chartResponse.json()
                this.updateChart(chartData)
            } catch (error) {
                console.error('Erro ao carregar dados:', error)
                alert('Erro ao carregar dados. Tente novamente.')
            }
        },
        updateChart(data) {
            const ctx = this.$refs.despesasChart.getContext('2d')
            
            if (this.despesasChart) {
                this.despesasChart.destroy()
            }

            this.despesasChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.map(item => item.categoria),
                    datasets: [{
                        data: data.map(item => item.total),
                        backgroundColor: [
                            '#4CAF50',
                            '#2196F3',
                            '#FFC107',
                            '#9C27B0',
                            '#FF5722',
                            '#607D8B',
                            '#795548',
                            '#3F51B5'
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
            })
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