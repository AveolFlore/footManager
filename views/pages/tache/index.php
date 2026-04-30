<?php
require_once __DIR__ . '/../../../middleware/Role.php';

requireRole('president');

// DB + MODEL
require_once __DIR__ . '/../../../config/Database.php';
require_once __DIR__ . '/../../../models/Tache/Tache.php';

use Config\Database;
use Models\Tache\Tache;

// connexion
$database = new Database();
$pdo = $database->connect();

// model
$tacheModel = new Tache($pdo);

// récupérer les utilisateurs
$taches = $tacheModel->readAll();
// var_dump($taches);
// compter les demandes en attente
// $total_attente = $tacheModel->countPending();

// récupérer les demandes en attente
// $demandes = $tacheModel->getPendingUsers();
$pageTitle = "Tâches du club";

// Badges statut
function badgeStatut(string $statut): string
{
    return match ($statut) {
        'a_faire'   => '<span class="bg-gray-100 text-gray-700 px-2 py-1 rounded-full text-xs font-medium">À faire</span>',
        'en_cours'  => '<span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-medium">En cours</span>',
        'termine'   => '<span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-medium">Terminé</span>',
        'en_retard' => '<span class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs font-medium animate-pulse">En retard</span>',
        default     => '<span class="bg-gray-100 text-gray-500 px-2 py-1 rounded-full text-xs">' . $statut . '</span>',
    };
}

