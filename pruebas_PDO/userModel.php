<?php
class userModel extends dbh{

    public function UpdateUser($user,$name,$surname,$email,$wEmail,$planta){
        /* $sql = "INSERT INTO users (Nombre,Apellido,Documento) VALUES (?,?,?)";
        $stmt = $this->conexion()->prepare($sql);
        if($stmt->execute([$name,$surname,$dni])){
            echo 'usuario creado';
        }else{
            echo 'ocurrió un error inesperado, intente nuevamente mas tarde';
        } */
        try{
            $pdo = $this->conexion();
            $pdo->beginTransaction();
            $query = "SELECT count(id) as cantUser, usuarios.* from usuarios where user =  :user";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':user',$user,PDO::PARAM_STR);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            var_dump($data);
            
            if($data){
                if($data['cantUser'] > 0){
                    $idUser = $data['id'];
                    //El usuario ya existe, procedemos a realizar un update
                    $query = 'UPDATE usuarios SET 
                                Nombre = :nombre,
                                Apellido = :apellido,
                                email = :email,
                                email_work = :workMail,
                                user = :user,
                                planta = :planta
                              WHERE id = :id';
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(':nombre',$name,PDO::PARAM_STR);
                    $stmt->bindParam(':apellido',$surname,PDO::PARAM_STR);
                    $stmt->bindParam(':email',$email,PDO::PARAM_STR);
                    $stmt->bindParam(':workMail',$wEmail,PDO::PARAM_STR);
                    $stmt->bindParam(':user',$user,PDO::PARAM_STR);
                    $stmt->bindParam(':planta',$planta,PDO::PARAM_INT);

                    $stmt->bindParam(':id',$idUser,PDO::PARAM_INT);

                    $stmt->execute();
                }elseif ($data['cantUser'] == 0){
                    $query = 'INSERT INTO usuarios (Nombre,Apellido,email,email_work,user,planta)
                        VALUES (
                        :nombre,:apellido,:email,:wMail,:user,:planta
                    );';
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(':nombre',$name,PDO::PARAM_STR);
                    $stmt->bindParam(':apellido',$surname,PDO::PARAM_STR);
                    $stmt->bindParam(':email',$email,PDO::PARAM_STR);
                    $stmt->bindParam(':wMail',$wEmail,PDO::PARAM_STR);
                    $stmt->bindParam(':user',$user,PDO::PARAM_STR);
                    $stmt->bindParam(':planta',$planta,PDO::PARAM_INT);
                    $stmt->execute();

                }
            }
            $pdo->commit();
        }catch(PDO $e){
            $pdo->rollBack();
            echo $e->getMessage();
        }
    }
} 

?>