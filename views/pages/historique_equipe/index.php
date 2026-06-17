<?php
require_once __DIR__ . '/../../../middleware/Role.php';
requireLogin();

$pageTitle = "Gestion des Équipes";
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - FC Blue Lock</title>
    <link rel="icon" type="image/png" href="/assets/images/blue_lock_logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            /* Image corrigée en team.jpg */
            background: url('/assets/images/team.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }
    </style>
</head>

<body class="text-slate-800 min-h-screen font-sans antialiased">
    <?php include_once __DIR__ . '/../../partials/floating-nav.php'; ?>

    <div class="pt-20 min-h-screen">
        <main class="overflow-y-auto p-6 md:p-10">
            <div class="max-w-7xl mx-auto space-y-6">

                <div class="glass-panel p-6 rounded-2xl shadow-lg flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-3">
                            <i class="fas fa-users-cog text-blue-600"></i>
                            <span>Gestion des Équipes</span>
                        </h1>
                        <p class="text-sm text-slate-600 mt-1">Configuration des effectifs et des catégories du Blue Lock</p>
                    </div>
                    <a href="/admin-createequipe" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition shadow-md">
                        <i class="fas fa-plus mr-2"></i>Créer une Équipe
                    </a>
                </div>

                <?php if (isset($_GET['msg'])): ?>
                    <div id="success-notification" class="px-5 py-4 bg-white/80 border border-emerald-200 text-emerald-800 rounded-xl shadow-lg backdrop-blur-sm flex items-center">
                        <i class="fas fa-check-circle mr-3 text-emerald-500"></i>
                        <?= htmlspecialchars($_GET['msg'] === 'updated' ? "Mise à jour effectuée avec succès." : "Initialisation réussie.") ?>
                    </div>
                <?php endif; ?>

                <div class="w-full md:max-w-md">
                    <div class="relative glass-panel rounded-xl">
                        <i class="fas fa-search absolute left-4 top-3.5 text-slate-400"></i>
                        <input type="text" id="searchInput" placeholder="Rechercher une équipe..."
                            class="w-full pl-12 pr-4 py-3 bg-transparent border-none focus:ring-0 outline-none text-sm placeholder-slate-500 text-slate-900">
                    </div>
                </div>

                <div class="glass-panel rounded-2xl shadow-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <!-- <?php
                        // TEST RAPIDE : Si ceci n'affiche rien, votre variable $equipes est vide.
                        echo "Nombre d'équipes trouvées : " . count($equipes);
                        ?> -->
                        <table class="w-full text-left">
                            <thead class="bg-slate-900/5 text-slate-700 text-xs uppercase tracking-wider">
                                <tr>
                                    <th class="p-5">Nom</th>
                                    <th class="p-5">Couleur</th>
                                    <th class="p-5">Catégorie</th>
                                    <th class="p-5">Création</th>
                                    <th class="p-5 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="equipesTableBody" class="divide-y divide-slate-200/50">
                                <?php
                                // On vérifie que $equipes contient bien quelque chose
                                if (!empty($equipes)):
                                    // Assurez-vous que c'est bien $equipes ici
                                    foreach ($equipes as $e): ?>
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="p-5 font-semibold text-slate-900"><?= htmlspecialchars($e['nom']) ?></td>
                                            <td class="p-5">
                                                <span class="inline-block w-4 h-4 rounded-full" style="background-color: <?= htmlspecialchars($e['couleur']) ?>;"></span>
                                                <span class="ml-2 text-xs font-mono"><?= htmlspecialchars($e['couleur']) ?></span>
                                            </td>
                                            <td class="p-5"><?= htmlspecialchars($e['categorie']) ?></td>
                                            <td class="p-5"><?= date('d/m/Y', strtotime($e['date_creation'])) ?></td>
                                            <td class="p-5 text-center">
                                                <a href="/admin-team-show?id=<?= $e['id'] ?>" class="text-slate-600 hover:text-blue-600 mx-1"><i class="fas fa-eye"></i></a>
                                                <a href="/admin-teamedit?id=<?= $e['id'] ?>" class="text-slate-600 hover:text-amber-600 mx-1"><i class="fas fa-edit"></i></a>
                                                <button onclick="openDeleteModal(<?= $e['id'] ?>)" class="text-slate-600 hover:text-rose-600 mx-1"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    <?php endforeach;
                                else: ?>
                                    <tr>
                                        <td colspan="5" class="p-10 text-center text-slate-500">Aucune équipe trouvée.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div id="deleteModal" class="fixed inset-0 bg-slate-900/20 backdrop-blur-sm flex items-center justify-center z-50 hidden p-4">
        <div class="glass-panel p-8 rounded-2xl max-w-sm w-full shadow-2xl text-center">
            <div class="w-16 h-16 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-trash-alt text-xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">Supprimer l'équipe ?</h3>
            <p class="text-sm text-slate-600 mb-6">Cette action est irréversible.</p>
            <div class="flex gap-3">
                <button onclick="closeDeleteModal()" class="flex-1 py-2.5 bg-white border border-slate-200 rounded-xl hover:bg-slate-50">Annuler</button>
                <a id="confirmDeleteBtn" href="#" class="flex-1 py-2.5 bg-rose-600 text-white rounded-xl hover:bg-rose-700 shadow-md">Confirmer</a>
            </div>
        </div>
    </div>

    <script>
        function openDeleteModal(id) {
            document.getElementById('confirmDeleteBtn').href = '/admin-teamdelete?id=' + id;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Gestion de la recherche (votre logique existante)
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', function() {
            // Logique de rafraîchissement tableau...
        });
    </script>
</body>

</html>