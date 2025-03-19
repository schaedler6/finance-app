const { createApp } = Vue

createApp({
    data() {
        return {
            email: '',
            senha: ''
        }
    },
    methods: {
        async handleLogin() {
            try {
                const response = await fetch('../api/auth.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'login',
                        email: this.email,
                        senha: this.senha
                    })
                });

                const data = await response.json();

                if (data.success) {
                    window.location.href = '../dashboard/dashboard.php';
                } else {
                    alert('Email ou senha inválidos');
                }
            } catch (error) {
                console.error('Erro ao fazer login:', error);
                alert('Erro ao fazer login. Tente novamente.');
            }
        }
    }
}).mount('#app') 