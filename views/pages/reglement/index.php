<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../config/Database.php';
require_once __DIR__ . '/../../../models/reglement/Reglement.php';
require_once __DIR__ . '/../../../models/vote/Vote.php';
require_once __DIR__ . '/../../../middleware/Role.php';

use Config\Database;
use Models\Reglement\Reglement;
use Models\Vote\Vote;

requireLogin();

$db = (new Database())->connect();
$reglementModel = new Reglement($db);
$voteModel = new Vote($db);

// Vérifier et mettre à jour les réglements expirés avant d'afficher la page
$reglementModel->checkAndUpdateExpiredVotes();

$reglements = $reglementModel->readAll();
$user_id = $_SESSION['user']['id'];

$pageTitle = "Règlements du Club";
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Règlement - Club Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-gray-100 font-sans">

    <div class="flex min-h-screen">
        <?php include __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1">
            <?php include __DIR__ . '/../../partials/header.php'; ?>
            <div class="p-8">
                <header class="flex justify-between items-center mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">Règlement Intérieur</h1>
                        <p class="text-gray-600">Votes ouverts pendant 24 heures</p>
                    </div>
                    <button onclick="document.getElementById('modal-propose').classList.remove('hidden')" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition shadow-md">
                        <i class="fas fa-plus mr-2"></i> Proposer un règlement
                    </button>
                </header>

                <?php if (isset($_GET['msg'])): ?>
                    <div class="mb-6 p-4 rounded-lg <?php
                                                    $msg = $_GET['msg'];
                                                    if (in_array($msg, ['proposition_envoyee', 'vote_enregistre'])) {
                                                        echo 'bg-green-100 text-green-700';
                                                    } else {
                                                        echo 'bg-yellow-100 text-yellow-700';
                                                    }
                                                    ?>">
                        <i class="fas fa-info-circle mr-2"></i>
                        <?php
                        $messages = [
                            'proposition_envoyee' => 'Votre proposition de règlement a été envoyée !',
                            'vote_enregistre' => 'Votre vote a bien été enregistré !',
                            'deja_vote' => 'Vous avez déjà voté pour ce règlement.',
                            'delai_depasse' => 'Le délai de vote pour ce règlement est dépassé.',
                            'reglement_introuvable' => 'Ce règlement n\'existe pas.',
                        ];
                        echo $messages[$msg] ?? $msg;
                        ?>
                    </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach ($reglements as $r): ?>
                        <?php
                        $hasVoted = $voteModel->hasVoted($r['id'], $user_id);
                        $voteResults = $voteModel->getResults($r['id']);
                        $totalVotes = 0;
                        $ouiVotes = 0;
                        $nonVotes = 0;
                        foreach ($voteResults as $vr) {
                            $totalVotes += $vr['total'];
                            if ($vr['choix'] === 'oui') $ouiVotes = $vr['total'];
                            if ($vr['choix'] === 'non') $nonVotes = $vr['total'];
                        }
                        $ouiPercent = $totalVotes > 0 ? round(($ouiVotes / $totalVotes) * 100) : 0;
                        $nonPercent = $totalVotes > 0 ? round(($nonVotes / $totalVotes) * 100) : 0;

                        // Calculer le temps restant
                        $tempsRestant = null;
                        $delaiDepasse = false;
                        if ($r['statut'] === 'reflexion') {
                            $dateDebut = new DateTime($r['date_debut_vote']);
                            $dateFin = (clone $dateDebut)->modify('+24 hours');
                            $maintenant = new DateTime();
                            $interval = $maintenant->diff($dateFin);

                            if ($maintenant > $dateFin) {
                                $delaiDepasse = true;
                            } else {
                                $tempsRestant = $interval;
                            }
                        }

                        // Couleur du badge selon le statut
                        $badgeClass = '';
                        $badgeLabel = '';
                        switch ($r['statut']) {
                            case 'actif':
                                $badgeClass = 'bg-green-100 text-green-700 border-green-200';
                                $badgeLabel = 'En vigueur';
                                break;
                            case 'rejete':
                                $badgeClass = 'bg-red-100 text-red-700 border-red-200';
                                $badgeLabel = 'Rejeté';
                                break;
                            default:
                                $badgeClass = 'bg-yellow-100 text-yellow-700 border-yellow-200';
                                $badgeLabel = 'En vote';
                        }
                        ?>
                        <div class="bg-white rounded-xl shadow-sm border-l-4 <?php
                                                                                echo $r['statut'] === 'actif' ? 'border-green-500' : ($r['statut'] === 'rejete' ? 'border-red-500' : 'border-yellow-500');
                                                                                ?> overflow-hidden">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h2 class="text-xl font-bold text-gray-800 mb-1"><?= htmlspecialchars($r['titre']) ?></h2>
                                        <span class="inline-block text-xs font-semibold uppercase px-3 py-1 rounded-full border <?= $badgeClass ?>">
                                            <?= $badgeLabel ?>
                                        </span>
                                    </div>
                                    <p class="text-2xl font-bold text-red-600">
                                        <?= number_format($r['montant_amende'], 0, ',', ' ') ?>
                                        <span class="text-sm text-gray-500">FCFA</span>
                                    </p>
                                </div>

                                <p class="text-gray-600 mb-4"><?= htmlspecialchars($r['description']) ?></p>

                                <!-- Affichage du temps restant pour le vote -->
                                <?php if ($r['statut'] === 'reflexion'): ?>
                                    <div class="mb-4 p-3 bg-blue-50 rounded-lg border border-blue-100">
                                        <div class="flex items-center gap-2 text-blue-700">
                                            <i class="fas fa-clock text-lg"></i>
                                            <?php if ($delaiDepasse): ?>
                                                <span class="text-sm font-medium">Délai de vote dépassé, résultat en attente...</span>
                                            <?php else: ?>
                                                <span class="text-sm font-medium">
                                                    Temps restant pour voter :
                                                    <?php
                                                    if ($tempsRestant->days > 0) echo $tempsRestant->days . 'j ';
                                                    echo $tempsRestant->h . 'h ' . $tempsRestant->i . 'm';
                                                    ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if ($r['statut'] === 'reflexion' && !$delaiDepasse): ?>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <?php if ($hasVoted): ?>
                                            <div class="text-center">
                                                <i class="fas fa-check-circle text-green-500 text-2xl mb-2"></i>
                                                <p class="text-sm font-medium text-gray-700">
                                                    Vous avez déjà voté :
                                                    <span class="font-bold <?= $hasVoted['choix'] === 'oui' ? 'text-green-600' : 'text-red-600' ?>">
                                                        <?= strtoupper($hasVoted['choix']) ?>
                                                    </span>
                                                </p>
                                            </div>
                                        <?php else: ?>
                                            <p class="text-sm font-medium text-gray-700 mb-3 text-center">Votez pour ce règlement :</p>
                                            <div class="flex gap-3">
                                                <form action="/reglement-voter" method="POST" class="flex-1">
                                                    <input type="hidden" name="reglement_id" value="<?= $r['id'] ?>">
                                                    <input type="hidden" name="choix" value="oui">
                                                    <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white py-3 rounded-lg font-semibold transition shadow">
                                                        <i class="fas fa-thumbs-up mr-2"></i> OUI
                                                    </button>
                                                </form>
                                                <form action="/reglement-voter" method="POST" class="flex-1">
                                                    <input type="hidden" name="reglement_id" value="<?= $r['id'] ?>">
                                                    <input type="hidden" name="choix" value="non">
                                                    <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white py-3 rounded-lg font-semibold transition shadow">
                                                        <i class="fas fa-thumbs-down mr-2"></i> NON
                                                    </button>
                                                </form>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Barre de progression des votes -->
                                        <?php if ($totalVotes > 0): ?>
                                            <div class="mt-4">
                                                <p class="text-xs text-gray-500 mb-2 text-center">
                                                    Résultats (<?= $totalVotes ?> vote<?= $totalVotes > 1 ? 's' : '' ?>)
                                                </p>
                                                <div class="flex h-3 rounded-full overflow-hidden bg-gray-200">
                                                    <div class="bg-green-500 transition-all duration-700" style="width: <?= $ouiPercent ?>%"></div>
                                                    <div class="bg-red-500 transition-all duration-700" style="width: <?= $nonPercent ?>%"></div>
                                                </div>
                                                <div class="flex justify-between text-xs text-gray-600 mt-1">
                                                    <span class="text-green-600 font-semibold">OUI (<?= $ouiPercent ?>%)</span>
                                                    <span class="text-red-600 font-semibold">NON (<?= $nonPercent ?>%)</span>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php elseif ($r['statut'] === 'actif' || $r['statut'] === 'rejete'): ?>
                                    <!-- Afficher les résultats finaux -->
                                    <div class="p-4 rounded-lg <?= $r['statut'] === 'actif' ? 'bg-green-50 border border-green-100' : 'bg-red-50 border border-red-100' ?>">
                                        <h4 class="text-sm font-semibold mb-2 text-gray-700">Résultats finaux :</h4>
                                        <div class="flex h-4 rounded-full overflow-hidden bg-gray-200 mb-2">
                                            <div class="bg-green-500" style="width: <?= $ouiPercent ?>%"></div>
                                            <div class="bg-red-500" style="width: <?= $nonPercent ?>%"></div>
                                        </div>
                                        <div class="flex justify-between text-sm font-semibold">
                                            <span class="text-green-600">OUI : <?= $ouiVotes ?></span>
                                            <span class="text-red-600">NON : <?= $nonVotes ?></span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Proposer -->
    <div id="modal-propose" class="fixed inset-0 bg-black/50 z-50 flex justify-center items-center hidden p-4">
        <div class="bg-white w-full max-w-lg rounded-xl shadow-2xl overflow-hidden">
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white">Proposer un nouveau règlement</h2>
            </div>
            <form action="/reglement-proposer" method="POST" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Titre du règlement</label>
                    <input type="text" name="titre" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Montant de l'amende (FCFA)</label>
                        <input type="number" name="montant_amende" required min="0" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type d'infraction</label>
                        <select name="type_infraction" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="retard">Retard</option>
                            <option value="absence">Absence</option>
                            <option value="comportement">Comportement</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" onclick="document.getElementById('modal-propose').classList.add('hidden')" class="px-6 py-2 text-gray-600 hover:text-gray-800 font-medium transition">
                        Annuler
                    </button>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-8 py-2 rounded-lg font-bold shadow-md transition">
                        Soumettre
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>ALTER TABLE reglement ADD COLUMN date_debut_vote DATETIME DEFAULT CURRENT_TIMESTAMP AFTER date_creation;
UPDATE reglement SET date_debut_vote = CONCAT(date_creation, ' 00:00:00') WHERE date_debut_vote IS NULL;