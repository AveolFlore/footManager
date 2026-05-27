<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../autoload.php';
require_once __DIR__ . '/../../../middleware/Role.php';
requireLogin();

use Config\Database;
use Models\Match_seance\MatchEntity;
use Models\Caisse\Caisse;
use Models\Activite_log\Activite;

$db = (new Database())->connect();
$matchModel = new MatchEntity($db);
$caisseModel = new Caisse($db);
$activiteModel = new Activite($db);

$nextMatch = $db->query("SELECT * FROM match_seance WHERE date >= CURDATE() ORDER BY date ASC LIMIT 1")->fetch();
$solde = $caisseModel->getSolde();
$activities = $activiteModel->getLatest(5);

$pageTitle = "Tableau de bord";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - Club Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-slate-50 to-gray-100 font-sans text-slate-800">

    <div class="flex min-h-screen">
        <?php include __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1">
            <?php include __DIR__ . '/../../partials/header.php'; ?>
            
            <div class="p-6 md:p-8 lg:p-10">
                <!-- Hero Header -->
                <div class="mb-10">
                    <h1 class="text-4xl font-extrabold bg-gradient-to-r from-green-700 to-green-500 bg-clip-text text-transparent">
                        Tableau de bord
                    </h1>
                    <p class="mt-2 text-slate-500 text-lg">
                        Bienvenue, <span class="font-semibold text-green-600"><?= htmlspecialchars($_SESSION['user']['prenom']) ?> !</span> Voici ce qui se passe aujourd'hui.
                    </p>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                    <!-- Widget 1: Prochain Match -->
                    <div class="group relative overflow-hidden bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 border border-slate-100">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-green-100 to-emerald-100 rounded-bl-full opacity-50 group-hover:opacity-70 transition-opacity"></div>
                        <div class="relative">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-lg">
                                    <i class="fa-solid fa-calendar-check text-white text-2xl"></i>
                                </div>
                                <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">
                                    Aujourd'hui
                                </span>
                            </div>
                            <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-2">Prochain événement</h3>
                            <?php if ($nextMatch): ?>
                                <p class="text-2xl font-bold text-slate-800">
                                    <?= date('d F', strtotime($nextMatch['date'])) ?>
                                </p>
                                <p class="text-sm text-slate-600 mt-1">
                                    <?= htmlspecialchars($nextMatch['lieu']) ?>
                                </p>
                                <span class="inline-block mt-3 text-xs font-semibold text-slate-700 bg-slate-100 px-3 py-1 rounded-lg">
                                    <?= $nextMatch['type'] === 'match' ? 'Match officiel' : 'Entraînement' ?>
                                </span>
                            <?php else: ?>
                                <p class="text-2xl font-bold text-slate-400">Aucun événement</p>
                                <p class="text-sm text-slate-500 mt-1">Préparez votre premier match !</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Widget 2: Solde Caisse (if authorized) -->
                    <?php if (in_array($_SESSION['user']['role'], ['president', 'censeur', 'organisateur'])): ?>
                        <div class="group relative overflow-hidden bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 border border-slate-100">
                            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-bl-full opacity-50 group-hover:opacity-70 transition-opacity"></div>
                            <div class="relative">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl shadow-lg">
                                        <i class="fa-solid fa-wallet text-white text-2xl"></i>
                                    </div>
                                    <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-3 py-1 rounded-full">
                                        Finances
                                    </span>
                                </div>
                                <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-2">Solde caisse</h3>
                                <p class="text-3xl font-extrabold text-slate-800">
                                    <?= number_format($solde ?? 0, 0, ',', ' ') ?>
                                    <span class="text-base font-normal text-slate-500">FCFA</span>
                                </p>
                                <p class="text-sm text-slate-500 mt-2">
                                    <i class="fa-solid fa-circle-check text-green-500 mr-1"></i>
                                    À jour
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Widget 3: Activités récentes (small widget) -->
                    <div class="group relative overflow-hidden bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 border border-slate-100">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-purple-100 to-pink-100 rounded-bl-full opacity-50 group-hover:opacity-70 transition-opacity"></div>
                        <div class="relative">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl shadow-lg">
                                    <i class="fa-solid fa-clock-rotate-left text-white text-2xl"></i>
                                </div>
                                <span class="text-xs font-semibold text-purple-600 bg-purple-50 px-3 py-1 rounded-full">
                                    Actualité
                                </span>
                            </div>
                            <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-2">Activités</h3>
                            <p class="text-2xl font-bold text-slate-800">
                                <?= count($activities) ?>
                            </p>
                            <p class="text-sm text-slate-500 mt-1">Dernières 24h</p>
                        </div>
                    </div>

                    <!-- Widget 4: Quick Actions -->
                    <div class="group relative overflow-hidden bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 border border-slate-100">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-orange-100 to-amber-100 rounded-bl-full opacity-50 group-hover:opacity-70 transition-opacity"></div>
                        <div class="relative">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl shadow-lg">
                                    <i class="fa-solid fa-bolt text-white text-2xl"></i>
                                </div>
                                <span class="text-xs font-semibold text-orange-600 bg-orange-50 px-3 py-1 rounded-full">
                                    Rapide
                                </span>
                            </div>
                            <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-2">Actions rapides</h3>
                            <div class="flex flex-wrap gap-2 mt-2">
                                <a href="/page-matchcreate" class="text-xs font-semibold text-white bg-gradient-to-r from-green-500 to-emerald-600 px-3 py-1.5 rounded-lg hover:shadow-lg transition-all">
                                    + Match
                                </a>
                                <a href="/page-rule" class="text-xs font-semibold text-slate-700 bg-slate-100 px-3 py-1.5 rounded-lg hover:bg-slate-200 transition-all">
                                    Règles
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Column 1 & 2: Activités -->
                    <div class="lg:col-span-2 space-y-8">
                        <!-- Activity Feed -->
                        <div class="bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden">
                            <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                                <div class="flex items-center justify-between">
                                    <h2 class="text-xl font-bold text-slate-800 flex items-center gap-3">
                                        <i class="fa-solid fa-list-check text-green-500"></i>
                                        Fil d'activité
                                    </h2>
                                    <span class="text-sm text-slate-500"><?= count($activities) ?> éléments</span>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <?php if (empty($activities)): ?>
                                        <div class="text-center py-12">
                                            <i class="fa-solid fa-inbox text-slate-300 text-5xl mb-4"></i>
                                            <p class="text-slate-500 font-medium">Aucune activité récente</p>
                                            <p class="text-sm text-slate-400 mt-2">Commencez par créer un match !</p>
                                        </div>
                                    <?php else: ?>
                                        <?php foreach ($activities as $act): ?>
                                            <div class="flex items-start gap-4 p-4 bg-slate-50 rounded-xl hover:bg-slate-100 transition-all">
                                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-100 to-emerald-200 flex items-center justify-center flex-shrink-0">
                                                    <i class="fa-solid fa-user-check text-emerald-600"></i>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="font-semibold text-slate-800">
                                                        <?= htmlspecialchars($act['description']) ?>
                                                    </p>
                                                    <div class="flex items-center gap-3 mt-1">
                                                        <span class="text-xs text-slate-400 flex items-center gap-1">
                                                            <i class="fa-regular fa-clock"></i>
                                                            <?= date('H:i - d F', strtotime($act['date_action'])) ?>
                                                        </span>
                                                        <span class="text-xs text-slate-500 font-medium">
                                                            par <?= htmlspecialchars($act['prenom'] ?? 'Système') ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Column 3: Side Widgets -->
                    <div class="space-y-8">
                        <!-- Top Joueur Placeholder -->
                        <div class="bg-gradient-to-br from-yellow-50 to-amber-50 rounded-2xl p-6 border border-yellow-100 shadow-lg">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="p-3 bg-yellow-100 rounded-xl">
                                    <i class="fa-solid fa-trophy text-yellow-600 text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-yellow-800">Top Joueur</h3>
                                    <p class="text-xs text-yellow-700/70">Mois en cours</p>
                                </div>
                            </div>
                            <div class="bg-white rounded-xl p-4 shadow-sm border border-yellow-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-yellow-200 to-amber-300 flex items-center justify-center text-2xl">
                                        🎯
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-700">En cours...</p>
                                        <p class="text-xs text-slate-500">Saisissez des performances !</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Info -->
                        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-lg">
                            <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-circle-info text-blue-500"></i>
                                Raccourcis utiles
                            </h3>
                            <div class="space-y-3">
                                <a href="/page-match" class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl hover:bg-slate-100 transition-all">
                                    <i class="fa-solid fa-futbol text-green-500"></i>
                                    <span class="font-medium text-slate-700">Voir les matchs</span>
                                </a>
                                <a href="/page-convocation" class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl hover:bg-slate-100 transition-all">
                                    <i class="fa-solid fa-clipboard-list text-blue-500"></i>
                                    <span class="font-medium text-slate-700">Gérer convocations</span>
                                </a>
                                <a href="/page-rule" class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl hover:bg-slate-100 transition-all">
                                    <i class="fa-solid fa-scale-balanced text-purple-500"></i>
                                    <span class="font-medium text-slate-700">Voir règlements</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

</body>
</html>
