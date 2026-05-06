<?php
// Les variables $qualifiedPlayers, $matches, $summonedMap et $pageTitle sont fournies par le contrôleur
$roleUser = $_SESSION['user']['role'] ?? 'joueur';
$currentUserId = $_SESSION['user']['id'] ?? null;
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
            <!-- Alertes de retour -->
            <?php if (isset($_GET['msg'])): ?>
                <div class="mb-6 p-4 rounded-lg flex items-center gap-3 <?= $_GET['msg'] === 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200' ?>">
                    <span class="text-lg">
                        <?= $_GET['msg'] === 'success' ? '✅' : '⚠️' ?>
                    </span>
                    <p class="text-sm font-medium">
                        <?php 
                            if ($_GET['msg'] === 'success') echo "Le joueur a été convoqué avec succès !";
                            if ($_GET['msg'] === 'already_summoned') echo "Ce joueur est déjà convoqué pour ce match précis.";
                            if ($_GET['msg'] === 'overlap') echo "Impossible : ce joueur est déjà convoqué pour un autre match à ce créneau horaire.";
                        ?>
                    </p>
                </div>
            <?php endif; ?>

            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800">Joueurs Éligibles</h2>
                <p class="text-gray-600 italic">Classement basé sur la performance (60%) et la présence (40%).</p>
            </div>

            <!-- Filtres de recherche -->
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
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-lg font-bold text-gray-900"><?= htmlspecialchars($player['nom']) ?></h3>
                                            
                                            <?php 
                                            // Condition : Identité du user connecté + statut convoqué (présent dans le map)
                                            if ($player['id'] == $currentUserId && !empty($summonedMap[$player['id']])): 
                                            ?>
                                                <!-- Indicateur visuel SVG (Etoile de sélection) -->
                                                <svg class="w-5 h-5 text-yellow-500 animate-pulse" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                            <?php endif; ?>
                                        </div>
                                        <p class="text-sm text-green-600 font-medium">Score : <?= number_format($player['score'], 2) ?></p>
                                    </div>
                                    <span class="bg-blue-50 text-blue-700 text-xs px-2 py-1 rounded-full font-semibold">
                                        <?= $player['nb_convocations'] ?> <?= $player['nb_convocations'] > 1 ? 'matchs' : 'match' ?>
                                    </span>
                                </div>

                                <?php
                                // RÉVÉLATION DES DÉTAILS : Uniquement pour le joueur connecté sur sa propre carte
                                if ($player['id'] == $currentUserId && !empty($summonedMap[$player['id']])): 
                                ?>
                                    <div class="mt-2 mb-4 p-3 bg-blue-50 rounded-lg border border-blue-100">
                                        <p class="text-[10px] font-bold text-blue-800 uppercase mb-2 flex items-center gap-1">
                                            <span>📅</span> Tes prochains rendez-vous
                                        </p>
                                        <ul class="space-y-2">
                                            <?php 
                                            foreach ($summonedMap[$player['id']] as $mId): 
                                                // On retrouve les infos du match dans la liste globale $matches
                                                $matchInfo = array_filter($matches, fn($m) => $m['id'] == $mId);
                                                $matchInfo = reset($matchInfo);
                                                if ($matchInfo):
                                            ?>
                                                <li class="text-xs text-blue-700 leading-tight">
                                                    <span class="font-bold"><?= date('d/m à H:i', strtotime($matchInfo['date'])) ?></span><br>
                                                    <span class="text-blue-900"><?= htmlspecialchars($matchInfo['description']) ?></span> @ <?= htmlspecialchars($matchInfo['lieu']) ?>
                                                </li>
                                            <?php endif; endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>

                                <?php if ($roleUser !== 'joueur'): ?>
                                    <!-- Formulaire de convocation -->
                                    <form action="/Convocation-invoke" method="POST" class="mt-4 border-t pt-4">
                                        <?php 
                                            // LOGIQUE PHP : On filtre les matchs disponibles pour ce joueur précis
                                            $playerConvocations = $summonedMap[$player['id']] ?? [];
                                            $availableMatches = array_filter($matches, function($m) use ($playerConvocations) {
                                                return !in_array($m['id'], $playerConvocations);
                                            });
                                            $isFullySummoned = empty($availableMatches);
                                        ?>

                                        <input type="hidden" name="joueur_id" value="<?= $player['id'] ?>">
                                        <div class="mb-3">
                                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Sélectionner le Match</label>
                                            <select name="match_id" required class="w-full bg-gray-50 border border-gray-200 rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-500 outline-none" <?= $isFullySummoned ? 'disabled' : '' ?>>
                                                <?php if (!$isFullySummoned): ?>
                                                    <?php foreach ($availableMatches as $match): ?>
                                                        <option value="<?= $match['id'] ?>">
                                                            [<?= strtoupper($match['type']) ?>] <?= date('d/m', strtotime($match['date'])) ?> - <?= htmlspecialchars($match['lieu']) ?> (<?= htmlspecialchars($match['description']) ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <option disabled selected>Déjà convoqué partout</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>

                                        <!-- Sélection de l'équipe -->
                                        <div class="mb-4">
                                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Équipe attribuée pour le match</label>
                                            <select name="equipe" required class="w-full bg-gray-50 border border-gray-200 rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-500 outline-none" <?= $isFullySummoned ? 'disabled' : '' ?>>
                                                <option value="A" <?= ($player['equipe'] == 1 || $player['equipe'] === 'A') ? 'selected' : '' ?>>Équipe A</option>
                                                <option value="B" <?= ($player['equipe'] == 2 || $player['equipe'] === 'B') ? 'selected' : '' ?>>Équipe B</option>
                                            </select>
                                        </div>

                                        <button type="submit" 
                                            <?= $isFullySummoned ? 'disabled' : '' ?>
                                            class="w-full <?= $isFullySummoned ? 'bg-gray-400' : 'bg-green-600 hover:bg-green-700' ?> text-white font-semibold py-2 rounded-lg transition-colors text-sm">
                                            <?= $isFullySummoned ? 'Déjà convoqué' : 'Convoquer' ?>
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

    <script>
        /**
         * Cette fonction gère uniquement l'aspect visuel du bouton
         * car PHP a déjà marqué les options invalides comme 'disabled'
         */
        function checkConvocation(selectElement) {
            const form = selectElement.closest('form');
            const submitBtn = form.querySelector('button[type="submit"]');
            const selectedOption = selectElement.options[selectElement.selectedIndex];

            // Si l'option sélectionnée est désactivée (PHP l'a décidé)
            if (selectedOption.disabled) {
                submitBtn.disabled = true;
                submitBtn.innerText = 'Déjà convoqué';
                submitBtn.classList.replace('bg-green-600', 'bg-gray-400');
            } else {
                submitBtn.disabled = false;
                submitBtn.innerText = 'Convoquer';
                submitBtn.classList.replace('bg-gray-400', 'bg-green-600');
            }
        }

        // On lance la vérification au chargement pour bloquer le bouton si le 1er match de la liste est déjà pris
        document.querySelectorAll('.match-select').forEach(select => checkConvocation(select));
    </script>
</body>

</html>