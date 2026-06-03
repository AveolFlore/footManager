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
    <link rel="icon" type="image/png" href="/images/blue_lock_logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-slate-50 to-slate-100">
    <?php include_once __DIR__ . '/../../partials/header.php'; ?>
    <div class="flex min-h-screen">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <div class="flex-1 flex flex-col overflow-hidden">
            <main class="flex-1 p-6 md:p-8 lg:p-10 overflow-y-auto">
                <!-- Notification de succès pour création -->
                <?php if (isset($_GET['msg']) && $_GET['msg'] === 'success'): ?>
                    <div id="success-notification" class="mb-8 px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-700 font-semibold rounded-2xl shadow-md animate-bounce">
                        <i class="fas fa-check-circle mr-3 text-2xl"></i>
                        L'équipe a été créée avec succès !
                    </div>
                <?php endif; ?>
                
                <!-- Notification de succès pour modification -->
                <?php if (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
                    <div id="success-notification" class="mb-8 px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-700 font-semibold rounded-2xl shadow-md animate-bounce">
                        <i class="fas fa-check-circle mr-3 text-2xl"></i>
                        L'équipe a été modifiée avec succès !
                    </div>
                <?php endif; ?>
                
                <script>
                    const notification = document.getElementById('success-notification');
                    if (notification) {
                        setTimeout(() => {
                            notification.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                            notification.style.opacity = '0';
                            notification.style.transform = 'translateY(-10px)';
                            setTimeout(() => { notification.remove(); }, 500);
                        }, 3000);
                    }
                </script>

                <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 gap-4">
                    <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 flex items-center gap-3">
                        <i class="fas fa-users-cog text-green-600"></i>
                        <?php echo isset($equipe) ? 'Modifier l\'Équipe' : 'Créer l\'Équipe'; ?>
                    </h1>
                    <a href="/admin-team" class="flex items-center gap-3 bg-gradient-to-r from-slate-200 to-slate-300 text-slate-800 px-6 py-3 rounded-2xl font-bold hover:from-slate-300 hover:to-slate-400 transition-all shadow-md">
                        <i class="fas fa-arrow-left"></i>
                        Retour
                    </a>
                </div>

                <div class="max-w-3xl mx-auto bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-600 to-green-700 p-8 text-center">
                        <h2 class="text-white text-2xl font-extrabold tracking-tight flex items-center justify-center gap-3">
                            <i class="fas fa-flag text-3xl"></i>
                            <?php echo isset($equipe) ? 'Modifier l\'Équipe' : 'Créer l\'Équipe'; ?>
                        </h2>
                    </div>

                    <form action="<?= isset($equipe) ? '/admin-teamupdate?id=' . $equipe['id'] : '/admin-storeequipe' ?>" method="POST" class="p-10 space-y-8">
                        
                        <div>
                            <label class="block text-base font-bold text-slate-700 mb-3">Nom de l'équipe</label>
                            <input type="text" name="nom" placeholder="Ex: Les Lions du Green" required 
                                   value="<?= isset($equipe) ? htmlspecialchars($equipe['nom']) : '' ?>"
                                   class="w-full px-6 py-4 border-2 border-slate-200 rounded-2xl focus:ring-4 focus:ring-green-200 focus:border-green-500 outline-none text-slate-800 font-semibold text-lg placeholder:text-slate-400 transition-all bg-white shadow-sm">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-base font-bold text-slate-700 mb-3">Couleur Distinctive</label>
                                <input type="color" name="couleur" 
                                       value="<?= isset($equipe) ? htmlspecialchars($equipe['couleur']) : '#000000' ?>"
                                       class="w-full h-16 p-3 border-2 border-slate-200 rounded-2xl cursor-pointer shadow-sm">
                            </div>
                            <div>
                                <label class="block text-base font-bold text-slate-700 mb-3">Catégorie officielle</label>
                                <select name="categorie" 
                                        class="w-full px-6 py-4 border-2 border-slate-200 rounded-2xl focus:ring-4 focus:ring-green-200 focus:border-green-500 outline-none text-slate-800 font-semibold text-lg bg-white transition-all shadow-sm">
                                    <option value="Senior" <?= (isset($equipe) && $equipe['categorie'] === 'Senior') ? 'selected' : '' ?>>Senior</option>
                                    <option value="Reserve" <?= (isset($equipe) && $equipe['categorie'] === 'Reserve') ? 'selected' : '' ?>>Reserve</option>
                                    <option value="Jeune" <?= (isset($equipe) && $equipe['categorie'] === 'Jeune') ? 'selected' : '' ?>>Jeune</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-base font-bold text-slate-700 mb-3">Date de Fondation officielle</label>
                            <input type="date" name="date_creation" value="<?= isset($equipe) ? htmlspecialchars($equipe['date_creation']) : date('Y-m-d') ?>" 
                                   class="w-full px-6 py-4 border-2 border-slate-200 rounded-2xl focus:ring-4 focus:ring-green-200 focus:border-green-500 outline-none text-slate-800 font-semibold text-lg transition-all bg-white shadow-sm">
                        </div>

                        <div class="flex items-center justify-end gap-6 pt-8 border-t border-slate-100">
                            <a href="/admin-team" class="px-8 py-4 bg-gradient-to-r from-slate-200 to-slate-300 text-slate-800 rounded-2xl font-bold hover:from-slate-300 hover:to-slate-400 transition-all text-center shadow-md">
                                Annuler
                            </a>
                            <button type="submit" 
                                    class="px-10 py-4 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-2xl font-extrabold text-lg hover:from-green-700 hover:to-green-800 transition-all shadow-lg shadow-green-200 active:scale-95">
                                <i class="fas fa-save mr-2"></i>
                                <?php echo isset($equipe) ? 'Modifier l\'équipe' : 'Créer l\'équipe'; ?>
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
