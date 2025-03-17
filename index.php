<!DOCTYPE html>
<html lang="pt_br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard inicial</title>
    <style>
        body{
            font-family:Helvetica;
            display:flex;
            margin:0;
        }
        .sidebar{
            width:250px;
            background:#333;
            color:#fefefe;
            height:100vh;
            padding:20px;
        }
        .sidebar ul{
            list-style:none;
            padding:0px;
        }
        .sidebar ul li {
            padding: 10px 0px;
        }
        .sidebar ul li a{
            color:#fefefe;
            text-decoration:none;
        }
        .content{
            flex-grow:1;
            padding:20px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2> Dashboard </h2>
        <ul>
            <li><a href="#">Inicio</a></li>
            <li><a href="#">Relatórios</a></li>
            <li><a href="#">Configuração</a></li>
        </ul>
    </div>
    <div class="content">
        <h2>Bem vindo! <b>Fulano</b> </h2>
        <p> Este é o painel inicial </p>
    </div>
</body>
</html>
<?php
session_start();

if (isset($_SESSION['id'])) {

    header("Location: dashboard.php");
} else {

    header("Location: login.php");
}
exit;
?>