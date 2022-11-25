<?php

class dbh {
    private $host = 'localhost';
    private $user = 'root';
    private $pw = '';
    private $db = 'pdo_test';


    public function conexion(){
        try{
            $dsn = 'mysql:host='. $this->host . ";dbname=". $this->db;
            $pdo = new PDO($dsn, $this->user, $this->pw);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $pdo;
        }
        catch(PDOexception $e){
            return $e->getMessage();
        }
    }
}