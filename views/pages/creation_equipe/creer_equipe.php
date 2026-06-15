<?php
require_once __DIR__ . '/../../../middleware/Role.php';
requireLogin();

$pageTitle = isset($equipe) ? "Modifier l'Équipe" : "Créer l'Équipe";
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
<body class="bg-slate-100 text-slate-800 min-h-screen font-sans">
    <?php include_once __DIR__ . '/../../partials/header.php'; ?>
    
    <div class="flex min-h-screen">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <div class="flex-1 flex flex-col overflow-hidden">
            <main class="flex-1 bg-slate-50 p-6 md:p-8 overflow-y-auto">
                
                <?php if (isset($_GET['msg']) && $_GET['msg'] === 'success'): ?>
                    <div id="success-notification" class="mb-6 px-4 py-3 bg-emerald-100 border border-emerald-200 text-emerald-800 font-medium rounded-lg flex items-center shadow-sm">
                        <i class="fas fa-check-circle mr-3 text-emerald-500 text-lg"></i>
                        <span>L'équipe a été créée avec succès !</span>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
                    <div id="success-notification" class="mb-6 px-4 py-3 bg-emerald-100 border border-emerald-200 text-emerald-800 font-medium rounded-lg flex items-center shadow-sm">
                        <i class="fas fa-check-circle mr-3 text-emerald-500 text-lg"></i>
                        <span>L'équipe a été modifiée avec succès !</span>
                    </div>
                <?php endif; ?>

                <script>
                    const notification = document.getElementById('success-notification');
                    if (notification) {
                        setTimeout(() => {
                            notification.style.transition = 'all 0.5s ease';
                            notification.style.opacity = '0';
                            notification.style.transform = 'translateY(-10px)';
                            setTimeout(() => notification.remove(), 500);
                        }, 3000);
                    }
                </script>

                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-3">
                            <i class="fas fa-users-cog text-slate-500"></i>
                            <span><?= isset($equipe) ? 'Modifier l\'Équipe' : 'Créer une Équipe' ?></span>
                        </h1>
                        <p class="text-sm text-slate-500">Formulaire de configuration du groupe</p>
                    </div>
                    <a href="/admin-team" 
                       class="flex items-center gap-2 px-4 py-2 text-sm font-medium bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-lg shadow-sm transition-colors">
                        <i class="fas fa-arrow-left text-xs"></i>
                        <span>Retour au Tableau</span>
                    </a>
                </div>

                <div class="max-w-2xl mx-auto bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                    <form action="<?= isset($equipe) ? '/admin-teamupdate?id=' . $equipe['id'] : '/admin-storeequipe' ?>" 
                          method="POST" 
                          class="p-6 md:p-8 space-y-6">
                        
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
                                Nom de l'équipe
                            </label>
                            <input type="text" name="nom" placeholder="Ex: Shadow Reapers" required 
                                   value="<?= isset($equipe) ? htmlspecialchars($equipe['nom']) : '' ?>"
                                   class="w-full px-4 py-2.5 border border-slate-200 rounded-lg bg-slate-50 focus:bg-white text-slate-900 focus:outline-none focus:border-slate-400 transition-colors">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
                                    Couleur Distinctive
                                </label>
                                <input type="color" name="couleur" 
                                       value="<?= isset($equipe) ? htmlspecialchars($equipe['couleur']) : '#3b82f6' ?>"
                                       class="w-full h-11 p-1 bg-slate-50 rounded-lg cursor-pointer border border-slate-200 hover:border-slate-300 transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
                                    Catégorie
                                </label>
                                <select name="categorie" 
                                        class="w-full px-4 py-2.5 border border-slate-200 rounded-lg bg-slate-50 focus:bg-white text-slate-900 focus:outline-none focus:border-slate-400 transition-colors">
                                    <option value="Senior" <?= (isset($equipe) && $equipe['categorie'] === 'Senior') ? 'selected' : '' ?>>Senior</option>
                                    <option value="Reserve" <?= (isset($equipe) && $equipe['categorie'] === 'Reserve') ? 'selected' : '' ?>>Reserve</option>
                                    <option value="Jeune" <?= (isset($equipe) && $equipe['categorie'] === 'Jeune') ? 'selected' : '' ?>>Jeune</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
                                Date de Fondation
                            </label>
                            <input type="date" name="date_creation" 
                                   value="<?= isset($equipe) ? htmlspecialchars($equipe['date_creation']) : date('Y-m-d') ?>" 
                                   class="w-full px-4 py-2.5 border border-slate-200 rounded-lg bg-slate-50 focus:bg-white text-slate-900 focus:outline-none focus:border-slate-400 transition-colors">
                        </div>

                        <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-100">
                            <a href="/admin-team" 
                               class="px-5 py-2 border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-lg font-medium text-sm transition-colors">
                                Annuler
                            </a>
                            <button type="submit" 
                                    class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg font-medium text-sm shadow-sm transition-colors">
                                <i class="fas fa-save mr-2 text-xs"></i>
                                <?= isset($equipe) ? 'Enregistrer les modifications' : 'Créer l\'équipe' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
</body>
</html>