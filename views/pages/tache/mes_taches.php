<?php
$pageTitle = "Mes tâches";
$joueurId  = $_SESSION['user']['id'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes tâches — Club Manager</title>
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

            <!-- FLASH -->
            <?php if (!empty($_GET['msg'])): ?>
                <?php $msgs = [
                    'tache_terminee' => ['✅ Tâche marquée comme terminée !', 'green'],
                    'erreur'         => ['❌ Une erreur est survenue.', 'red'],
                    'message_vide'   => ['Le commentaire ne peut pas être vide.', 'yellow'],
                ]; $m = $msgs[$_GET['msg']] ?? null; ?>
                <?php if ($m): ?>
                    <div class="mb-4 px-4 py-3 rounded-lg bg-<?= $m[1] ?>-50 border border-<?= $m[1] ?>-200 text-<?= $m[1] ?>-700 text-sm">
                        <?= $m[0] ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- EN-TÊTE -->
            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-gray-800">Mes tâches</h1>
                <p class="text-sm text-gray-500">Toutes les tâches qui te sont assignées</p>
            </div>

            <!-- ALERTE RETARD -->
            <?php
            $enRetard = array_filter($taches, fn($t) => $t['statut'] === 'en_retard');
            if (count($enRetard) > 0):
            ?>
                <div class="mb-6 px-4 py-3 rounded-xl bg-red-50 border border-red-300 text-red-700 flex items-center gap-3">
                    <span class="text-2xl">🚨</span>
                    <div>
                        <p class="font-semibold">
                            <?= count($enRetard) ?> tâche<?= count($enRetard) > 1 ? 's' : '' ?> en retard !
                        </p>
                        <p class="text-sm">Traite-les immédiatement pour éviter une sanction.</p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (empty($taches)): ?>
                <!-- AUCUNE TÂCHE -->
                <div class="bg-white rounded-xl shadow-sm p-12 text-center text-gray-400">
                    <p class="text-5xl mb-4">🎉</p>
                    <p class="font-semibold text-lg text-gray-600">Aucune tâche en cours !</p>
                    <p class="text-sm mt-1">Tu n'as pas de tâche assignée pour le moment.</p>
                </div>

            <?php else: ?>

                <!-- SECTION : EN RETARD -->
                <?php $retards = array_filter($taches, fn($t) => $t['statut'] === 'en_retard'); ?>
                <?php if (!empty($retards)): ?>
                    <h2 class="text-sm font-semibold text-red-600 uppercase tracking-wider mb-3">🚨 En retard</h2>
                    <div class="space-y-3 mb-8">
                        <?php foreach ($retards as $t): ?>
                            <?= carteTache($t) ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- SECTION : À FAIRE -->
                <?php $aFaire = array_filter($taches, fn($t) => $t['statut'] === 'a_faire'); ?>
                <?php if (!empty($aFaire)): ?>
                    <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">📋 À faire</h2>
                    <div class="space-y-3 mb-8">
                        <?php foreach ($aFaire as $t): ?>
                            <?= carteTache($t) ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- SECTION : EN COURS -->
                <?php $enCours = array_filter($taches, fn($t) => $t['statut'] === 'en_cours'); ?>
                <?php if (!empty($enCours)): ?>
                    <h2 class="text-sm font-semibold text-blue-600 uppercase tracking-wider mb-3">⏳ En cours</h2>
                    <div class="space-y-3 mb-8">
                        <?php foreach ($enCours as $t): ?>
                            <?= carteTache($t) ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>

<?php
function carteTache(array $t): string
{
    $couleurBord = match ($t['statut']) {
        'en_retard' => 'border-red-400 bg-red-50',
        'en_cours'  => 'border-blue-400 bg-blue-50',
        default     => 'border-gray-200 bg-white',
    };

    $deadlineStr = date('d/m/Y à H:i', strtotime($t['deadline']));

    $btnCloturer = '';
    if (in_array($t['statut'], ['a_faire', 'en_cours', 'en_retard'])) {
        $btnCloturer = '
        <button onclick="ouvrirModal(' . $t['id'] . ', \'' . htmlspecialchars(addslashes($t['titre']), ENT_QUOTES) . '\')"
                class="bg-green-600 text-white text-xs px-3 py-1.5 rounded-lg hover:bg-green-700 transition">
            ✅ Marquer terminée
        </button>';
    }

    $recurrente = $t['recurrente'] ? '<span class="text-xs text-purple-500 ml-2">🔁 Récurrente</span>' : '';

    return '
    <div class="rounded-xl border-l-4 ' . $couleurBord . ' shadow-sm p-4 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div class="flex-1">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-lg">' . htmlspecialchars($t['categorie_icone']) . '</span>
                <p class="font-semibold text-gray-800">' . htmlspecialchars($t['titre']) . '</p>
                ' . $recurrente . '
            </div>
            <p class="text-xs text-gray-500 mt-1">
                Deadline : <span class="font-medium text-gray-700">' . $deadlineStr . '</span>
            </p>
            <p class="text-xs text-gray-400 mt-0.5">
                Catégorie : ' . htmlspecialchars($t['categorie_nom']) . '
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="/tache-detail?id=' . $t['id'] . '"
               class="text-green-600 hover:underline text-xs font-medium border border-green-200 px-3 py-1.5 rounded-lg">
                Voir détail
            </a>
            ' . $btnCloturer . '
        </div>
    </div>';
}
?>

<!-- MODAL CLÔTURE -->
<div id="modalCloture" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-1">Marquer comme terminée</h3>
        <p class="text-sm text-gray-500 mb-4" id="modalTitre"></p>

        <form method="POST" action="/tache-cloturer">
            <input type="hidden" name="id" id="modalTacheId">

            <label class="block text-sm font-medium text-gray-700 mb-2">
                Commentaire de clôture <span class="text-gray-400">(optionnel)</span>
            </label>
            <textarea name="commentaire_cloture" rows="3"
                      placeholder="Ex: Tâche effectuée, maillots rangés proprement."
                      class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 resize-none"></textarea>

            <div class="flex gap-3 mt-4">
                <button type="submit"
                        class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 text-sm font-medium">
                    Confirmer
                </button>
                <button type="button" onclick="fermerModal()"
                        class="flex-1 border border-gray-200 text-gray-600 py-2 rounded-lg hover:bg-gray-50 text-sm">
                    Annuler
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function ouvrirModal(id, titre) {
    document.getElementById('modalTacheId').value = id;
    document.getElementById('modalTitre').textContent = titre;
    document.getElementById('modalCloture').classList.remove('hidden');
}
function fermerModal() {
    document.getElementById('modalCloture').classList.add('hidden');
}
</script>