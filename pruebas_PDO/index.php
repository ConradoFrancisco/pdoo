<?php
    include('dbh.class.php');
    include('userModel.php');
    include('controller.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Probando PDO</h1>
    <form action="server.php" method="post">
        <input type="text" name="nombre"placeholder="Nombre">
        <input type="text" name="apellido"placeholder="Apellido">
        <input type="text" name="email"placeholder="email">
        <input type="text" name="email_work"placeholder="email de trabajo">
        <input type="text" name="user"placeholder="user">
        <input type="text" name="planta"placeholder="planta">
        <button type="submit">Enviar</button>
    </form>
</body>
</html>