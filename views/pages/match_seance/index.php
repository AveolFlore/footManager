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
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            /* Linear gradient sombre + image */
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
                        url('/assets/images/impact.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .glass-panel {
            /* Glassmorphism appliqué */
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
        }
    </style>
</head>

<body class="text-white min-h-screen font-sans antialiased">
    <?php include_once __DIR__ . '/../../partials/floating-nav.php'; ?>
    
    <div class="pt-20 min-h-screen">
        <main class="overflow-y-auto p-6 md:p-10">
            <div class="max-w-7xl mx-auto">
                
                <div class="glass-panel p-6 rounded-2xl shadow-lg flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                    <div>
                        <h1 class="text-2xl font-bold text-white flex items-center gap-3">
                            <i class="fas fa-futbol text-blue-400"></i>
                            <span>Liste des Matchs</span>
                        </h1>
                        <p class="text-sm text-slate-200 mt-1"><?= count($matchs) ?> matchs enregistrés</p>
                    </div>
                    <?php if (in_array($_SESSION['user']['role'], ['president', 'organisateur'])): ?>
                        <a href="/page-matchcreate" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition shadow-md">
                            <i class="fas fa-plus mr-2"></i>Créer un match
                        </a>
                    <?php endif; ?>
                </div>

                <div class="flex flex-wrap gap-3 mb-8">
                    <button onclick="filtrer('tous')" id="btn-tous" class="filtre-btn px-5 py-2 rounded-xl bg-blue-600 text-white font-semibold text-sm shadow-md transition-all">Tous</button>
                    <button onclick="filtrer('planifie')" id="btn-planifie" class="filtre-btn px-5 py-2 rounded-xl bg-white/20 hover:bg-white/40 text-white font-semibold text-sm shadow-sm transition-all">À venir</button>
                    <button onclick="filtrer('termine')" id="btn-termine" class="filtre-btn px-5 py-2 rounded-xl bg-white/20 hover:bg-white/40 text-white font-semibold text-sm shadow-sm transition-all">Terminés</button>
                </div>

                <?php if (isset($_GET['msg'])): ?>
                    <div class="mb-6 p-4 bg-emerald-900/50 border border-emerald-500 text-white rounded-xl flex items-center">
                        <i class="fas fa-check-circle mr-3"></i> <?= htmlspecialchars($_GET['msg']) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($matchs_en_retard)): ?>
                    <div class="mb-6 p-4 bg-amber-900/50 border border-amber-500 text-white rounded-xl flex items-center">
                        <i class="fas fa-exclamation-triangle mr-3"></i>
                        <span><strong>Attention :</strong> <?= count($matchs_en_retard) ?> match(s) en attente de score.</span>
                    </div>
                <?php endif; ?>

                <div id="liste-matchs" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($matchs as $match): 
                        $badge = match ($match['statut']) {
                            'planifie', 'publie' => ['label' => 'À VENIR', 'class' => 'bg-blue-600 text-white'],
                            'termine' => ['label' => 'TERMINÉ', 'class' => 'bg-white/20 text-white'],
                            default => ['label' => strtoupper($match['statut']), 'class' => 'bg-gray-600 text-white']
                        };
                        $nb_convoques = count($convocationController->index((int)$match['id']));
                        $score = null;
                        if ($match['statut'] === 'termine') {
                            $res = $resultatController->index((int)$match['id']);
                            if ($res) $score = $res['buts_equipe_a'] . ' - ' . $res['buts_equipe_b'];
                        }
                    ?>
                    <a href="/page-matchdetail?id=<?= $match['id'] ?>" class="match-card glass-panel block p-5 rounded-2xl shadow-lg hover:scale-[1.02] transition-transform duration-200" data-statut="<?= $match['statut'] ?>">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-[10px] px-3 py-1 rounded-full font-bold uppercase <?= $badge['class'] ?>"><?= $badge['label'] ?></span>
                            <?php if ($score): ?><span class="text-lg font-black text-white"><?= $score ?></span><?php endif; ?>
                        </div>
                        <h2 class="text-base font-bold text-white mb-4">Équipe A vs Équipe B</h2>
                        <div class="space-y-2 text-sm text-slate-200 mb-4">
                            <div class="flex items-center gap-2"><i class="fas fa-calendar w-4 text-blue-400"></i><?= date('d/m/Y', strtotime($match['date'])) ?></div>
                            <div class="flex items-center gap-2"><i class="fas fa-clock w-4 text-blue-400"></i><?= date('H:i', strtotime($match['date'])) ?></div>
                            <div class="flex items-center gap-2"><i class="fas fa-map-marker-alt w-4 text-blue-400"></i><?= htmlspecialchars($match['lieu']) ?></div>
                        </div>
                        <div class="border-t border-white/10 pt-4 text-xs font-bold text-slate-300 flex justify-between">
                            <span>Convoqués : <?= $nb_convoques ?></span>
                            <span class="text-blue-400">Détails <i class="fas fa-chevron-right text-[10px]"></i></span>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        function filtrer(statut) {
            document.querySelectorAll('.filtre-btn').forEach(btn => {
                btn.className = "filtre-btn px-5 py-2 rounded-xl bg-white/20 hover:bg-white/40 text-white font-semibold text-sm shadow-sm transition-all";
            });
            document.getElementById('btn-' + statut).className = "filtre-btn px-5 py-2 rounded-xl bg-blue-600 text-white font-semibold text-sm shadow-md transition-all";
            
            document.querySelectorAll('.match-card').forEach(card => {
                if (statut === 'tous') card.style.display = '';
                else if (statut === 'planifie') card.style.display = (card.dataset.statut === 'planifie' || card.dataset.statut === 'publie') ? '' : 'none';
                else if (statut === 'termine') card.style.display = (card.dataset.statut === 'termine') ? '' : 'none';
            });
        }
    </script>
</body>
</html>