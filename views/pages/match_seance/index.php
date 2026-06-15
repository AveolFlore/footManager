<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../middleware/Role.php';

requireLogin();

use Controllers\MatchSeanceController;
use Controllers\Convocation\ConvocationController;
use Controllers\ResultatMatchController;

$matchController = new MatchSeanceController();
$convocationController = new ConvocationController();

$matchs = $matchController->index();
$matchs_en_retard = [];
foreach ($matchs as $match) {
    if (
        $match['statut'] === 'publie' &&
        strtotime($match['date']) < strtotime('-1 day')
    ) {
        $matchs_en_retard[] = $match;
    }
}
$resultatController = new ResultatMatchController();
$pageTitle = "Matchs";

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100 text-gray-900 min-h-screen antialiased">
    <?php include_once __DIR__ . '/../../partials/header.php'; ?>
    
    <div class="flex min-h-screen">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1 p-6 md:p-8 lg:p-10 max-w-7xl mx-auto">
            
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4 border-b border-gray-200 pb-6">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800 flex items-center gap-3">
                        <i class="fas fa-futbol text-gray-600"></i>
                        Liste des Matchs
                    </h1>
                    <p class="text-gray-500 mt-1 text-sm"><?= count($matchs) ?> matchs enregistrés</p>
                </div>
                <?php if (in_array($_SESSION['user']['role'], ['president', 'organisateur'])): ?>
                    <a href="/page-matchcreate"
                        class="flex items-center gap-2 bg-blue-600 text-white px-5 py-2.5 rounded font-medium hover:bg-blue-700 transition-colors shadow-sm text-sm">
                        <i class="fas fa-plus"></i>
                        Créer un match
                    </a>
                <?php endif; ?>
            </div>

            <div class="flex flex-wrap gap-2 mb-6 text-sm">
                <button onclick="filtrer('tous')"
                    id="btn-tous"
                    class="filtre-btn px-4 py-2 rounded border border-blue-600 bg-blue-600 text-white font-medium transition-all shadow-sm">
                    Tous les matchs
                </button>
                <button onclick="filtrer('planifie')"
                    id="btn-planifie"
                    class="filtre-btn px-4 py-2 rounded border border-gray-300 bg-white text-gray-700 font-medium hover:bg-gray-50 transition-all shadow-sm">
                    À venir
                </button>
                <button onclick="filtrer('termine')"
                    id="btn-termine"
                    class="filtre-btn px-4 py-2 rounded border border-gray-300 bg-white text-gray-700 font-medium hover:bg-gray-50 transition-all shadow-sm">
                    Terminés
                </button>
            </div>

            <?php if (isset($_GET['msg'])): ?>
                <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded shadow-sm flex items-center">
                    <i class="fas fa-check-circle mr-3"></i>
                    <?= htmlspecialchars($_GET['msg']) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($matchs_en_retard)): ?>
                <div class="mb-6 p-4 bg-amber-100 border border-amber-400 text-amber-800 rounded shadow-sm flex items-center">
                    <i class="fas fa-exclamation-triangle mr-3 text-amber-600"></i>
                    <span><strong>Attention :</strong> <?= count($matchs_en_retard) ?> match(s) en attente de clôture. Veuillez saisir les scores manquants.</span>
                </div>
            <?php endif; ?>

            <div id="liste-matchs" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                <?php foreach ($matchs as $match): ?>

                    <?php
                    $badge = match ($match['statut']) {
                        'planifie', 'publie' => ['label' => 'À VENIR', 'class' => 'bg-blue-100 text-blue-800 border-blue-200'],
                        'termine' => ['label' => 'TERMINÉ', 'class' => 'bg-gray-100 text-gray-800 border-gray-200'],
                        default => ['label' => strtoupper($match['statut']), 'class' => 'bg-gray-50 text-gray-600 border-gray-200']
                    };

                    $convoques = $convocationController->index((int)$match['id']);
                    $nb_convoques = count($convoques);

                    $score = null;
                    if ($match['statut'] === 'termine') {
                        $resultat = $resultatController->index((int)$match['id']);
                        if ($resultat) {
                            $score = $resultat['buts_equipe_a'] . ' - ' . $resultat['buts_equipe_b'];
                        }
                    }
                    ?>

                    <a href="/page-matchdetail?id=<?= $match['id'] ?>"
                        class="match-card block bg-white border border-gray-200 p-5 rounded-lg shadow-sm hover:shadow-md hover:border-gray-300 transition-all duration-200"
                        data-statut="<?= $match['statut'] ?>">
                        
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-xs px-2.5 py-0.5 border rounded-full font-semibold <?= $badge['class'] ?>">
                                <?= $badge['label'] ?>
                            </span>
                            <?php if ($score): ?>
                                <span class="text-xl font-bold text-gray-800 tracking-tight"><?= $score ?></span>
                            <?php endif; ?>
                        </div>

                        <h2 class="text-base font-bold text-gray-900 mb-4 truncate">
                            Équipe A <span class="text-gray-400 font-normal text-sm">vs</span> Équipe B
                        </h2>

                        <div class="space-y-2 mb-4 text-sm text-gray-600">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-calendar text-gray-400 w-4"></i>
                                <span><?= date('d/m/Y', strtotime($match['date'])) ?></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-clock text-gray-400 w-4"></i>
                                <span><?= date('H:i', strtotime($match['date'])) ?></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-map-marker-alt text-gray-400 w-4"></i>
                                <span class="truncate"><?= htmlspecialchars($match['lieu']) ?></span>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-3 flex items-center justify-between text-xs text-gray-500">
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fas fa-users text-gray-400"></i>
                                Joueurs convoqués : <strong><?= $nb_convoques ?></strong>
                            </span>
                            <span class="text-blue-600 group-hover:underline">Détails <i class="fas fa-chevron-right text-[10px]"></i></span>
                        </div>

                    </a>

                <?php endforeach; ?>

            </div>

        </main>
    </div>

    <script>
        function filtrer(statut) {
            document.querySelectorAll('.filtre-btn').forEach(btn => {
                btn.className = "filtre-btn px-4 py-2 rounded border border-gray-300 bg-white text-gray-700 font-medium hover:bg-gray-50 transition-all shadow-sm";
            });

            const btnActif = document.getElementById('btn-' + statut);
            if (btnActif) {
                btnActif.className = "filtre-btn px-4 py-2 rounded border border-blue-600 bg-blue-600 text-white font-medium transition-all shadow-sm";
            }

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