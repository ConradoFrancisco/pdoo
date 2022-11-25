<?php

    class userController extends userModel{

        public function updateUserData($user,$name,$surname,$email,$wEmail,$planta){
            /* $nombre = $_POST['nombre'];
            $apellido = $_POST['apellido'];
            $documento = $_POST['documento'];
            $test = new userModel();
            $test->insertUser($nombre,$apellido,$documento); */
            
            $model = new userModel();
            $model->UpdateUser($user,$name,$surname,$email,$wEmail,$planta);

        }
    }
?>