function badgePriorite(string $p): string
{
    return match ($p) {
        'haute'   => '<span class="bg-red-50 text-red-600 border border-red-200 px-2 py-0.5 rounded text-xs">🔴 Haute</span>',
        'moyenne' => '<span class="bg-yellow-50 text-yellow-600 border border-yellow-200 px-2 py-0.5 rounded text-xs">🟡 Moyenne</span>',
        'faible'  => '<span class="bg-gray-50 text-gray-500 border border-gray-200 px-2 py-0.5 rounded text-xs">⚪ Faible</span>',
        default   => '',
    };
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tâches — Club Manager</title>
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

            <!-- FLASH MESSAGE -->
            <?php if (!empty($_GET['msg'])): ?>
                <?php $msgs = [
                    'tache_creee'    => ['Tâche créée avec succès.', 'green'],
                    'tache_modifiee' => ['Tâche modifiée.', 'blue'],
                    'tache_supprimee'=> ['Tâche supprimée.', 'red'],
                    'introuvable'    => ['Tâche introuvable.', 'red'],
                    'erreur'         => ['Une erreur est survenue.', 'red'],
                ];
                $m = $msgs[$_GET['msg']] ?? null; ?>
                <?php if ($m): ?>
                    <div class="mb-4 px-4 py-3 rounded-lg bg-<?= $m[1] ?>-50 border border-<?= $m[1] ?>-200 text-<?= $m[1] ?>-700 text-sm">
                        <?= $m[0] ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- EN-TÊTE -->
            <div class="flex flex-wrap justify-between items-center mb-6 gap-3">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Tâches internes</h1>
                    <p class="text-sm text-gray-500">Gestion et suivi des tâches du club</p>
                </div>
                <div class="flex gap-2">
                    <a href="/page-kanban"
                       class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 text-sm">
                        🗂 Kanban
                    </a>
                    <?php if (in_array($_SESSION['user']['role'], ['president','censeur','organisateur'])): ?>
                        <a href="/page-create"
                           class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-sm font-medium">
                            + Nouvelle tâche
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- FILTRES -->
            <form method="GET" action="/tache-index1"
                  class="bg-white rounded-xl shadow-sm p-4 mb-6 flex flex-wrap gap-3 items-end">

                <div class="flex flex-col gap-1">
                    <label class="text-xs text-gray-500 font-medium">Statut</label>
                    <select name="statut" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Tous les statuts</option>
                        <option value="a_faire"   <?= ($_GET['statut'] ?? '') === 'a_faire'   ? 'selected' : '' ?>>À faire</option>
                        <option value="en_cours"  <?= ($_GET['statut'] ?? '') === 'en_cours'  ? 'selected' : '' ?>>En cours</option>
                        <option value="termine"   <?= ($_GET['statut'] ?? '') === 'termine'   ? 'selected' : '' ?>>Terminé</option>
                        <option value="en_retard" <?= ($_GET['statut'] ?? '') === 'en_retard' ? 'selected' : '' ?>>En retard</option>
                    </select>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-xs text-gray-500 font-medium">Catégorie</label>
                    <select name="categorie_id" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Toutes</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"
                                <?= ($_GET['categorie_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                <?= $cat['icone'] ?> <?= htmlspecialchars($cat['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-xs text-gray-500 font-medium">Priorité</label>
                    <select name="priorite" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Toutes</option>
                        <option value="haute"   <?= ($_GET['priorite'] ?? '') === 'haute'   ? 'selected' : '' ?>>Haute</option>
                        <option value="moyenne" <?= ($_GET['priorite'] ?? '') === 'moyenne' ? 'selected' : '' ?>>Moyenne</option>
                        <option value="faible"  <?= ($_GET['priorite'] ?? '') === 'faible'  ? 'selected' : '' ?>>Faible</option>
                    </select>
                </div>

                <button type="submit"
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-sm">
                    Filtrer
                </button>
                <a href="/tache-index" class="text-gray-400 text-sm hover:text-gray-600 py-2">
                    Réinitialiser
                </a>
            </form>

            <!-- COMPTEURS RAPIDES -->
            <?php
            $nbParStatut = ['a_faire' => 0, 'en_cours' => 0, 'termine' => 0, 'en_retard' => 0];
            foreach ($taches as $t) { $nbParStatut[$t['statut']] = ($nbParStatut[$t['statut']] ?? 0) + 1; }
            ?>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-gray-300">
                    <p class="text-2xl font-bold text-gray-700"><?= $nbParStatut['a_faire'] ?></p>
                    <p class="text-xs text-gray-500 mt-1">À faire</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-400">
                    <p class="text-2xl font-bold text-blue-600"><?= $nbParStatut['en_cours'] ?></p>
                    <p class="text-xs text-gray-500 mt-1">En cours</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-500">
                    <p class="text-2xl font-bold text-green-600"><?= $nbParStatut['termine'] ?></p>
                    <p class="text-xs text-gray-500 mt-1">Terminées</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-red-500">
                    <p class="text-2xl font-bold text-red-600"><?= $nbParStatut['en_retard'] ?></p>
                    <p class="text-xs text-gray-500 mt-1">En retard</p>
                </div>
            </div>

            <!-- TABLEAU DES TÂCHES -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">

                <div class="p-4 border-b flex justify-between items-center">
                    <h3 class="font-medium text-gray-700">
                        <?= count($taches) ?> tâche<?= count($taches) > 1 ? 's' : '' ?>
                    </h3>
                    <!-- Recherche live -->
                    <input type="text" id="recherche"
                           placeholder="Rechercher..."
                           class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 w-48">
                </div>

                <?php if (empty($taches)): ?>
                    <div class="p-12 text-center text-gray-400">
                        <p class="text-4xl mb-3">📋</p>
                        <p class="font-medium">Aucune tâche trouvée</p>
                        <p class="text-sm mt-1">Modifiez les filtres ou créez une nouvelle tâche.</p>
                    </div>
                <?php else: ?>
                    <table class="w-full text-left text-sm" id="tableTaches">
                        <thead class="bg-green-600 text-white">
                            <tr>
                                <th class="p-3">Tâche</th>
                                <th class="p-3 hidden md:table-cell">Catégorie</th>
                                <th class="p-3">Assignée à</th>
                                <th class="p-3 hidden lg:table-cell">Deadline</th>
                                <th class="p-3">Statut</th>
                                <th class="p-3 hidden md:table-cell">Priorité</th>
                                <th class="p-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($taches as $t): ?>
                                <tr class="hover:bg-gray-50 ligne-tache
                                    <?= $t['statut'] === 'en_retard' ? 'bg-red-50' : '' ?>">

                                    <td class="p-3">
                                        <p class="font-medium text-gray-800 truncate max-w-[180px]">
                                            <?= htmlspecialchars($t['titre']) ?>
                                        </p>
                                        <?php if ($t['recurrente']): ?>
                                            <span class="text-xs text-purple-500">🔁 Récurrente</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="p-3 hidden md:table-cell">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium text-white"
                                              style="background-color: <?= $t['categorie_couleur'] ?>">
                                            <?= $t['categorie_icone'] ?>
                                            <?= htmlspecialchars($t['categorie_nom']) ?>
                                        </span>
                                    </td>

                                    <td class="p-3">
                                        <p class="font-medium text-gray-700">
                                            <?= htmlspecialchars($t['joueur_prenom'] . ' ' . $t['joueur_nom']) ?>
                                        </p>
                                    </td>

                                    <td class="p-3 hidden lg:table-cell text-gray-500">
                                        <?= date('d/m/Y H:i', strtotime($t['deadline'])) ?>
                                    </td>

                                    <td class="p-3">
                                        <?= badgeStatut($t['statut']) ?>
                                    </td>

                                    <td class="p-3 hidden md:table-cell">
                                        <?= badgePriorite($t['priorite']) ?>
                                    </td>

                                    <td class="p-3">
                                        <div class="flex gap-2">
                                            <a href="/tache-detail?id=<?= $t['id'] ?>"
                                               class="text-green-600 hover:underline text-xs font-medium">
                                                Voir
                                            </a>
                                            <?php if (in_array($_SESSION['user']['role'], ['president','censeur','organisateur'])): ?>
                                                <a href="/tache-edit?id=<?= $t['id'] ?>"
                                                   class="text-blue-600 hover:underline text-xs">
                                                    Modifier
                                                </a>
                                            <?php endif; ?>
                                            <?php if ($_SESSION['user']['role'] === 'president'): ?>
                                                <form method="POST" action="/tache-supprimer"
                                                      onsubmit="return confirm('Supprimer cette tâche ?')">
                                                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                                    <button class="text-red-500 hover:underline text-xs">
                                                        Supprimer
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

        </div>
    </main>
</div>

<!-- RECHERCHE LIVE -->
<script>
document.getElementById('recherche').addEventListener('input', function () {
    const val = this.value.toLowerCase();
    document.querySelectorAll('.ligne-tache').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(val) ? '' : 'none';
    });
});
</script>

</body>
</html>