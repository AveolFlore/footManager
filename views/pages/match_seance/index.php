<?php
require_once __DIR__ . '/../../../middleware/Role.php';

requireLogin();

// on récupère les données via le controller
use Controllers\MatchSeanceController;
use Controllers\ConvocationController;
use Controllers\ResultatMatchController;

$matchController = new MatchSeanceController();
$convocationController = new ConvocationController();

$matchs = $matchController->index();
$resultatController = new ResultatMatchController();

// var_dump($_SESSION);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Match</title>
    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <?php include_once __DIR__ . '/../../partials/header.php'; ?>
    <div class="flex">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1 overflow-y-auto p-6">
            <!-- Mon contenu -->
            <!-- En-tête -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Matchs</h1>
                    <p class="text-sm text-gray-500"><?= count($matchs) ?> matchs</p>
                </div>
                <?php if (in_array($_SESSION['user']['role'], ['president', 'organisateur'])): ?>
                    <a href="/page-matchcreate"
                        class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                        + Créer un match
                    </a>
                <?php endif; ?>
            </div>

            <!-- Filtres -->
            <div class="flex gap-2 mb-6">
                <button onclick="filtrer('tous')"
                    id="btn-tous"
                    class="filtre-btn px-4 py-2 rounded-full text-sm font-medium bg-green-600 text-white">
                    Tous
                </button>
                <button onclick="filtrer('planifie')"
                    id="btn-planifie"
                    class="filtre-btn px-4 py-2 rounded-full text-sm font-medium border border-gray-300 text-gray-600 hover:bg-gray-100">
                    À venir
                </button>
                <button onclick="filtrer('termine')"
                    id="btn-termine"
                    class="filtre-btn px-4 py-2 rounded-full text-sm font-medium border border-gray-300 text-gray-600 hover:bg-gray-100">
                    Terminés
                </button>
            </div>

            <!-- Message flash -->
            <?php if (isset($_GET['msg'])): ?>
                <div class="mb-4 px-4 py-3 rounded-lg bg-green-100 text-green-700 text-sm">
                    <?= htmlspecialchars($_GET['msg']) ?>
                </div>
            <?php endif; ?>

            <!-- Cards matchs -->
            <div id="liste-matchs" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

                <?php foreach ($matchs as $match): ?>

                    <?php
                    // badge statut
                    $badge = match ($match['statut']) {
                        'planifie' => ['label' => 'À venir',  'class' => 'bg-blue-100 text-blue-600'],
                        'publie'   => ['label' => 'À venir',  'class' => 'bg-blue-100 text-blue-600'],
                        'termine'  => ['label' => 'Terminé',  'class' => 'bg-green-100 text-green-600'],
                        default    => ['label' => $match['statut'], 'class' => 'bg-gray-100 text-gray-600']
                    };

                    // nombre de convoqués
                    $convoques = $convocationController->index((int) $match['id']);
                    $nb_convoques = count($convoques);

                    // résultat si match terminé
                    $score = null;
                    if ($match['statut'] === 'termine') {

                        
                        $resultat = $resultatController->index((int) $match['id']);
                        if ($resultat) {
                            $score = $resultat['buts_equipe_a'] . ' - ' . $resultat['buts_equipe_b'];
                        }
                    }
                    ?>

                    <a href="/page-matchdetail?id=<?= $match['id'] ?>"
                        class="match-card block bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition"
                        data-statut="<?= $match['statut'] ?>">

                        <!-- Header card -->
                        <div class="flex justify-between items-start mb-3">
                            <span class="text-xs px-3 py-1 rounded-full font-medium <?= $badge['class'] ?>">
                                <?= $badge['label'] ?>
                            </span>
                            <?php if ($score): ?>
                                <span class="text-lg font-bold text-gray-800"><?= $score ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Nom adversaire / titre -->
                        <h2 class="text-lg font-semibold text-gray-800 mb-3">
                            Équipe A vs Équipe B
                        </h2>

                        <!-- Infos -->
                        <div class="space-y-1 mb-4">
                            <div class="flex items-center gap-2 text-sm text-gray-500">
                                <span>📅</span>
                                <span><?= date('l d F', strtotime($match['date'])) ?></span>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-500">
                                <span>🕐</span>
                                <span><?= date('H:i', strtotime($match['date'])) ?></span>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-500">
                                <span>📍</span>
                                <span><?= htmlspecialchars($match['lieu']) ?></span>
                            </div>
                        </div>

                        <!-- Séparateur -->
                        <hr class="border-gray-100 mb-3">

                        <!-- Nb convoqués -->
                        <p class="text-sm text-gray-400"><?= $nb_convoques ?> joueurs convoqués</p>

                    </a>

                <?php endforeach; ?>

            </div>

        </main>
    </div>

    <!-- Filtre JS -->
    <script>
        function filtrer(statut) {

            // reset tous les boutons
            document.querySelectorAll('.filtre-btn').forEach(btn => {
                btn.classList.remove('bg-green-600', 'text-white');
                btn.classList.add('border', 'border-gray-300', 'text-gray-600');
            });

            // activer le bouton cliqué
            const btnActif = document.getElementById('btn-' + statut);
            if (btnActif) {
                btnActif.classList.add('bg-green-600', 'text-white');
                btnActif.classList.remove('border', 'border-gray-300', 'text-gray-600');
            }

            // filtrer les cards
            document.querySelectorAll('.match-card').forEach(card => {
                if (statut === 'tous') {
                    card.style.display = '';
                } else if (statut === 'planifie') {
                    card.style.display = (card.dataset.statut === 'planifie' || card.dataset.statut === 'publie') ? '' : 'none';
                } else if (statut === 'termine') {
                    card.style.display = card.dataset.statut === 'termine' ? '' : 'none';
                }
            });
        }
    </script>
</body>

</html>