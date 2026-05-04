<?php
require_once 'Config/Database.php'; 

use Config\Database;        
$db = new Database();
$pdo = $db->connect(); 

// 1. Désactiver les contraintes de clés étrangères
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");

// 2. Récupérer toutes les tables
$stmt = $pdo->query("SHOW TABLES");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

// 3. Supprimer chaque table
foreach ($tables as $table) {
    echo "Suppression de la table : $table\n";
    $pdo->exec("DROP TABLE IF EXISTS `$table` CASCADE");
}

// 4. Réactiver les contraintes de clés étrangères
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

echo "\n--- Base de données vidée ---\n\n";

// 5. Relancer les migrations
$files = glob("migrations/*.sql");
foreach ($files as $file) {
    echo "Migration : $file\n";
    $sql = file_get_contents($file);
    $pdo->exec($sql);
}

echo "\n--- Migrations terminées ---\n\n";

// 6. Relancer les seeds (en utilisant le format TRUNCATE pour être sûr)
$seedFiles = glob("seeders/*.sql");
foreach ($seedFiles as $file) {
    echo "Seeder : $file\n";
    $sql = file_get_contents($file);
    try {
        $pdo->exec($sql);
    } catch (Exception $e) {
        echo "Erreur sur le seeder $file : " . $e->getMessage() . "\n";
    }
}

echo "\n--- Base de données réinitialisée avec succès ! ---\n";
