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
$reglements = $reglementModel->readAll();
$user_id = $_SESSION['user']['id'];
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
                    <h1 class="text-3xl font-bold text-gray-800">Règlement Intérieur</h1>
                    <button onclick="document.getElementById('modal-propose').classList.remove('hidden')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition">
                        Proposer une règle
                    </button>
                </header>

                <?php if (isset($_GET['msg'])): ?>
                    <div class="mb-6 p-4 rounded-lg bg-green-100 text-green-700">
                        <?= htmlspecialchars($_GET['msg']) ?>
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
                        ?>
                        <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 <?= $r['statut'] === 'actif' ? 'border-green-500' : 'border-yellow-500' ?>">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h2 class="text-lg font-bold text-gray-800"><?= $r['titre'] ?></h2>
                                    <span class="text-xs font-semibold uppercase px-2 py-1 rounded <?= $r['statut'] === 'actif' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' ?>">
                                        <?= $r['statut'] ?>
                                    </span>
                                </div>
                                <p class="text-xl font-bold text-red-600"><?= number_format($r['montant_amende'], 0, ',', ' ') ?> <span class="text-xs">FCFA</span></p>
                            </div>
                            <p class="text-gray-600 text-sm mb-4"><?= $r['description'] ?></p>

                            <?php if ($r['statut'] === 'reflexion'): ?>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <?php if ($hasVoted): ?>
                                        <p class="text-sm font-medium text-gray-700 mb-3">
                                            <i class="fas fa-check-circle text-green-500 mr-2"></i> Vous avez déjà voté : 
                                            <span class="font-bold <?= $hasVoted['choix'] === 'oui' ? 'text-green-600' : 'text-red-600' ?>">
                                                <?= strtoupper($hasVoted['choix']) ?>
                                            </span>
                                        </p>
                                    <?php else: ?>
                                        <p class="text-sm font-medium text-gray-700 mb-3">Votez pour cette règle :</p>
                                        <div class="flex space-x-3">
                                            <form action="/reglement-voter" method="POST" class="flex-1">
                                                <input type="hidden" name="reglement_id" value="<?= $r['id'] ?>">
                                                <input type="hidden" name="choix" value="oui">
                                                <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white py-2 rounded font-medium transition">OUI</button>
                                            </form>
                                            <form action="/reglement-voter" method="POST" class="flex-1">
                                                <input type="hidden" name="reglement_id" value="<?= $r['id'] ?>">
                                                <input type="hidden" name="choix" value="non">
                                                <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white py-2 rounded font-medium transition">NON</button>
                                            </form>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($totalVotes > 0): ?>
                                        <div class="mt-4">
                                            <p class="text-xs text-gray-500 mb-2">
                                                Résultats (<?= $totalVotes ?> vote<?= $totalVotes > 1 ? 's' : '' ?>)
                                            </p>
                                            <div class="flex h-4 rounded-full overflow-hidden">
                                                <div class="bg-green-500 transition-all duration-500" style="width: <?= $ouiPercent ?>%"></div>
                                                <div class="bg-red-500 transition-all duration-500" style="width: <?= $nonPercent ?>%"></div>
                                            </div>
                                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                                <span class="text-green-600 font-semibold">OUI (<?= $ouiPercent ?>%)</span>
                                                <span class="text-red-600 font-semibold">NON (<?= $nonPercent ?>%)</span>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>

    <div id="modal-propose" class="fixed inset-0 bg-black/50 z-50 flex justify-center items-center hidden">
        <div class="bg-white w-full max-w-lg p-8 rounded-xl">
            <h2 class="text-2xl font-bold mb-6">Proposer un nouveau règlement</h2>
            <form action="/reglement-proposer" method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Titre</label>
                    <input type="text" name="titre" required class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" required class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-green-500 outline-none"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amende (FCFA)</label>
                        <input type="number" name="montant_amende" required class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type d'infraction</label>
                        <select name="type_infraction" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                            <option value="retard">Retard</option>
                            <option value="absence">Absence</option>
                            <option value="comportement">Comportement</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="document.getElementById('modal-propose').classList.add('hidden')" class="px-4 py-2 text-gray-500 hover:text-gray-700">Annuler</button>
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg font-bold">Soumettre</button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>