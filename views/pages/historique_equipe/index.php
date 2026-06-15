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
</head>

<body class="bg-slate-100 text-slate-800 min-h-screen font-sans antialiased">
    <?php include_once __DIR__ . '/../../partials/header.php'; ?>
    
    <div class="flex min-h-screen">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1 overflow-y-auto bg-slate-50">
            <div class="p-6 md:p-8 lg:p-10 max-w-7xl mx-auto space-y-6">

                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 border border-slate-200 rounded-xl shadow-sm">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-3">
                            <i class="fas fa-users-cog text-slate-500"></i>
                            <span>Gestion des Équipes</span>
                        </h1>
                        <p class="text-sm text-slate-500 mt-1">Configuration des effectifs et des catégories du Blue Lock</p>
                    </div>
                    <a href="/admin-createequipe" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm">
                        <i class="fas fa-plus text-xs"></i>
                        Créer une Équipe
                    </a>
                </div>

                <?php if (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
                    <div id="success-notification" class="px-5 py-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm shadow-sm flex items-center">
                        <i class="fas fa-check-circle mr-3 text-lg text-emerald-500"></i>
                        Mise à jour effectuée : L'équipe a été modifiée avec succès.
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['msg']) && $_GET['msg'] === 'success'): ?>
                    <div id="success-notification" class="px-5 py-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm shadow-sm flex items-center">
                        <i class="fas fa-check-circle mr-3 text-lg text-emerald-500"></i>
                        Initialisation réussie : Nouvelle équipe enregistrée dans la base du Blue Lock.
                    </div>
                <?php endif; ?>

                <div class="w-full md:max-w-md">
                    <form onsubmit="return false;">
                        <div class="relative w-full">
                            <input type="text" id="searchInput" name="search" placeholder="Rechercher par nom d'équipe..."
                                value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                                class="w-full px-4 pl-10 py-2.5 bg-white border border-slate-300 text-slate-900 placeholder-slate-400 rounded-lg focus:ring-2 focus:ring-slate-900 focus:border-slate-900 outline-none transition-all text-sm shadow-sm">
                            <i class="fas fa-search absolute left-3.5 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                        </div>
                    </form>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-200 bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    <th class="p-4">Nom de l'Équipe</th>
                                    <th class="p-4">Code Couleur</th>
                                    <th class="p-4">Catégorie</th>
                                    <th class="p-4">Date de Création</th>
                                    <th class="p-4 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="equipesTableBody" class="divide-y divide-slate-100 text-sm text-slate-700">
                                <?php if (!empty($equipes)):
                                    foreach ($equipes as $e): ?>
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="p-4 font-semibold text-slate-900">
                                                <?= htmlspecialchars($e['nom']) ?>
                                            </td>
                                            <td class="p-4">
                                                <div class="flex items-center gap-2.5">
                                                    <span class="w-5 h-5 rounded border border-slate-200 shadow-sm inline-block" style="background-color: <?= $e['couleur'] ?>;"></span>
                                                    <span class="text-xs text-slate-500 font-mono uppercase"><?= htmlspecialchars($e['couleur']) ?></span>
                                                </div>
                                            </td>
                                            <td class="p-4">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                                    <?= htmlspecialchars($e['categorie']) ?>
                                                </span>
                                            </td>
                                            <td class="p-4 text-slate-500">
                                                <?= date('d/m/Y', strtotime($e['date_creation'])) ?>
                                            </td>
                                            <td class="p-4">
                                                <div class="flex justify-center gap-1.5">
                                                    <a href="/admin-team-show?id=<?= $e['id'] ?>" class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-white border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors shadow-sm" title="Voir les détails">
                                                        <i class="fas fa-eye text-xs"></i>
                                                    </a>
                                                    <a href="/admin-teamedit?id=<?= $e['id'] ?>" class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-white border border-slate-200 text-amber-600 hover:text-amber-700 hover:bg-amber-50 transition-colors shadow-sm" title="Modifier">
                                                        <i class="fas fa-edit text-xs"></i>
                                                    </a>
                                                    <button onclick="openDeleteModal(<?= $e['id'] ?>)" class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-white border border-slate-200 text-rose-600 hover:text-rose-700 hover:bg-rose-50 transition-colors shadow-sm" title="Supprimer">
                                                        <i class="fas fa-trash text-xs"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach;
                                else: ?>
                                    <tr>
                                        <td colspan="5" class="p-12 text-center text-slate-400">
                                            <div class="inline-flex items-center justify-center w-12 h-12 bg-slate-100 rounded-full mb-3 text-slate-400">
                                                <i class="fas fa-folder-open text-lg"></i>
                                            </div>
                                            <p class="font-medium text-slate-600">Aucune équipe trouvée</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div id="deleteModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center z-50 hidden p-4">
        <div class="bg-white border border-slate-200 rounded-xl p-6 max-w-md w-full mx-4 shadow-xl transition-all">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-rose-50 text-rose-600 mb-4">
                    <i class="fas fa-exclamation-triangle text-lg"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-1">Confirmer la suppression</h3>
                <p class="text-sm text-slate-500 mb-6 leading-relaxed">
                    Êtes-vous sûr de vouloir supprimer définitivement cette équipe de la base de données ? Cette action est irréversible.
                </p>
            </div>
            <div class="flex gap-3 text-sm font-medium">
                <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-lg transition-colors">
                    Annuler
                </button>
                <a id="confirmDeleteBtn" href="#" class="flex-1 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-center rounded-lg transition-colors shadow-sm">
                    Supprimer
                </a>
            </div>
        </div>
    </div>

    <script>
        let equipeIdToDelete = null;
        let searchDebounceTimer;

        function formatDate(dateString) {
            const date = new Date(dateString);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        }

        function generateEquipeRow(equipe) {
            return `
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="p-4 font-semibold text-slate-900">${escapeHtml(equipe.nom)}</td>
                    <td class="p-4">
                        <div class="flex items-center gap-2.5">
                            <span class="w-5 h-5 rounded border border-slate-200 shadow-sm inline-block" style="background-color: ${escapeHtml(equipe.couleur)};"></span>
                            <span class="text-xs text-slate-500 font-mono uppercase">${escapeHtml(equipe.couleur)}</span>
                        </div>
                    </td>
                    <td class="p-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">${escapeHtml(equipe.categorie)}</span>
                    </td>
                    <td class="p-4 text-slate-500">${formatDate(equipe.date_creation)}</td>
                    <td class="p-4">
                        <div class="flex justify-center gap-1.5">
                            <a href="/admin-team-show?id=${equipe.id}" class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-white border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors shadow-sm" title="Voir les détails">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            <a href="/admin-teamedit?id=${equipe.id}" class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-white border border-slate-200 text-amber-600 hover:text-amber-700 hover:bg-amber-50 transition-colors shadow-sm" title="Modifier">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <button onclick="openDeleteModal(${equipe.id})" class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-white border border-slate-200 text-rose-600 hover:text-rose-700 hover:bg-rose-50 transition-colors shadow-sm" title="Supprimer">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        async function updateTable(searchTerm) {
            try {
                const response = await fetch(`/admin-teamsearchajax?search=${encodeURIComponent(searchTerm)}`);
                const equipes = await response.json();
                const tbody = document.getElementById('equipesTableBody');

                if (equipes.length > 0) {
                    tbody.innerHTML = equipes.map(equipe => generateEquipeRow(equipe)).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="5" class="p-12 text-center text-slate-400"><div class="inline-flex items-center justify-center w-12 h-12 bg-slate-100 rounded-full mb-3 text-slate-400"><i class="fas fa-folder-open text-lg"></i></div><p class="font-medium text-slate-600">Aucune équipe trouvée</p></td></tr>';
                }
            } catch (error) {
                console.error('Erreur lors de la recherche:', error);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const notification = document.getElementById('success-notification');
            if (notification) {
                setTimeout(() => {
                    notification.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    notification.style.opacity = '0';
                    notification.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        notification.remove();
                    }, 500);
                }, 4000);
            }

            const searchInput = document.getElementById('searchInput');
            searchInput.addEventListener('input', function() {
                clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(() => {
                    updateTable(this.value);
                }, 300);
            });
        });

        function openDeleteModal(id) {
            equipeIdToDelete = id;
            document.getElementById('confirmDeleteBtn').href = '/admin-teamdelete?id=' + id;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            equipeIdToDelete = null;
            document.getElementById('deleteModal').classList.add('hidden');
        }

        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    </script>
</body>

</html>