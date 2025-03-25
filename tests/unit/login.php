<?php
require_once '../../api/controllers/auth_controller.php';

$auth = new AuthController();

if ($auth->isLoggedIn()) {
    header("Location: ../dashboard/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Controle Financeiro</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
</head>
<body>
    <div id="app">
        <div class="login-container">
            <div class="login-header">
                <i class='bx bx-wallet'></i>
                <h1>Controle Financeiro</h1>
            </div>
            
            <form @submit.prevent="handleLogin" class="login-form">
                <div class="form-group">
                    <i class='bx bx-envelope'></i>
                    <input 
                        type="email" 
                        v-model="email" 
                        placeholder="Email" 
                        required
                    >
                </div>
                
                <div class="form-group">
                    <i class='bx bx-lock-alt'></i>
                    <input 
                        type="password" 
                        v-model="senha" 
                        placeholder="Senha" 
                        required
                    >
                </div>
                
                <button type="submit" class="btn">
                    <i class='bx bx-log-in'></i>
                    Entrar
                </button>
            </form>
            
            <div class="login-footer">
                <p>Usuário padrão: admin@email.com / Senha: 123456</p>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html> 