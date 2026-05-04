<?php
// $colonnes est injecté par TacheController::kanban()
// Ne pas redéfinir $colonnes ici — les données viennent du controller
$pageTitle = "Kanban — Tâches";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Kanban Tâches — Club Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
<div class="flex h-screen">

    <!-- SIDEBAR -->
    <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

    <!-- CONTENU -->
    <main class="flex-1 overflow-y-auto">

        <?php include_once __DIR__ . '/../../partials/header.php'; ?>

        <div class="p-6">

            <!-- EN-TÊTE -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Kanban des tâches</h1>
                    <p class="text-sm text-gray-500">Vue visuelle par statut</p>
                </div>
                <div class="flex gap-2">
                    <a href="/tache-index"
                       class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 text-sm">
                        ← Liste
                    </a>
                    <?php if (in_array($_SESSION['user']['role'], ['president','censeur','organisateur'])): ?>
                        <a href="/tache-create"
                           class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-sm font-medium">
                            + Nouvelle tâche
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- KANBAN BOARD -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">

                <!-- COLONNE : À FAIRE -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="bg-gray-100 border-b border-gray-200 px-4 py-3 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-gray-400 inline-block"></span>
                            <span class="font-semibold text-gray-700 text-sm">À faire</span>
                        </div>
                        <span class="bg-gray-200 text-gray-600 text-xs px-2 py-0.5 rounded-full font-medium">
                            <?= count($colonnes['a_faire']) ?>
                        </span>
                    </div>
                    <div class="p-3 space-y-3 min-h-[200px]">
                        <?php foreach ($colonnes['a_faire'] as $t): ?>
                            <?= carteKanban($t) ?>
                        <?php endforeach; ?>
                        <?php if (empty($colonnes['a_faire'])): ?>
                            <p class="text-center text-gray-300 text-xs py-6">Aucune tâche</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- COLONNE : EN COURS -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="bg-blue-50 border-b border-blue-100 px-4 py-3 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span>
                            <span class="font-semibold text-blue-700 text-sm">En cours</span>
                        </div>
                        <span class="bg-blue-100 text-blue-600 text-xs px-2 py-0.5 rounded-full font-medium">
                            <?= count($colonnes['en_cours']) ?>
                        </span>
                    </div>
                    <div class="p-3 space-y-3 min-h-[200px]">
                        <?php foreach ($colonnes['en_cours'] as $t): ?>
                            <?= carteKanban($t) ?>
                        <?php endforeach; ?>
                        <?php if (empty($colonnes['en_cours'])): ?>
                            <p class="text-center text-gray-300 text-xs py-6">Aucune tâche</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- COLONNE : TERMINÉ -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="bg-green-50 border-b border-green-100 px-4 py-3 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span>
                            <span class="font-semibold text-green-700 text-sm">Terminé</span>
                        </div>
                        <span class="bg-green-100 text-green-600 text-xs px-2 py-0.5 rounded-full font-medium">
                            <?= count($colonnes['termine']) ?>
                        </span>
                    </div>
                    <div class="p-3 space-y-3 min-h-[200px]">
                        <?php foreach ($colonnes['termine'] as $t): ?>
                            <?= carteKanban($t) ?>
                        <?php endforeach; ?>
                        <?php if (empty($colonnes['termine'])): ?>
                            <p class="text-center text-gray-300 text-xs py-6">Aucune tâche</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- COLONNE : EN RETARD -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="bg-red-50 border-b border-red-200 px-4 py-3 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-red-500 inline-block animate-pulse"></span>
                            <span class="font-semibold text-red-700 text-sm">En retard</span>
                        </div>
                        <span class="bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded-full font-medium">
                            <?= count($colonnes['en_retard']) ?>
                        </span>
                    </div>
                    <div class="p-3 space-y-3 min-h-[200px]">
                        <?php foreach ($colonnes['en_retard'] as $t): ?>
                            <?= carteKanban($t) ?>
                        <?php endforeach; ?>
                        <?php if (empty($colonnes['en_retard'])): ?>
                            <p class="text-center text-gray-300 text-xs py-6">Aucun retard 🎉</p>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </main>
</div>
</body>
</html>

<?php
function carteKanban(array $t): string
{
    $prioriteCouleur = match ($t['priorite']) {
        'haute'   => 'border-t-red-400',
        'moyenne' => 'border-t-yellow-400',
        'faible'  => 'border-t-gray-200',
        default   => 'border-t-gray-200',
    };

    $deadlineStr = date('d/m à H:i', strtotime($t['deadline']));
    $recurrente  = $t['recurrente'] ? '<span class="text-purple-400 text-xs">🔁</span>' : '';

    $initiales = strtoupper(
        mb_substr($t['joueur_prenom'], 0, 1) .
        mb_substr($t['joueur_nom'],   0, 1)
    );

    return '
    <div class="border border-gray-100 border-t-4 ' . $prioriteCouleur . ' rounded-lg p-3 hover:shadow-md transition cursor-pointer"
         onclick="window.location=\'/tache-detail?id=' . $t['id'] . '\'">

        <div class="flex items-center justify-between mb-2">
            <span class="text-xs px-2 py-0.5 rounded-full text-white font-medium"
                  style="background-color: ' . $t['categorie_couleur'] . '">
                ' . $t['categorie_icone'] . ' ' . htmlspecialchars($t['categorie_nom']) . '
            </span>
            ' . $recurrente . '
        </div>

        <p class="text-sm font-semibold text-gray-800 leading-snug mb-3">
            ' . htmlspecialchars($t['titre']) . '
        </p>

        <div class="flex items-center justify-between">
            <div class="flex items-center gap-1.5">
                <div class="w-6 h-6 rounded-full bg-green-700 text-white text-xs flex items-center justify-center font-bold">
                    ' . $initiales . '
                </div>
                <span class="text-xs text-gray-500">' . htmlspecialchars($t['joueur_prenom']) . '</span>
            </div>
            <span class="text-xs text-gray-400">📅 ' . $deadlineStr . '</span>
        </div>
    </div>';
}
?>