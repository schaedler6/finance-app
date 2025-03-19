<?php
require_once '../api/controllers/auth_controller.php';

$auth = new AuthController();

if ($auth->isLoggedIn()) {
    header("Location: dashboard/dashboard.php");
} else {
    header("Location: login/login.php");
}
exit;
?> 