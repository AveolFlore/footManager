<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../middleware/Role.php';

requireLogin();

use Controllers\MatchSeanceController;

$matchController = new MatchSeanceController();

// Récupérer tous les matchs et filtrer les entraînements
$allMatchs = $matchController->index();
$entrainements = array_filter($allMatchs, function($m) {
    return $m['type'] === 'entrainement';
});

$pageTitle = "Entraînements";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - FC Blue Lock</title>
    <link rel="icon" type="image/png" href="/images/blue_lock_logo.png">
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
            transform: translateY(-6px);
            box-shadow: 0 0 40px rgba(34, 211, 238, 0.25);
            border-color: rgba(34, 211, 238, 0.4);
        }
        
        .neon-text {
            text-shadow: 0 0 10px rgb(34 211 238),
                        0 0 20px rgb(34 211 238);
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
            background: linear-gradient(120deg, transparent, rgba(255,255,255,0.35), transparent);
            transform: skewX(-25deg);
            animation: scan 4s linear infinite;
        }
        
        @keyframes scan {
            0% { transform: translateX(-150%) skewX(-25deg); }
            100% { transform: translateX(400%) skewX(-25deg); }
        }
    </style>
</head>
<body class="cyber-bg text-slate-200 min-h-screen">

    <?php include_once __DIR__ . '/../../partials/header.php'; ?>
    
    <div class="flex min-h-screen">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1 overflow-y-auto">
            <div class="p-6 md:p-8 lg:p-10">
                
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
                    <div>
                        <h1 class="text-4xl md:text-5xl font-bold tracking-tighter neon-text flex items-center gap-4 font-['Orbitron']">
                            <i class="fas fa-running text-cyan-400"></i>
                            ENTRAÎNEMENTS
                        </h1>
                        <p class="text-slate-400 mt-2 text-lg"><?= count($entrainements) ?> séances programmées</p>
                    </div>
                    
                    <?php if (in_array($_SESSION['user']['role'], ['president', 'entraineur'])): ?>
                        <a href="/page-matchcreate" 
                           class="btn-glow flex items-center gap-3 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white px-8 py-4 rounded-2xl font-bold shadow-lg shadow-cyan-500/50 transition-all">
                            <i class="fas fa-plus"></i>
                            PLANIFIER ENTRAÎNEMENT
                        </a>
                    <?php endif; ?>
                </div>

                <?php if (isset($_GET['msg'])): ?>
                    <div class="mb-8 px-6 py-4 bg-gradient-to-r from-cyan-500/10 to-emerald-500/10 border border-cyan-400/30 text-cyan-300 rounded-2xl flex items-center">
                        <i class="fas fa-check-circle mr-3 text-xl"></i>
                        <?= htmlspecialchars($_GET['msg']) ?>
                    </div>
                <?php endif; ?>

                <?php if (empty($entrainements)): ?>
                    <div class="holo-card rounded-3xl p-16 text-center">
                        <i class="fas fa-futbol text-slate-500 text-8xl mb-6"></i>
                        <p class="text-2xl text-slate-400">Aucun entraînement planifié pour le moment</p>
                        <p class="text-slate-500 mt-3">Le staff technique va bientôt charger le planning.</p>
                    </div>
                <?php else: ?>
                    <div id="liste-entrainements" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        
                        <?php foreach ($entrainements as $entrainement): ?>
                            <?php
                            $badge = match ($entrainement['statut']) {
                                'planifie', 'publie' => ['label' => 'À VENIR', 'class' => 'bg-cyan-500/20 text-cyan-400 border-cyan-400/30'],
                                'termine' => ['label' => 'TERMINÉ', 'class' => 'bg-emerald-500/20 text-emerald-400 border-emerald-400/30'],
                                default => ['label' => strtoupper($entrainement['statut']), 'class' => 'bg-slate-700 text-slate-300']
                            };
                            ?>

                            <a href="/page-matchdetail?id=<?= $entrainement['id'] ?>" 
                               class="holo-card rounded-3xl p-8 block hover:scale-[1.02] transition-all duration-300">
                                
                                <div class="flex justify-between items-start mb-6">
                                    <span class="px-5 py-2 text-xs font-bold rounded-2xl border <?= $badge['class'] ?>">
                                        <i class="fas fa-clock mr-2"></i>
                                        <?= $badge['label'] ?>
                                    </span>
                                </div>

                                <h2 class="text-2xl font-semibold text-white mb-8 line-clamp-2">
                                    <?= htmlspecialchars($entrainement['description'] ?: 'Entraînement collectif') ?>
                                </h2>

                                <div class="space-y-5 text-slate-300">
                                    <div class="flex items-center gap-4">
                                        <i class="fas fa-calendar text-cyan-400 text-2xl w-8"></i>
                                        <span class="font-medium"><?= date('l d F', strtotime($entrainement['date'])) ?></span>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <i class="fas fa-clock text-cyan-400 text-2xl w-8"></i>
                                        <span class="font-medium"><?= date('H:i', strtotime($entrainement['date'])) ?></span>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <i class="fas fa-map-marker-alt text-cyan-400 text-2xl w-8"></i>
                                        <span class="font-medium"><?= htmlspecialchars($entrainement['lieu']) ?></span>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>