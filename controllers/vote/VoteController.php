<?php
require_once __DIR__ . '/../../models/vote/Vote.php';

// On suppose que $db est déjà initialisée dans ton index.php (public)
$voteModel = new Vote($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_vote'])) {
    $reglement_id = $_POST['reglement_id'];
    $joueur_id = $_SESSION['user_id'] ?? 2; // Jean Dupont par défaut pour tes tests
    $choix = $_POST['choix']; // 'oui' ou 'non'

    // On vérifie si l'utilisateur n'a pas déjà voté
    if (!$voteModel->aDejaVote($reglement_id, $joueur_id)) {
        $voteModel->voter($reglement_id, $joueur_id, $choix);
        header("Location: index.php?page=reglement&vote_success=1");
    } else {
        header("Location: index.php?page=reglement&error=already_voted");
    }
    exit();
}