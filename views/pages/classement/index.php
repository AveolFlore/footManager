<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../middleware/Role.php';
require_once __DIR__ . '/../../../config/Database.php';
require_once __DIR__ . '/../../../models/performance/Performance.php';

use Config\Database;
use Models\Performance\Performance;

requireLogin();

$db = (new Database())->connect();
$performanceModel = new Performance($db);

$month = $_GET['month'] ?? date('m');
$year = $_GET['year'] ?? date('Y');

$ranking = $performanceModel->getRanking($month, $year);

$months = [
    '01' => 'Janvier', '02' => 'Février', '03' => 'Mars', '04' => 'Avril',
    '05' => 'Mai', '06' => 'Juin', '07' => 'Juillet', '08' => 'Août',
    '09' => 'Septembre', '10' => 'Octobre', '11' => 'Novembre', '12' => 'Décembre'
];

$pageTitle = "Classement des Joueurs";
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
                <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 flex items-center gap-3">
                            <i class="fas fa-trophy text-green-600"></i>
                            Classement des Joueurs
                        </h1>
                        <p class="text-slate-600 text-lg mt-2">Performances basées sur les buts (3pts) et passes (2pts)</p>
                    </div>
                    
                    <form action="/page-classement" method="GET" class="flex items-center space-x-4 bg-white p-4 rounded-3xl shadow-xl border border-slate-100">
                        <select name="month" class="bg-transparent outline-none text-slate-700 font-semibold text-lg px-3 py-2">
                            <?php foreach ($months as $mNum => $mName): ?>
                                <option value="<?= $mNum ?>" <?= $month == $mNum ? 'selected' : '' ?>><?= $mName ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="year" class="bg-transparent outline-none text-slate-700 font-semibold text-lg px-3 py-2">
                            <?php for($y = date('Y'); $y >= 2024; $y--): ?>
                                <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                        <button type="submit" class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-6 py-3 rounded-2xl font-bold transition-all shadow-lg shadow-green-200">
                            <i class="fas fa-filter mr-2"></i> Filtrer
                        </button>
                    </form>
                </header>

                <!-- Ranking Table -->
                <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-gradient-to-r from-slate-50 to-slate-100 text-slate-600 text-sm uppercase border-b-2 border-slate-100">
                            <tr>
                                <th class="px-8 py-6 font-extrabold text-lg">Rang</th>
                                <th class="px-8 py-6 font-extrabold text-lg">Joueur</th>
                                <th class="px-8 py-6 font-extrabold text-lg text-center">Buts</th>
                                <th class="px-8 py-6 font-extrabold text-lg text-center">Passes</th>
                                <th class="px-8 py-6 font-extrabold text-lg text-center">Points</th>
                                <th class="px-8 py-6 font-extrabold text-lg">Progression</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($ranking)): ?>
                                <tr>
                                    <td colspan="6" class="px-8 py-20 text-center text-slate-400 italic font-semibold text-xl">
                                        <i class="fas fa-inbox text-8xl mb-6"></i>
                                        <p>Aucune performance enregistrée pour cette période.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($ranking as $index => $row): ?>
                                    <tr class="hover:bg-slate-50 transition-all duration-200">
                                        <td class="px-8 py-6">
                                            <?php if ($index === 0): ?>
                                                <span class="flex items-center justify-center w-12 h-12 rounded-3xl bg-gradient-to-br from-yellow-100 to-yellow-200 text-yellow-700 font-extrabold text-lg border border-yellow-300 shadow-md">1</span>
                                            <?php elseif ($index === 1): ?>
                                                <span class="flex items-center justify-center w-12 h-12 rounded-3xl bg-gradient-to-br from-slate-100 to-slate-200 text-slate-700 font-extrabold text-lg border border-slate-300 shadow-md">2</span>
                                            <?php elseif ($index === 2): ?>
                                                <span class="flex items-center justify-center w-12 h-12 rounded-3xl bg-gradient-to-br from-orange-100 to-orange-200 text-orange-700 font-extrabold text-lg border border-orange-300 shadow-md">3</span>
                                            <?php else: ?>
                                                <span class="font-semibold text-slate-500 text-lg"><?= $index + 1 ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-8 py-6">
                                            <div class="flex items-center space-x-4">
                                                <img src="/<?= $row['photo_profil'] ?: 'assets/default-avatar.png' ?>" class="w-14 h-14 rounded-3xl object-cover border border-slate-200 shadow-md">
                                                <div>
                                                    <p class="font-extrabold text-slate-800 text-lg"><?= htmlspecialchars($row['nom'] . ' ' . $row['prenom']) ?></p>
                                                    <p class="text-xs text-slate-500 uppercase font-semibold"><?= $month == date('m') ? 'En forme' : 'Stats archivées' ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 text-center font-extrabold text-slate-700 text-xl"><?= $row['total_buts'] ?></td>
                                        <td class="px-8 py-6 text-center font-extrabold text-slate-700 text-xl"><?= $row['total_passes'] ?></td>
                                        <td class="px-8 py-6 text-center">
                                            <span class="bg-gradient-to-r from-green-100 to-emerald-100 text-green-700 px-5 py-2 rounded-3xl font-extrabold text-lg border border-green-200">
                                                <?= $row['score'] ?> pts
                                            </span>
                                        </td>
                                        <td class="px-8 py-6">
                                            <div class="w-full bg-slate-100 rounded-3xl h-4 shadow-inner">
                                                <?php 
                                                $maxScore = $ranking[0]['score'] ?: 1;
                                                $percent = ($row['score'] / $maxScore) * 100;
                                                ?>
                                                <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-4 rounded-3xl" style="width: <?= $percent ?>%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
