<?php
require_once __DIR__ . '/../../../middleware/Role.php';
requireLogin();

$pageTitle = "Détails Equipe - " . htmlspecialchars($equipe['nom']);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - FC Blue Lock</title>
    <link rel="icon" type="image/png" href="/assets/images/blue_lock_logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&display=swap');

        .cyber-bg {
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 100%);
        }

        .holo-card {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(34, 211, 238, 0.25);
            box-shadow: 0 0 30px rgba(34, 211, 238, 0.1);
            transition: all 0.4s ease;
        }

        .holo-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 0 45px rgba(34, 211, 238, 0.25);
            border-color: rgba(34, 211, 238, 0.4);
        }

        .neon-text {
            text-shadow: 0 0 12px rgb(34 211 238),
                0 0 25px rgb(34 211 238);
        }

        .avatar-cyber {
            background: linear-gradient(135deg, #22d3ee, #3b82f6);
            box-shadow: 0 0 25px rgba(34, 211, 238, 0.5);
        }
    </style>
</head>

<body class="cyber-bg text-slate-200 min-h-screen">

    <?php include_once __DIR__ . '/../../partials/header.php'; ?>

    <div class="flex min-h-screen">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1 overflow-y-auto">
            <div class="p-6 md:p-8 lg:p-10">

                <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-10 gap-4">
                    <h1 class="text-4xl md:text-5xl font-bold tracking-tighter neon-text flex items-center gap-4 font-['Orbitron']">
                        <i class="fas fa-users text-cyan-400"></i>
                        JOUEURS • <?= htmlspecialchars($equipe['nom']) ?>
                    </h1>
                    <a href="/admin-team"
                        class="flex items-center gap-3 px-6 py-3 border border-slate-600 hover:border-cyan-400 text-slate-300 hover:text-cyan-400 rounded-2xl font-medium transition-all">
                        <i class="fas fa-arrow-left"></i>
                        Retour à la liste
                    </a>
                </div>

                <!-- Grille des joueurs -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php if (!empty($joueurs)): ?>
                        <?php foreach ($joueurs as $j): ?>
                            <div class="holo-card rounded-3xl p-8 group">
                                <div class="flex items-center mb-8">
                                    <div class="h-20 w-20 avatar-cyber text-white rounded-3xl flex items-center justify-center text-4xl font-bold border-2 border-cyan-300/30 shadow-inner">
                                        <?= strtoupper(substr($j['nom'], 0, 1)) ?>
                                    </div>
                                    <div class="ml-6">
                                        <h3 class="font-bold text-2xl text-white">
                                            <?= htmlspecialchars($j['prenom'] . ' ' . $j['nom']) ?>
                                        </h3>
                                        <p class="text-cyan-400 text-sm font-medium"><?= htmlspecialchars($j['email']) ?></p>
                                    </div>
                                </div>

                                <div class="space-y-5 text-slate-300">
                                    <div class="flex justify-between items-center pb-4 border-b border-slate-700">
                                        <span class="text-slate-400 font-medium">Poste</span>
                                        <span class="font-semibold uppercase tracking-wider"><?= htmlspecialchars($j['poste'] ?? 'N/A') ?></span>
                                    </div>
                                    <div class="flex justify-between items-center pb-4 border-b border-slate-700">
                                        <span class="text-slate-400 font-medium">Pied dominant</span>
                                        <span class="font-semibold"><?= htmlspecialchars($j['pied_dominant'] ?? 'Non défini') ?></span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-400 font-medium">Date de naissance</span>
                                        <span class="font-medium"><?= date('d/m/Y', strtotime($j['date_naissance'])) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-full holo-card rounded-3xl p-20 text-center">
                            <i class="fas fa-user-slash text-slate-500 text-8xl mb-6"></i>
                            <p class="text-2xl text-slate-400 font-medium">Aucun joueur assigné à cette équipe</p>
                            <p class="text-slate-500 mt-3">Les joueurs seront visibles une fois validés par le staff.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>

</html>