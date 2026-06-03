<?php
require_once __DIR__ . '/config/Database.php';

use Config\Database;

$db = (new Database())->connect();

try {
    // Check if table exists first
    $checkTable = $db->query("SHOW TABLES LIKE 'galerie'");
    if ($checkTable->rowCount() === 0) {
        $sql = file_get_contents(__DIR__ . '/migrations/017_create_galerie_table.sql');
        $db->exec($sql);
        echo "✅ Table 'galerie' créée avec succès !\n";
    } else {
        echo "ℹ️ Table 'galerie' existe déjà !\n";
    }
} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}
