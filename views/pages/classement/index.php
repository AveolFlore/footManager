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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classement - Club Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 font-sans">
    <div class="flex min-h-screen">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>
        
        <main class="flex-1 p-8">
            <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Classement des Joueurs</h1>
                    <p class="text-gray-600">Performances basées sur les buts (3pts) et passes (2pts)</p>
                </div>
                
                <form action="/page-classement" method="GET" class="flex items-center space-x-2 bg-white p-2 rounded-lg shadow-sm">
                    <select name="month" class="bg-transparent outline-none text-gray-700">
                        <?php foreach ($months as $mNum => $mName): ?>
                            <option value="<?= $mNum ?>" <?= $month == $mNum ? 'selected' : '' ?>><?= $mName ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="year" class="bg-transparent outline-none text-gray-700">
                        <?php for($y = date('Y'); $y >= 2024; $y--): ?>
                            <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                    <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded-md hover:bg-green-700 transition">
                        <i class="fas fa-filter"></i>
                    </button>
                </form>
            </header>

            <!-- Ranking Table -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-sm uppercase">
                            <th class="px-6 py-4 font-semibold">Rang</th>
                            <th class="px-6 py-4 font-semibold">Joueur</th>
                            <th class="px-6 py-4 font-semibold text-center">Buts</th>
                            <th class="px-6 py-4 font-semibold text-center">Passes</th>
                            <th class="px-6 py-4 font-semibold text-center">Points</th>
                            <th class="px-6 py-4 font-semibold">Progression</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (empty($ranking)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-20 text-center text-gray-500 italic">
                                    Aucune performance enregistrée pour cette période.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($ranking as $index => $row): ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <?php if ($index === 0): ?>
                                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-yellow-100 text-yellow-600 font-bold">1</span>
                                        <?php elseif ($index === 1): ?>
                                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-200 text-gray-600 font-bold">2</span>
                                        <?php elseif ($index === 2): ?>
                                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-orange-600 font-bold">3</span>
                                        <?php else: ?>
                                            <span class="px-2 font-medium text-gray-400"><?= $index + 1 ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-3">
                                            <img src="/<?= $row['photo_profil'] ?: 'assets/default-avatar.png' ?>" class="w-10 h-10 rounded-full object-cover border border-gray-200">
                                            <div>
                                                <p class="font-bold text-gray-800"><?= htmlspecialchars($row['nom'] . ' ' . $row['prenom']) ?></p>
                                                <p class="text-xs text-gray-500 uppercase"><?= $month == date('m') ? 'En forme' : 'Stats archivées' ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center font-semibold text-gray-700"><?= $row['total_buts'] ?></td>
                                    <td class="px-6 py-4 text-center font-semibold text-gray-700"><?= $row['total_passes'] ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full font-bold">
                                            <?= $row['score'] ?> pts
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="w-full bg-gray-100 rounded-full h-2">
                                            <?php 
                                            $maxScore = $ranking[0]['score'] ?: 1;
                                            $percent = ($row['score'] / $maxScore) * 100;
                                            ?>
                                            <div class="bg-green-500 h-2 rounded-full" style="width: <?= $percent ?>%"></div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
