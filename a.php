<?php 
if(!empty($_POST['nome'])){
    $cavalo = $_POST['nome'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1> Seu nome é <?php echo $cavalo; ?> e voce está aprendendo PHP </h1>
        <br />
    <?php echo "<h1> Seu nome é ".$cavalo." e voce está aprendendo PHP </h1>"; ?>
</body>
</html>