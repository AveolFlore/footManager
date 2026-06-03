<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../autoload.php';
require_once __DIR__ . '/../../../middleware/Role.php';
requireLogin();

use Config\Database;
use Models\Match_seance\MatchEntity;
use Models\Caisse\Caisse;
use Models\Activite_log\Activite;
use Models\utilisateur\User;

$db = (new Database())->connect();
$matchModel = new MatchEntity($db);
$caisseModel = new Caisse($db);
$activiteModel = new Activite($db);
$userModel = new User($db);

// Get Data
$nextMatch = $db->query("SELECT * FROM match_seance WHERE date >= CURDATE() ORDER BY date ASC LIMIT 1")->fetch();
$recentMatches = $db->query("SELECT * FROM match_seance ORDER BY date DESC LIMIT 4")->fetchAll();
$solde = $caisseModel->getSolde();
$activities = $activiteModel->getLatest(8);
$totalJoueurs = $db->query("SELECT COUNT(*) as count FROM users WHERE role = 'joueur'")->fetch()['count'];
$totalMatchs = $db->query("SELECT COUNT(*) as count FROM match_seance")->fetch()['count'];
$totalEquipes = 3; // Static for demo
$tauxPresence = 87; // Static for demo
$totalConvocations = 24; // Static for demo

// Mock classement data
$classement = [
    ['position' => 1, 'club' => 'FC Blue Lock', 'matches' => 12, 'wins' => 10, 'draws' => 1, 'losses' => 1, 'points' => 31],
    ['position' => 2, 'club' => 'Team V', 'matches' => 12, 'wins' => 9, 'draws' => 2, 'losses' => 1, 'points' => 29],
    ['position' => 3, 'club' => 'Team Z', 'matches' => 12, 'wins' => 8, 'draws' => 2, 'losses' => 2, 'points' => 26],
    ['position' => 4, 'club' => 'Team X', 'matches' => 12, 'wins' => 7, 'draws' => 2, 'losses' => 3, 'points' => 23],
    ['position' => 5, 'club' => 'Team Y', 'matches' => 12, 'wins' => 6, 'draws' => 3, 'losses' => 3, 'points' => 21],
];

