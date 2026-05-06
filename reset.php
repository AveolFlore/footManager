<?php
$pdo = new PDO("mysql:host=localhost;dbname=footmanager", "root", "");
 
// Désactiver les clés étrangères pour éviter les blocages
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
 
// Récupérer toutes les tables
$query = $pdo->query("SHOW TABLES");
$tables = $query->fetchAll(PDO::FETCH_COLUMN);
 
// Supprimer chaque table
foreach ($tables as $table) {
    $pdo->exec("DROP TABLE IF EXISTS `$table` ");
    echo "Table $table supprimée.\n";
}
 
// Réactiver les clés étrangères
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
echo "Base de données nettoyée !\n";