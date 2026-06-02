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

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entraînements</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php include_once __DIR__ . '/../../partials/header.php'; ?>
    <div class="flex">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1 overflow-y-auto p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Entraînements</h1>
                    <p class="text-sm text-gray-500"><?= count($entrainements) ?> séances</p>
                </div>
                <?php if (in_array($_SESSION['user']['role'], ['president', 'entraineur'])): ?>
                    <a href="/page-matchcreate"
                        class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                        + Planifier un entraînement
                    </a>
                <?php endif; ?>
            </div>

            <?php if (isset($_GET['msg'])): ?>
                <div class="mb-4 px-4 py-3 rounded-lg bg-green-100 text-green-700 text-sm">
                    <?= htmlspecialchars($_GET['msg']) ?>
                </div>
            <?php endif; ?>

            <?php if (empty($entrainements)): ?>
                <div class="text-center py-12">
                    <div class="text-6xl mb-4">⚽</div>
                    <p class="text-gray-500">Aucun entraînement planifié pour le moment</p>
                </div>
            <?php else: ?>
                <div id="liste-entrainements" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

                    <?php foreach ($entrainements as $entrainement): ?>

                        <?php
                        $badge = match ($entrainement['statut']) {
                            'planifie' => ['label' => 'À venir',  'class' => 'bg-blue-100 text-blue-600'],
                            'publie'   => ['label' => 'À venir',  'class' => 'bg-blue-100 text-blue-600'],
                            'termine'  => ['label' => 'Terminé',  'class' => 'bg-green-100 text-green-600'],
                            default    => ['label' => $entrainement['statut'], 'class' => 'bg-gray-100 text-gray-600']
                        };
                        ?>

                        <a href="/page-matchdetail?id=<?= $entrainement['id'] ?>"
                            class="block bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition">

                            <div class="flex justify-between items-start mb-3">
                                <span class="text-xs px-3 py-1 rounded-full font-medium <?= $badge['class'] ?>">
                                    <?= $badge['label'] ?>
                                </span>
                            </div>

                            <h2 class="text-lg font-semibold text-gray-800 mb-3">
                                <?= htmlspecialchars($entrainement['description'] ?: 'Entraînement') ?>
                            </h2>

                            <div class="space-y-1 mb-4">
                                <div class="flex items-center gap-2 text-sm text-gray-500">
                                    <span>📅</span>
                                    <span><?= date('l d F', strtotime($entrainement['date'])) ?></span>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-gray-500">
                                    <span>🕐</span>
                                    <span><?= date('H:i', strtotime($entrainement['date'])) ?></span>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-gray-500">
                                    <span>📍</span>
                                    <span><?= htmlspecialchars($entrainement['lieu']) ?></span>
                                </div>
                            </div>

                        </a>

                    <?php endforeach; ?>

                </div>
            <?php endif; ?>

        </main>
    </div>
</body>

</html>