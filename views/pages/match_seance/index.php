<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../middleware/Role.php';

requireLogin();

use Controllers\MatchSeanceController;
use Controllers\Convocation\ConvocationController;
use Controllers\ResultatMatchController;

$matchController = new MatchSeanceController();
$convocationController = new ConvocationController();

$matchs = $matchController->index();
$matchs_en_retard = [];
foreach ($matchs as $match) {
    if (
        $match['statut'] === 'publie' &&
        strtotime($match['date']) < strtotime('-1 day')
    ) {
        $matchs_en_retard[] = $match;
    }
}
$resultatController = new ResultatMatchController();
$pageTitle = "Matchs";

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
            opacity: 0.25;
            pointer-events: none;
            background-size: 100% 4px, 6px 100%;
        }
        .glow-cyan { box-shadow: 0 0 15px rgba(6, 182, 212, 0.2); }
        .glow-orange { box-shadow: 0 0 15px rgba(245, 158, 11, 0.15); }
    </style>
</head>

<body class="cyber-bg text-slate-100 min-h-screen font-sans antialiased">
    <?php include_once __DIR__ . '/../../partials/header.php'; ?>
    <div class="flex min-h-screen">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1 overflow-y-auto bg-slate-950/40 backdrop-blur-sm">
            <div class="p-6 md:p-8 lg:p-10 max-w-7xl mx-auto">
                
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 gap-4 border-b border-slate-800/60 pb-6">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-black uppercase tracking-wider text-transparent bg-clip-text bg-gradient-to-r from-white to-slate-400 flex items-center gap-4 font-mono">
                            <i class="fas fa-futbol text-cyan-400 filter drop-shadow-[0_0_8px_rgba(6,182,212,0.5)]"></i>
                            Base_Matchs
                        </h1>
                        <p class="text-slate-500 mt-1 font-mono text-xs uppercase tracking-widest">// Index: <?= count($matchs) ?> entités chargées</p>
                    </div>
                    <?php if (in_array($_SESSION['user']['role'], ['president', 'organisateur'])): ?>
                        <a href="/page-matchcreate"
                            class="flex items-center gap-3 bg-cyan-950/40 border border-cyan-500 text-cyan-400 px-6 py-3 font-mono text-xs uppercase tracking-widest font-bold hover:bg-cyan-500 hover:text-black transition-all glow-cyan relative overflow-hidden">
                            <i class="fas fa-plus"></i>
                            Initialiser_Match
                        </a>
                    <?php endif; ?>
                </div>

                <div class="flex flex-wrap gap-3 mb-8 font-mono text-xs uppercase tracking-widest">
                    <button onclick="filtrer('tous')"
                        id="btn-tous"
                        class="filtre-btn px-5 py-3 border border-cyan-500 bg-cyan-950/40 text-cyan-400 font-bold transition-all glow-cyan">
                        [ Tous_Logs ]
                    </button>
                    <button onclick="filtrer('planifie')"
                        id="btn-planifie"
                        class="filtre-btn px-5 py-3 border border-slate-800 bg-slate-950/40 text-slate-400 font-bold hover:border-slate-700 hover:text-slate-200 transition-all">
                        [ À_Venir ]
                    </button>
                    <button onclick="filtrer('termine')"
                        id="btn-termine"
                        class="filtre-btn px-5 py-3 border border-slate-800 bg-slate-950/40 text-slate-400 font-bold hover:border-slate-700 hover:text-slate-200 transition-all">
                        [ Archives ]
                    </button>
                </div>

                <?php if (isset($_GET['msg'])): ?>
                    <div class="mb-8 px-6 py-4 bg-emerald-950/30 text-emerald-400 font-mono text-xs uppercase tracking-wider border border-emerald-500/30 relative">
                        <div class="absolute top-0 left-0 w-1 h-full bg-emerald-500"></div>
                        <i class="fas fa-check-circle mr-3 text-emerald-500"></i>
                        SYSTEM_SUCCESS // <?= htmlspecialchars($_GET['msg']) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($matchs_en_retard)): ?>
                    <div class="mb-8 px-6 py-4 bg-amber-950/30 text-amber-500 font-mono text-xs uppercase tracking-wider border border-amber-500/30 glow-orange relative">
                        <div class="absolute top-0 left-0 w-1 h-full bg-amber-500"></div>
                        <i class="fas fa-exclamation-triangle mr-3 text-amber-500"></i>
                        ATTENTION_REQUIS // <?= count($matchs_en_retard) ?> session(s) en attente de clôture de score. Saisie impérative.
                    </div>
                <?php endif; ?>

                <div id="liste-matchs" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                    <?php foreach ($matchs as $match): ?>

                        <?php
                        $badge = match ($match['statut']) {
                            'planifie', 'publie' => ['label' => 'RUNNING', 'class' => 'border-cyan-500/40 text-cyan-400 bg-cyan-950/20'],
                            'termine' => ['label' => 'ARCHIVED', 'class' => 'border-slate-700 text-slate-400 bg-slate-900/40'],
                            default => ['label' => strtoupper($match['statut']), 'class' => 'border-slate-800 text-slate-500 bg-slate-950']
                        };

                        $convoques = $convocationController->index((int)$match['id']);
                        $nb_convoques = count($convoques);

                        $score = null;
                        if ($match['statut'] === 'termine') {
                            $resultat = $resultatController->index((int)$match['id']);
                            if ($resultat) {
                                $score = $resultat['buts_equipe_a'] . ' - ' . $resultat['buts_equipe_b'];
                            }
                        }
                        ?>

                        <a href="/page-matchdetail?id=<?= $match['id'] ?>"
                            class="match-card block bg-slate-900/30 border border-slate-800/80 p-6 hover:border-cyan-500/50 hover:bg-slate-900/60 transition-all duration-300 relative group overflow-hidden"
                            data-statut="<?= $match['statut'] ?>">
                            
                            <div class="absolute -top-[1px] -left-[1px] w-2 h-2 border-t border-l border-cyan-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="absolute -bottom-[1px] -right-[1px] w-2 h-2 border-b border-r border-cyan-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>

                            <div class="flex justify-between items-center mb-5 font-mono">
                                <span class="text-[10px] px-3 py-1 border font-bold uppercase tracking-widest <?= $badge['class'] ?>">
                                    <?= $badge['label'] ?>
                                </span>
                                <?php if ($score): ?>
                                    <span class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-slate-200 to-slate-400 tracking-tighter filter drop-shadow-[0_2px_4px_rgba(0,0,0,0.5)]"><?= $score ?></span>
                                <?php endif; ?>
                            </div>

                            <h2 class="text-md font-bold uppercase tracking-wider text-slate-200 mb-5 font-mono truncate">
                                Équipe A <span class="text-cyan-500/60 font-light text-xs">vs</span> Équipe B
                            </h2>

                            <div class="space-y-2.5 mb-5 font-mono text-xs tracking-wider text-slate-400">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-calendar text-cyan-500/70 w-4 text-center"></i>
                                    <span><?= strtoupper(date('D d M Y', strtotime($match['date']))) ?></span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-clock text-cyan-500/70 w-4 text-center"></i>
                                    <span><?= date('H:i', strtotime($match['date'])) ?> H</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-map-marker-alt text-cyan-500/70 w-4 text-center"></i>
                                    <span class="truncate"><?= htmlspecialchars($match['lieu']) ?></span>
                                </div>
                            </div>

                            <div class="h-[1px] bg-gradient-to-r from-slate-800/20 via-slate-800 to-slate-800/20 mb-4"></div>

                            <p class="text-[11px] font-mono tracking-widest text-slate-500 uppercase group-hover:text-cyan-400/80 transition-colors">
                                <i class="fas fa-users mr-2 text-slate-600 group-hover:text-cyan-500/60 transition-colors"></i>
                                [ Unités_Convoquées: <?= $nb_convoques ?> ]
                            </p>

                        </a>

                    <?php endforeach; ?>

                </div>

            </div>
        </main>
    </div>

    <script>
        function filtrer(statut) {
            document.querySelectorAll('.filtre-btn').forEach(btn => {
                btn.className = "filtre-btn px-5 py-3 border border-slate-800 bg-slate-950/40 text-slate-400 font-bold hover:border-slate-700 hover:text-slate-200 transition-all";
            });

            const btnActif = document.getElementById('btn-' + statut);
            if (btnActif) {
                btnActif.className = "filtre-btn px-5 py-3 border border-cyan-500 bg-cyan-950/40 text-cyan-400 font-bold transition-all glow-cyan";
            }

            document.querySelectorAll('.match-card').forEach(card => {
                if (statut === 'tous') {
                    card.style.display = '';
                } else if (statut === 'planifie') {
                    card.style.display = (card.dataset.statut === 'planifie' || card.dataset.statut === 'publie') ? '' : 'none';
                } else if (statut === 'termine') {
                    card.style.display = card.dataset.statut === 'termine' ? '' : 'none';
                }
            });
        }
    </script>
</body>

</html>