<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../config/Database.php';
require_once __DIR__ . '/../../../middleware/Role.php';

requireLogin();

use Controllers\MatchSeanceController;
use Controllers\Convocation\ConvocationController;
use Controllers\ResultatMatchController;

$matchController = new MatchSeanceController();
$convocationController = new ConvocationController();
$resultatController = new ResultatMatchController();

$id = (int) $_GET['id'];
$match = $matchController->read_one($id);
$convoques = $convocationController->index($id);
$resultat = $resultatController->index($id);

// Grouper les convoqués par équipe
$joueursParEquipe = ['A' => [], 'B' => []];
foreach ($convoques as $convoque) {
    $joueursParEquipe[$convoque['equipe_match']][] = $convoque;
}

$pageTitle = "Détails du match";
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
        .cyber-bg {
            background: radial-gradient(circle at 50% 50%, #0f172a 0%, #020617 100%);
            position: relative;
        }
        .cyber-bg::before {
            content: " ";
            display: block;
            position: fixed;
            top: 0; left: 0; bottom: 0; right: 0;
            background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.25) 50%), linear-gradient(90deg, rgba(6, 182, 212, 0.04), rgba(0, 0, 0, 0), rgba(244, 63, 94, 0.04));
            z-index: 99999;
            opacity: 0.3;
            pointer-events: none;
            background-size: 100% 4px, 6px 100%;
        }
        .glow-cyan { box-shadow: 0 0 15px rgba(6, 182, 212, 0.15); }
        .glow-rose { box-shadow: 0 0 15px rgba(244, 63, 94, 0.15); }
    </style>
</head>

