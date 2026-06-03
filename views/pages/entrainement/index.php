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
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 flex items-center gap-3">
                            <i class="fas fa-running text-green-600"></i>
                            Entraînements
                        </h1>
                        <p class="text-slate-600 mt-2 text-lg"><?= count($entrainements) ?> séances</p>
                    </div>
                    <?php if (in_array($_SESSION['user']['role'], ['president', 'entraineur'])): ?>
                        <a href="/page-matchcreate"
                            class="flex items-center gap-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-6 py-3 rounded-2xl font-bold transition-all shadow-lg shadow-green-200">
                            <i class="fas fa-plus"></i>
                            Planifier un entraînement
                        </a>
                    <?php endif; ?>
                </div>

                <?php if (isset($_GET['msg'])): ?>
                    <div class="mb-8 px-6 py-4 rounded-2xl bg-gradient-to-r from-green-50 to-emerald-50 text-green-700 font-semibold border border-green-200">
                        <i class="fas fa-check-circle mr-3"></i>
                        <?= htmlspecialchars($_GET['msg']) ?>
                    </div>
                <?php endif; ?>

                <?php if (empty($entrainements)): ?>
                    <div class="text-center py-20 bg-white rounded-3xl shadow-xl border border-slate-100">
                        <i class="fas fa-futbol text-slate-300 text-8xl mb-6"></i>
                        <p class="text-xl text-slate-500 italic">Aucun entraînement planifié pour le moment</p>
                    </div>
                <?php else: ?>
                    <div id="liste-entrainements" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                        <?php foreach ($entrainements as $entrainement): ?>

                            <?php
                            $badge = match ($entrainement['statut']) {
                                'planifie' => ['label' => 'À venir',  'class' => 'bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-700 border border-blue-200'],
                                'publie'   => ['label' => 'À venir',  'class' => 'bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-700 border border-blue-200'],
                                'termine'  => ['label' => 'Terminé',  'class' => 'bg-gradient-to-r from-green-100 to-emerald-100 text-green-700 border border-green-200'],
                                default    => ['label' => $entrainement['statut'], 'class' => 'bg-slate-100 text-slate-700 border border-slate-200']
                            };
                            ?>

                            <a href="/page-matchdetail?id=<?= $entrainement['id'] ?>"
                                class="block bg-white rounded-3xl border border-slate-100 p-8 hover:shadow-2xl transition-all duration-300 shadow-xl">

                                <div class="flex justify-between items-start mb-6">
                                    <span class="text-sm px-4 py-2 rounded-2xl font-bold border <?= $badge['class'] ?>">
                                        <i class="fas fa-clock mr-2"></i>
                                        <?= $badge['label'] ?>
                                    </span>
                                </div>

                                <h2 class="text-xl font-extrabold text-slate-800 mb-6">
                                    <?= htmlspecialchars($entrainement['description'] ?: 'Entraînement') ?>
                                </h2>

                                <div class="space-y-4 mb-6">
                                    <div class="flex items-center gap-3 text-slate-700">
                                        <i class="fas fa-calendar text-blue-600 text-2xl"></i>
                                        <span class="font-semibold text-lg"><?= date('l d F', strtotime($entrainement['date'])) ?></span>
                                    </div>
                                    <div class="flex items-center gap-3 text-slate-700">
                                        <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                                        <span class="font-semibold text-lg"><?= date('H:i', strtotime($entrainement['date'])) ?></span>
                                    </div>
                                    <div class="flex items-center gap-3 text-slate-700">
                                        <i class="fas fa-map-marker-alt text-purple-600 text-2xl"></i>
                                        <span class="font-semibold text-lg"><?= htmlspecialchars($entrainement['lieu']) ?></span>
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
