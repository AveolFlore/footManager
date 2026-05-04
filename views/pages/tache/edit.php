<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier tâche — Club Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
<div class="flex h-screen">

    <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

    <main class="flex-1 overflow-y-auto">

        <?php include_once __DIR__ . '/../../partials/header.php'; ?>

        <div class="p-6 max-w-2xl mx-auto">

            <div class="flex items-center gap-3 mb-6">
                <a href="/tache-detail?id=<?= $tache['id'] ?>" class="text-gray-400 hover:text-gray-600 text-sm">← Retour</a>
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Modifier la tâche</h1>
                    <p class="text-sm text-gray-500 truncate max-w-sm"><?= htmlspecialchars($tache['titre']) ?></p>
                </div>
            </div>

            <form method="POST" action="/tache-update" class="space-y-5">
                <input type="hidden" name="id" value="<?= $tache['id'] ?>">

                <!-- INFORMATIONS -->
                <div class="bg-white rounded-xl shadow-sm p-5">
                    <h2 class="text-sm font-semibold text-gray-700 mb-4 pb-2 border-b">Informations</h2>
                    <div class="space-y-4">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Titre <span class="text-red-500">*</span></label>
                            <input type="text" name="titre" required value="<?= htmlspecialchars($tache['titre']) ?>"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" rows="3"
                                      class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 resize-none"><?= htmlspecialchars($tache['description'] ?? '') ?></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie <span class="text-red-500">*</span></label>
                                <select name="categorie_id" required
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $tache['categorie_id'] ? 'selected' : '' ?>>
                                            <?= $cat['icone'] ?> <?= htmlspecialchars($cat['nom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Priorité</label>
                                <select name="priorite"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <option value="haute"   <?= $tache['priorite'] === 'haute'   ? 'selected' : '' ?>>🔴 Haute</option>
                                    <option value="moyenne" <?= $tache['priorite'] === 'moyenne' ? 'selected' : '' ?>>🟡 Moyenne</option>
                                    <option value="faible"  <?= $tache['priorite'] === 'faible'  ? 'selected' : '' ?>>⚪ Faible</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- ASSIGNATION -->
                <div class="bg-white rounded-xl shadow-sm p-5">
                    <h2 class="text-sm font-semibold text-gray-700 mb-4 pb-2 border-b">Assignation</h2>
                    <div class="space-y-4">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Joueur assigné <span class="text-red-500">*</span></label>
                            <select name="assigne_a" required
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                                <?php foreach ($joueurs as $j): ?>
                                    <option value="<?= $j['id'] ?>" <?= $j['id'] == $tache['assigne_a'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($j['prenom'] . ' ' . $j['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deadline <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="deadline" required
                                   value="<?= date('Y-m-d\TH:i', strtotime($tache['deadline'])) ?>"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>

                    </div>
                </div>

                <!-- RÉCURRENCE -->
                <div class="bg-white rounded-xl shadow-sm p-5">
                    <h2 class="text-sm font-semibold text-gray-700 mb-4 pb-2 border-b">Récurrence</h2>
                    <div class="flex items-center gap-3 mb-4">
                        <input type="checkbox" name="recurrente" id="recurrente" value="1"
                               <?= $tache['recurrente'] ? 'checked' : '' ?>
                               class="w-4 h-4 accent-green-600"
                               onchange="document.getElementById('blocRecurrence').classList.toggle('hidden', !this.checked)">
                        <label for="recurrente" class="text-sm text-gray-700 cursor-pointer">Tâche récurrente</label>
                    </div>
                    <div id="blocRecurrence" class="<?= $tache['recurrente'] ? '' : 'hidden' ?>">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Répéter tous les combien de jours ?</label>
                        <input type="number" name="intervalle_jours" min="1" max="365"
                               value="<?= $tache['intervalle_jours'] ?? '' ?>"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                </div>

                <!-- BOUTONS -->
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-green-600 text-white py-3 rounded-xl hover:bg-green-700 font-medium">
                        Enregistrer
                    </button>
                    <a href="/tache-detail?id=<?= $tache['id'] ?>"
                       class="flex-1 text-center border border-gray-200 text-gray-600 py-3 rounded-xl hover:bg-gray-50 font-medium">
                        Annuler
                    </a>
                </div>

            </form>
        </div>
    </main>
</div>
</body>
</html>