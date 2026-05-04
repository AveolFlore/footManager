<?php
$pageTitle = "Performances";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - FC Green Lions</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#f8fafc]">

<div class="flex min-h-screen">
    <!-- Sidebar -->
    <?php require_once "../views/partials/sidebar.php"; ?>

    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Header -->
        <?php require_once "../views/partials/header.php"; ?>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto p-4 md:p-8">
            <div class="max-w-7xl mx-auto">
                
                <!-- Page Title Section -->
                <div class="mb-6">
                    <h1 class="text-lg font-bold text-gray-800 tracking-tight">Performances</h1>
                    <p class="text-[11px] text-gray-500 mt-0.5">Suivi détaillé des performances individuelles</p>
                </div>

                <!-- Player Selector Section -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6">
                    <label for="playerSelect" class="block text-[10px] font-bold text-gray-400 mb-3 uppercase tracking-widest">Sélectionner un joueur</label>
                    <form action="/performance-index" method="GET" id="playerForm">
                        <div class="relative max-w-xs">
                            <select name="id" id="playerSelect" onchange="document.getElementById('playerForm').submit()"
                                    class="w-full bg-gray-50 border border-gray-200 text-gray-600 text-xs font-medium rounded-xl focus:ring-green-500 focus:border-green-500 block p-3 appearance-none cursor-pointer transition-all hover:bg-gray-100">
                                <?php foreach ($allJoueurs as $joueur): ?>
                                    <option value="<?= $joueur['id'] ?>" <?= $joueurId == $joueur['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($joueur['prenom'] . ' ' . $joueur['nom']) ?> - <?= htmlspecialchars($joueur['equipe_nom'] ?? 'Sans équipe') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Tabs & Performance Section -->
                <div class="bg-white rounded-[1.5rem] shadow-sm border border-gray-100 overflow-hidden mb-6">
                    <!-- Tabs Header -->
                    <div class="flex bg-gray-50/50">
                        <button id="tab-global" onclick="switchTab('global')" class="flex-1 py-3.5 text-[10px] font-bold uppercase tracking-widest text-white bg-[#00a84d] transition-all">
                            Performance Globale
                        </button>
                        <button id="tab-match" onclick="switchTab('match')" class="flex-1 py-3.5 text-[10px] font-bold uppercase tracking-widest text-gray-400 hover:text-gray-600 transition-all">
                            Par Match
                        </button>
                    </div>

                    <!-- Global Performance Content -->
                    <div id="content-global" class="p-6 md:p-8">
                        <!-- Stats Cards Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                            <!-- Buts Card -->
                            <div class="bg-[#f0fdf4] p-6 rounded-[1.25rem] border border-[#dcfce7] relative group hover:shadow-md transition-all">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-8 h-8 bg-white rounded-lg shadow-sm flex items-center justify-center text-base">🏆</div>
                                    <span class="text-[10px] font-bold text-[#15803d] uppercase tracking-widest">Buts</span>
                                </div>
                                <div class="text-3xl font-black text-[#14532d] mb-1"><?= $globalStats['total_buts'] ?></div>
                                <div class="text-[10px] font-bold text-[#16a34a] uppercase tracking-tighter"><?= $globalStats['buts_par_match'] ?>/match</div>
                            </div>

                            <!-- Passes Card -->
                            <div class="bg-[#eff6ff] p-6 rounded-[1.25rem] border border-[#dbeafe] relative group hover:shadow-md transition-all">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-8 h-8 bg-white rounded-lg shadow-sm flex items-center justify-center text-base">🎯</div>
                                    <span class="text-[10px] font-bold text-[#1d4ed8] uppercase tracking-widest">Passes</span>
                                </div>
                                <div class="text-3xl font-black text-[#1e3a8a] mb-1"><?= $globalStats['total_passes'] ?></div>
                                <div class="text-[10px] font-bold text-[#2563eb] uppercase tracking-tighter"><?= $globalStats['passes_par_match'] ?>/match</div>
                            </div>

                            <!-- Note Card -->
                            <div class="bg-[#faf5ff] p-6 rounded-[1.25rem] border border-[#f3e8ff] relative group hover:shadow-md transition-all">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-8 h-8 bg-white rounded-lg shadow-sm flex items-center justify-center text-sm text-purple-600 font-bold">📈</div>
                                    <span class="text-[10px] font-bold text-[#7e22ce] uppercase tracking-widest">Note moy.</span>
                                </div>
                                <div class="text-3xl font-black text-[#581c87] mb-1"><?= $globalStats['note_moyenne'] ?></div>
                                <div class="text-[10px] font-bold text-[#9333ea] uppercase tracking-tighter">/10</div>
                            </div>

                            <!-- Victoires Card -->
                            <div class="bg-[#fff7ed] p-6 rounded-[1.25rem] border border-[#ffedd5] relative group hover:shadow-md transition-all">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-8 h-8 bg-white rounded-lg shadow-sm flex items-center justify-center text-sm text-orange-600 font-bold">⚽</div>
                                    <span class="text-[10px] font-bold text-[#c2410c] uppercase tracking-widest">Victoires</span>
                                </div>
                                <div class="text-3xl font-black text-[#7c2d12] mb-1"><?= $globalStats['win_rate'] ?>%</div>
                                <div class="text-[10px] font-bold text-[#ea580c] uppercase tracking-tighter"><?= $globalStats['victoires'] ?>/<?= $globalStats['total_matchs'] ?></div>
                            </div>
                        </div>

                        <!-- Evolution Chart Area -->
                        <div class="bg-white rounded-2xl border border-gray-100 p-6">
                            <h3 class="text-sm font-bold text-gray-800 mb-8">Évolution de la Performance</h3>
                            <div class="h-[350px]">
                                <canvas id="evolutionChart"></canvas>
                            </div>
                            <div class="flex justify-center gap-6 mt-6">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-0.5 bg-green-500"></span>
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Note</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-0.5 bg-blue-500"></span>
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Buts</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-0.5 bg-purple-500"></span>
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Passes</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Per Match Performance Content -->
                    <div id="content-match" class="p-6 md:p-8 hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-gray-100">
                                        <th class="py-4 px-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Date</th>
                                        <th class="py-4 px-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Match</th>
                                        <th class="py-4 px-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Buts</th>
                                        <th class="py-4 px-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Passes</th>
                                        <th class="py-4 px-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Note</th>
                                        <th class="py-4 px-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Résultat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($matchPerformances as $perf): ?>
                                        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                                            <td class="py-4 px-2">
                                                <div class="text-xs font-bold text-gray-700"><?= date('d/m/Y', strtotime($perf['date'])) ?></div>
                                                <div class="text-[10px] text-gray-400"><?= htmlspecialchars($perf['lieu']) ?></div>
                                            </td>
                                            <td class="py-4 px-2 text-xs font-semibold text-gray-600">
                                                <?= $perf['type'] === 'match' ? '⚽ Match' : '🏃 Entraînement' ?>
                                            </td>
                                            <td class="py-4 px-2 text-center text-xs font-black text-gray-700"><?= $perf['buts'] ?></td>
                                            <td class="py-4 px-2 text-center text-xs font-black text-gray-700"><?= $perf['passes'] ?></td>
                                            <td class="py-4 px-2 text-center">
                                                <span class="px-2 py-1 rounded-lg text-[10px] font-black <?= $perf['note'] >= 7 ? 'bg-green-100 text-green-700' : ($perf['note'] >= 5 ? 'bg-orange-100 text-orange-700' : 'bg-red-100 text-red-700') ?>">
                                                    <?= $perf['note'] ?>/10
                                                </span>
                                            </td>
                                            <td class="py-4 px-2 text-center">
                                                <?php 
                                                $res = 'N/A';
                                                $color = 'gray';
                                                if ($perf['equipe_gagnante'] === 'nul') {
                                                    $res = 'Nul'; $color = 'orange';
                                                } elseif ($perf['equipe_gagnante'] === $perf['equipe_type']) {
                                                    $res = 'Victoire'; $color = 'green';
                                                } else {
                                                    $res = 'Défaite'; $color = 'red';
                                                }
                                                ?>
                                                <span class="text-[10px] font-bold uppercase tracking-tighter text-<?= $color ?>-600">
                                                    <?= $res ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Comparison & Analysis Grid -->
                <div class="space-y-6">
                    <!-- Comparison Card -->
                    <div class="bg-white p-8 rounded-[1.5rem] shadow-sm border border-gray-100">
                        <h3 class="text-sm font-bold text-gray-800 mb-8">Comparaison avec la Moyenne de l'Équipe</h3>
                        <div class="h-[300px]">
                            <canvas id="comparisonChart"></canvas>
                        </div>
                    </div>

                    <!-- Analysis Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Points Forts -->
                        <div class="bg-[#f0fdf4]/50 p-8 rounded-[1.5rem] border border-[#dcfce7]">
                            <h3 class="text-[#15803d] font-bold mb-6 flex items-center text-xs uppercase tracking-widest">
                                <span class="mr-3 text-lg">↗️</span> Points Forts
                            </h3>
                            <ul class="space-y-4">
                                <?php if ($globalStats['buts_par_match'] > $teamAverages['avg_buts']): ?>
                                    <li class="flex items-center text-[#166534] font-semibold text-[11px]">
                                        <span class="w-1.5 h-1.5 bg-[#22c55e] rounded-full mr-3"></span>
                                        Efficacité offensive au-dessus de la moyenne
                                    </li>
                                <?php endif; ?>
                                <?php if ($globalStats['passes_par_match'] > $teamAverages['avg_passes']): ?>
                                    <li class="flex items-center text-[#166534] font-semibold text-[11px]">
                                        <span class="w-1.5 h-1.5 bg-[#22c55e] rounded-full mr-3"></span>
                                        Excellent créateur de jeu
                                    </li>
                                <?php endif; ?>
                                <?php if ($globalStats['note_moyenne'] >= 7): ?>
                                    <li class="flex items-center text-[#166534] font-semibold text-[11px]">
                                        <span class="w-1.5 h-1.5 bg-[#22c55e] rounded-full mr-3"></span>
                                        Note moyenne élevée (<?= $globalStats['note_moyenne'] ?>/10)
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <!-- Axes d'Amélioration -->
                        <div class="bg-[#fff7ed]/50 p-8 rounded-[1.5rem] border border-[#ffedd5]">
                            <h3 class="text-[#c2410c] font-bold mb-6 flex items-center text-xs uppercase tracking-widest">
                                <span class="mr-3 text-lg">↘️</span> Axes d'Amélioration
                            </h3>
                            <ul class="space-y-4">
                                <?php if ($globalStats['note_moyenne'] < 6): ?>
                                    <li class="flex items-center text-[#9a3412] font-semibold text-[11px]">
                                        <span class="w-1.5 h-1.5 bg-[#f97316] rounded-full mr-3"></span>
                                        Régularité à améliorer sur l'ensemble du match
                                    </li>
                                <?php endif; ?>
                                <?php if ($globalStats['total_matchs'] < 3): ?>
                                    <li class="flex items-center text-[#9a3412] font-semibold text-[11px]">
                                        <span class="w-1.5 h-1.5 bg-[#f97316] rounded-full mr-3"></span>
                                        Manque de temps de jeu pour une analyse stable
                                    </li>
                                <?php endif; ?>
                                <li class="flex items-center text-[#9a3412] font-semibold text-[11px]">
                                    <span class="w-1.5 h-1.5 bg-[#f97316] rounded-full mr-3"></span>
                                    Travail sur la condition physique en fin de match
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#94a3b8';

    // Evolution Chart
    new Chart(document.getElementById('evolutionChart'), {
        type: 'line',
        data: {
            labels: <?= json_encode($chartLabels) ?>,
            datasets: [
                {
                    label: 'Note',
                    data: <?= json_encode($chartNotes) ?>,
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34, 197, 94, 0.05)',
                    borderWidth: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#22c55e',
                    pointBorderWidth: 3,
                    pointRadius: 6,
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Buts',
                    data: <?= json_encode($chartButs) ?>,
                    borderColor: '#3b82f6',
                    borderWidth: 2,
                    pointRadius: 0,
                    tension: 0.4
                },
                {
                    label: 'Passes',
                    data: <?= json_encode($chartPasses) ?>,
                    borderColor: '#a855f7',
                    borderWidth: 2,
                    pointRadius: 0,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { 
                    beginAtZero: true, max: 12,
                    grid: { borderDash: [8, 8], color: '#f1f5f9' },
                    border: { display: false }
                },
                x: { grid: { display: false }, border: { display: false } }
            }
        }
    });

    // Comparison Chart
    new Chart(document.getElementById('comparisonChart'), {
        type: 'bar',
        data: {
            labels: ['Buts/match', 'Passes/match'],
            datasets: [
                {
                    label: '<?= htmlspecialchars($selectedJoueur['prenom'] . ' ' . $selectedJoueur['nom']) ?>',
                    data: [<?= $globalStats['buts_par_match'] ?>, <?= $globalStats['passes_par_match'] ?>],
                    backgroundColor: '#22c55e',
                    borderRadius: 12,
                    barThickness: 140
                },
                {
                    label: 'Moyenne équipe',
                    data: [<?= $teamAverages['avg_buts'] ?>, <?= $teamAverages['avg_passes'] ?>],
                    backgroundColor: '#cbd5e1',
                    borderRadius: 12,
                    barThickness: 140
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, font: { weight: '700' }, padding: 30 } },
                tooltip: {
                    backgroundColor: '#fff',
                    titleColor: '#1e293b',
                    bodyColor: '#1e293b',
                    borderColor: '#e2e8f0',
                    borderWidth: 1,
                    padding: 16,
                    cornerRadius: 16,
                    displayColors: false
                }
            },
            scales: {
                y: { grid: { borderDash: [8, 8], color: '#f1f5f9' }, border: { display: false } },
                x: { grid: { display: false }, border: { display: false } }
            }
        }
    });

    // Tab Switching Logic
    function switchTab(tab) {
        const contentGlobal = document.getElementById('content-global');
        const contentMatch = document.getElementById('content-match');
        const tabGlobal = document.getElementById('tab-global');
        const tabMatch = document.getElementById('tab-match');

        if (tab === 'global') {
            contentGlobal.classList.remove('hidden');
            contentMatch.classList.add('hidden');
            
            // Update Tab Styles
            tabGlobal.classList.remove('text-gray-400', 'hover:text-gray-600', 'bg-gray-50/50');
            tabGlobal.classList.add('text-white', 'bg-[#00a84d]');
            
            tabMatch.classList.remove('text-white', 'bg-[#00a84d]');
            tabMatch.classList.add('text-gray-400', 'hover:text-gray-600');
        } else {
            contentGlobal.classList.add('hidden');
            contentMatch.classList.remove('hidden');
            
            // Update Tab Styles
            tabMatch.classList.remove('text-gray-400', 'hover:text-gray-600');
            tabMatch.classList.add('text-white', 'bg-[#00a84d]');
            
            tabGlobal.classList.remove('text-white', 'bg-[#00a84d]');
            tabGlobal.classList.add('text-gray-400', 'hover:text-gray-600');
        }
    }
</script>

</body>
</html>
