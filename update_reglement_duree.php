<?php
require_once 'Config/Database.php';
use Config\Database;

$db = new Database();
$pdo = $db->connect();

try {
    // Vérifier si la colonne existe déjà
    $stmt = $pdo->query("SHOW COLUMNS FROM reglement LIKE 'duree_vote_heures'");
    if (!$stmt->fetch()) {
        echo "Ajout de la colonne duree_vote_heures à la table reglement...\n";
        $pdo->exec("ALTER TABLE reglement ADD COLUMN duree_vote_heures INT DEFAULT 24 AFTER date_debut_vote");
        echo "Colonne ajoutée avec succès !\n";
    } else {
        echo "La colonne existe déjà.\n";
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
echo "Terminé !\n";
