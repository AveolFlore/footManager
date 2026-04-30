<?php
$pageTitle = "Performances - " . ($selectedJoueur['prenom'] ?? 'Joueur');
require_once "../views/partials/header.php";
?>

<div class="flex flex-col md:flex-row min-h-screen bg-gray-50">
    <!-- Sidebar -->
    <?php require_once "../views/partials/sidebar.php"; ?>

    <!-- Main Content -->
    <main class="flex-1 p-4 md:p-8">
        <div class="max-w-6xl mx-auto">
            
            <!-- Header Section -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800">Performances</h2>
                <p class="text-gray-600">Suivi détaillé des performances individuelles</p>
            </div>

            <!-- Player Selector (Admin only or for self-view) -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-8">
                <label for="playerSelect" class="block text-sm font-medium text-gray-700 mb-2">Sélectionner un joueur</label>
                <form action="/performance-index" method="GET" id="playerForm">
                    <select name="id" id="playerSelect" onchange="document.getElementById('playerForm').submit()"
                            class="w-full md:w-1/3 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block p-2.5">
                        <?php foreach ($allJoueurs as $joueur): ?>
                            <option value="<?= $joueur['id'] ?>" <?= $joueurId == $joueur['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($joueur['prenom'] . ' ' . $joueur['nom']) ?> - <?= htmlspecialchars($joueur['poste'] ?? 'N/A') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>

            <!-- Stats Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <!-- Buts Card -->
                <div class="bg-green-50 p-6 rounded-xl border border-green-100">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-green-600">⚽ Buts</span>
                    </div>
                    <div class="text-3xl font-bold text-green-800"><?= $globalStats['total_buts'] ?></div>
                    <div class="text-sm text-green-600 mt-1"><?= $globalStats['buts_par_match'] ?>/match</div>
                </div>

                <!-- Passes Card -->
                <div class="bg-blue-50 p-6 rounded-xl border border-blue-100">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-blue-600">🎯 Passes</span>
                    </div>
                    <div class="text-3xl font-bold text-blue-800"><?= $globalStats['total_passes'] ?></div>
                    <div class="text-sm text-blue-600 mt-1"><?= $globalStats['passes_par_match'] ?>/match</div>
                </div>

                <!-- Note Card -->
                <div class="bg-purple-50 p-6 rounded-xl border border-purple-100">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-purple-600">📈 Note moy.</span>
                    </div>
                    <div class="text-3xl font-bold text-purple-800"><?= $globalStats['note_moyenne'] ?></div>
                    <div class="text-sm text-purple-600 mt-1">/10</div>
                </div>

                <!-- Victoires Card -->
                <div class="bg-orange-50 p-6 rounded-xl border border-orange-100">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-orange-600">🏆 Victoires</span>
                    </div>
                    <div class="text-3xl font-bold text-orange-800"><?= $globalStats['win_rate'] ?>%</div>
                    <div class="text-sm text-orange-600 mt-1"><?= $globalStats['victoires'] ?>/<?= $globalStats['total_matchs'] ?></div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Evolution Chart -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6">Évolution de la Performance</h3>
                    <div class="h-64">
                        <canvas id="evolutionChart"></canvas>
                    </div>
                </div>

                <!-- Comparison Chart -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6">Comparaison avec la Moyenne de l'Équipe</h3>
                    <div class="h-64">
                        <canvas id="comparisonChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Analysis Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Strong Points -->
                <div class="bg-green-50 p-6 rounded-xl border border-green-100">
                    <h3 class="text-green-800 font-semibold mb-4 flex items-center">
                        <span class="mr-2">↗️</span> Points Forts
                    </h3>
                    <ul class="space-y-2 text-green-700">
                        <?php if ($globalStats['buts_par_match'] > $teamAverages['avg_buts']): ?>
                            <li class="flex items-start">
                                <span class="mr-2">•</span> Efficacité offensive au-dessus de la moyenne
                            </li>
                        <?php endif; ?>
                        <?php if ($globalStats['passes_par_match'] > $teamAverages['avg_passes']): ?>
                            <li class="flex items-start">
                                <span class="mr-2">•</span> Excellent créateur de jeu
                            </li>
                        <?php endif; ?>
                        <?php if ($globalStats['win_rate'] > 50): ?>
                            <li class="flex items-start">
                                <span class="mr-2">•</span> Impact positif sur les résultats (High Win Rate)
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Areas for Improvement -->
                <div class="bg-orange-50 p-6 rounded-xl border border-orange-100">
                    <h3 class="text-orange-800 font-semibold mb-4 flex items-center">
                        <span class="mr-2">↘️</span> Axes d'Amélioration
                    </h3>
                    <ul class="space-y-2 text-orange-700">
                        <?php if ($globalStats['note_moyenne'] < 6): ?>
                            <li class="flex items-start">
                                <span class="mr-2">•</span> Régularité à améliorer sur l'ensemble du match
                            </li>
                        <?php endif; ?>
                        <?php if ($globalStats['total_matchs'] < 3): ?>
                            <li class="flex items-start">
                                <span class="mr-2">•</span> Manque de temps de jeu pour une analyse stable
                            </li>
                        <?php endif; ?>
                        <li class="flex items-start">
                            <span class="mr-2">•</span> Travail sur la condition physique en fin de match
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Evolution Chart
    const ctxEvolution = document.getElementById('evolutionChart').getContext('2d');
    new Chart(ctxEvolution, {
        type: 'line',
        data: {
            labels: <?= json_encode($chartLabels) ?>,
            datasets: [
                {
                    label: 'Note',
                    data: <?= json_encode($chartNotes) ?>,
                    borderColor: '#9333ea',
                    backgroundColor: 'transparent',
                    tension: 0.4
                },
                {
                    label: 'Buts',
                    data: <?= json_encode($chartButs) ?>,
                    borderColor: '#16a34a',
                    backgroundColor: 'transparent',
                    tension: 0.4
                },
                {
                    label: 'Passes',
                    data: <?= json_encode($chartPasses) ?>,
                    borderColor: '#2563eb',
                    backgroundColor: 'transparent',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            },
            scales: {
                y: { beginAtZero: true, max: 10 }
            }
        }
    });

    // Comparison Chart
    const ctxComparison = document.getElementById('comparisonChart').getContext('2d');
    new Chart(ctxComparison, {
        type: 'bar',
        data: {
            labels: ['Buts/match', 'Passes/match'],
            datasets: [
                {
                    label: '<?= htmlspecialchars($selectedJoueur['prenom']) ?>',
                    data: [<?= $globalStats['buts_par_match'] ?>, <?= $globalStats['passes_par_match'] ?>],
                    backgroundColor: '#16a34a'
                },
                {
                    label: 'Moyenne équipe',
                    data: [<?= $teamAverages['avg_buts'] ?>, <?= $teamAverages['avg_passes'] ?>],
                    backgroundColor: '#9ca3af'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>

<?php require_once "../views/partials/footer.php"; ?>
