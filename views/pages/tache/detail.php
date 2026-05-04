<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détail tâche — Club Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
<div class="flex h-screen">

    <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

    <main class="flex-1 overflow-y-auto">

        <?php include_once __DIR__ . '/../../partials/header.php'; ?>

        <div class="p-6 max-w-3xl mx-auto">

            <?php
            $badgeStatut = match ($tache['statut']) {
                'a_faire'   => '<span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm font-medium">📋 À faire</span>',
                'en_cours'  => '<span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-medium">⏳ En cours</span>',
                'termine'   => '<span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium">✅ Terminé</span>',
                'en_retard' => '<span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-medium animate-pulse">🚨 En retard</span>',
                default     => '',
            };
            $estAssigne   = $_SESSION['user']['id'] == $tache['assigne_a'];
            $estBureau    = in_array($_SESSION['user']['role'], ['president', 'censeur', 'organisateur']);
            $peutCloturer = $estAssigne && $tache['statut'] !== 'termine';
            ?>

            <!-- FLASH -->
            <?php if (!empty($_GET['msg'])): ?>
                <?php $msgs = ['tache_modifiee' => ['Tâche modifiée.', 'green'], 'erreur' => ['Erreur.', 'red']];
                $m = $msgs[$_GET['msg']] ?? null; ?>
                <?php if ($m): ?>
                    <div class="mb-4 px-4 py-3 rounded-lg bg-<?= $m[1] ?>-50 border border-<?= $m[1] ?>-200 text-<?= $m[1] ?>-700 text-sm"><?= $m[0] ?></div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- RETOUR -->
            <div class="mb-4">
                <a href="/tache-index" class="text-gray-400 hover:text-gray-600 text-sm">← Toutes les tâches</a>
            </div>

            <!-- ALERTE RETARD -->
            <?php if ($tache['statut'] === 'en_retard'): ?>
                <div class="mb-4 px-4 py-3 rounded-xl bg-red-50 border border-red-300 text-red-700 flex items-center gap-3">
                    <span class="text-2xl">🚨</span>
                    <div>
                        <p class="font-semibold">Tâche en retard !</p>
                        <p class="text-sm">Deadline dépassée le <?= date('d/m/Y à H:i', strtotime($tache['deadline'])) ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

                <!-- COLONNE PRINCIPALE -->
                <div class="lg:col-span-2 space-y-5">

                    <!-- FICHE -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="h-2" style="background-color: <?= $tache['categorie_couleur'] ?>"></div>
                        <div class="p-5">
                            <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
                                <div>
                                    <span class="text-xs px-2 py-0.5 rounded-full text-white font-medium mb-2 inline-block"
                                          style="background-color: <?= $tache['categorie_couleur'] ?>">
                                        <?= $tache['categorie_icone'] ?> <?= htmlspecialchars($tache['categorie_nom']) ?>
                                    </span>
                                    <h1 class="text-xl font-bold text-gray-800">
                                        <?= htmlspecialchars($tache['titre']) ?>
                                        <?php if ($tache['recurrente']): ?>
                                            <span class="text-purple-400 text-sm font-normal ml-1">🔁</span>
                                        <?php endif; ?>
                                    </h1>
                                </div>
                                <?= $badgeStatut ?>
                            </div>

                            <?php if (!empty($tache['description'])): ?>
                                <p class="text-sm text-gray-600 leading-relaxed mb-4">
                                    <?= nl2br(htmlspecialchars($tache['description'])) ?>
                                </p>
                            <?php endif; ?>

                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-xs text-gray-400 mb-1">Assignée à</p>
                                    <p class="font-semibold text-gray-700"><?= htmlspecialchars($tache['joueur_prenom'] . ' ' . $tache['joueur_nom']) ?></p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-xs text-gray-400 mb-1">Assignée par</p>
                                    <p class="font-semibold text-gray-700"><?= htmlspecialchars($tache['bureau_prenom'] . ' ' . $tache['bureau_nom']) ?></p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-xs text-gray-400 mb-1">Deadline</p>
                                    <p class="font-semibold text-gray-700"><?= date('d/m/Y à H:i', strtotime($tache['deadline'])) ?></p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-xs text-gray-400 mb-1">Priorité</p>
                                    <p class="font-semibold text-gray-700"><?= ucfirst($tache['priorite']) ?></p>
                                </div>
                                <?php if ($tache['statut'] === 'termine' && $tache['date_cloture']): ?>
                                    <div class="bg-green-50 rounded-lg p-3 col-span-2">
                                        <p class="text-xs text-green-500 mb-1">✅ Clôturée le</p>
                                        <p class="font-semibold text-green-700"><?= date('d/m/Y à H:i', strtotime($tache['date_cloture'])) ?></p>
                                        <?php if ($tache['commentaire_cloture']): ?>
                                            <p class="text-xs text-green-600 mt-1 italic">"<?= htmlspecialchars($tache['commentaire_cloture']) ?>"</p>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- COMMENTAIRES -->
                    <div class="bg-white rounded-xl shadow-sm p-5" id="commentaires">
                        <h2 class="font-semibold text-gray-700 mb-4">
                            💬 Commentaires
                            <span class="bg-gray-100 text-gray-500 text-xs px-2 py-0.5 rounded-full ml-1"><?= count($commentaires) ?></span>
                        </h2>

                        <?php if (empty($commentaires)): ?>
                            <p class="text-sm text-gray-400 text-center py-4">Aucun commentaire.</p>
                        <?php else: ?>
                            <div class="space-y-4 mb-5">
                                <?php foreach ($commentaires as $c):
                                    $estMoi = $c['auteur_id'] == $_SESSION['user']['id']; ?>
                                    <div class="flex gap-3 <?= $estMoi ? 'flex-row-reverse' : '' ?>">
                                        <div class="w-8 h-8 rounded-full bg-green-700 text-white text-xs flex items-center justify-center font-bold flex-shrink-0">
                                            <?= strtoupper(mb_substr($c['auteur_prenom'], 0, 1) . mb_substr($c['auteur_nom'], 0, 1)) ?>
                                        </div>
                                        <div class="max-w-[80%] flex flex-col gap-1 <?= $estMoi ? 'items-end' : 'items-start' ?>">
                                            <div class="<?= $estMoi ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-800' ?> rounded-2xl px-4 py-2.5 text-sm">
                                                <?= nl2br(htmlspecialchars($c['message'])) ?>
                                            </div>
                                            <p class="text-xs text-gray-400 px-1">
                                                <?= htmlspecialchars($c['auteur_prenom']) ?> · <?= date('d/m à H:i', strtotime($c['date_message'])) ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="/tache-commenter" class="flex gap-2 border-t pt-4">
                            <input type="hidden" name="tache_id" value="<?= $tache['id'] ?>">
                            <input type="text" name="message" required placeholder="Écrire un commentaire..."
                                   class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-sm font-medium">
                                Envoyer
                            </button>
                        </form>
                    </div>

                </div>

                <!-- COLONNE ACTIONS -->
                <div class="space-y-4">

                    <?php if ($peutCloturer): ?>
                        <div class="bg-white rounded-xl shadow-sm p-5">
                            <h3 class="font-semibold text-gray-700 mb-3 text-sm">Actions</h3>
                            <button onclick="document.getElementById('modalCloture').classList.remove('hidden')"
                                    class="w-full bg-green-600 text-white py-2.5 rounded-lg hover:bg-green-700 font-medium text-sm">
                                ✅ Marquer comme terminée
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php if ($estBureau): ?>
                        <div class="bg-white rounded-xl shadow-sm p-5">
                            <h3 class="font-semibold text-gray-700 mb-3 text-sm">Gestion bureau</h3>
                            <div class="space-y-2">
                                <a href="/tache-edit?id=<?= $tache['id'] ?>"
                                   class="block w-full text-center border border-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-50 text-sm">
                                    ✏️ Modifier
                                </a>
                                <?php if ($_SESSION['user']['role'] === 'president'): ?>
                                    <form method="POST" action="/tache-supprimer"
                                          onsubmit="return confirm('Supprimer cette tâche ?')">
                                        <input type="hidden" name="id" value="<?= $tache['id'] ?>">
                                        <button class="w-full text-red-500 hover:underline text-sm py-1">🗑 Supprimer</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($tache['recurrente']): ?>
                        <div class="bg-purple-50 border border-purple-100 rounded-xl p-4">
                            <p class="text-xs font-semibold text-purple-600 mb-1">🔁 Tâche récurrente</p>
                            <p class="text-xs text-purple-500">Se répète tous les <?= $tache['intervalle_jours'] ?> jours.</p>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </main>
</div>

<!-- MODAL CLÔTURE -->
<div id="modalCloture" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-1">Confirmer la clôture</h3>
        <p class="text-sm text-gray-500 mb-4"><?= htmlspecialchars($tache['titre']) ?></p>
        <form method="POST" action="/tache-cloturer">
            <input type="hidden" name="id" value="<?= $tache['id'] ?>">
            <label class="block text-sm font-medium text-gray-700 mb-2">Commentaire <span class="text-gray-400">(optionnel)</span></label>
            <textarea name="commentaire_cloture" rows="3" placeholder="Ex: Terrain préparé correctement."
                      class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 resize-none mb-4"></textarea>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 text-sm font-medium">Confirmer</button>
                <button type="button" onclick="document.getElementById('modalCloture').classList.add('hidden')"
                        class="flex-1 border border-gray-200 text-gray-600 py-2 rounded-lg hover:bg-gray-50 text-sm">Annuler</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>