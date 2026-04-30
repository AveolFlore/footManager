<?php
require "../autoload.php";


$database = new \Config\Database();

// 2. On crée la connexion et on la stocke dans $db
$db = $database->connect();

require('../core/router.php');