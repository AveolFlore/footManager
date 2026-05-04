<?php
// Les variables $qualifiedPlayers, $matches et $pageTitle sont fournies par le contrôleur
$roleUser = $_SESSION['user']['role'] ?? 'joueur';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>convocation page</title>
    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <?php include_once __DIR__ . '/../../partials/header.php'; ?>
    <div class="flex">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1 p-4 md:p-6">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800">Joueurs Éligibles</h2>
                <p class="text-gray-600 italic">Classement basé sur la performance (60%) et la présence (40%).</p>
            </div>

            <!-- Filtres de recherche      -->
            <form method="GET" action="/Convocation-convocation" class="mb-6 bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Rechercher un nom</label>
                    <input type="text" name="search_nom" value="<?= htmlspecialchars($_GET['search_nom'] ?? '') ?>" placeholder="Ex: Doe" class="w-full bg-gray-50 border border-gray-200 rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                </div>
                <div class="w-32">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Équipe</label>
                    <select name="filter_equipe" class="w-full bg-gray-50 border border-gray-200 rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                        <option value="">Toutes</option>
                        <option value="1" <?= ($_GET['filter_equipe'] ?? '') === '1' ? 'selected' : '' ?>>Équipe 1</option>
                        <option value="2" <?= ($_GET['filter_equipe'] ?? '') === '2' ? 'selected' : '' ?>>Équipe 2</option>
                    </select>
                </div>
                <div class="w-32">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Score Min</label>
                    <input type="number" step="0.1" name="filter_score" value="<?= htmlspecialchars($_GET['filter_score'] ?? '') ?>" placeholder="0.0" class="w-full bg-gray-50 border border-gray-200 rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors text-sm font-bold">
                    Filtrer
                </button>
                <a href="/Convocation-convocation" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition-colors text-sm font-bold">
                    Réinitialiser
                </a>
            </form>

            <!-- Grille des joueurs -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (!empty($qualifiedPlayers)): ?>
                    <?php foreach ($qualifiedPlayers as $player): ?>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                            <div class="p-5">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900"><?= htmlspecialchars($player['nom']) ?></h3>
                                        <p class="text-sm text-green-600 font-medium">Score : <?= number_format($player['score'], 2) ?></p>
                                    </div>
                                    <span class="bg-blue-50 text-blue-700 text-xs px-2 py-1 rounded-full font-semibold">
                                        <?= $player['presences'] ?> matchs
                                    </span>
                                </div>

                                <?php if ($roleUser !== 'joueur'): ?>
                                    <!-- Formulaire de convocation -->
                                    <form action="/Convocation-invoke" method="POST" class="mt-4 border-t pt-4">
                                        <input type="hidden" name="joueur_id" value="<?= $player['id'] ?>">
                                        <div class="mb-3">
                                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Sélectionner le Match</label>
                                            <select name="match_id" required class="w-full bg-gray-50 border border-gray-200 rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                                                <?php if (!empty($matches)): ?>
                                                    <?php foreach ($matches as $match): ?>
                                                        <option value="<?= $match['id'] ?>">
                                                            [<?= strtoupper($match['type']) ?>] <?= date('d/m', strtotime($match['date'])) ?> - <?= htmlspecialchars($match['lieu']) ?> (<?= htmlspecialchars($match['description']) ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <option disabled>Aucun match disponible</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>

                                        <!-- Sélection de l'équipe -->
                                        <div class="mb-4">
                                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Équipe attribuée pour le match</label>
                                            <select name="equipe" required class="w-full bg-gray-50 border border-gray-200 rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                                                <option value="A" <?= ($player['equipe'] == 1 || $player['equipe'] === 'A') ? 'selected' : '' ?>>Équipe A</option>
                                                <option value="B" <?= ($player['equipe'] == 2 || $player['equipe'] === 'B') ? 'selected' : '' ?>>Équipe B</option>
                                            </select>
                                        </div>

                                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 rounded-lg transition-colors text-sm">
                                            Convoquer
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <div class="mt-4 border-t pt-4 text-center">
                                        <span class="text-xs text-gray-400 italic">Consultation uniquement</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-full py-12 text-center bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                        <p class="text-gray-500 italic">Aucun joueur éligible pour le moment.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>

</html>