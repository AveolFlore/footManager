<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../config/Database.php';
require_once __DIR__ . '/../../../middleware/Role.php';

requireLogin();

use Controllers\MatchSeanceController;
use Controllers\Convocation\ConvocationController;
use Controllers\ResultatMatchController;

$matchController = new MatchSeanceController();
$convocationController = new ConvocationController();
$resultatController = new ResultatMatchController();

$id = (int) $_GET['id'];
$match = $matchController->read_one($id);
$convoques = $convocationController->index($id);
$resultat = $resultatController->index($id);

// Grouper les convoqués par équipe
$joueursParEquipe = ['A' => [], 'B' => []];
foreach ($convoques as $convoque) {
    $joueursParEquipe[$convoque['equipe_match']][] = $convoque;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails Match</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php include_once __DIR__ . '/../../partials/header.php'; ?>
    <div class="flex">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1 overflow-y-auto p-4 md:p-6">
            <a href="/page-match"
                class="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 mb-6">
                ← Retour aux matchs
            </a>

            <div class="bg-white rounded-xl border border-gray-200 p-6 mb-4">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-800 mb-2">
                            Équipe A vs Équipe B
                        </h1>

                        <?php
                        $badge = match ($match['statut']) {
                            'planifie' => ['label' => 'À venir',  'class' => 'bg-blue-100 text-blue-600'],
                            'publie'   => ['label' => 'À venir',  'class' => 'bg-blue-100 text-blue-600'],
                            'termine'  => ['label' => 'Terminé',  'class' => 'bg-green-100 text-green-600'],
                            default    => ['label' => $match['statut'], 'class' => 'bg-gray-100 text-gray-600']
                        };
                        ?>
                        <span class="text-xs px-3 py-1 rounded-full font-medium <?= $badge['class'] ?>">
                            <?= $badge['label'] ?>
                        </span>
                    </div>

                    <?php if ($match['statut'] === 'termine' && $resultat): ?>
                        <div class="text-right">
                            <p class="text-xs text-gray-400 mb-1">Score final</p>
                            <p class="text-4xl font-bold text-gray-800">
                                <?= $resultat['buts_equipe_a'] ?> - <?= $resultat['buts_equipe_b'] ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="grid grid-cols-3 gap-4 mt-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center">
                            📅
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Date</p>
                            <p class="text-sm font-medium text-gray-700">
                                <?php
                                $days = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
                                $months = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                                $timestamp = strtotime($match['date']);
                                echo $days[date('w', $timestamp)] . ' ' . date('d', $timestamp) . ' ' . $months[date('n', $timestamp)];
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center">
                            🕐
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Heure</p>
                            <p class="text-sm font-medium text-gray-700">
                                <?= date('H:i', strtotime($match['date'])) ?>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-purple-50 flex items-center justify-center">
                            📍
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Lieu</p>
                            <p class="text-sm font-medium text-gray-700">
                                <?= htmlspecialchars($match['lieu']) ?>
                            </p>
                        </div>
                    </div>
                </div>

                <?php if (in_array($_SESSION['user']['role'], ['president', 'organisateur'])): ?>
                    <div class="flex gap-3 mt-6 pt-4 border-t border-gray-100">
                        <?php if ($match['statut'] === 'planifie'): ?>
                            <a href="/page-matchconvocations?id=<?= $match['id'] ?>"
                                class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition">
                                Gérer les convocations
                            </a>
                            <a href="/matchSeance-edit?id=<?= $match['id'] ?>"
                                class="px-4 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition">
                                Modifier
                            </a>
                        <?php endif; ?>

                        <?php if ($match['statut'] !== 'termine'): ?>
                            <button onclick="openConfirmModal(
                                'Supprimer le match ?',
                                'Êtes-vous sûr de vouloir supprimer ce match ? Cette action est irréversible.',
                                () => window.location.href = '/matchSeance-delete?id=<?= $match['id'] ?>'
                            )" class="px-4 py-2 bg-red-100 text-red-600 text-sm rounded-lg hover:bg-red-200 transition">
                                Supprimer
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Équipe A -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <div class="flex items-center gap-2 mb-6">
                        <span class="text-lg">🔵</span>
                        <h2 class="text-lg font-semibold text-gray-700">
                            Équipe A (<?= count($joueursParEquipe['A']) ?> joueurs)
                        </h2>
                    </div>

                    <?php if (empty($joueursParEquipe['A'])): ?>
                        <p class="text-sm text-gray-400">Aucun joueur convoqué dans cette équipe.</p>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach ($joueursParEquipe['A'] as $convoque): ?>
                                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        🧑
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-700">
                                            <?= htmlspecialchars($convoque['nom']) ?>
                                            <?= htmlspecialchars($convoque['prenom']) ?>
                                        </p>
                                        <?php if ($convoque['numero_maillot']): ?>
                                            <p class="text-xs text-gray-500">#<?= $convoque['numero_maillot'] ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($convoque['est_capitaine']): ?>
                                        <span class="text-xs font-bold text-yellow-600 bg-yellow-50 px-2 py-1 rounded-full border border-yellow-200">
                                            Capitaine
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Équipe B -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <div class="flex items-center gap-2 mb-6">
                        <span class="text-lg">🔴</span>
                        <h2 class="text-lg font-semibold text-gray-700">
                            Équipe B (<?= count($joueursParEquipe['B']) ?> joueurs)
                        </h2>
                    </div>

                    <?php if (empty($joueursParEquipe['B'])): ?>
                        <p class="text-sm text-gray-400">Aucun joueur convoqué dans cette équipe.</p>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach ($joueursParEquipe['B'] as $convoque): ?>
                                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                                        🧑
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-700">
                                            <?= htmlspecialchars($convoque['nom']) ?>
                                            <?= htmlspecialchars($convoque['prenom']) ?>
                                        </p>
                                        <?php if ($convoque['numero_maillot']): ?>
                                            <p class="text-xs text-gray-500">#<?= $convoque['numero_maillot'] ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($convoque['est_capitaine']): ?>
                                        <span class="text-xs font-bold text-yellow-600 bg-yellow-50 px-2 py-1 rounded-full border border-yellow-200">
                                            Capitaine
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (
                in_array($_SESSION['user']['role'], ['president', 'censeur']) &&
                $match['statut'] === 'publie' &&
                !$resultat
            ): ?>
                <div class="bg-white rounded-xl border border-gray-200 p-6 mt-6">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4">Saisir le résultat</h2>

                    <form action="/resultatMatch-resultatsave" method="POST">
                        <input type="hidden" name="match_id" value="<?= $match['id'] ?>">

                        <div class="flex items-center gap-4">
                            <div>
                                <label class="text-sm text-gray-500 block mb-1">Équipe A</label>
                                <input type="number" name="buts_equipe_a" min="0" value="0"
                                    class="w-20 border border-gray-300 rounded-lg px-3 py-2 text-center text-lg font-bold">
                            </div>
                            <span class="text-2xl font-bold text-gray-400 mt-4">-</span>
                            <div>
                                <label class="text-sm text-gray-500 block mb-1">Équipe B</label>
                                <input type="number" name="buts_equipe_b" min="0" value="0"
                                    class="w-20 border border-gray-300 rounded-lg px-3 py-2 text-center text-lg font-bold">
                            </div>
                            <button type="submit" name="save_resultat" value="Enregistrer"
                                class="mt-4 px-6 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition">
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>

</html>