<?php
namespace Config;
class Database{
private $host = "127.0.0.1";
private $dbname = "footmanager";
private $user = "root";
private $password = "";
public $conn ;

public function connect():\PDO{
    $this->conn = null;
try {
    $this->conn = new \PDO("mysql:host=".$this->host ."; dbname=". $this->dbname, $this->user,$this->password);
   
    $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
} catch (\PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
return $this->conn;
}

}
