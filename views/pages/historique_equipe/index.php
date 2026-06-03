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
    <style>
        /* Effet scanline / holographique subtil en arrière-plan global */
        .cyber-bg {
            background: radial-gradient(circle at 50% 50%, #0f172a 0%, #020617 100%);
            position: relative;
        }
        .cyber-bg::before {
            content: " ";
            display: block;
            position: fixed;
            top: 0; left: 0; bottom: 0; right: 0;
            background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.25) 50%), linear-gradient(90deg, rgba(255, 0, 0, 0.06), rgba(0, 255, 36, 0.02), rgba(0, 0, 255, 0.06));
            z-index: 99999;
            opacity: 0.4;
            pointer-events: none;
            background-size: 100% 4px, 6px 100%;
        }
    </style>
</head>
<body class="cyber-bg text-slate-100 min-h-screen font-sans antialiased">
    <?php include_once __DIR__ . '/../../partials/header.php'; ?>
    <div class="flex min-h-screen">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>
        
        <main class="flex-1 overflow-y-auto bg-slate-950/40 backdrop-blur-sm">
            <div class="p-6 md:p-8 lg:p-10 max-w-7xl mx-auto">
                
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4 border-b border-cyan-500/20 pb-6 relative">
                    <div class="absolute bottom-0 left-0 h-[2px] w-24 bg-cyan-500 shadow-[0_0_15px_#06b6d4]"></div>
                    <div>
                        <h1 class="text-3xl md:text-4xl font-black uppercase tracking-wider text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-200 to-cyan-400 flex items-center gap-4">
                            <i class="fas fa-users-cog text-cyan-500 filter drop-shadow-[0_0_8px_rgba(6,182,212,0.6)]"></i>
                            SYSTEM_LOG : ÉQUIPES
                        </h1>
                        <p class="text-xs text-cyan-500/60 font-mono mt-1 tracking-widest uppercase">Blue Lock Project - Terminal d'administration</p>
                    </div>
                    <a href="/admin-createequipe" class="group relative flex items-center gap-3 bg-cyan-950/60 border border-cyan-500 text-cyan-400 px-6 py-3 rounded-none font-black tracking-widest uppercase transition-all duration-300 hover:bg-cyan-500 hover:text-black shadow-[0_0_15px_rgba(6,182,212,0.15)] hover:shadow-[0_0_25px_rgba(6,182,212,0.4)]">
                        <span class="absolute top-0 left-0 w-2 h-2 border-t-2 border-l-2 border-cyan-400 group-hover:border-black"></span>
                        <span class="absolute bottom-0 right-0 w-2 h-2 border-b-2 border-r-2 border-cyan-400 group-hover:border-black"></span>
                        <i class="fas fa-plus text-sm"></i>
                        Créer une Équipe
                    </a>
                </div>

                <?php if (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
                    <div id="success-notification" class="mb-8 px-6 py-4 bg-emerald-950/40 border-l-4 border-emerald-500 border-t border-b border-r border-emerald-500/20 text-emerald-400 font-mono text-sm uppercase tracking-wider shadow-[0_0_15px_rgba(16,185,129,0.1)] flex items-center">
                        <i class="fas fa-check-circle mr-3 text-xl animate-pulse"></i>
                        Mise à jour système effectuée : L'équipe a été modifiée avec succès.
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['msg']) && $_GET['msg'] === 'success'): ?>
                    <div id="success-notification" class="mb-8 px-6 py-4 bg-emerald-950/40 border-l-4 border-emerald-500 border-t border-b border-r border-emerald-500/20 text-emerald-400 font-mono text-sm uppercase tracking-wider shadow-[0_0_15px_rgba(16,185,129,0.1)] flex items-center">
                        <i class="fas fa-check-circle mr-3 text-xl animate-pulse"></i>
                        Initialisation réussie : Nouvelle équipe enregistrée dans la base du Blue Lock.
                    </div>
                <?php endif; ?>
                
                <div class="flex flex-col md:flex-row gap-4 items-start md:items-center mb-8">
                    <form class="flex-1 w-full md:max-w-md" onsubmit="return false;">
                        <div class="relative group">
                            <input type="text" id="searchInput" name="search" placeholder="SCANNER PAR NOM D'ÉQUIPE..." 
                                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                                   class="w-full px-5 pl-12 py-3.5 bg-slate-900/80 border border-slate-800 text-cyan-400 placeholder-slate-600 rounded-none focus:ring-1 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition-all duration-300 font-mono text-sm tracking-wider shadow-inner">
                            <i class="fas fa-search absolute left-5 top-1/2 transform -translate-y-1/2 text-slate-600 group-focus-within:text-cyan-500 transition-colors duration-300"></i>
                            <span class="absolute bottom-0 left-0 w-0 h-[1px] bg-cyan-400 transition-all duration-300 group-focus-within:w-full"></span>
                        </div>
                    </form>
                </div>

                <div class="bg-slate-900/40 border border-slate-800/80 backdrop-blur-md shadow-2xl relative">
                    <div class="absolute -top-[1px] -left-[1px] w-3 h-3 border-t-2 border-l-2 border-cyan-500/40"></div>
                    <div class="absolute -top-[1px] -right-[1px] w-3 h-3 border-t-2 border-r-2 border-cyan-500/40"></div>
                    <div class="absolute -bottom-[1px] -left-[1px] w-3 h-3 border-b-2 border-l-2 border-cyan-500/40"></div>
                    <div class="absolute -bottom-[1px] -right-[1px] w-3 h-3 border-b-2 border-r-2 border-cyan-500/40"></div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-800 bg-slate-950/80 font-mono text-xs uppercase tracking-widest text-cyan-500/70">
                                    <th class="p-5 font-bold">Nom de l'Unité</th>
                                    <th class="p-5 font-bold">Code Couleur</th>
                                    <th class="p-5 font-bold">Catégorie</th>
                                    <th class="p-5 font-bold">Date Indexation</th>
                                    <th class="p-5 font-bold text-center">Actions de contrôle</th>
                                </tr>
                            </thead>
                            <tbody id="equipesTableBody" class="divide-y divide-slate-900 font-mono text-sm">
                                <?php if (!empty($equipes)):
                                    foreach ($equipes as $e): ?>
                                <tr class="hover:bg-cyan-950/20 transition-all duration-150 group">
                                    <td class="p-5 font-bold text-white uppercase tracking-wide group-hover:text-cyan-400 transition-colors">
                                        <?= htmlspecialchars($e['nom']) ?>
                                    </td>
                                    <td class="p-5">
                                        <div class="flex items-center gap-3">
                                            <span class="w-6 h-6 border border-white/20 shadow-[0_0_10px_rgba(255,255,255,0.1)] relative" style="background-color: <?= $e['couleur'] ?>;">
                                                <span class="absolute inset-0 bg-gradient-to-tr from-black/40 to-transparent"></span>
                                            </span>
                                            <span class="text-xs text-slate-500 uppercase"><?= htmlspecialchars($e['couleur']) ?></span>
                                        </div>
                                    </td>
                                    <td class="p-5 text-slate-400">
                                        <span class="px-2 py-1 bg-slate-950 border border-slate-800 text-xs text-slate-300">
                                            <?= htmlspecialchars($e['categorie']) ?>
                                        </span>
                                    </td>
                                    <td class="p-5 text-slate-500">
                                        <?= date('d/m/Y', strtotime($e['date_creation'])) ?>
                                    </td>
                                    <td class="p-5">
                                        <div class="flex justify-center gap-2">
                                            <a href="/admin-team-show?id=<?= $e['id'] ?>" class="flex items-center justify-center w-9 h-9 bg-slate-950 border border-slate-800 text-cyan-500 hover:border-cyan-500 hover:bg-cyan-500 hover:text-black transition-all duration-200" title="Visualiser les Datas">
                                                <i class="fas fa-eye text-xs"></i>
                                            </a>
                                            <a href="/admin-teamedit?id=<?= $e['id'] ?>" class="flex items-center justify-center w-9 h-9 bg-slate-950 border border-slate-800 text-yellow-500 hover:border-yellow-500 hover:bg-yellow-500 hover:text-black transition-all duration-200" title="Modifier">
                                                <i class="fas fa-edit text-xs"></i>
                                            </a>
                                            <a href="#" onclick="openDeleteModal(<?= $e['id'] ?>)" class="flex items-center justify-center w-9 h-9 bg-slate-950 border border-slate-800 text-rose-500 hover:border-rose-500 hover:bg-rose-500 hover:text-black transition-all duration-200" title="Purger le Système">
                                                <i class="fas fa-trash text-xs"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach;
                                else: ?>
                                <tr>
                                    <td colspan="5" class="p-16 text-center text-slate-600">
                                        <div class="inline-block p-4 border border-dashed border-slate-800 mb-4">
                                            <i class="fas fa-folder-open text-4xl text-slate-700"></i>
                                        </div>
                                        <p class="uppercase tracking-widest text-xs font-mono">Aucune Entrée Matrice Trouvée</p>
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

    <div id="deleteModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center z-50 hidden p-4 animate-fade-in">
        <div class="bg-slate-900 border-2 border-rose-500/40 p-8 max-w-md w-full mx-4 relative shadow-[0_0_50px_rgba(244,63,94,0.15)]">
            <div class="absolute top-0 left-0 w-3 h-3 border-t-2 border-l-2 border-rose-500"></div>
            <div class="absolute top-0 right-0 w-3 h-3 border-t-2 border-r-2 border-rose-500"></div>
            <div class="absolute bottom-0 left-0 w-3 h-3 border-b-2 border-l-2 border-rose-500"></div>
            <div class="absolute bottom-0 right-0 w-3 h-3 border-b-2 border-r-2 border-rose-500"></div>

            <div class="text-center font-mono">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-none bg-rose-950/60 border border-rose-500 mb-6 shadow-[0_0_15px_rgba(244,63,94,0.3)]">
                    <i class="fas fa-exclamation-triangle text-rose-500 text-2xl animate-pulse"></i>
                </div>
                <h3 class="text-xl font-black uppercase tracking-wider text-white mb-2">Alerte Suppression</h3>
                <p class="text-slate-400 mb-8 text-xs uppercase tracking-wide leading-relaxed">
                    Confirmez-vous la purge définitive de cette unité de la base de données ? Cette action détruira définitivement ses métadonnées associés.
                </p>
            </div>
            <div class="flex gap-4 font-mono text-xs uppercase tracking-widest font-bold">
                <button onclick="closeDeleteModal()" class="flex-1 px-4 py-3 bg-slate-950 border border-slate-800 text-slate-400 hover:text-white hover:border-slate-600 transition-all">
                    Avorter
                </button>
                <a id="confirmDeleteBtn" href="#" class="flex-1 px-4 py-3 bg-rose-950/40 border border-rose-500 text-rose-400 hover:bg-rose-500 hover:text-black text-center transition-all shadow-[0_0_15px_rgba(244,63,94,0.2)]">
                    Purger
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
                <tr class="hover:bg-cyan-950/20 transition-all duration-150 group">
                    <td class="p-5 font-bold text-white uppercase tracking-wide group-hover:text-cyan-400 transition-colors">${escapeHtml(equipe.nom)}</td>
                    <td class="p-5">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 border border-white/20 shadow-[0_0_10px_rgba(255,255,255,0.1)] relative" style="background-color: ${escapeHtml(equipe.couleur)};">
                                <span class="absolute inset-0 bg-gradient-to-tr from-black/40 to-transparent"></span>
                            </span>
                            <span class="text-xs text-slate-500 uppercase">${escapeHtml(equipe.couleur)}</span>
                        </div>
                    </td>
                    <td class="p-5 text-slate-400">
                        <span class="px-2 py-1 bg-slate-950 border border-slate-800 text-xs text-slate-300">${escapeHtml(equipe.categorie)}</span>
                    </td>
                    <td class="p-5 text-slate-500">${formatDate(equipe.date_creation)}</td>
                    <td class="p-5">
                        <div class="flex justify-center gap-2">
                            <a href="/admin-team-show?id=${equipe.id}" class="flex items-center justify-center w-9 h-9 bg-slate-950 border border-slate-800 text-cyan-500 hover:border-cyan-500 hover:bg-cyan-500 hover:text-black transition-all duration-200" title="Visualiser les Datas">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            <a href="/admin-teamedit?id=${equipe.id}" class="flex items-center justify-center w-9 h-9 bg-slate-950 border border-slate-800 text-yellow-500 hover:border-yellow-500 hover:bg-yellow-500 hover:text-black transition-all duration-200" title="Modifier">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <a href="#" onclick="openDeleteModal(${equipe.id})" class="flex items-center justify-center w-9 h-9 bg-slate-950 border border-slate-800 text-rose-500 hover:border-rose-500 hover:bg-rose-500 hover:text-black transition-all duration-200" title="Purger le Système">
                                <i class="fas fa-trash text-xs"></i>
                            </a>
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
                    tbody.innerHTML = '<tr><td colspan="5" class="p-16 text-center text-slate-600"><div class="inline-block p-4 border border-dashed border-slate-800 mb-4"><i class="fas fa-folder-open text-4xl text-slate-700"></i></div><p class="uppercase tracking-widest text-xs font-mono">Aucune Entrée Matrice Trouvée</p></td></tr>';
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
                    setTimeout(() => { notification.remove(); }, 500);
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