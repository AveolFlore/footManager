<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../middleware/Role.php';

requireLogin();

use Controllers\MatchSeanceController;

$matchController = new MatchSeanceController();

// Récupérer tous les matchs et filtrer les entraînements
$allMatchs = $matchController->index();
$entrainements = array_filter($allMatchs, function($m) {
    return $m['type'] === 'entrainement';
});

$pageTitle = "Entraînements";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - FC Blue Lock</title>
    <link rel="icon" type="image/png" href="/assets/images/blue_lock_logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen font-sans">
    <?php include_once __DIR__ . '/../../partials/floating-nav.php'; ?>
    <div class="pt-20 min-h-screen">
        <main class="bg-slate-50 overflow-y-auto">
            <div class="p-6 md:p-8 lg:p-10 max-w-7xl mx-auto space-y-6">
                
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 border border-slate-200 rounded-xl shadow-sm">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-3">
                            <i class="fas fa-running text-slate-500"></i>
                            <span>Entraînements</span>
                        </h1>
                        <p class="text-sm text-slate-500 mt-1"><?= count($entrainements) ?> séance(s) programmée(s)</p>
                    </div>
                    
                    <?php if (in_array($_SESSION['user']['role'], ['president', 'entraineur'])): ?>
                        <a href="/page-matchcreate" 
                           class="w-full sm:w-auto text-center px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-sm font-medium rounded-lg shadow-sm transition-colors flex items-center justify-center gap-2">
                            <i class="fas fa-plus text-xs"></i>
                            <span>Planifier un entraînement</span>
                        </a>
                    <?php endif; ?>
                </div>

                <?php if (isset($_GET['msg'])): ?>
                    <div class="px-4 py-3 bg-blue-50 border border-blue-200 text-blue-800 text-sm font-medium rounded-lg shadow-sm flex items-center">
                        <i class="fas fa-info-circle mr-3 text-blue-500 text-base"></i>
                        <?= htmlspecialchars($_GET['msg']) ?>
                    </div>
                <?php endif; ?>

                <?php if (empty($entrainements)): ?>
                    <div class="bg-white border border-slate-200 rounded-xl p-12 text-center shadow-sm">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-slate-100 rounded-full mb-4 text-slate-400">
                            <i class="fas fa-futbol text-2xl"></i>
                        </div>
                        <p class="text-lg font-semibold text-slate-900">Aucun entraînement planifié</p>
                        <p class="text-sm text-slate-500 mt-1">Le staff technique n'a pas encore partagé le planning des prochaines sessions.</p>
                    </div>
                <?php else: ?>
                    <div id="liste-entrainements" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        
                        <?php foreach ($entrainements as $entrainement): ?>
                            <?php
                            $badge = match ($entrainement['statut']) {
                                'planifie', 'publie' => ['label' => 'À venir', 'class' => 'bg-blue-50 text-blue-700 border-blue-200'],
                                'termine' => ['label' => 'Terminé', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                                default => ['label' => ucfirst($entrainement['statut']), 'class' => 'bg-slate-100 text-slate-700 border-slate-200']
                            };
                            ?>

                            <a href="/page-matchdetail?id=<?= $entrainement['id'] ?>" 
                               class="group bg-white border border-slate-200 rounded-xl p-6 block hover:border-slate-300 hover:shadow-md transition-all duration-200">
                                
                                <div class="flex justify-between items-start mb-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-md border <?= $badge['class'] ?>">
                                        <?= $badge['label'] ?>
                                    </span>
                                </div>

                                <h2 class="text-lg font-bold text-slate-900 mb-6 group-hover:text-blue-600 transition-colors line-clamp-2">
                                    <?= htmlspecialchars($entrainement['description'] ?: 'Entraînement collectif') ?>
                                </h2>

                                <div class="space-y-3 text-sm text-slate-600 border-t border-slate-100 pt-4">
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-calendar text-slate-400 w-4 text-center"></i>
                                        <span><?= date('l d F', strtotime($entrainement['date'])) ?></span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-clock text-slate-400 w-4 text-center"></i>
                                        <span><?= date('H:i', strtotime($entrainement['date'])) ?></span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-map-marker-alt text-slate-400 w-4 text-center"></i>
                                        <span class="truncate"><?= htmlspecialchars($entrainement['lieu']) ?></span>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>