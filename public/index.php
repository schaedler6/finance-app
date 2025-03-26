<?php
require_once(__DIR__ . '\..\api\controllers\auth_controller.php');

$auth = new AuthController();

// Usando caminhos relativos à URL base
if ($auth->isLoggedIn()) {
    header("Location: /dashboard/dashboard.php");
} else {
    header("Location: /login/login.php");

}
exit;
?> 