$pageTitle = "Tableau de bord";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - FC Blue Lock</title>
    <link rel="icon" type="image/png" href="/images/blue_lock_logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-slate-50 to-slate-100 font-sans text-slate-800">

    <div class="flex min-h-screen">
        <?php include __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1">
            <?php include __DIR__ . '/../../partials/header.php'; ?>
            
            <div class="p-6 md:p-8 lg:p-10">
                <!-- HERO WELCOME SECTION -->
                <div class="mb-10 bg-gradient-to-r from-slate-900 via-green-900 to-slate-900 rounded-3xl p-8 md:p-10 shadow-2xl overflow-hidden relative">
                    <!-- Decorative Elements -->
                    <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-green-500/30 to-transparent rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                    <div class="absolute bottom-0 left-0 w-96 h-96 bg-gradient-to-tr from-blue-500/20 to-transparent rounded-full blur-3xl translate-y-1/2 -translate-x-1/2"></div>

                    <div class="relative z-10 flex flex-col md:flex-row gap-8 items-center justify-between">
                        <div class="flex-1">
                            <h2 class="text-green-400 font-semibold uppercase tracking-widest text-sm mb-3">Welcome back, Coach!</h2>
                            <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4">
                                Bonjour, <span class="text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-blue-400"><?= htmlspecialchars($_SESSION['user']['prenom']) ?></span> 👋
                            </h1>
                            <p class="text-slate-300 text-lg mb-6 max-w-2xl">
                                Voici ce qui se passe aujourd'hui dans le club. Votre équipe est prête à dominer le terrain!
                            </p>

                            <!-- Quick Stats Row -->
                            <div class="flex flex-wrap gap-4">
                                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl px-5 py-3 flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-green-500/30 flex items-center justify-center text-green-400 text-xl">
                                        <i class="fa-solid fa-calendar-check"></i>
                                    </div>
                                    <div>
                                        <p class="text-slate-300 text-xs uppercase font-semibold">Prochain Match</p>
                                        <p class="text-white font-bold text-sm">
                                            <?= $nextMatch ? date('d M, H:i', strtotime($nextMatch['date'])) : 'À planifier' ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl px-5 py-3 flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-blue-500/30 flex items-center justify-center text-blue-400 text-xl">
                                        <i class="fa-solid fa-users"></i>
                                    </div>
                                    <div>
                                        <p class="text-slate-300 text-xs uppercase font-semibold">Joueurs Actifs</p>
                                        <p class="text-white font-bold text-sm"><?= $totalJoueurs ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="flex flex-col gap-3 w-full md:w-auto">
                            <a href="/page-matchcreate" class="flex items-center justify-center gap-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold px-8 py-4 rounded-2xl shadow-lg shadow-green-900/30 transition-all hover:scale-105">
                                <i class="fa-solid fa-plus"></i>
                                Planifier un Match
                            </a>
                            <a href="/page-convocation" class="flex items-center justify-center gap-3 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white font-bold px-8 py-4 rounded-2xl transition-all">
                                <i class="fa-solid fa-clipboard-list"></i>
                                Envoyer une Convocation
                            </a>
                        </div>
                    </div>
                </div>

                <!-- STATISTICS CARDS GRID -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                    <!-- Total Players -->
                    <div class="group relative overflow-hidden bg-white rounded-3xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 border border-slate-100">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-green-100 to-emerald-100 rounded-bl-full opacity-70 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-700 rounded-2xl flex items-center justify-center shadow-lg shadow-green-200">
                                    <i class="fa-solid fa-users text-white text-2xl"></i>
                                </div>
                                <span class="flex items-center gap-1 text-xs font-semibold text-green-600 bg-green-100 px-3 py-1.5 rounded-full">
                                    <i class="fa-solid fa-arrow-trend-up"></i>
                                    12%
                                </span>
                            </div>
                            <p class="text-slate-500 text-sm font-semibold uppercase tracking-widest mb-1">Joueurs Total</p>
                            <p class="text-4xl font-extrabold text-slate-800"><?= $totalJoueurs ?></p>
                        </div>
                    </div>

                    <!-- Total Teams -->
                    <div class="group relative overflow-hidden bg-white rounded-3xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 border border-slate-100">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-bl-full opacity-70 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-700 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-200">
                                    <i class="fa-solid fa-people-group text-white text-2xl"></i>
                                </div>
                                <span class="flex items-center gap-1 text-xs font-semibold text-blue-600 bg-blue-100 px-3 py-1.5 rounded-full">
                                    <i class="fa-solid fa-minus"></i>
                                    0%
                                </span>
                            </div>
                            <p class="text-slate-500 text-sm font-semibold uppercase tracking-widest mb-1">Équipes</p>
                            <p class="text-4xl font-extrabold text-slate-800"><?= $totalEquipes ?></p>
                        </div>
                    </div>

                    <!-- Total Matches -->
                    <div class="group relative overflow-hidden bg-white rounded-3xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 border border-slate-100">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-purple-100 to-pink-100 rounded-bl-full opacity-70 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-700 rounded-2xl flex items-center justify-center shadow-lg shadow-purple-200">
                                    <i class="fa-solid fa-futbol text-white text-2xl"></i>
                                </div>
                                <span class="flex items-center gap-1 text-xs font-semibold text-purple-600 bg-purple-100 px-3 py-1.5 rounded-full">
                                    <i class="fa-solid fa-arrow-trend-up"></i>
                                    8%
                                </span>
                            </div>
                            <p class="text-slate-500 text-sm font-semibold uppercase tracking-widest mb-1">Matchs Total</p>
                            <p class="text-4xl font-extrabold text-slate-800"><?= $totalMatchs ?></p>
                        </div>
                    </div>

                    <!-- Participation Rate -->
                    <div class="group relative overflow-hidden bg-white rounded-3xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 border border-slate-100">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-orange-100 to-yellow-100 rounded-bl-full opacity-70 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-yellow-600 rounded-2xl flex items-center justify-center shadow-lg shadow-orange-200">
                                    <i class="fa-solid fa-check-to-slot text-white text-2xl"></i>
                                </div>
                                <span class="flex items-center gap-1 text-xs font-semibold text-orange-600 bg-orange-100 px-3 py-1.5 rounded-full">
                                    <i class="fa-solid fa-check"></i>
                                    Excellent
                                </span>
                            </div>
                            <p class="text-slate-500 text-sm font-semibold uppercase tracking-widest mb-1">Taux de Présence</p>
                            <p class="text-4xl font-extrabold text-slate-800"><?= $tauxPresence ?>%</p>
                        </div>
                    </div>
                </div>

                <!-- MAIN CONTENT GRID -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- COLUMN 1 & 2 (MATCHES & CLASSMENT) -->
                    <div class="lg:col-span-2 space-y-8">
                        <!-- MATCHES SECTION -->
                        <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                            <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-white to-slate-50">
                                <div class="flex items-center justify-between">
                                    <h2 class="text-2xl font-extrabold text-slate-800 flex items-center gap-3">
                                        <i class="fa-solid fa-futbol text-green-600"></i>
                                        Derniers Matchs
                                    </h2>
                                    <a href="/page-match" class="text-green-600 font-semibold text-sm hover:text-green-700 transition flex items-center gap-2">
                                        Voir tous <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="p-6">
                                <?php if (empty($recentMatches)): ?>
                                    <div class="text-center py-12">
                                        <i class="fas fa-calendar-times text-slate-300 text-5xl mb-4"></i>
                                        <p class="text-slate-500 font-medium">Aucun match programmé</p>
                                        <a href="/page-matchcreate" class="inline-block mt-4 text-green-600 font-semibold">Planifier un match →</a>
                                    </div>
                                <?php else: ?>
                                    <div class="space-y-4">
                                        <?php foreach ($recentMatches as $match): ?>
                                            <div class="flex items-center gap-4 p-4 bg-slate-50 hover:bg-slate-100 rounded-2xl transition-all border border-slate-100">
                                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-slate-800 to-slate-700 flex items-center justify-center text-white text-lg font-bold shadow-lg">
                                                    <?= date('d', strtotime($match['date'])) ?>
                                                </div>
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <span class="px-3 py-1 rounded-full text-xs font-bold <?= $match['type'] === 'match' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' ?>">
                                                            <?= strtoupper($match['type']) ?>
                                                        </span>
                                                        <span class="text-slate-500 text-xs"><?= date('M Y', strtotime($match['date'])) ?></span>
                                                    </div>
                                                    <h3 class="font-bold text-slate-800"><?= htmlspecialchars($match['titre'] ?? 'Match sans titre') ?></h3>
                                                    <p class="text-sm text-slate-500"><i class="fas fa-map-marker-alt mr-1"></i> <?= htmlspecialchars($match['lieu'] ?? 'Lieu non défini') ?></p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-xs text-slate-500 font-semibold uppercase">Heure</p>
                                                    <p class="text-lg font-extrabold text-slate-700"><?= date('H:i', strtotime($match['date'])) ?></p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- CLASSMENT SECTION -->
                        <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                            <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-white to-slate-50">
                                <h2 class="text-2xl font-extrabold text-slate-800 flex items-center gap-3">
                                    <i class="fa-solid fa-trophy text-yellow-600"></i>
                                    Classement
                                </h2>
                            </div>

                            <div class="p-6">
                                <table class="w-full">
                                    <thead>
                                        <tr class="text-xs text-slate-500 uppercase tracking-widest border-b-2 border-slate-100">
                                            <th class="pb-3 text-left w-12">#</th>
                                            <th class="pb-3 text-left">Club</th>
                                            <th class="pb-3 text-center w-16">M</th>
                                            <th class="pb-3 text-center w-16">V</th>
                                            <th class="pb-3 text-center w-16">N</th>
                                            <th class="pb-3 text-center w-16">D</th>
                                            <th class="pb-3 text-right">Pts</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <?php foreach ($classement as $equipe): ?>
                                            <tr class="hover:bg-slate-50 transition-all <?= $equipe['club'] === 'FC Blue Lock' ? 'bg-gradient-to-r from-green-50 to-transparent border-l-4 border-l-green-500' : '' ?>">
                                                <td class="py-4">
                                                    <div class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-sm
                                                        <?php if ($equipe['position'] === 1): ?>
                                                            bg-gradient-to-br from-yellow-400 to-yellow-600 text-white shadow-md
                                                        <?php elseif ($equipe['position'] === 2): ?>
                                                            bg-gradient-to-br from-slate-300 to-slate-400 text-slate-800
                                                        <?php elseif ($equipe['position'] === 3): ?>
                                                            bg-gradient-to-br from-orange-400 to-orange-600 text-white
                                                        <?php else: ?>
                                                            bg-slate-100 text-slate-600
                                                        <?php endif; ?>">
                                                        <?= $equipe['position'] ?>
                                                    </div>
                                                </td>
                                                <td class="py-4">
                                                    <span class="font-bold text-slate-800 text-lg"><?= $equipe['club'] ?></span>
                                                </td>
                                                <td class="py-4 text-center text-slate-600 font-semibold"><?= $equipe['matches'] ?></td>
                                                <td class="py-4 text-center text-green-700 font-bold"><?= $equipe['wins'] ?></td>
                                                <td class="py-4 text-center text-slate-500 font-semibold"><?= $equipe['draws'] ?></td>
                                                <td class="py-4 text-center text-red-600 font-semibold"><?= $equipe['losses'] ?></td>
                                                <td class="py-4 text-right">
                                                    <span class="text-2xl font-extrabold text-slate-800"><?= $equipe['points'] ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- COLUMN 3 (ACTIVITIES & QUICK INFO) -->
                    <div class="space-y-8">
                        <!-- ACTIVITY FEED -->
                        <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                            <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-white to-slate-50">
                                <div class="flex items-center justify-between">
                                    <h2 class="text-xl font-extrabold text-slate-800 flex items-center gap-2">
                                        <i class="fa-solid fa-list-check text-green-600"></i>
                                        Activités Récentes
                                    </h2>
                                    <span class="text-xs text-slate-500 font-semibold bg-slate-100 px-3 py-1 rounded-full"><?= count($activities) ?></span>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="space-y-5">
                                    <?php if (empty($activities)): ?>
                                        <div class="text-center py-10">
                                            <i class="fas fa-inbox text-slate-300 text-4xl mb-3"></i>
                                            <p class="text-slate-500 font-medium">Aucune activité</p>
                                        </div>
                                    <?php else: ?>
                                        <?php foreach ($activities as $index => $act): ?>
                                            <div class="flex gap-4">
                                                <div class="flex flex-col items-center">
                                                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-green-100 to-emerald-200 flex items-center justify-center text-emerald-700 text-lg shadow-sm">
                                                        <i class="fas fa-<?= $index % 3 === 0 ? 'clipboard-list' : ($index % 3 === 1 ? 'user-plus' : 'futbol') ?>"></i>
                                                    </div>
                                                    <?php if ($index < count($activities) - 1): ?>
                                                        <div class="w-0.5 h-full bg-slate-200 mt-2"></div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="flex-1 pb-4">
                                                    <p class="font-semibold text-slate-800 text-sm">
                                                        <?= htmlspecialchars($act['description']) ?>
                                                    </p>
                                                    <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                                                        <i class="far fa-clock"></i>
                                                        <?= date('d M, H:i', strtotime($act['date_action'])) ?>
                                                    </p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- QUICK INFO CARD -->
                        <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl shadow-xl p-6 text-white border border-slate-700">
                            <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-fire text-orange-400"></i>
                                Focus Aujourd'hui
                            </h3>
                            <ul class="space-y-3">
                                <li class="flex items-center gap-3 text-sm">
                                    <div class="w-2 h-2 rounded-full bg-green-400"></div>
                                    <span class="text-slate-300">Rassemblement 18:00</span>
                                </li>
                                <li class="flex items-center gap-3 text-sm">
                                    <div class="w-2 h-2 rounded-full bg-yellow-400"></div>
                                    <span class="text-slate-300">Récupération active</span>
                                </li>
                                <li class="flex items-center gap-3 text-sm">
                                    <div class="w-2 h-2 rounded-full bg-blue-400"></div>
                                    <span class="text-slate-300">Stratégie offensive</span>
                                </li>
                            </ul>
                        </div>

                        <!-- FINANCE SNIPPET -->
                        <?php if (in_array($_SESSION['user']['role'], ['president', 'censeur', 'organisateur'])): ?>
                            <div class="bg-white rounded-3xl shadow-xl border border-slate-100 p-6">
                                <h3 class="font-bold text-lg text-slate-800 mb-4 flex items-center gap-2">
                                    <i class="fa-solid fa-wallet text-green-600"></i>
                                    Solde Caisse
                                </h3>
                                <div class="text-center">
                                    <p class="text-5xl font-extrabold bg-gradient-to-r from-green-600 to-green-800 bg-clip-text text-transparent mb-2">
                                        <?= number_format($solde ?? 0, 0, ',', ' ') ?>
                                    </p>
                                    <p class="text-sm text-slate-500 uppercase tracking-wider font-semibold">FCFA</p>
                                </div>
                                <div class="mt-6 pt-4 border-t border-slate-100">
                                    <a href="/page-finance" class="flex items-center justify-center gap-2 text-green-600 font-semibold text-sm hover:text-green-700 transition">
                                        Voir les détails financiers <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

</body>
</html>
