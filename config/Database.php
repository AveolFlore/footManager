<?php
namespace Config;

class Database{
private $host = "localhost";
private $dbname = "footmanager";
private $user = "root";
private $password = "";
public $conn ;

public function connect():\PDO{
    $this->conn = null;
try {
    $this->conn = new \PDO("mysql:host=".$this->host ."; dbname=". $this->dbname, $this->user,$this->password);
   
    $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    echo "";
} catch (\PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
return $this->conn;
}
}
