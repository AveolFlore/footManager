<?php
require "autoload.php";

use Config\Database;

// Initialisation de la connexion à la base de données
$db = new Database();
$pdo = $db->connect();

// 1. DESACTIVATION DES VERIFICATIONS DES CLES ETRANGERES
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");

// Récupération de tous les fichiers .sql présents dans le dossier "seeders"
$files = glob("seeders/*.sql");

foreach ($files as $file) {

    echo "Seeder : $file\n";

    // Lecture du contenu complet du fichier SQL
    $sqlContent = file_get_contents($file);

    // Suppression propre de tous les commentaires commençant par "--"
    $sqlCleaned = preg_replace('/--.*\r?\n/', '', $sqlContent);

    // Découpage du contenu en un tableau de requêtes individuelles
    $queries = explode(';', $sqlCleaned);

    // Exécution de chaque requête valide
    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query)) {
            $pdo->exec($query);
        }
    }
}

// 2. REACTIVATION DES VERIFICATIONS DES CLES ETRANGERES
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

echo "Seeders exécutés avec succès.\n";
