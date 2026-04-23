<?php

require 'config/database.php';

$files = glob("migrations/*.sql");

foreach ($files as $file) {

    echo "Exécution : $file\n";

    $sql = file_get_contents($file);

    $pdo->exec($sql);
}

echo "Migrations exécutées avec succès.";