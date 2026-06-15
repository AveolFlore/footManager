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
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-100 text-slate-800 font-sans min-h-screen">
    
    <div class="flex min-h-screen">
        <?php include __DIR__ . '/../../partials/sidebar.php'; ?>

        <div class="flex-1 flex flex-col overflow-hidden">
            <?php include __DIR__ . '/../../partials/header.php'; ?>
            
            <main class="flex-1 bg-slate-50 p-6 overflow-y-auto">
                <div class="max-w-7xl mx-auto space-y-6">
                    
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 border border-slate-200 rounded-xl shadow-sm">
                        <div>
                            <h2 class="text-xl font-bold text-slate-900">Bonjour, <?= htmlspecialchars($_SESSION['user']['prenom']) ?> 👋</h2>
                            <p class="text-sm text-slate-500">Ravi de vous revoir sur le tableau de bord.</p>
                        </div>
                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            <a href="/page-matchcreate" class="flex-1 sm:flex-none text-center px-4 py-2 text-sm font-medium bg-slate-900 hover:bg-slate-800 text-white rounded-lg shadow-sm transition-colors">
                                <i class="fas fa-calendar-plus mr-2 text-xs"></i>Planifier Match
                            </a>
                            <a href="/page-convocation" class="flex-1 sm:flex-none text-center px-4 py-2 text-sm font-medium bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-lg shadow-sm transition-colors">
                                <i class="fas fa-bullhorn mr-2 text-xs"></i>Convocation
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-white border border-slate-200 p-4 rounded-xl shadow-sm">
                            <div class="flex items-center justify-between text-slate-400 mb-2">
                                <span class="text-xs font-bold uppercase tracking-wider">Joueurs actifs</span>
                                <i class="fas fa-users text-sm"></i>
                            </div>
                            <p class="text-2xl font-bold text-slate-900"><?= $totalJoueurs ?></p>
                        </div>

                        <div class="bg-white border border-slate-200 p-4 rounded-xl shadow-sm">
                            <div class="flex items-center justify-between text-slate-400 mb-2">
                                <span class="text-xs font-bold uppercase tracking-wider">Équipes</span>
                                <i class="fas fa-shield-alt text-sm"></i>
                            </div>
                            <p class="text-2xl font-bold text-slate-900"><?= $totalEquipes ?></p>
                        </div>

                        <div class="bg-white border border-slate-200 p-4 rounded-xl shadow-sm">
                            <div class="flex items-center justify-between text-slate-400 mb-2">
                                <span class="text-xs font-bold uppercase tracking-wider">Matchs</span>
                                <i class="fas fa-running text-sm"></i>
                            </div>
                            <p class="text-2xl font-bold text-slate-900"><?= $totalMatchs ?></p>
                        </div>

                        <div class="bg-white border border-slate-200 p-4 rounded-xl shadow-sm">
                            <div class="flex items-center justify-between text-slate-400 mb-2">
                                <span class="text-xs font-bold uppercase tracking-wider">Taux présence</span>
                                <i class="fas fa-check-double text-sm"></i>
                            </div>
                            <p class="text-2xl font-bold text-slate-900"><?= $tauxPresence ?>%</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                        
                        <div class="lg:col-span-2 space-y-6">
                            
                            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
                                <h3 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
                                    <i class="fas fa-history text-slate-400"></i> Derniers matchs
                                </h3>
                                <?php if (empty($recentMatches)): ?>
                                    <p class="text-sm text-slate-400 italic py-4 text-center">Aucun match récent disponible.</p>
                                <?php else: ?>
                                    <div class="divide-y divide-slate-100">
                                        <?php foreach ($recentMatches as $match): ?>
                                            <div class="py-3 first:pt-0 last:pb-0 flex flex-col sm:flex-row justify-between sm:items-center gap-2 text-sm">
                                                <div>
                                                    <span class="font-semibold text-slate-900 block"><?= htmlspecialchars($match['titre'] ?? 'Match') ?></span>
                                                    <span class="text-slate-400 text-xs flex items-center gap-2 mt-0.5">
                                                        <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($match['lieu'] ?? 'Lieu inconnu') ?>
                                                    </span>
                                                </div>
                                                <div class="text-xs font-medium text-slate-500 bg-slate-100 px-2 py-1 rounded w-fit self-start sm:self-center">
                                                    <?= date('d/m/Y H:i', strtotime($match['date'])) ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
                                <h3 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
                                    <i class="fas fa-trophy text-slate-400"></i> Classement Général
                                </h3>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-sm border-collapse">
                                        <thead>
                                            <tr class="border-b border-slate-200 text-xs font-bold text-slate-400 uppercase">
                                                <th class="pb-2 w-10">#</th>
                                                <th class="pb-2">Club</th>
                                                <th class="pb-2 text-center w-12">M</th>
                                                <th class="pb-2 text-center w-12">V</th>
                                                <th class="pb-2 text-center w-12">N</th>
                                                <th class="pb-2 text-center w-12">D</th>
                                                <th class="pb-2 text-right w-16">Pts</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100">
                                            <?php foreach ($classement as $equipe): ?>
                                                <tr class="<?= $equipe['club'] === 'FC Blue Lock' ? 'bg-slate-50/80 font-semibold' : '' ?>">
                                                    <td class="py-3 text-slate-500"><?= $equipe['position'] ?></td>
                                                    <td class="py-3 text-slate-900 flex items-center gap-2">
                                                        <?php if ($equipe['club'] === 'FC Blue Lock'): ?>
                                                            <span class="inline-block w-2 h-2 rounded-full bg-blue-500"></span>
                                                        <?php endif; ?>
                                                        <?= $equipe['club'] ?>
                                                    </td>
                                                    <td class="py-3 text-center text-slate-600"><?= $equipe['matches'] ?></td>
                                                    <td class="py-3 text-center text-emerald-600"><?= $equipe['wins'] ?></td>
                                                    <td class="py-3 text-center text-slate-500"><?= $equipe['draws'] ?></td>
                                                    <td class="py-3 text-center text-red-500"><?= $equipe['losses'] ?></td>
                                                    <td class="py-3 text-right text-slate-950 font-bold"><?= $equipe['points'] ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <?php if (in_array($_SESSION['user']['role'], ['president', 'censeur', 'organisateur'])): ?>
                                <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6 border-l-4 border-l-emerald-500">
                                    <div class="flex items-center justify-between text-slate-400 mb-3">
                                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Solde caisse</h3>
                                        <i class="fas fa-wallet text-emerald-500 text-sm"></i>
                                    </div>
                                    <p class="text-2xl font-bold text-slate-900 mb-4"><?= number_format($solde ?? 0, 0, ',', ' ') ?> <span class="text-sm font-normal text-slate-500">FCFA</span></p>
                                    <a href="/page-finance" class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">
                                        Voir les détails comptables <i class="fas fa-arrow-right text-[10px]"></i>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            </main>
        </div>
    </div>

</body>
</html>