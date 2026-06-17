<?php
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/models/convocation/Convocation.php';

use Config\Database;
use Models\Convocation\Convocation;

$db = new Database();
$pdo = $db->connect();
$convocationModel = new Convocation($pdo);

echo "<h1>Test de la convocation</h1>";

// 1. Voir toutes les convocations existantes
echo "<h2>Convocations existantes</h2>";
$stmt = $pdo->query("SELECT * FROM convocation");
$convocations = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($convocations)) {
    echo "<p>Aucune convocation trouvée</p>";
} else {
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>ID</th><th>Match ID</th><th>Joueur ID</th><th>Équipe</th><th>Capitaine</th><th>Maillot</th></tr>";
    foreach ($convocations as $c) {
        echo "<tr>";
        echo "<td>{$c['id']}</td>";
        echo "<td>{$c['match_id']}</td>";
        echo "<td>{$c['joueur_id']}</td>";
        echo "<td>{$c['equipe_match']}</td>";
        echo "<td>" . ($c['est_capitaine'] ? 'Oui' : 'Non') . "</td>";
        echo "<td>{$c['numero_maillot']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// 2. Voir tous les matchs
echo "<h2>Tous les matchs</h2>";
$stmt = $pdo->query("SELECT * FROM match_seance");
$matches = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($matches)) {
    echo "<p>Aucun match trouvé</p>";
} else {
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>ID</th><th>Date</th><th>Lieu</th><th>Statut</th></tr>";
    foreach ($matches as $m) {
        echo "<tr>";
        echo "<td>{$m['id']}</td>";
        echo "<td>{$m['date']}</td>";
        echo "<td>{$m['lieu']}</td>";
        echo "<td>{$m['statut']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// 3. Voir les joueurs valides
echo "<h2>Joueurs valides</h2>";
$stmt = $pdo->query("SELECT id, nom, prenom FROM users WHERE statut='valide' AND role='joueur'");
$joueurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($joueurs)) {
    echo "<p>Aucun joueur valide trouvé</p>";
} else {
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>ID</th><th>Nom</th><th>Prénom</th></tr>";
    foreach ($joueurs as $j) {
        echo "<tr>";
        echo "<td>{$j['id']}</td>";
        echo "<td>{$j['nom']}</td>";
        echo "<td>{$j['prenom']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}
