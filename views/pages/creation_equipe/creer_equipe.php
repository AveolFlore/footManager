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
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&display=swap');
        
        .cyber-bg {
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 100%);
        }
        
        .holo-card {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(34, 211, 238, 0.3);
            box-shadow: 0 0 35px rgba(34, 211, 238, 0.15);
        }
        
        .btn-glow {
            position: relative;
            overflow: hidden;
        }
        
        .btn-glow::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 40%;
            height: 200%;
            background: linear-gradient(
                120deg,
                transparent,
                rgba(255,255,255,0.4),
                transparent
            );
            transform: skewX(-25deg);
            animation: scan 4s linear infinite;
        }
        
        @keyframes scan {
            0% { transform: translateX(-150%) skewX(-25deg); }
            100% { transform: translateX(400%) skewX(-25deg); }
        }
        
        .neon-text {
            text-shadow: 0 0 10px rgb(34 211 238),
                        0 0 20px rgb(34 211 238);
        }
        
        .input-cyber {
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(34, 211, 238, 0.4);
            transition: all 0.3s ease;
        }
        
        .input-cyber:focus {
            border-color: rgb(34 211 238);
            box-shadow: 0 0 0 4px rgba(34, 211, 238, 0.2);
            background: rgba(15, 23, 42, 0.9);
        }
    </style>
</head>
<body class="cyber-bg text-slate-200 min-h-screen">
    <?php include_once __DIR__ . '/../../partials/header.php'; ?>
    
    <div class="flex min-h-screen">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <div class="flex-1 flex flex-col overflow-hidden">
            <main class="flex-1 p-6 md:p-8 lg:p-10 overflow-y-auto">
                
                <!-- Notification Cyber -->
                <?php if (isset($_GET['msg']) && $_GET['msg'] === 'success'): ?>
                    <div id="success-notification" class="mb-8 px-6 py-4 bg-gradient-to-r from-cyan-500/10 to-emerald-500/10 border border-cyan-400/30 text-cyan-400 font-semibold rounded-2xl shadow-lg shadow-cyan-500/20 flex items-center">
                        <i class="fas fa-check-circle mr-3 text-2xl"></i>
                        <span>L'équipe a été créée avec succès !</span>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
                    <div id="success-notification" class="mb-8 px-6 py-4 bg-gradient-to-r from-cyan-500/10 to-emerald-500/10 border border-cyan-400/30 text-cyan-400 font-semibold rounded-2xl shadow-lg shadow-cyan-500/20 flex items-center">
                        <i class="fas fa-check-circle mr-3 text-2xl"></i>
                        <span>L'équipe a été modifiée avec succès !</span>
                    </div>
                <?php endif; ?>

                <script>
                    const notification = document.getElementById('success-notification');
                    if (notification) {
                        setTimeout(() => {
                            notification.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                            notification.style.opacity = '0';
                            notification.style.transform = 'translateY(-20px)';
                            setTimeout(() => notification.remove(), 600);
                        }, 3200);
                    }
                </script>

                <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-10 gap-4">
                    <h1 class="text-4xl md:text-5xl font-bold tracking-tighter neon-text flex items-center gap-4">
                        <i class="fas fa-users-cog text-cyan-400"></i>
                        <span class="font-['Orbitron']"><?= isset($equipe) ? 'MODIFIER ÉQUIPE' : 'CRÉER ÉQUIPE' ?></span>
                    </h1>
                    <a href="/admin-team" 
                       class="flex items-center gap-3 px-6 py-3 rounded-2xl border border-slate-700 hover:border-cyan-400 text-slate-300 hover:text-cyan-400 transition-all duration-300">
                        <i class="fas fa-arrow-left"></i>
                        <span class="font-semibold">Retour au Tableau</span>
                    </a>
                </div>

                <!-- Carte Principale -->
                <div class="max-w-3xl mx-auto holo-card rounded-3xl overflow-hidden">
                    <!-- Header Holographique -->
                    <div class="bg-gradient-to-r from-cyan-600 via-blue-600 to-violet-600 p-8 text-center relative overflow-hidden">
                        <div class="absolute inset-0 bg-[linear-gradient(transparent_50%,rgba(255,255,255,0.08)_50%)] bg-[length:100%_4px] animate-[scan_3s_linear_infinite]"></div>
                        <h2 class="text-white text-3xl font-bold font-['Orbitron'] tracking-widest flex items-center justify-center gap-4 relative z-10">
                            <i class="fas fa-flag text-4xl"></i>
                            <?= isset($equipe) ? 'MODIFIER L\'ÉQUIPE' : 'NOUVELLE ÉQUIPE' ?>
                        </h2>
                    </div>

                    <form action="<?= isset($equipe) ? '/admin-teamupdate?id=' . $equipe['id'] : '/admin-storeequipe' ?>" 
                          method="POST" 
                          class="p-10 space-y-10">
                        
                        <div>
                            <label class="block text-sm uppercase tracking-widest text-cyan-400 font-medium mb-3">
                                Nom de l'équipe
                            </label>
                            <input type="text" name="nom" placeholder="Ex: Shadow Reapers" required 
                                   value="<?= isset($equipe) ? htmlspecialchars($equipe['nom']) : '' ?>"
                                   class="input-cyber w-full px-6 py-5 rounded-2xl text-lg font-semibold focus:outline-none">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-sm uppercase tracking-widest text-cyan-400 font-medium mb-3">
                                    Couleur Distinctive
                                </label>
                                <input type="color" name="couleur" 
                                       value="<?= isset($equipe) ? htmlspecialchars($equipe['couleur']) : '#22d3ee' ?>"
                                       class="w-full h-20 p-2 bg-slate-900 rounded-2xl cursor-pointer border border-slate-700 hover:border-cyan-400 transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm uppercase tracking-widest text-cyan-400 font-medium mb-3">
                                    Catégorie
                                </label>
                                <select name="categorie" 
                                        class="input-cyber w-full px-6 py-5 rounded-2xl text-lg font-semibold focus:outline-none">
                                    <option value="Senior" <?= (isset($equipe) && $equipe['categorie'] === 'Senior') ? 'selected' : '' ?>>Senior</option>
                                    <option value="Reserve" <?= (isset($equipe) && $equipe['categorie'] === 'Reserve') ? 'selected' : '' ?>>Reserve</option>
                                    <option value="Jeune" <?= (isset($equipe) && $equipe['categorie'] === 'Jeune') ? 'selected' : '' ?>>Jeune</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm uppercase tracking-widest text-cyan-400 font-medium mb-3">
                                Date de Fondation
                            </label>
                            <input type="date" name="date_creation" 
                                   value="<?= isset($equipe) ? htmlspecialchars($equipe['date_creation']) : date('Y-m-d') ?>" 
                                   class="input-cyber w-full px-6 py-5 rounded-2xl text-lg font-semibold focus:outline-none">
                        </div>

                        <div class="flex items-center justify-end gap-6 pt-8 border-t border-slate-700">
                            <a href="/admin-team" 
                               class="px-8 py-4 border border-slate-600 hover:border-red-500 text-slate-400 hover:text-red-400 rounded-2xl font-bold transition-all">
                                Annuler
                            </a>
                            <button type="submit" 
                                    class="btn-glow px-10 py-4 bg-gradient-to-r from-cyan-500 to-blue-600 text-white rounded-2xl font-bold text-lg shadow-lg shadow-cyan-500/50 hover:shadow-cyan-400/70 active:scale-95 transition-all">
                                <i class="fas fa-save mr-3"></i>
                                <?= isset($equipe) ? 'Modifier l\'équipe' : 'Créer l\'équipe' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
</body>
</html>