<?php
    include('dbh.class.php');
    include('usermodel.php');
    include('controller.php');
    /* if (isset($_POST['nombre']) && isset($_POST['apellido']) && isset($_POST['documento'])){ */
            $user = $_POST['user'];
            $name = $_POST['nombre'];
            $surname = $_POST['apellido'];
            $email = $_POST['email'];
            $wEmail = $_POST['email_work'];
            $planta = $_POST['planta'];
        $userController = new userController();
        $userController->updateUserData($user,$name,$surname,$email,$wEmail,$planta);
    /* } */
?>