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
                <!-- HEADER -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 flex items-center gap-3">
                            <i class="fas fa-users-cog text-green-600"></i>
                            Gestion des Équipes
                        </h1>
                    </div>
                    <a href="/admin-createequipe" class="flex items-center gap-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-6 py-3 rounded-2xl font-bold transition-all shadow-lg shadow-green-200">
                        <i class="fas fa-plus"></i>
                        Ajouter une équipe
                    </a>
                </div>

                <!-- Notification de succès pour modification -->
                <?php if (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
                    <div id="success-notification" class="mb-8 px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-700 font-semibold rounded-2xl shadow-md animate-bounce">
                        <i class="fas fa-check-circle mr-3 text-2xl"></i>
                        L'équipe a été modifiée avec succès !
                    </div>
                <?php endif; ?>
                
                <!-- Notification de succès pour création -->
                <?php if (isset($_GET['msg']) && $_GET['msg'] === 'success'): ?>
                    <div id="success-notification" class="mb-8 px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-700 font-semibold rounded-2xl shadow-md animate-bounce">
                        <i class="fas fa-check-circle mr-3 text-2xl"></i>
                        L'équipe a été créée avec succès !
                    </div>
                <?php endif; ?>
                
                <!-- Formulaire de recherche -->
                <div class="flex flex-col md:flex-row gap-4 items-start md:items-center mb-8">
                    <form class="flex-1 w-full md:max-w-md" onsubmit="return false;">
                        <div class="relative">
                            <input type="text" id="searchInput" name="search" placeholder="Rechercher par nom..." 
                                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                                   class="w-full px-5 pl-12 py-4 border-2 border-slate-200 rounded-2xl focus:ring-4 focus:ring-green-200 focus:border-green-500 outline-none transition-all font-semibold text-slate-700 bg-white shadow-sm">
                            <i class="fas fa-search absolute left-5 top-1/2 transform -translate-y-1/2 text-slate-400 text-xl"></i>
                        </div>
                    </form>
                </div>

                <!-- TABLE -->
                <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-gradient-to-r from-slate-50 to-slate-100 border-b-2 border-slate-100">
                            <tr>
                                <th class="p-6 text-sm font-extrabold text-slate-600 uppercase tracking-wide">Nom</th>
                                <th class="p-6 text-sm font-extrabold text-slate-600 uppercase tracking-wide">Couleur</th>
                                <th class="p-6 text-sm font-extrabold text-slate-600 uppercase tracking-wide">Catégorie</th>
                                <th class="p-6 text-sm font-extrabold text-slate-600 uppercase tracking-wide">Date Création</th>
                                <th class="p-6 text-sm font-extrabold text-slate-600 uppercase tracking-wide text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="equipesTableBody" class="divide-y divide-slate-100">
                            <?php if (!empty($equipes)):
                                foreach ($equipes as $e): ?>
                            <tr class="hover:bg-slate-50 transition-all duration-200">
                                <td class="p-6 font-semibold text-slate-800 text-lg"><?= htmlspecialchars($e['nom']) ?></td>
                                <td class="p-6">
                                    <span class="w-12 h-12 rounded-2xl flex items-center justify-center border border-slate-200" style="background-color: <?= $e['couleur'] ?>;">
                                        &nbsp;
                                    </span>
                                </td>
                                <td class="p-6 text-slate-700 font-semibold"><?= htmlspecialchars($e['categorie']) ?></td>
                                <td class="p-6 text-slate-600 font-semibold"><?= date('d/m/Y', strtotime($e['date_creation'])) ?></td>
                                
                                <td class="p-6 flex justify-center gap-3">
                                    <a href="/admin-team-show?id=<?= $e['id'] ?>" class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-blue-100 to-indigo-100 text-blue-700 hover:from-blue-200 hover:to-indigo-200 rounded-2xl transition-all" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="/admin-teamedit?id=<?= $e['id'] ?>" class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-yellow-100 to-orange-100 text-yellow-700 hover:from-yellow-200 hover:to-orange-200 rounded-2xl transition-all" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" onclick="openDeleteModal(<?= $e['id'] ?>)" class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-red-100 to-pink-100 text-red-700 hover:from-red-200 hover:to-pink-200 rounded-2xl transition-all" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach;
                            else: ?>
                            <tr><td colspan="5" class="p-16 text-center text-slate-400 font-semibold text-lg">
                                <i class="fas fa-folder-open text-6xl mb-4"></i>
                                <p>Aucune équipe trouvée.</p>
                            </td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 hidden p-4">
        <div class="bg-white rounded-3xl shadow-2xl p-8 max-w-md w-full mx-4 transform transition-all duration-300">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-gradient-to-br from-red-100 to-pink-100 mb-6">
                    <i class="fas fa-exclamation-triangle text-red-600 text-4xl"></i>
                </div>
                <h3 class="text-2xl font-extrabold text-slate-800 mb-3">Confirmation de suppression</h3>
                <p class="text-slate-500 mb-8 text-lg">Voulez-vous confirmer la suppression de cette équipe ?</p>
            </div>
            <div class="flex gap-4">
                <button onclick="closeDeleteModal()" class="flex-1 px-6 py-4 bg-gradient-to-br from-slate-200 to-slate-300 text-slate-800 rounded-2xl font-bold hover:from-slate-300 hover:to-slate-400 transition-all shadow-md">
                    Annuler
                </button>
                <a id="confirmDeleteBtn" href="#" class="flex-1 px-6 py-4 bg-gradient-to-br from-red-600 to-pink-700 text-white rounded-2xl font-bold hover:from-red-700 hover:to-pink-800 transition-all text-center shadow-lg shadow-red-200">
                    Supprimer
                </a>
            </div>
        </div>
    </div>

    <!-- Script pour la recherche en temps réel et le modal de suppression -->
    <script>
        // Variable pour stocker l'ID de l'équipe à supprimer
        let equipeIdToDelete = null;
        
        // Variable pour le debounce de la recherche
        let searchDebounceTimer;
        
        // Fonction pour formater la date
        function formatDate(dateString) {
            const date = new Date(dateString);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        }
        
        // Fonction pour générer le HTML d'une ligne de tableau
        function generateEquipeRow(equipe) {
            return `
                <tr class="hover:bg-slate-50 transition-all duration-200">
                    <td class="p-6 font-semibold text-slate-800 text-lg">${escapeHtml(equipe.nom)}</td>
                    <td class="p-6">
                        <span class="w-12 h-12 rounded-2xl flex items-center justify-center border border-slate-200" style="background-color: ${escapeHtml(equipe.couleur)};">
                            &nbsp;
                        </span>
                    </td>
                    <td class="p-6 text-slate-700 font-semibold">${escapeHtml(equipe.categorie)}</td>
                    <td class="p-6 text-slate-600 font-semibold">${formatDate(equipe.date_creation)}</td>
                    <td class="p-6 flex justify-center gap-3">
                        <a href="/admin-team-show?id=${equipe.id}" class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-blue-100 to-indigo-100 text-blue-700 hover:from-blue-200 hover:to-indigo-200 rounded-2xl transition-all" title="Voir">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="/admin-teamedit?id=${equipe.id}" class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-yellow-100 to-orange-100 text-yellow-700 hover:from-yellow-200 hover:to-orange-200 rounded-2xl transition-all" title="Modifier">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="#" onclick="openDeleteModal(${equipe.id})" class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-red-100 to-pink-100 text-red-700 hover:from-red-200 hover:to-pink-200 rounded-2xl transition-all" title="Supprimer">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
            `;
        }
        
        // Fonction pour échapper les caractères HTML (éviter les attaques XSS)
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Fonction pour mettre à jour le tableau avec les résultats de recherche
        async function updateTable(searchTerm) {
            try {
                // Envoie une requête AJAX à l'API de recherche
                const response = await fetch(`/admin-teamsearchajax?search=${encodeURIComponent(searchTerm)}`);
                const equipes = await response.json();
                
                // Récupère le corps du tableau
                const tbody = document.getElementById('equipesTableBody');
                
                // Génère le HTML pour chaque équipe
                if (equipes.length > 0) {
                    tbody.innerHTML = equipes.map(equipe => generateEquipeRow(equipe)).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="5" class="p-16 text-center text-slate-400 font-semibold text-lg"><i class="fas fa-folder-open text-6xl mb-4"></i><p>Aucune équipe trouvée.</p></td></tr>';
                }
            } catch (error) {
                console.error('Erreur lors de la recherche:', error);
            }
        }
        
        // Attend que le DOM soit chargé
        document.addEventListener('DOMContentLoaded', function() {
            // Récupère l'élément de notification
            const notification = document.getElementById('success-notification');
            
            // Si la notification existe
            if (notification) {
                // Attend 3 secondes (3000 millisecondes)
                setTimeout(() => {
                    // Ajoute une transition de fondu
                    notification.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    notification.style.opacity = '0';
                    notification.style.transform = 'translateY(-10px)';
                    
                    // Après la transition, supprime l'élément
                    setTimeout(() => {
                        notification.remove();
                    }, 500);
                }, 3000);
            }

            // Récupère l'élément de recherche
            const searchInput = document.getElementById('searchInput');
            
            // Ajoute un écouteur d'événement pour la saisie
            searchInput.addEventListener('input', function() {
                // Annule le timer précédent (debounce)
                clearTimeout(searchDebounceTimer);
                
                // Définit un nouveau timer pour attendre 300ms après la dernière saisie
                searchDebounceTimer = setTimeout(() => {
                    updateTable(this.value);
                }, 300);
            });
        });

        // Fonction pour ouvrir le modal
        function openDeleteModal(id) {
            equipeIdToDelete = id;
            // Met à jour le lien du bouton de confirmation
            document.getElementById('confirmDeleteBtn').href = '/admin-teamdelete?id=' + id;
            // Affiche le modal
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        // Fonction pour fermer le modal
        function closeDeleteModal() {
            equipeIdToDelete = null;
            // Cache le modal
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Ferme le modal si on clique en dehors
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    </script>
</body>
</html>
