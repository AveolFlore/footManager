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

$pageTitle = "Détails du match";
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - FC Blue Lock</title>
    <link rel="icon" type="image/png" href="/images/blue_lock_logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gradient-to-br from-slate-50 to-slate-100">
    <?php include_once __DIR__ . '/../../partials/header.php'; ?>
    <div class="flex min-h-screen">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1 overflow-y-auto">
            <div class="p-6 md:p-8 lg:p-10">
                <a href="/page-match"
                    class="flex items-center gap-2 text-slate-500 hover:text-slate-700 mb-8 font-semibold">
                    <i class="fas fa-arrow-left"></i>
                    Retour aux matchs
                </a>

                <div class="bg-white rounded-3xl border border-slate-100 p-8 mb-8 shadow-xl">
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 gap-4">
                        <div>
                            <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 mb-3 flex items-center gap-3">
                                <i class="fas fa-futbol text-green-600"></i>
                                Équipe A vs Équipe B
                            </h1>

                            <?php
                            $badge = match ($match['statut']) {
                                'planifie' => ['label' => 'À venir', 'class' => 'bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-700 border border-blue-200'],
                                'publie' => ['label' => 'À venir', 'class' => 'bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-700 border border-blue-200'],
                                'termine' => ['label' => 'Terminé', 'class' => 'bg-gradient-to-r from-green-100 to-emerald-100 text-green-700 border border-green-200'],
                                default => ['label' => $match['statut'], 'class' => 'bg-slate-100 text-slate-700 border border-slate-200']
                            };
                            ?>
                            <span class="inline-block text-sm px-5 py-2 rounded-2xl font-bold <?= $badge['class'] ?>">
                                <i class="fas fa-clock mr-2"></i>
                                <?= $badge['label'] ?>
                            </span>
                        </div>

                        <?php if ($match['statut'] === 'termine' && $resultat): ?>
                            <div class="text-center lg:text-right">
                                <p class="text-sm text-slate-500 mb-2 font-semibold">Score final</p>
                                <p class="text-5xl font-extrabold text-slate-800">
                                    <?= $resultat['buts_equipe_a'] ?> <span class="text-slate-400 text-4xl mx-2">—</span> <?= $resultat['buts_equipe_b'] ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                        <div class="flex items-center gap-4 p-6 rounded-2xl bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center shadow-lg shadow-blue-200">
                                <i class="fas fa-calendar text-white text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-slate-500 font-semibold uppercase tracking-wide">Date</p>
                                <p class="text-xl font-bold text-slate-800">
                                    <?php
                                    $days = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
                                    $months = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                                    $timestamp = strtotime($match['date']);
                                    echo $days[date('w', $timestamp)] . ' ' . date('d', $timestamp) . ' ' . $months[date('n', $timestamp)];
                                    ?>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 p-6 rounded-2xl bg-gradient-to-br from-yellow-50 to-orange-50 border border-yellow-200">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-yellow-600 to-orange-700 flex items-center justify-center shadow-lg shadow-yellow-200">
                                <i class="fas fa-clock text-white text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-slate-500 font-semibold uppercase tracking-wide">Heure</p>
                                <p class="text-xl font-bold text-slate-800">
                                    <?= date('H:i', strtotime($match['date'])) ?>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 p-6 rounded-2xl bg-gradient-to-br from-purple-50 to-pink-50 border border-purple-200">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-600 to-pink-700 flex items-center justify-center shadow-lg shadow-purple-200">
                                <i class="fas fa-map-marker-alt text-white text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-slate-500 font-semibold uppercase tracking-wide">Lieu</p>
                                <p class="text-xl font-bold text-slate-800">
                                    <?= htmlspecialchars($match['lieu']) ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <?php if (in_array($_SESSION['user']['role'], ['president', 'organisateur'])): ?>
                        <div class="flex flex-wrap gap-4 mt-8 pt-6 border-t-2 border-slate-100">
                            <?php if ($match['statut'] === 'planifie'): ?>
                                <a href="/page-matchconvocations?id=<?= $match['id'] ?>"
                                    class="flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-2xl font-bold hover:from-green-700 hover:to-green-800 transition-all shadow-lg shadow-green-200">
                                    <i class="fas fa-users"></i>
                                    Gérer les convocations
                                </a>
                                <a href="/matchSeance-edit?id=<?= $match['id'] ?>"
                                    class="flex items-center gap-3 px-6 py-3 border-2 border-slate-200 text-slate-700 rounded-2xl font-bold hover:bg-slate-50 transition-all bg-white shadow-sm">
                                    <i class="fas fa-edit"></i>
                                    Modifier
                                </a>
                            <?php endif; ?>

                            <?php if ($match['statut'] !== 'termine'): ?>
                                <button onclick="window.location.href = '/matchSeance-delete?id=<?= $match['id'] ?>'" class="flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-red-50 to-pink-50 text-red-600 border border-red-200 rounded-2xl font-bold hover:bg-gradient-to-r from-red-100 to-pink-100 transition-all shadow-sm">
                                    <i class="fas fa-trash"></i>
                                    Supprimer
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Équipe A -->
                    <div class="bg-white rounded-3xl border border-slate-100 p-8 shadow-xl">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center shadow-lg shadow-blue-200">
                                <i class="fas fa-users text-white text-xl"></i>
                            </div>
                            <h2 class="text-2xl font-extrabold text-slate-800">
                                Équipe A <span class="text-xl text-slate-400 font-semibold">(<?= count($joueursParEquipe['A']) ?> joueurs)</span>
                            </h2>
                        </div>

                        <?php if (empty($joueursParEquipe['A'])): ?>
                            <div class="text-center py-12 text-slate-400">
                                <i class="fas fa-user-slash text-5xl mb-4"></i>
                                <p class="text-lg font-semibold">Aucun joueur convoqué dans cette équipe.</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach ($joueursParEquipe['A'] as $convoque): ?>
                                    <div class="flex items-center gap-4 p-5 bg-slate-50 rounded-2xl border border-slate-100 hover:bg-slate-100 transition-all">
                                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
                                            <i class="fas fa-user text-blue-600 text-2xl"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-lg font-bold text-slate-800">
                                                <?= htmlspecialchars($convoque['nom']) ?>
                                                <?= htmlspecialchars($convoque['prenom']) ?>
                                            </p>
                                            <?php if ($convoque['numero_maillot']): ?>
                                                <p class="text-sm text-slate-500 font-semibold">N° <?= $convoque['numero_maillot'] ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($convoque['est_capitaine']): ?>
                                            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-extrabold bg-gradient-to-r from-yellow-100 to-orange-100 text-yellow-700 border border-yellow-200">
                                                <i class="fas fa-crown"></i>
                                                Capitaine
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Équipe B -->
                    <div class="bg-white rounded-3xl border border-slate-100 p-8 shadow-xl">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-red-600 to-pink-700 flex items-center justify-center shadow-lg shadow-red-200">
                                <i class="fas fa-users text-white text-xl"></i>
                            </div>
                            <h2 class="text-2xl font-extrabold text-slate-800">
                                Équipe B <span class="text-xl text-slate-400 font-semibold">(<?= count($joueursParEquipe['B']) ?> joueurs)</span>
                            </h2>
                        </div>

                        <?php if (empty($joueursParEquipe['B'])): ?>
                            <div class="text-center py-12 text-slate-400">
                                <i class="fas fa-user-slash text-5xl mb-4"></i>
                                <p class="text-lg font-semibold">Aucun joueur convoqué dans cette équipe.</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach ($joueursParEquipe['B'] as $convoque): ?>
                                    <div class="flex items-center gap-4 p-5 bg-slate-50 rounded-2xl border border-slate-100 hover:bg-slate-100 transition-all">
                                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-red-100 to-pink-100 flex items-center justify-center">
                                            <i class="fas fa-user text-red-600 text-2xl"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-lg font-bold text-slate-800">
                                                <?= htmlspecialchars($convoque['nom']) ?>
                                                <?= htmlspecialchars($convoque['prenom']) ?>
                                            </p>
                                            <?php if ($convoque['numero_maillot']): ?>
                                                <p class="text-sm text-slate-500 font-semibold">N° <?= $convoque['numero_maillot'] ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($convoque['est_capitaine']): ?>
                                            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-extrabold bg-gradient-to-r from-yellow-100 to-orange-100 text-yellow-700 border border-yellow-200">
                                                <i class="fas fa-crown"></i>
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
                    <div class="bg-white rounded-3xl border border-slate-100 p-8 mt-8 shadow-xl">
                        <h2 class="text-2xl font-extrabold text-slate-800 mb-8 flex items-center gap-3">
                            <i class="fas fa-clipboard-list text-green-600"></i>
                            Saisir le résultat
                        </h2>

                        <form action="/resultatMatch-resultatsave" method="POST">
                            <input type="hidden" name="match_id" value="<?= $match['id'] ?>">

                            <div class="flex flex-col md:flex-row items-center gap-6">
                                <div class="text-center">
                                    <label class="text-sm text-slate-500 font-bold uppercase tracking-wide block mb-3">Équipe A</label>
                                    <input type="number" name="buts_equipe_a" min="0" value="0"
                                        class="w-24 border-2 border-slate-200 rounded-2xl px-5 py-4 text-center text-3xl font-extrabold focus:outline-none focus:ring-4 focus:ring-green-200 focus:border-green-500 bg-white">
                                </div>
                                <span class="text-4xl font-extrabold text-slate-300 mt-8 md:mt-0">—</span>
                                <div class="text-center">
                                    <label class="text-sm text-slate-500 font-bold uppercase tracking-wide block mb-3">Équipe B</label>
                                    <input type="number" name="buts_equipe_b" min="0" value="0"
                                        class="w-24 border-2 border-slate-200 rounded-2xl px-5 py-4 text-center text-3xl font-extrabold focus:outline-none focus:ring-4 focus:ring-green-200 focus:border-green-500 bg-white">
                                </div>
                                <div class="mt-8 md:mt-0">
                                    <button type="submit" name="save_resultat" value="Enregistrer"
                                        class="px-8 py-4 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-2xl text-lg font-bold hover:from-green-700 hover:to-green-800 transition-all shadow-lg shadow-green-200">
                                        <i class="fas fa-check mr-2"></i>
                                        Enregistrer
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>

</html>
