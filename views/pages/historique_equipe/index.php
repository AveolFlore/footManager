<?php
require_once __DIR__ . '/../../../middleware/Role.php';

requireLogin();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Team</title>
        <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <?php include_once __DIR__ . '/../../partials/header.php'; ?>
    <div class="flex">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>
        
        <main class="flex-1 p-4 md:p-6">
        <div class="flex h-screen bg-gray-100">
    <!-- Sidebar et Header inclus -->
    <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>
    <div class="flex-1 flex flex-col overflow-hidden">
        <?php include_once __DIR__ . '/../../partials/header.php'; ?>

        <main class="flex-1 p-6 overflow-y-auto">
            <!-- Notification de succès pour modification -->
            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
                <div id="success-notification" class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 font-medium rounded shadow-sm animate-bounce">
                    ✅ L'équipe a été modifiée avec succès !
                </div>
            <?php endif; ?>
            
            <!-- Notification de succès pour création -->
            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'success'): ?>
                <div id="success-notification" class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 font-medium rounded shadow-sm animate-bounce">
                    ✅ L'équipe a été créée avec succès !
                </div>
            <?php endif; ?>
            
            <!-- Script pour faire disparaître la notification après 3 secondes -->
            <script>
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
            </script>
            
            <!-- Modal de confirmation de suppression -->
            <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="bg-white rounded-xl shadow-2xl p-8 max-w-md w-full mx-4 transform transition-all duration-300">
                    <div class="text-center">
                        <!-- Icône d'avertissement -->
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Confirmation de suppression</h3>
                        <p class="text-gray-500 mb-6">Voulez-vous confirmer la suppression de cette équipe ?</p>
                    </div>
                    <div class="flex space-x-4">
                        <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition">
                            Annuler
                        </button>
                        <a id="confirmDeleteBtn" href="#" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition text-center">
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
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 font-medium text-gray-700">${escapeHtml(equipe.nom)}</td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded text-xs text-white" style="background-color: ${escapeHtml(equipe.couleur)};">
                                    &nbsp;
                                </span>
                            </td>
                            <td class="p-4 text-gray-600">${escapeHtml(equipe.categorie)}</td>
                            <td class="p-4 text-gray-500 text-sm">${formatDate(equipe.date_creation)}</td>
                            <td class="p-4 flex justify-center space-x-2">
                                <a href="/admin-team-show?id=${equipe.id}" class="text-blue-600 hover:bg-blue-50 p-2 rounded-full transition" title="Voir">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="/admin-teamedit?id=${equipe.id}" class="text-yellow-600 hover:bg-yellow-50 p-2 rounded-full transition" title="Modifier">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <a href="#" onclick="openDeleteModal(${equipe.id})" class="text-red-600 hover:bg-red-50 p-2 rounded-full transition" title="Supprimer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
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
                            tbody.innerHTML = '<tr><td colspan="5" class="p-10 text-center text-gray-400">Aucune équipe trouvée.</td></tr>';
                        }
                    } catch (error) {
                        console.error('Erreur lors de la recherche:', error);
                    }
                }
                
                // Attend que le DOM soit chargé
                document.addEventListener('DOMContentLoaded', function() {
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
            
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800 mb-4">Gestion des Équipes</h1>
                
                <!-- Formulaire de recherche -->
                <div class="flex flex-col md:flex-row gap-4 items-start md:items-center">
                    <form class="flex-1 w-full md:max-w-md" onsubmit="return false;">
                        <div class="relative">
                            <input type="text" id="searchInput" name="search" placeholder="Rechercher par nom..." 
                                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                                   class="w-full px-4 py-2.5 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                            <!-- Icône de recherche -->
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </form>
                    
                    <!-- Bouton Vert (Green-600) -->
                    <a href="/admin-createequipe" class="btn btn-success bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-lg shadow transition">
                        + Ajouter une équipe
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <table class="w-full text-left border-collapse">
<thead class="bg-gray-50 border-b border-gray-200">
    <tr>
        <th class="p-4 text-xs font-semibold text-gray-500 uppercase">Nom</th>
        <th class="p-4 text-xs font-semibold text-gray-500 uppercase">Couleur</th>
        <th class="p-4 text-xs font-semibold text-gray-500 uppercase">Catégorie</th>
        <th class="p-4 text-xs font-semibold text-gray-500 uppercase">Date Création</th>
        <th class="p-4 text-xs font-semibold text-gray-500 uppercase text-center">Actions</th>
    </tr>
</thead>
<tbody id="equipesTableBody" class="divide-y divide-gray-100">
    <?php if (!empty($equipes)):
        foreach ($equipes as $e): ?>
    <tr class="hover:bg-gray-50 transition">
        <td class="p-4 font-medium text-gray-700"><?= htmlspecialchars($e['nom']) ?></td>
        <td class="p-4">
            <span class="px-2 py-1 rounded text-xs text-white" style="background-color: <?= $e['couleur'] ?>;">
                &nbsp;
            </span>
        </td>
        <td class="p-4 text-gray-600"><?= htmlspecialchars($e['categorie']) ?></td>
        <td class="p-4 text-gray-500 text-sm"><?= date('d/m/Y', strtotime($e['date_creation'])) ?></td>
        
        <td class="p-4 flex justify-center space-x-2">
            <a href="/admin-details?id=<?= $e['id'] ?>" class="text-blue-600 hover:bg-blue-50 p-2 rounded-full transition" title="Voir">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </a>

            <a href="/admin-teamedit?id=<?= $e['id'] ?>" class="text-yellow-600 hover:bg-yellow-50 p-2 rounded-full transition" title="Modifier">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </a>

            <a href="#" 
               onclick="openDeleteModal(<?= $e['id'] ?>)" 
               class="text-red-600 hover:bg-red-50 p-2 rounded-full transition" title="Supprimer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </a>
        </td>
    </tr>
    <?php endforeach;
    else: ?>
    <tr><td colspan="5" class="p-10 text-center text-gray-400">Aucune équipe trouvée.</td></tr>
    <?php endif; ?>
</tbody>
                </table>
            </div>
        </main>
    </div>
</div>
    </main>
</div>  
</body>
</html>