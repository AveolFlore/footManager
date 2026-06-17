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
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .bg-welcome {
            background-image: url('/assets/images/welcome.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .glass { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="bg-welcome min-h-screen">

    <?php include_once __DIR__ . '/../../partials/floating-nav.php'; ?>
    
    <div class="pt-20 min-h-screen">
        <main class="p-6 md:p-10">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-800">Classement des Joueurs</h1>
                    <p class="text-slate-500 text-sm uppercase tracking-wider font-bold mt-1">
                        <i class="fas fa-chart-line mr-2"></i>But (3 pts) | Passe décisive (2 pts)
                    </p>
                </div>
                
                <form action="/page-classement" method="GET" class="flex gap-2 bg-white p-2 rounded-2xl shadow-lg border border-slate-100">
                    <select name="month" class="bg-slate-50 px-4 py-2 rounded-xl font-bold text-sm outline-none">
                        <?php foreach ($months as $mNum => $mName): ?>
                            <option value="<?= $mNum ?>" <?= $month == $mNum ? 'selected' : '' ?>><?= $mName ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="year" class="bg-slate-50 px-4 py-2 rounded-xl font-bold text-sm outline-none">
                        <?php for($y = date('Y'); $y >= 2024; $y--): ?>
                            <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-xl font-bold text-sm transition-all">
                        <i class="fas fa-filter mr-1"></i>Filtrer
                    </button>
                </form>
            </div>

            <div class="glass rounded-3xl shadow-2xl border border-white/50 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-slate-50/50 border-b border-slate-200">
                        <tr>
                            <th class="px-8 py-5 text-left text-xs font-black text-slate-400 uppercase">Rang</th>
                            <th class="px-8 py-5 text-left text-xs font-black text-slate-400 uppercase">Joueur</th>
                            <th class="px-8 py-5 text-center text-xs font-black text-slate-400 uppercase">Buts</th>
                            <th class="px-8 py-5 text-center text-xs font-black text-slate-400 uppercase">Passes</th>
                            <th class="px-8 py-5 text-center text-xs font-black text-slate-400 uppercase">Points</th>
                            <th class="px-8 py-5 text-left text-xs font-black text-slate-400 uppercase">Ratio</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($ranking)): ?>
                            <tr><td colspan="6" class="text-center py-20 text-slate-500 font-bold italic">Aucune donnée enregistrée pour cette phase.</td></tr>
                        <?php else: foreach ($ranking as $index => $row): ?>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-8 py-6">
                                    <div class="w-10 h-10 flex items-center justify-center rounded-xl font-black text-lg 
                                        <?= $index==0 ? 'bg-amber-400 text-amber-900' : ($index==1 ? 'bg-slate-300 text-slate-700' : ($index==2 ? 'bg-orange-300 text-orange-900' : 'text-slate-400')) ?>">
                                        <?= $index + 1 ?>
                                    </div>
                                </td>
                                <td class="px-8 py-6 flex items-center gap-4">
                                    <div class="relative">
                                        <img src="/<?= $row['photo_profil'] ?: 'assets/default-avatar.png' ?>" class="w-12 h-12 rounded-2xl object-cover border-2 border-white shadow-md">
                                        <?php if ($index === 0): ?><span class="absolute -top-2 -right-2 text-xs">👑</span><?php endif; ?>
                                    </div>
                                    <div>
                                        <p class="font-black text-slate-800"><?= htmlspecialchars($row['nom'] . ' ' . $row['prenom']) ?></p>
                                        <p class="text-[10px] uppercase font-bold text-slate-400"><?= $month == date('m') ? 'En cours' : 'Archivé' ?></p>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center font-bold"><?= $row['total_buts'] ?></td>
                                <td class="px-8 py-6 text-center font-bold text-slate-600"><?= $row['total_passes'] ?></td>
                                <td class="px-8 py-6 text-center">
                                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-lg font-black text-sm"><?= $row['score'] ?> pts</span>
                                </td>
                                <td class="px-8 py-6">
                                    <?php 
                                    $maxScore = $ranking[0]['score'] ?: 1;
                                    $percent = ($row['score'] / $maxScore) * 100;
                                    ?>
                                    <div class="w-32 bg-slate-200 rounded-full h-2 overflow-hidden">
                                        <div class="bg-blue-600 h-full" style="width: <?= $percent ?>%"></div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>