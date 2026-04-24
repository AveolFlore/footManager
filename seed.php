<?php
// require "autoload.php";
// use Config\Database;

// $db = new Database();

// $pdo = $db->connect();

// $files = glob("seeders/*.sql");

// foreach ($files as $file) {

//     echo "Seeder : $file\n";

//     $sql = file_get_contents($file);

//     $pdo->execute($sql);
// }

// echo "Seeders exécutés."
require "autoload.php";

use Config\Database;

$db = new Database();

$pdo = $db->connect();

$files = glob("seeders/*.sql");

foreach ($files as $file) {

    echo "Seeder : $file\n";

    $sql = file_get_contents($file);

    $pdo->exec($sql);
}

echo "Seeders exécutés.";