<body class="cyber-bg text-slate-100 min-h-screen font-sans antialiased">
    <?php include_once __DIR__ . '/../../partials/header.php'; ?>
    <div class="flex min-h-screen">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1 overflow-y-auto bg-slate-950/40 backdrop-blur-sm">
            <div class="p-6 md:p-8 lg:p-10 max-w-7xl mx-auto">
                
                <a href="/page-match"
                    class="inline-flex items-center gap-2 text-cyan-500/60 hover:text-cyan-400 mb-8 font-mono text-xs uppercase tracking-widest transition-colors">
                    <i class="fas fa-arrow-left"></i>
                    [ Retour aux matchs ]
                </a>

                <div class="bg-slate-900/40 border border-slate-800/80 backdrop-blur-md p-8 mb-8 relative overflow-hidden">
                    <div class="absolute -top-[1px] -left-[1px] w-3 h-3 border-t-2 border-l-2 border-cyan-500"></div>
                    <div class="absolute -top-[1px] -right-[1px] w-3 h-3 border-t-2 border-r-2 border-cyan-500"></div>

                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 gap-6 relative z-10">
                        <div>
                            <h1 class="text-2xl md:text-3xl font-black uppercase tracking-wider text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-200 to-slate-400 flex items-center gap-3 font-mono">
                                <i class="fas fa-futbol text-cyan-500 filter drop-shadow-[0_0_8px_rgba(6,182,212,0.5)]"></i>
                                ÉQUIPE A <span class="text-cyan-500 font-light mx-1">VS</span> ÉQUIPE B
                            </h1>

                            <?php
                            $badge = match ($match['statut']) {
                                'planifie', 'publie' => ['label' => 'PHASE_ATTENTE', 'class' => 'border-cyan-500/30 bg-cyan-950/30 text-cyan-400 shadow-[0_0_10px_rgba(6,182,212,0.1)]'],
                                'termine' => ['label' => 'MATRICE_TERMINÉE', 'class' => 'border-emerald-500/30 bg-emerald-950/30 text-emerald-400 shadow-[0_0_10px_rgba(16,185,129,0.1)]'],
                                default => ['label' => strtoupper($match['statut']), 'class' => 'border-slate-700 bg-slate-900 text-slate-400']
                            };
                            ?>
                            <span class="inline-flex items-center text-xs px-4 py-1.5 mt-3 rounded-none font-mono font-bold uppercase tracking-widest border <?= $badge['class'] ?>">
                                <i class="fas fa-clock mr-2 text-[10px]"></i>
                                <?= $badge['label'] ?>
                            </span>
                        </div>

                        <?php if ($match['statut'] === 'termine' && $resultat): ?>
                            <div class="text-left lg:text-right bg-slate-950/50 border border-slate-800/80 p-4 px-6 min-w-[200px] font-mono">
                                <p class="text-[10px] text-slate-500 uppercase tracking-widest mb-1">// SCORE_FINAL</p>
                                <p class="text-4xl font-black tracking-wider text-white">
                                    <span class="text-cyan-400"><?= $resultat['buts_equipe_a'] ?></span>
                                    <span class="text-slate-600 text-2xl mx-2">:</span>
                                    <span class="text-rose-500"><?= $resultat['buts_equipe_b'] ?></span>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8 font-mono">
                        <div class="flex items-center gap-4 p-4 border border-slate-800/60 bg-slate-950/30">
                            <div class="w-10 h-10 bg-cyan-950/40 border border-cyan-800/50 flex items-center justify-center text-cyan-400">
                                <i class="fas fa-calendar text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-500 uppercase tracking-widest">Date système</p>
                                <p class="text-sm font-bold text-slate-300">
                                    <?php
                                    $days = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
                                    $months = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                                    $timestamp = strtotime($match['date']);
                                    echo strtoupper($days[date('w', $timestamp)]) . ' ' . date('d', $timestamp) . ' ' . strtoupper($months[date('n', $timestamp)]);
                                    ?>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 p-4 border border-slate-800/60 bg-slate-950/30">
                            <div class="w-10 h-10 bg-cyan-950/40 border border-cyan-800/50 flex items-center justify-center text-cyan-400">
                                <i class="fas fa-clock text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-500 uppercase tracking-widest">Index Temporel</p>
                                <p class="text-sm font-bold text-slate-300">
                                    <?= date('H:i', strtotime($match['date'])) ?> UTC
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 p-4 border border-slate-800/60 bg-slate-950/30">
                            <div class="w-10 h-10 bg-cyan-950/40 border border-cyan-800/50 flex items-center justify-center text-cyan-400">
                                <i class="fas fa-map-marker-alt text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-500 uppercase tracking-widest">Coordonnées Zone</p>
                                <p class="text-sm font-bold text-slate-300 uppercase tracking-wide truncate max-w-[180px]">
                                    <?= htmlspecialchars($match['lieu']) ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <?php if (in_array($_SESSION['user']['role'], ['president', 'organisateur'])): ?>
                        <div class="flex flex-wrap gap-3 mt-6 pt-6 border-t border-slate-800/60 font-mono text-xs uppercase tracking-widest">
                            <?php if ($match['statut'] === 'planifie'): ?>
                                <a href="/page-matchconvocations?id=<?= $match['id'] ?>"
                                    class="flex items-center gap-2 px-5 py-3 bg-cyan-950/40 border border-cyan-500 text-cyan-400 hover:bg-cyan-500 hover:text-black transition-all glow-cyan font-bold">
                                    <i class="fas fa-users"></i> [ Gérer convocations ]
                                </a>
                                <a href="/matchSeance-edit?id=<?= $match['id'] ?>"
                                    class="flex items-center gap-2 px-5 py-3 border border-slate-800 text-slate-400 hover:text-white hover:border-slate-600 transition-all bg-slate-950/20">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                            <?php endif; ?>

                            <?php if ($match['statut'] !== 'termine'): ?>
                                <button onclick="confirm('Confirmer la purge ?') && (window.location.href = '/matchSeance-delete?id=<?= $match['id'] ?>')" 
                                    class="flex items-center gap-2 px-5 py-3 bg-rose-950/20 border border-rose-900/60 text-rose-400 hover:border-rose-500 transition-all ml-auto">
                                    <i class="fas fa-trash"></i> Purger
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 font-mono">
                    
                    <div class="bg-slate-900/40 border border-slate-800/80 p-6 relative">
                        <div class="absolute top-0 left-0 w-1.5 h-6 bg-cyan-500"></div>
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-800/60">
                            <h2 class="text-base font-black uppercase tracking-wider text-cyan-400 flex items-center gap-2">
                                <i class="fas fa-shield-alt"></i> SÉLECTION_A
                            </h2>
                            <span class="text-xs text-slate-500 font-bold">[<?= count($joueursParEquipe['A']) ?> UNITÉS]</span>
                        </div>

                        <?php if (empty($joueursParEquipe['A'])): ?>
                            <div class="text-center py-12 border border-dashed border-slate-800/80 text-slate-600">
                                <i class="fas fa-user-slash text-3xl mb-3 block"></i>
                                <p class="text-xs uppercase tracking-wider">Aucun flux de données joueur</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-2">
                                <?php foreach ($joueursParEquipe['A'] as $convoque): ?>
                                    <div class="flex items-center justify-between p-3 bg-slate-950/40 border border-slate-800/40 hover:border-cyan-500/30 transition-all">
                                        <div class="flex items-center gap-3">
                                            <span class="text-xs font-bold font-mono text-cyan-500 bg-cyan-950/50 border border-cyan-900/50 w-7 h-7 flex items-center justify-center">
                                                <?= $convoque['numero_maillot'] ? str_pad($convoque['numero_maillot'], 2, '0', STR_PAD_LEFT) : 'X' ?>
                                            </span>
                                            <p class="text-xs font-bold uppercase tracking-wide text-slate-300">
                                                <?= htmlspecialchars($convoque['nom']) ?> <?= htmlspecialchars($convoque['prenom']) ?>
                                            </p>
                                        </div>
                                        <?php if ($convoque['est_capitaine']): ?>
                                            <span class="text-[10px] font-black uppercase tracking-widest px-2 py-0.5 border border-amber-500/30 bg-amber-950/20 text-amber-400">
                                                <i class="fas fa-crown mr-1"></i> CAP
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="bg-slate-900/40 border border-slate-800/80 p-6 relative">
                        <div class="absolute top-0 left-0 w-1.5 h-6 bg-rose-500"></div>
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-800/60">
                            <h2 class="text-base font-black uppercase tracking-wider text-rose-400 flex items-center gap-2">
                                <i class="fas fa-shield-alt"></i> SÉLECTION_B
                            </h2>
                            <span class="text-xs text-slate-500 font-bold">[<?= count($joueursParEquipe['B']) ?> UNITÉS]</span>
                        </div>

                        <?php if (empty($joueursParEquipe['B'])): ?>
                            <div class="text-center py-12 border border-dashed border-slate-800/80 text-slate-600">
                                <i class="fas fa-user-slash text-3xl mb-3 block"></i>
                                <p class="text-xs uppercase tracking-wider">Aucun flux de données joueur</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-2">
                                <?php foreach ($joueursParEquipe['B'] as $convoque): ?>
                                    <div class="flex items-center justify-between p-3 bg-slate-950/40 border border-slate-800/40 hover:border-rose-500/30 transition-all">
                                        <div class="flex items-center gap-3">
                                            <span class="text-xs font-bold font-mono text-rose-500 bg-rose-950/50 border border-rose-900/50 w-7 h-7 flex items-center justify-center">
                                                <?= $convoque['numero_maillot'] ? str_pad($convoque['numero_maillot'], 2, '0', STR_PAD_LEFT) : 'X' ?>
                                            </span>
                                            <p class="text-xs font-bold uppercase tracking-wide text-slate-300">
                                                <?= htmlspecialchars($convoque['nom']) ?> <?= htmlspecialchars($convoque['prenom']) ?>
                                            </p>
                                        </div>
                                        <?php if ($convoque['est_capitaine']): ?>
                                            <span class="text-[10px] font-black uppercase tracking-widest px-2 py-0.5 border border-amber-500/30 bg-amber-950/20 text-amber-400">
                                                <i class="fas fa-crown mr-1"></i> CAP
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (
                    in_array($_SESSION['user']['role'], ['president', 'censeur']) &&
                    $match['statut'] === 'publie' &&
                    !$resultat
                ): ?>
                    <div class="bg-slate-900/40 border border-slate-800/80 p-8 mt-8 relative font-mono">
                        <div class="absolute -bottom-[1px] -right-[1px] w-3 h-3 border-b-2 border-r-2 border-cyan-400"></div>
                        
                        <h2 class="text-base font-black uppercase tracking-wider text-slate-200 mb-6 flex items-center gap-2">
                            <i class="fas fa-clipboard-list text-cyan-400"></i> ENREGISTREMENT_RÉSULTATS
                        </h2>

                        <form action="/resultatMatch-resultatsave" method="POST">
                            <input type="hidden" name="match_id" value="<?= $match['id'] ?>">

                            <div class="flex flex-col md:flex-row items-center gap-6 bg-slate-950/40 p-6 border border-slate-800/60">
                                
                                <div class="text-center">
                                    <label class="text-[10px] text-cyan-400 font-bold uppercase tracking-widest block mb-2">// SCORE_A</label>
                                    <input type="number" name="buts_equipe_a" min="0" value="0"
                                        class="w-20 bg-slate-900 border border-slate-800 text-cyan-400 text-center py-2 text-2xl font-black outline-none focus:border-cyan-500">
                                </div>

                                <span class="text-2xl font-black text-slate-700 mt-4 md:mt-6">:</span>

                                <div class="text-center">
                                    <label class="text-[10px] text-rose-400 font-bold uppercase tracking-widest block mb-2">// SCORE_B</label>
                                    <input type="number" name="buts_equipe_b" min="0" value="0"
                                        class="w-20 bg-slate-900 border border-slate-800 text-rose-400 text-center py-2 text-2xl font-black outline-none focus:border-rose-500">
                                </div>

                                <div class="md:ml-auto w-full md:w-auto mt-4 md:mt-4">
                                    <button type="submit" name="save_resultat" value="Enregistrer"
                                        class="w-full md:w-auto px-6 py-3 bg-cyan-950/60 border border-cyan-500 text-cyan-400 text-xs font-black uppercase tracking-widest hover:bg-cyan-500 hover:text-black transition-all glow-cyan">
                                        <i class="fas fa-check mr-2"></i> Valider la matrice
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>

</html>