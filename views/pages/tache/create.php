<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer une tâche — Club Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<div class="flex h-screen">
    <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>
    <main class="flex-1 overflow-y-auto">
        <?php include_once __DIR__ . '/../../partials/header.php'; ?>
        <div class="p-6 max-w-2xl mx-auto">
            <?php if (!empty($_GET['msg']) && $_GET['msg'] === 'champs_manquants'): ?>
                <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">⚠️ Tous les champs obligatoires doivent être remplis.</div>
            <?php endif; ?>
            <div class="flex items-center gap-3 mb-6">
                <a href="/tache-index" class="text-gray-400 hover:text-gray-600 text-sm">← Retour</a>
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Nouvelle tâche</h1>
                    <p class="text-sm text-gray-500">Assigner une tâche à un joueur du club</p>
                </div>
            </div>
            <form method="POST" action="/tache-store" class="space-y-5">
                <div class="bg-white rounded-xl shadow-sm p-5">
                    <h2 class="text-sm font-semibold text-gray-700 mb-4 pb-2 border-b">Informations générales</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Titre <span class="text-red-500">*</span></label>
                            <input type="text" name="titre" required placeholder="Ex: Laver les maillots après le match"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-gray-400">(optionnel)</span></label>
                            <textarea name="description" rows="3" placeholder="Détails, instructions..."
                                      class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 resize-none"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie <span class="text-red-500">*</span></label>
                                <select name="categorie_id" required class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <option value="">-- Choisir --</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>"><?= $cat['icone'] ?> <?= htmlspecialchars($cat['nom']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Priorité <span class="text-red-500">*</span></label>
                                <select name="priorite" required class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <option value="haute">🔴 Haute</option>
                                    <option value="moyenne" selected>🟡 Moyenne</option>
                                    <option value="faible">⚪ Faible</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5">
                    <h2 class="text-sm font-semibold text-gray-700 mb-4 pb-2 border-b">Assignation</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Joueur assigné <span class="text-red-500">*</span></label>
                            <select name="assigne_a" required class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="">-- Sélectionner un joueur --</option>
                                <?php foreach ($joueurs as $j): ?>
                                    <option value="<?= $j['id'] ?>"><?= htmlspecialchars($j['prenom'] . ' ' . $j['nom']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deadline <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="deadline" required min="<?= date('Y-m-d\TH:i') ?>"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lier à une séance <span class="text-gray-400">(optionnel)</span></label>
                            <select name="seance_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="">-- Aucune séance --</option>
                                <?php foreach ($seances as $s): ?>
                                    <option value="<?= $s['id'] ?>"><?= ucfirst($s['type']) ?> — <?= date('d/m/Y', strtotime($s['date'])) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5">
                    <h2 class="text-sm font-semibold text-gray-700 mb-4 pb-2 border-b">Récurrence</h2>
                    <div class="flex items-center gap-3 mb-4">
                        <input type="checkbox" name="recurrente" id="recurrente" value="1" class="w-4 h-4 accent-green-600"
                               onchange="document.getElementById('blocRecurrence').classList.toggle('hidden', !this.checked)">
                        <label for="recurrente" class="text-sm text-gray-700 cursor-pointer">Cette tâche est récurrente</label>
                    </div>
                    <div id="blocRecurrence" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Répéter tous les combien de jours ?</label>
                        <input type="number" name="intervalle_jours" min="1" max="365" placeholder="Ex: 7"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-green-600 text-white py-3 rounded-xl hover:bg-green-700 font-medium">Créer la tâche</button>
                    <a href="/tache-index" class="flex-1 text-center border border-gray-200 text-gray-600 py-3 rounded-xl hover:bg-gray-50 font-medium">Annuler</a>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>