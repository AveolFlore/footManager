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
    <link rel="icon" type="image/png" href="/assets/images/blue_lock_logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Style mémorisé - Blue Lock Holographic & Cyber Tech */
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(3deg); }
        }

        @keyframes energyPulse {
            0%, 100% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.3); }
            50% { box-shadow: 0 0 35px rgba(59, 130, 246, 0.6); }
        }

        @keyframes holographic {
            0% { transform: translateX(-100%); opacity: 0; }
            50% { opacity: 0.3; }
            100% { transform: translateX(200%); opacity: 0; }
        }

        .float-ball { animation: float 6s ease-in-out infinite; }
        .energy-pulse { animation: energyPulse 3s ease-in-out infinite; }

        .holographic-scan::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.15), transparent);
            animation: holographic 4s linear infinite;
            pointer-events: none;
        }

        .glass-effect {
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        .btn-glow-green {
            background: linear-gradient(135deg, #16a34a, #15803d);
            transition: all 0.4s ease;
        }

        .btn-glow-green:hover {
            box-shadow: 0 0 25px rgba(34, 197, 94, 0.5);
            transform: translateY(-2px);
        }

        .input-glow-dark:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.3);
        }
    </style>
</head>
<body class="min-h-screen text-slate-100 bg-gradient-to-br from-slate-950 via-blue-950 to-slate-900 bg-fixed">

    <?php include_once __DIR__ . '/../../partials/header.php'; ?>
    
    <div class="flex min-h-screen">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>
        
        <main class="flex-1 overflow-y-auto relative z-10">
            <div class="p-6 md:p-8 lg:p-10">
                
                <header class="flex flex-col xl:flex-row justify-between items-start xl:items-center mb-8 gap-6 border-b border-blue-500/20 pb-6">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-black bg-gradient-to-r from-white to-blue-300 bg-clip-text text-transparent flex items-center gap-3">
                            <i class="fas fa-trophy text-blue-400"></i>
                            Classement des Joueurs
                        </h1>
                        <p class="text-blue-300/60 text-sm uppercase tracking-widest mt-2">Critères d'évaluation : But (3 pts) | Passe décisive (2 pts)</p>
                    </div>
                    
                    <form action="/page-classement" method="GET" class="flex flex-wrap items-center gap-4 bg-slate-950/50 p-3 rounded-2xl border border-blue-500/20 w-full xl:w-auto">
                        <div class="flex items-center flex-1 sm:flex-none bg-slate-900/80 rounded-xl px-3 border border-blue-500/10 input-glow-dark">
                            <i class="fas fa-calendar-alt text-blue-400 mr-2"></i>
                            <select name="month" class="bg-transparent outline-none text-white font-bold text-sm py-3 pr-8 cursor-pointer appearance-none">
                                <?php foreach ($months as $mNum => $mName): ?>
                                    <option value="<?= $mNum ?>" <?= $month == $mNum ? 'selected' : '' ?> class="bg-slate-950"><?= $mName ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="flex items-center flex-1 sm:flex-none bg-slate-900/80 rounded-xl px-3 border border-blue-500/10 input-glow-dark">
                            <i class="fas fa-history text-blue-400 mr-2"></i>
                            <select name="year" class="bg-transparent outline-none text-white font-bold text-sm py-3 pr-8 cursor-pointer appearance-none">
                                <?php for($y = date('Y'); $y >= 2024; $y--): ?>
                                    <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?> class="bg-slate-950"><?= $y ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <button type="submit" class="w-full sm:w-auto btn-glow-green text-white px-6 py-3 rounded-xl font-black text-xs uppercase tracking-wider shadow-lg flex items-center justify-center gap-2">
                            <i class="fas fa-filter"></i> Filtrer
                        </button>
                    </form>
                </header>

                <div class="glass-effect rounded-3xl shadow-2xl overflow-hidden relative holographic-scan">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-blue-500/20 bg-slate-900/60 text-blue-300 uppercase tracking-widest text-xs font-black">
                                    <th class="px-8 py-6">Rang</th>
                                    <th class="px-8 py-6">Joueur</th>
                                    <th class="px-8 py-6 text-center">Buts</th>
                                    <th class="px-8 py-6 text-center">Passes</th>
                                    <th class="px-8 py-6 text-center">Points</th>
                                    <th class="px-8 py-6">Ratio Global</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-blue-500/10 bg-slate-950/10">
                                <?php if (empty($ranking)): ?>
                                    <tr>
                                        <td colspan="6" class="px-8 py-20 text-center text-blue-300/40 italic font-bold text-lg">
                                            <i class="fas fa-inbox text-6xl mb-4 block text-blue-500/20"></i>
                                            <p>Aucune donnée de performance enregistrée pour cette phase.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($ranking as $index => $row): ?>
                                        <tr class="hover:bg-blue-500/5 transition-all duration-200 group">
                                            
                                            <td class="px-8 py-6 whitespace-nowrap">
                                                <?php if ($index === 0): ?>
                                                    <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-yellow-400 to-amber-600 text-slate-950 font-black text-md shadow-lg shadow-amber-500/20 border border-yellow-300">1</span>
                                                <?php elseif ($index === 1): ?>
                                                    <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-slate-300 to-slate-500 text-slate-950 font-black text-md shadow-lg shadow-slate-400/10 border border-slate-200">2</span>
                                                <?php elseif ($index === 2): ?>
                                                    <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-orange-400 to-amber-700 text-slate-950 font-black text-md shadow-lg shadow-orange-500/10 border border-orange-300">3</span>
                                                <?php else: ?>
                                                    <span class="font-black text-slate-400 text-md pl-4">#<?= $index + 1 ?></span>
                                                <?php endif; ?>
                                            </td>

                                            <td class="px-8 py-6 whitespace-nowrap">
                                                <div class="flex items-center space-x-4">
                                                    <div class="relative">
                                                        <img src="/<?= $row['photo_profil'] ?: 'assets/default-avatar.png' ?>" class="w-14 h-14 rounded-2xl object-cover border border-blue-500/30 shadow-md bg-slate-900">
                                                        <?php if ($index === 0): ?>
                                                            <div class="absolute -top-1 -right-1 bg-amber-500 text-slate-950 w-5 h-5 rounded-full flex items-center justify-center border border-yellow-300 shadow"><i class="fas fa-crown text-[9px]"></i></div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div>
                                                        <p class="font-black text-white text-md tracking-wide group-hover:text-blue-400 transition-colors"><?= htmlspecialchars($row['nom'] . ' ' . $row['prenom']) ?></p>
                                                        <p class="text-[10px] text-blue-300/50 uppercase font-black tracking-wider mt-0.5"><?= $month == date('m') ? '🧬 Évaluation active' : '🗄️ Phase archivée' ?></p>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="px-8 py-6 text-center font-black text-white text-lg whitespace-nowrap"><?= $row['total_buts'] ?></td>
                                            <td class="px-8 py-6 text-center font-black text-slate-300 text-lg whitespace-nowrap"><?= $row['total_passes'] ?></td>
                                            
                                            <td class="px-8 py-6 text-center whitespace-nowrap">
                                                <span class="bg-blue-500/10 text-blue-400 border border-blue-500/30 px-4 py-1.5 rounded-lg font-black text-sm tracking-wide">
                                                    <?= $row['score'] ?> <span class="text-[10px] uppercase opacity-70">pts</span>
                                                </span>
                                            </td>

                                            <td class="px-8 py-6 min-w-[180px]">
                                                <div class="flex items-center justify-between gap-3">
                                                    <div class="flex-1 bg-slate-950 rounded-full h-2.5 shadow-inner border border-blue-500/5 overflow-hidden">
                                                        <?php 
                                                        $maxScore = $ranking[0]['score'] ?: 1;
                                                        $percent = ($row['score'] / $maxScore) * 100;
                                                        ?>
                                                        <div class="bg-gradient-to-r from-blue-600 via-cyan-500 to-emerald-400 h-full rounded-full transition-all duration-500" style="width: <?= $percent ?>%"></div>
                                                    </div>
                                                    <span class="text-xs font-bold text-slate-400 w-8 text-right"><?= round($percent) ?>%</span>
                                                </div>
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>
</body>
</html>