<?php
require_once '../api/controllers/AuthController.php';

$auth = new AuthController();

if ($auth->isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Controle Financeiro</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-container">
        <h1>Controle Financeiro</h1>
        
        <form id="loginForm">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn">Entrar</button>
        </form>
        
        <div class="login-footer">
            <p>Usuário padrão: admin@email.com / Senha: 123456</p>
        </div>
    </div>

    <script src="js/main.js"></script>
</body>
</html> 