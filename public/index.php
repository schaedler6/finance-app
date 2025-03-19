<?php
require_once '../api/controllers/auth_controller.php';

$auth = new AuthController();

if ($auth->isLoggedIn()) {
    header("Location: dashboard.php");
} else {
    header("Location: login.php");
}
exit;
?> 