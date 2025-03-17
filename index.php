<?php
session_start();
if (isset($_SESSION['id'])) {
    header("Location: dashboard.php");
} else {
    header("Location: login.php");
}
exit;
?>