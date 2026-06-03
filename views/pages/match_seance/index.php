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
    <title><?= $pageTitle ?> - FC Blue Lock</title>
    <link rel="icon" type="image/png" href="/images/blue_lock_logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gradient-to-br from-slate-50 to-slate-100">
    <?php include_once __DIR__ . '/../../partials/header.php'; ?>
    <div class="flex min-h-screen">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1 overflow-y-auto">
            <div class="p-6 md:p-8 lg:p-10">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 gap-4">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 flex items-center gap-4">
                            <i class="fas fa-futbol text-green-600"></i>
                            Matchs
                        </h1>
                        <p class="text-slate-500 mt-2 text-lg"><?= count($matchs) ?> matchs</p>
                    </div>
                    <?php if (in_array($_SESSION['user']['role'], ['president', 'organisateur'])): ?>
                        <a href="/page-matchcreate"
                            class="flex items-center gap-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-6 py-3 rounded-2xl text-lg font-bold transition-all shadow-lg shadow-green-200">
                            <i class="fas fa-plus"></i>
                            Créer un match
                        </a>
                    <?php endif; ?>
                </div>

                <div class="flex flex-wrap gap-3 mb-8">
                    <button onclick="filtrer('tous')"
                        id="btn-tous"
                        class="filtre-btn px-6 py-3 rounded-2xl text-base font-bold bg-gradient-to-r from-green-600 to-green-700 text-white shadow-lg shadow-green-200">
                        Tous
                    </button>
                    <button onclick="filtrer('planifie')"
                        id="btn-planifie"
                        class="filtre-btn px-6 py-3 rounded-2xl text-base font-bold border-2 border-slate-200 text-slate-600 hover:bg-slate-100 transition-all bg-white shadow-sm">
                        À venir
                    </button>
                    <button onclick="filtrer('termine')"
                        id="btn-termine"
                        class="filtre-btn px-6 py-3 rounded-2xl text-base font-bold border-2 border-slate-200 text-slate-600 hover:bg-slate-100 transition-all bg-white shadow-sm">
                        Terminés
                    </button>
                </div>

                <?php if (isset($_GET['msg'])): ?>
                    <div class="mb-8 px-6 py-4 rounded-2xl bg-gradient-to-r from-green-50 to-emerald-50 text-green-700 text-lg font-semibold border border-green-200 shadow-sm">
                        <i class="fas fa-check-circle mr-3"></i>
                        <?= htmlspecialchars($_GET['msg']) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($matchs_en_retard)): ?>
                    <div class="mb-8 px-6 py-4 rounded-2xl bg-gradient-to-r from-orange-50 to-yellow-50 text-orange-700 text-lg font-semibold border border-orange-200 shadow-sm">
                        <i class="fas fa-exclamation-triangle mr-3"></i>
                        <?= count($matchs_en_retard) ?> match(s) non clôturé(s) depuis plus d'un jour — pensez à saisir les résultats.
                    </div>
                <?php endif; ?>

                <div id="liste-matchs" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                    <?php foreach ($matchs as $match): ?>

                        <?php
                        $badge = match ($match['statut']) {
                            'planifie' => ['label' => 'À venir', 'class' => 'bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-600 border border-blue-200'],
                            'publie' => ['label' => 'À venir', 'class' => 'bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-600 border border-blue-200'],
                            'termine' => ['label' => 'Terminé', 'class' => 'bg-gradient-to-r from-green-100 to-emerald-100 text-green-600 border border-green-200'],
                            default => ['label' => $match['statut'], 'class' => 'bg-slate-100 text-slate-600 border border-slate-200']
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
                            class="match-card block bg-white rounded-3xl border border-slate-100 p-6 hover:shadow-2xl transition-all duration-300 shadow-xl"
                            data-statut="<?= $match['statut'] ?>">

                            <div class="flex justify-between items-start mb-5">
                                <span class="text-sm px-4 py-2 rounded-2xl font-bold <?= $badge['class'] ?>">
                                    <?= $badge['label'] ?>
                                </span>
                                <?php if ($score): ?>
                                    <span class="text-3xl font-extrabold text-slate-800"><?= $score ?></span>
                                <?php endif; ?>
                            </div>

                            <h2 class="text-xl font-bold text-slate-800 mb-5">
                                Équipe A vs Équipe B
                            </h2>

                            <div class="space-y-3 mb-5">
                                <div class="flex items-center gap-3 text-slate-600">
                                    <i class="fas fa-calendar text-blue-600 text-lg"></i>
                                    <span class="font-semibold text-lg"><?= date('l d F', strtotime($match['date'])) ?></span>
                                </div>
                                <div class="flex items-center gap-3 text-slate-600">
                                    <i class="fas fa-clock text-yellow-600 text-lg"></i>
                                    <span class="font-semibold text-lg"><?= date('H:i', strtotime($match['date'])) ?></span>
                                </div>
                                <div class="flex items-center gap-3 text-slate-600">
                                    <i class="fas fa-map-marker-alt text-purple-600 text-lg"></i>
                                    <span class="font-semibold text-lg"><?= htmlspecialchars($match['lieu']) ?></span>
                                </div>
                            </div>

                            <div class="h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent mb-4"></div>

                            <p class="text-sm text-slate-400 font-semibold">
                                <i class="fas fa-users mr-2 text-green-600"></i>
                                <?= $nb_convoques ?> joueurs convoqués
                            </p>

                        </a>

                    <?php endforeach; ?>

                </div>

            </div>
        </main>
    </div>

    <script>
        function filtrer(statut) {
            document.querySelectorAll('.filtre-btn').forEach(btn => {
                btn.classList.remove('bg-gradient-to-r', 'from-green-600', 'to-green-700', 'text-white', 'shadow-lg', 'shadow-green-200');
                btn.classList.add('border-2', 'border-slate-200', 'text-slate-600', 'bg-white', 'shadow-sm');
            });

            const btnActif = document.getElementById('btn-' + statut);
            if (btnActif) {
                btnActif.classList.add('bg-gradient-to-r', 'from-green-600', 'to-green-700', 'text-white', 'shadow-lg', 'shadow-green-200');
                btnActif.classList.remove('border-2', 'border-slate-200', 'text-slate-600', 'bg-white', 'shadow-sm');
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
