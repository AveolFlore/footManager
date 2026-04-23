<?php

require 'config/database.php';

$files = glob("seeders/*.sql");

foreach ($files as $file) {

    echo "Seeder : $file\n";

    $sql = file_get_contents($file);

    $pdo->exec($sql);
}

echo "Seeders exécutés.";