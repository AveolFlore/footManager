<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../config/Database.php';
require_once __DIR__ . '/../../../models/match_seance/MatchEntity.php';
require_once __DIR__ . '/../../../models/caisse/Caisse.php';
require_once __DIR__ . '/../../../models/activite_log/Activite.php';

use Config\Database;
use Models\match_seance\MatchEntity;
use Models\Caisse\Caisse;
use Models\Activite_log\Activite;

$db = (new Database())->connect();
$matchModel = new MatchEntity($db);
$caisseModel = new Caisse($db);
$activiteModel = new Activite($db);

$nextMatch = $db->query("SELECT * FROM match_seance WHERE date >= CURDATE() ORDER BY date ASC LIMIT 1")->fetch();
$solde = $caisseModel->getSolde();
$activities = $activiteModel->getLatest(5);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Club Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../../partials/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <header class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Tableau de bord</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">Bienvenue, <?= $_SESSION['user']['prenom'] ?></span>
                    <img src="/<?= $_SESSION['user']['photo'] ?? 'assets/default-avatar.png' ?>" class="w-10 h-10 rounded-full border-2 border-green-500">
                </div>
            </header>

            <!-- Grid Layout -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <!-- Widget: Prochain Match -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-700">Prochain Match</h2>
                        <i class="fas fa-calendar-alt text-green-500"></i>
                    </div>
                    <?php if ($nextMatch): ?>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-green-600"><?= date('d/m', strtotime($nextMatch['date'])) ?></p>
                            <p class="text-gray-600"><?= $nextMatch['lieu'] ?></p>
                            <p class="text-sm text-gray-400 mt-2"><?= $nextMatch['type'] === 'match' ? 'Match Officiel' : 'Entraînement' ?></p>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500 text-center">Aucun match prévu</p>
                    <?php endif; ?>
                </div>

                <!-- Widget: Solde Caisse (Bureau seul) -->
                <?php if (in_array($_SESSION['user']['role'], ['admin', 'president', 'censeur'])): ?>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-700">Solde Caisse</h2>
                        <i class="fas fa-wallet text-blue-500"></i>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-gray-800"><?= number_format($solde, 0, ',', ' ') ?> <span class="text-sm">FCFA</span></p>
                        <p class="text-sm text-gray-400 mt-2">Dernière mise à jour : Aujourd'hui</p>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Widget: Activités récentes -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 lg:row-span-2">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-700">Fil d'activité</h2>
                        <i class="fas fa-history text-purple-500"></i>
                    </div>
                    <div class="space-y-4">
                        <?php foreach ($activities as $act): ?>
                            <div class="flex items-start space-x-3 text-sm">
                                <div class="w-2 h-2 mt-1.5 rounded-full bg-green-500"></div>
                                <div>
                                    <p class="font-medium text-gray-800"><?= $act['description'] ?></p>
                                    <p class="text-xs text-gray-400"><?= date('H:i', strtotime($act['date_action'])) ?> - <?= $act['prenom'] ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Widget: Top Joueur -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-700">Top Joueur (Mois)</h2>
                        <i class="fas fa-trophy text-yellow-500"></i>
                    </div>
                    <div class="flex items-center space-x-4">
                        <img src="/assets/default-avatar.png" class="w-16 h-16 rounded-full bg-gray-200">
                        <div>
                            <p class="font-bold text-gray-800">En cours de calcul</p>
                            <p class="text-sm text-gray-500">Bientôt disponible</p>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

</body>
</html>
