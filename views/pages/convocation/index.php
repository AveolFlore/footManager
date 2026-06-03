<?php
// =====================================================================
// DONNÉES FOURNIES PAR LE CONTRÔLEUR
// =====================================================================
// $qualifiedPlayers : joueurs déjà paginés
// $matches : liste des matchs disponibles
// $summonedMap : convocations par joueur
// $matchesById : index des matchs par id
// $page : page actuelle
// $perPage : nombre d'éléments par page
// $totalPlayers : nombre total de joueurs correspondant aux filtres
// $totalPages : nombre total de pages
// =====================================================================

$roleUser = $_SESSION['user']['role'] ?? 'joueur';
$currentUserId = $_SESSION['user']['id'] ?? null;


// =====================================================================
// CONFIGURATION DE LA PAGINATION
// =====================================================================

// Page actuelle envoyée par le contrôleur
$currentPage = $page;

// Les joueurs sont déjà paginés dans le modèle.
// On ne doit PAS refaire un array_slice ici.
$playersToShow = $qualifiedPlayers;

// Offset utilisé uniquement pour l'affichage
$offset = ($currentPage - 1) * $perPage;

// Sécurité : si quelqu'un saisit une page trop grande
if ($currentPage > $totalPages && $totalPages > 0) {
    $currentPage = $totalPages;
}


// =====================================================================
// GÉNÉRATION DES LIENS DE PAGINATION
// Conserve tous les filtres dans l'URL
// =====================================================================

function buildPaginationLink($pageNum)
{
    $params = $_GET;
    $params['page'] = $pageNum;

    return '/Convocation-convocation?' . http_build_query($params);
}


// =====================================================================
// GÉNÉRATION DES NUMÉROS DE PAGE
// Exemple : 1 ... 4 5 6 7 8 ... 20
// =====================================================================

function getPaginationRange($currentPage, $totalPages, $delta = 2)
{
    $range = [];

    // Peu de pages => tout afficher
    if ($totalPages <= 7) {
        for ($i = 1; $i <= $totalPages; $i++) {
            $range[] = $i;
        }

        return $range;
    }

    // Première page
    $range[] = 1;

    // Fenêtre autour de la page actuelle
    $start = max(2, $currentPage - $delta);
    $end = min($totalPages - 1, $currentPage + $delta);

    // Ellipse après la page 1
    if ($start > 2) {
        $range[] = '...';
    }

    // Pages centrales
    for ($i = $start; $i <= $end; $i++) {
        $range[] = $i;
    }

    // Ellipse avant la dernière page
    if ($end < $totalPages - 1) {
        $range[] = '...';
    }

    // Dernière page
    $range[] = $totalPages;

    return $range;
}


// =====================================================================
// TABLEAU FINAL DE PAGINATION
// =====================================================================

$paginationRange = getPaginationRange(
    $currentPage,
    $totalPages
);

