<?php
require_once '../api/controllers/AuthController.php';

$auth = new AuthController();

if ($auth->isLoggedIn()) {
    header("Location: dashboard.php");
} else {
    header("Location: login.php");
}
exit;
?> 