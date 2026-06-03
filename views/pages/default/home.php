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
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&display=swap');
        
        .cyber-bg {
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 100%);
        }
        
        .holo-card {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(34, 211, 238, 0.25);
            box-shadow: 0 0 35px rgba(34, 211, 238, 0.1);
        }
        
        .neon-text {
            text-shadow: 0 0 10px rgb(34 211 238),
                        0 0 20px rgb(34 211 238);
        }
        
        .stat-glow {
            transition: all 0.4s ease;
        }
        
        .stat-glow:hover {
            transform: translateY(-4px);
            box-shadow: 0 0 30px rgba(34, 211, 238, 0.3);
        }
        
        .scan-line {
            position: relative;
        }
        
        .scan-line::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 40%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
            animation: scan 6s linear infinite;
        }
        
        @keyframes scan {
            0% { left: -100%; }
            100% { left: 300%; }
        }
    </style>
</head>
<body class="cyber-bg text-slate-200 min-h-screen font-sans">

    <div class="flex min-h-screen">
        <?php include __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1">
            <?php include __DIR__ . '/../../partials/header.php'; ?>
            
            <div class="p-6 md:p-8 lg:p-10">
                
                <!-- HERO CYBER -->
                <div class="mb-10 holo-card rounded-3xl p-8 md:p-12 relative overflow-hidden scan-line">
                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/10 via-transparent to-violet-500/10"></div>
                    
                    <div class="relative z-10 flex flex-col md:flex-row gap-8 items-center justify-between">
                        <div class="flex-1">
                            <h2 class="text-cyan-400 font-medium uppercase tracking-[4px] text-sm mb-2">SYSTÈME OPÉRATIONNEL</h2>
                            <h1 class="text-4xl md:text-5xl font-bold tracking-tighter neon-text mb-4">
                                BIENVENUE, <span class="text-cyan-300"><?= htmlspecialchars($_SESSION['user']['prenom']) ?></span>
                            </h1>
                            <p class="text-slate-400 text-lg max-w-xl">
                                Tableau de contrôle central • FC Blue Lock
                            </p>
                        </div>

                        <div class="flex flex-col gap-3 w-full md:w-auto">
                            <a href="/page-matchcreate" 
                               class="btn-glow flex items-center justify-center gap-3 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold px-8 py-4 rounded-2xl shadow-lg shadow-cyan-500/50 transition-all">
                                <i class="fa-solid fa-plus"></i>
                                PLANIFIER MATCH
                            </a>
                            <a href="/page-convocation" 
                               class="flex items-center justify-center gap-3 border border-slate-600 hover:border-cyan-400 text-slate-300 hover:text-cyan-400 font-bold px-8 py-4 rounded-2xl transition-all">
                                <i class="fa-solid fa-clipboard-list"></i>
                                CONVOCATION
                            </a>
                        </div>
                    </div>
                </div>

                <!-- STATISTICS GRID -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                    <!-- Joueurs -->
                    <div class="holo-card rounded-3xl p-6 stat-glow">
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-16 h-16 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-2xl flex items-center justify-center">
                                <i class="fa-solid fa-users text-white text-3xl"></i>
                            </div>
                            <span class="text-emerald-400 text-sm font-medium flex items-center gap-1">
                                <i class="fa-solid fa-arrow-trend-up"></i> +12%
                            </span>
                        </div>
                        <p class="text-slate-400 text-sm uppercase tracking-widest">JOUEURS ACTIFS</p>
                        <p class="text-5xl font-bold text-white mt-2"><?= $totalJoueurs ?></p>
                    </div>

                    <!-- Équipes -->
                    <div class="holo-card rounded-3xl p-6 stat-glow">
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-16 h-16 bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl flex items-center justify-center">
                                <i class="fa-solid fa-people-group text-white text-3xl"></i>
                            </div>
                            <span class="text-slate-400 text-sm font-medium">STABLE</span>
                        </div>
                        <p class="text-slate-400 text-sm uppercase tracking-widest">ÉQUIPES</p>
                        <p class="text-5xl font-bold text-white mt-2"><?= $totalEquipes ?></p>
                    </div>

                    <!-- Matchs -->
                    <div class="holo-card rounded-3xl p-6 stat-glow">
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-rose-600 rounded-2xl flex items-center justify-center">
                                <i class="fa-solid fa-futbol text-white text-3xl"></i>
                            </div>
                            <span class="text-emerald-400 text-sm font-medium flex items-center gap-1">
                                <i class="fa-solid fa-arrow-trend-up"></i> +8%
                            </span>
                        </div>
                        <p class="text-slate-400 text-sm uppercase tracking-widest">MATCHS</p>
                        <p class="text-5xl font-bold text-white mt-2"><?= $totalMatchs ?></p>
                    </div>

                    <!-- Présence -->
                    <div class="holo-card rounded-3xl p-6 stat-glow">
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-16 h-16 bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center">
                                <i class="fa-solid fa-chart-pie text-white text-3xl"></i>
                            </div>
                            <span class="text-emerald-400 text-sm font-medium">EXCELLENT</span>
                        </div>
                        <p class="text-slate-400 text-sm uppercase tracking-widest">TAUX PRÉSENCE</p>
                        <p class="text-5xl font-bold text-white mt-2"><?= $tauxPresence ?>%</p>
                    </div>
                </div>

                <!-- MAIN GRID -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- LEFT + CENTER -->
                    <div class="lg:col-span-2 space-y-8">
                        
                        <!-- Derniers Matchs -->
                        <div class="holo-card rounded-3xl overflow-hidden">
                            <div class="p-6 border-b border-slate-700 bg-gradient-to-r from-slate-900 to-transparent">
                                <h2 class="text-2xl font-bold flex items-center gap-3">
                                    <i class="fa-solid fa-futbol text-cyan-400"></i>
                                    DERNIERS MATCHS
                                </h2>
                            </div>
                            <div class="p-6 space-y-4">
                                <?php if (empty($recentMatches)): ?>
                                    <p class="text-slate-400 text-center py-12">Aucun match récent</p>
                                <?php else: ?>
                                    <?php foreach ($recentMatches as $match): ?>
                                        <div class="flex items-center gap-5 p-5 bg-slate-900/60 hover:bg-slate-800/80 rounded-2xl border border-slate-700 transition-all">
                                            <div class="w-16 h-16 bg-slate-800 rounded-2xl flex items-center justify-center text-2xl font-bold text-cyan-300">
                                                <?= date('d', strtotime($match['date'])) ?>
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center gap-3 mb-2">
                                                    <span class="px-4 py-1 text-xs font-bold rounded-full <?= $match['type'] === 'match' ? 'bg-cyan-500/20 text-cyan-400' : 'bg-violet-500/20 text-violet-400' ?>">
                                                        <?= strtoupper($match['type']) ?>
                                                    </span>
                                                    <span class="text-xs text-slate-400"><?= date('M Y', strtotime($match['date'])) ?></span>
                                                </div>
                                                <h3 class="font-semibold text-lg"><?= htmlspecialchars($match['titre'] ?? 'Match') ?></h3>
                                                <p class="text-slate-400 text-sm"><?= htmlspecialchars($match['lieu'] ?? 'Lieu inconnu') ?></p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-cyan-300 text-2xl font-bold"><?= date('H:i', strtotime($match['date'])) ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Classement -->
                        <div class="holo-card rounded-3xl overflow-hidden">
                            <div class="p-6 border-b border-slate-700">
                                <h2 class="text-2xl font-bold flex items-center gap-3">
                                    <i class="fa-solid fa-trophy text-yellow-400"></i>
                                    CLASSEMENT
                                </h2>
                            </div>
                            <div class="p-6">
                                <table class="w-full">
                                    <thead>
                                        <tr class="text-xs uppercase text-slate-400 border-b border-slate-700">
                                            <th class="pb-4 text-left w-12">#</th>
                                            <th class="pb-4 text-left">Club</th>
                                            <th class="pb-4 text-center">M</th>
                                            <th class="pb-4 text-center">V</th>
                                            <th class="pb-4 text-center">N</th>
                                            <th class="pb-4 text-center">D</th>
                                            <th class="pb-4 text-right">Pts</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-700">
                                        <?php foreach ($classement as $equipe): ?>
                                            <tr class="hover:bg-slate-800/50 transition-all <?= $equipe['club'] === 'FC Blue Lock' ? 'bg-cyan-900/30 border-l-4 border-cyan-400' : '' ?>">
                                                <td class="py-5">
                                                    <div class="w-9 h-9 rounded-2xl flex items-center justify-center font-bold <?= $equipe['position'] <= 3 ? 'bg-gradient-to-br from-yellow-400 to-amber-500 text-black' : 'bg-slate-700 text-slate-300' ?>">
                                                        <?= $equipe['position'] ?>
                                                    </div>
                                                </td>
                                                <td class="py-5 font-semibold"><?= $equipe['club'] ?></td>
                                                <td class="py-5 text-center text-slate-300"><?= $equipe['matches'] ?></td>
                                                <td class="py-5 text-center text-emerald-400 font-bold"><?= $equipe['wins'] ?></td>
                                                <td class="py-5 text-center text-slate-400"><?= $equipe['draws'] ?></td>
                                                <td class="py-5 text-center text-red-400"><?= $equipe['losses'] ?></td>
                                                <td class="py-5 text-right font-bold text-xl"><?= $equipe['points'] ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN -->
                    <div class="space-y-8">
                        <!-- Activités Récentes -->
                        <div class="holo-card rounded-3xl overflow-hidden">
                            <div class="p-6 border-b border-slate-700">
                                <h2 class="text-xl font-bold flex items-center gap-2">
                                    <i class="fa-solid fa-list-check text-cyan-400"></i>
                                    ACTIVITÉS RÉCENTES
                                </h2>
                            </div>
                            <div class="p-6 space-y-6">
                                <?php foreach ($activities as $index => $act): ?>
                                    <div class="flex gap-5">
                                        <div class="text-cyan-400 text-2xl">
                                            <i class="fas fa-<?= $index % 3 === 0 ? 'clipboard-list' : ($index % 3 === 1 ? 'user-plus' : 'futbol') ?>"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm text-slate-200"><?= htmlspecialchars($act['description']) ?></p>
                                            <p class="text-xs text-slate-500 mt-1"><?= date('d M, H:i', strtotime($act['date_action'])) ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Focus Aujourd'hui -->
                        <div class="holo-card rounded-3xl p-6">
                            <h3 class="font-bold text-lg mb-5 flex items-center gap-2 text-orange-400">
                                <i class="fa-solid fa-fire"></i>
                                FOCUS DU JOUR
                            </h3>
                            <ul class="space-y-4 text-sm">
                                <li class="flex items-center gap-3"><div class="w-2 h-2 bg-emerald-400 rounded-full"></div> Rassemblement 18:00</li>
                                <li class="flex items-center gap-3"><div class="w-2 h-2 bg-yellow-400 rounded-full"></div> Récupération active</li>
                                <li class="flex items-center gap-3"><div class="w-2 h-2 bg-cyan-400 rounded-full"></div> Stratégie offensive</li>
                            </ul>
                        </div>

                        <!-- Solde Caisse -->
                        <?php if (in_array($_SESSION['user']['role'], ['president', 'censeur', 'organisateur'])): ?>
                            <div class="holo-card rounded-3xl p-6 text-center">
                                <h3 class="font-bold text-lg mb-4 flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-wallet text-emerald-400"></i>
                                    SOLDE CAISSE
                                </h3>
                                <p class="text-5xl font-bold text-emerald-400">
                                    <?= number_format($solde ?? 0, 0, ',', ' ') ?> <span class="text-2xl">FCFA</span>
                                </p>
                                <a href="/page-finance" class="mt-6 inline-block text-cyan-400 hover:text-cyan-300 font-medium">
                                    Détails financiers →
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>