$pageTitle = "Convocations";
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - FC Blue Lock</title>
    <link rel="icon" type="image/png" href="/images/blue_lock_logo.png">
    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .player-card {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="bg-gradient-to-br from-slate-50 to-slate-100">
    <div class="flex min-h-screen">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1">
            <?php include_once __DIR__ . '/../../partials/header.php'; ?>
            <div class="p-6 md:p-8 lg:p-10">
                <!-- Alertes de retour -->
                <?php if (isset($_GET['msg'])): ?>
                    <div class="mb-8 p-4 rounded-2xl flex items-center gap-3 <?= $_GET['msg'] === 'success' ? 'bg-green-50 text-green-700 border border-green-200 shadow-sm' : 'bg-red-50 text-red-700 border border-red-200 shadow-sm' ?>">
                        <span class="text-xl">
                            <?= $_GET['msg'] === 'success' ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-exclamation-triangle"></i>' ?>
                        </span>
                        <p class="font-semibold">
                            <?php
                            if ($_GET['msg'] === 'success') echo "Le joueur a été convoqué avec succès !";
                            if ($_GET['msg'] === 'already_summoned') echo "Ce joueur est déjà convoqué pour ce match précis.";
                            if ($_GET['msg'] === 'overlap') echo "Impossible : ce joueur est déjà convoqué pour un autre match à ce créneau horaire.";
                            ?>
                        </p>
                    </div>
                <?php endif; ?>

                <div class="mb-10 flex flex-col md:flex-row justify-between items-end gap-4">
                    <div>
                        <h2 class="text-3xl font-extrabold text-slate-800 flex items-center gap-3">
                            <i class="fas fa-clipboard-list text-green-600"></i>
                            Joueurs Éligibles
                        </h2>
                        <p class="text-slate-500 mt-2">Classement basé sur la performance (60%) et la présence (40%).</p>
                    </div>
                    <!-- Indicateur de pagination -->
                    <div class="text-sm text-slate-500 bg-white px-4 py-2 rounded-xl border border-slate-100 shadow-sm">
                        <span class="font-semibold text-slate-700"><?= $totalPlayers ?></span> joueur<?= $totalPlayers > 1 ? 's' : '' ?> au total
                    </div>
                </div>

                <!-- Filtres de recherche -->
                <form method="GET" action="/Convocation-convocation" class="mb-8 bg-white p-6 rounded-3xl shadow-lg border border-slate-100 flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Rechercher un nom</label>
                        <input type="text" name="search_nom" value="<?= htmlspecialchars($_GET['search_nom'] ?? '') ?>" placeholder="Ex: Doe" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all">
                    </div>
                    <div class="w-32">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Équipe</label>
                        <select name="filter_equipe" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all">
                            <option value="">Toutes</option>
                            <option value="1" <?= ($_GET['filter_equipe'] ?? '') === '1' ? 'selected' : '' ?>>Équipe 1</option>
                            <option value="2" <?= ($_GET['filter_equipe'] ?? '') === '2' ? 'selected' : '' ?>>Équipe 2</option>
                        </select>
                    </div>
                    <div class="w-32">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Score Min</label>
                        <input type="number" step="0.1" name="filter_score" value="<?= htmlspecialchars($_GET['filter_score'] ?? '') ?>" placeholder="0.0" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all">
                    </div>
                    <!-- Conservation de la page lors du filtrage (reset à 1) -->
                    <input type="hidden" name="page" value="1">
                    <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-xl transition-all shadow-lg shadow-blue-200 font-bold">
                        <i class="fas fa-filter mr-2"></i> Filtrer
                    </button>
                    <a href="/Convocation-convocation" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-6 py-3 rounded-xl transition-all font-bold">
                        Réinitialiser
                    </a>
                </form>

                <!-- Grille des joueurs -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php if (!empty($playersToShow)): ?>
                        <?php foreach ($playersToShow as $player): ?>
                            <div class="player-card bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden hover:shadow-2xl transition-all duration-300">
                                <div class="p-6">
                                    <div class="flex justify-between items-start mb-5">
                                        <div>
                                            <div class="flex items-center gap-2 mb-2">
                                                <h3 class="text-xl font-extrabold text-slate-900"><?= htmlspecialchars($player['nom']) ?></h3>
                                                <?php
                                                // CONDITION UNIQUE : SVG uniquement pour moi si je suis convoqué
                                                if ($player['id'] == $currentUserId && !empty($summonedMap[$player['id']])):
                                                ?>
                                                    <i class="fas fa-star text-yellow-500 animate-pulse text-lg"></i>
                                                <?php endif; ?>
                                            </div>
                                            <p class="text-sm text-green-600 font-semibold flex items-center gap-2">
                                                <i class="fas fa-chart-line"></i>
                                                Score : <?= number_format($player['score'], 2) ?>
                                            </p>
                                        </div>
                                        <span class="bg-blue-100 text-blue-700 text-xs px-3 py-1.5 rounded-full font-bold">
                                            <i class="fas fa-calendar mr-1"></i>
                                            <?= $player['nb_convocations'] ?> <?= $player['nb_convocations'] > 1 ? 'matchs' : 'match' ?>
                                        </span>
                                    </div>

                                    <?php
                                    if ($player['id'] == $currentUserId && !empty($summonedMap[$player['id']])):
                                    ?>
                                        <div class="mt-2 mb-5 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl border border-blue-100">
                                            <p class="text-xs font-extrabold text-blue-800 uppercase mb-3 flex items-center gap-2 tracking-wider">
                                                <i class="fas fa-calendar-check"></i> Détails de ma convocation
                                            </p>
                                            <ul class="space-y-2">
                                                <?php
                                                foreach ($summonedMap[$player['id']] as $mId):
                                                    $matchInfo = $matchesById[$mId] ?? null;
                                                    if ($matchInfo):
                                                ?>
                                                        <li class="text-sm text-blue-700 leading-tight">
                                                            <i class="far fa-clock mr-2"></i>
                                                            <span class="font-bold text-blue-900"><?= date('d/m', strtotime($matchInfo['date'])) ?> à <?= date('H:i', strtotime($matchInfo['date'])) ?></span>
                                                            <span class="block text-xs text-blue-700 mt-1"><?= htmlspecialchars($matchInfo['description']) ?></span>
                                                        </li>
                                                <?php endif;
                                                endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($roleUser !== 'joueur'): ?>
                                        <form action="/Convocation-invoke" method="POST" class="mt-5 border-t border-slate-100 pt-5">
                                            <?php
                                            $playerConvocations = $summonedMap[$player['id']] ?? [];
                                            $availableMatches = array_filter($matches, function ($m) use ($playerConvocations) {
                                                return !in_array($m['id'], $playerConvocations);
                                            });
                                            $isFullySummoned = empty($availableMatches);
                                            ?>

                                            <input type="hidden" name="joueur_id" value="<?= $player['id'] ?>">
                                            <div class="mb-4">
                                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Sélectionner le Match</label>
                                                <select name="match_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all" <?= $isFullySummoned ? 'disabled' : '' ?>>
                                                    <?php if (!$isFullySummoned): ?>
                                                        <?php foreach ($availableMatches as $match): ?>
                                                            <option value="<?= $match['id'] ?>">
                                                                [<?= strtoupper($match['type']) ?>] <?= date('d/m', strtotime($match['date'])) ?> - <?= htmlspecialchars($match['lieu']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <option disabled selected>Déjà convoqué partout</option>
                                                    <?php endif; ?>
                                                </select>
                                            </div>

                                            <div class="mb-5">
                                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Équipe attribuée pour le match</label>
                                                <select name="equipe" required class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all" <?= $isFullySummoned ? 'disabled' : '' ?>>
                                                    <option value="A" <?= ($player['equipe'] == 1 || $player['equipe'] === 'A') ? 'selected' : '' ?>>Équipe A</option>
                                                    <option value="B" <?= ($player['equipe'] == 2 || $player['equipe'] === 'B') ? 'selected' : '' ?>>Équipe B</option>
                                                </select>
                                            </div>

                                            <button type="submit"
                                                <?= $isFullySummoned ? 'disabled' : '' ?>
                                                class="w-full <?= $isFullySummoned ? 'bg-slate-400 cursor-not-allowed' : 'bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 shadow-lg shadow-green-200' ?> text-white font-bold py-3 rounded-xl transition-all text-sm">
                                                <i class="fas fa-paper-plane mr-2"></i>
                                                <?= $isFullySummoned ? 'Déjà convoqué' : 'Convoquer' ?>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <div class="mt-5 border-t border-slate-100 pt-5 text-center">
                                            <span class="text-xs text-slate-400 italic flex items-center justify-center gap-2">
                                                <i class="fas fa-eye"></i>
                                                Consultation uniquement
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-full py-16 text-center bg-white rounded-3xl border-2 border-dashed border-slate-200 shadow-lg">
                            <i class="fas fa-user-slash text-slate-300 text-6xl mb-4"></i>
                            <p class="text-slate-500 italic text-lg font-medium">Aucun joueur éligible pour le moment.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ==================== PAGINATION NAVIGATION ==================== -->
                <?php if ($totalPages > 1): ?>
                    <div class="mt-12">
                        <nav class="flex justify-center items-center gap-3" aria-label="Pagination">

                            <!-- Bouton Précédent -->
                            <?php if ($currentPage > 1): ?>
                                <a href="<?= buildPaginationLink($currentPage - 1) ?>"
                                    class="flex items-center justify-center w-12 h-12 rounded-2xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900 hover:border-slate-300 transition-all shadow-sm group">
                                    <i class="fas fa-chevron-left group-hover:-translate-x-1 transition-transform"></i>
                                </a>
                            <?php else: ?>
                                <span class="flex items-center justify-center w-12 h-12 rounded-2xl bg-slate-100 border border-slate-200 text-slate-400 cursor-not-allowed">
                                    <i class="fas fa-chevron-left"></i>
                                </span>
                            <?php endif; ?>

                            <!-- Numéros de pages avec ellipses -->
                            <?php foreach ($paginationRange as $page): ?>
                                <?php if ($page === '...'): ?>
                                    <span class="w-12 h-12 flex items-center justify-center text-slate-400 font-bold">...</span>
                                <?php elseif ($page == $currentPage): ?>
                                    <span class="w-12 h-12 flex items-center justify-center rounded-2xl bg-gradient-to-r from-green-600 to-green-700 text-white font-bold shadow-lg shadow-green-200">
                                        <?= $page ?>
                                    </span>
                                <?php else: ?>
                                    <a href="<?= buildPaginationLink($page) ?>"
                                        class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900 hover:border-slate-300 transition-all font-bold shadow-sm">
                                        <?= $page ?>
                                    </a>
                                <?php endif; ?>
                            <?php endforeach; ?>

                            <!-- Bouton Suivant -->
                            <?php if ($currentPage < $totalPages): ?>
                                <a href="<?= buildPaginationLink($currentPage + 1) ?>"
                                    class="flex items-center justify-center w-12 h-12 rounded-2xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900 hover:border-slate-300 transition-all shadow-sm group">
                                    <i class="fas fa-chevron-right group-hover:translate-x-1 transition-transform"></i>
                                </a>
                            <?php else: ?>
                                <span class="flex items-center justify-center w-12 h-12 rounded-2xl bg-slate-100 border border-slate-200 text-slate-400 cursor-not-allowed">
                                    <i class="fas fa-chevron-right"></i>
                                </span>
                            <?php endif; ?>
                        </nav>

                        <!-- Info complémentaire -->
                        <p class="text-center text-xs text-slate-400 mt-4 font-medium">
                            Affichage des joueurs <?= $offset + 1 ?> à <?= min($offset + $perPage, $totalPlayers) ?> sur <?= $totalPlayers ?>
                        </p>
                    </div>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <script>
        function checkConvocation(selectElement) {
            const form = selectElement.closest('form');
            const submitBtn = form.querySelector('button[type="submit"]');
            const selectedOption = selectElement.options[selectElement.selectedIndex];

            if (selectedOption.disabled) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Déjà convoqué';
                submitBtn.classList.add('bg-slate-400', 'cursor-not-allowed');
                submitBtn.classList.remove('bg-gradient-to-r', 'from-green-600', 'to-green-700', 'hover:from-green-700', 'hover:to-green-800', 'shadow-lg', 'shadow-green-200');
            } else {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Convoquer';
                submitBtn.classList.add('bg-gradient-to-r', 'from-green-600', 'to-green-700', 'hover:from-green-700', 'hover:to-green-800', 'shadow-lg', 'shadow-green-200');
                submitBtn.classList.remove('bg-slate-400', 'cursor-not-allowed');
            }
        }

        document.querySelectorAll('select[name="match_id"]').forEach(select => checkConvocation(select));
    </script>
</body>

</html>