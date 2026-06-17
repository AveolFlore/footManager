<?php
require_once __DIR__ . '/../../../middleware/Role.php';
requireLogin();

if (!in_array($_SESSION['user']['role'], ['president', 'organisateur'])) {
    header('Location: /page-match');
    exit;
}

use Controllers\Convocation\ConvocationController;
use Controllers\MatchSeanceController;

$matchController      = new MatchSeanceController();
$convocationController = new ConvocationController();

$id        = $_GET['id'] ?? null;
$match     = $matchController->read_one((int) $id);
$suggestion = $convocationController->get_suggestion();
$deja_convoques = $convocationController->index((int) $id);

// ids des joueurs déjà convoqués
$ids_convoques = array_column($deja_convoques, 'joueur_id');

// tous les joueurs validés
use Config\Database;

$db      = new Database();
$pdo     = $db->connect();
$joueurs = $pdo->query(
    "SELECT id, nom, prenom, poste, numero_maillot
     FROM users
     WHERE statut = 'valide' AND role = 'joueur'
     ORDER BY nom"
)->fetchAll(PDO::FETCH_ASSOC);

// Récupérer TOUTES les convocations pour vérifier les chevauchements
$allConvocations = $convocationController->getConvocationsMap();

// Récupérer TOUS les matchs pour afficher les infos
$allMatches = $pdo->query("SELECT id, date, lieu FROM match_seance ORDER BY date DESC")->fetchAll(PDO::FETCH_ASSOC);
$matchesById = [];
foreach ($allMatches as $m) {
    $matchesById[$m['id']] = $m;
}

// Pour chaque joueur, déterminer s'il a des convocations qui se chevauchent
$playersWithOverlap = [];
$currentMatchDate = strtotime($match['date']);
$currentMatchEnd = $currentMatchDate + (2 * 60 * 60); // +2h

foreach ($joueurs as $joueur) {
    $playerId = $joueur['id'];
    $hasOverlap = false;
    $overlappingMatches = [];
    
    if (isset($allConvocations[$playerId])) {
        foreach ($allConvocations[$playerId] as $summonedMatchId) {
            if ($summonedMatchId == $id) continue; // skip le match actuel
            
            if (isset($matchesById[$summonedMatchId])) {
                $summonedMatch = $matchesById[$summonedMatchId];
                $summonedDate = strtotime($summonedMatch['date']);
                $summonedEnd = $summonedDate + (2 * 60 * 60);
                
                // Vérifier le chevauchement
                if (($currentMatchDate < $summonedEnd) && ($currentMatchEnd > $summonedDate)) {
                    $hasOverlap = true;
                    $overlappingMatches[] = $summonedMatch;
                }
            }
        }
    }
    
    $playersWithOverlap[$playerId] = [
        'has_overlap' => $hasOverlap,
        'matches' => $overlappingMatches,
        'all_summoned_matches' => $allConvocations[$playerId] ?? []
    ];
}

$pageTitle = "Gérer les convocations";
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
            opacity: 0.4;
            pointer-events: none;
            background-size: 100% 4px, 6px 100%;
        }
        .btn-glow-cyan {
            box-shadow: 0 0 15px rgba(6, 182, 212, 0.2);
            transition: all 0.3s ease;
        }
        .btn-glow-cyan:hover {
            box-shadow: 0 0 25px rgba(6, 182, 212, 0.5);
        }
        .warning-badge {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: bold;
        }
        .summoned-badge {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: bold;
        }
        .overlap-row {
            background: rgba(245, 158, 11, 0.1);
            border-color: rgba(245, 158, 11, 0.3);
        }
    </style>
</head>

<body class="cyber-bg text-slate-100 min-h-screen font-sans antialiased">
    <?php include_once __DIR__ . '/../../partials/floating-nav.php'; ?>
    <div class="pt-20 min-h-screen">
        <main class="overflow-y-auto bg-slate-950/40 backdrop-blur-sm">
            <div class="p-6 md:p-8 lg:p-10 max-w-7xl mx-auto">

                <a href="/page-matchdetail?id=<?= $id ?>"
                    class="inline-flex items-center gap-2 text-cyan-500/60 hover:text-cyan-400 mb-8 font-mono text-xs uppercase tracking-widest transition-colors">
                    <i class="fas fa-arrow-left"></i>
                    [ Retour au match ]
                </a>

                <?php if (isset($_GET['msg'])): ?>
                    <div class="mb-8 px-6 py-4 rounded-none font-mono text-sm uppercase tracking-wider border-l-4 border-t border-b border-r
                        <?php if (str_contains($_GET['msg'], 'Erreur') || str_contains($_GET['msg'], 'ne peut pas') || str_contains($_GET['msg'], 'chevauche')): ?>
                            bg-red-950/40 border-red-500 border-red-500/20 text-red-400 shadow-[0_0_15px_rgba(244,63,94,0.1)]
                        <?php else: ?>
                            bg-emerald-950/40 border-emerald-500 border-emerald-500/20 text-emerald-400 shadow-[0_0_15px_rgba(16,185,129,0.1)]
                        <?php endif; ?>">
                        <i class="fas fa-info-circle mr-3"></i>
                        SYSTEM_ALERT : <?= htmlspecialchars($_GET['msg']) ?>
                    </div>
                <?php endif; ?>

                <div class="bg-slate-900/40 border border-slate-700/80 backdrop-blur-md p-8 mb-8 relative">
                    <div class="absolute -top-[1px] -left-[1px] w-3 h-3 border-t-2 border-l-2 border-cyan-500"></div>
                    <div class="absolute -top-[1px] -right-[1px] w-3 h-3 border-t-2 border-r-2 border-cyan-500"></div>
                    
                    <h1 class="text-3xl font-black uppercase tracking-wider text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-200 to-cyan-400 flex items-center gap-4 mb-4">
                        <i class="fas fa-users text-cyan-500 filter drop-shadow-[0_0_8px_rgba(6,182,212,0.6)]"></i>
                        SÉLECTION_MATRIX : BL-MATCH
                    </h1>
                    <p class="text-sm font-mono text-slate-400 tracking-wide flex flex-wrap items-center gap-2">
                        <span class="text-cyan-400"><i class="fas fa-calendar mr-2"></i><?= date('d/m/Y H:i', strtotime($match['date'])) ?></span>
                        <span class="text-slate-700 font-bold px-2">//</span>
                        <span class="text-slate-300"><i class="fas fa-map-marker-alt mr-2"></i><?= htmlspecialchars($match['lieu']) ?></span>
                    </p>
                    <p class="text-xs text-slate-500 mt-3 flex items-center gap-2">
                        <i class="fas fa-info-circle text-amber-400"></i>
                        Les joueurs avec un badge ⚠️ sont déjà convoqués pour un match qui se chevauche dans le temps.
                    </p>
                </div>

                <?php if (!empty($suggestion)): ?>
                    <div class="bg-gradient-to-br from-cyan-950/30 to-slate-950/50 border border-cyan-500/30 p-8 mb-8 relative overflow-hidden shadow-[inset_0_0_20px_rgba(6,182,212,0.1)]">
                        <div class="absolute top-0 right-0 bg-cyan-500/10 text-cyan-400 font-mono text-[10px] uppercase tracking-widest px-3 py-1 border-b border-l border-cyan-500/30">
                            BlueLock_Al_Engine_v4.0
                        </div>
                        <p class="text-xs font-mono font-bold text-cyan-400 mb-6 flex items-center gap-3 uppercase tracking-widest">
                            <i class="fas fa-star text-amber-500 animate-pulse filter drop-shadow-[0_0_8px_rgba(245,158,11,0.5)]"></i>
                            SUGGESTION_AUTOMATIQUE — TOP <?= count($suggestion) ?> ÉGOÏSTES_DU_MOIS
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <?php foreach ($suggestion as $s): ?>
                                <div class="flex items-center justify-between bg-slate-950/60 rounded-none px-5 py-4 border border-slate-800 relative group hover:border-cyan-500/40 transition-colors">
                                    <span class="font-mono text-sm font-bold text-slate-200 uppercase tracking-wide">
                                        <?= htmlspecialchars($s['nom']) ?> <?= htmlspecialchars($s['prenom']) ?>
                                    </span>
                                    <span class="inline-flex items-center gap-2 bg-cyan-950/60 border border-cyan-500/40 text-cyan-400 px-3 py-1 font-mono text-xs font-bold">
                                        <?= $s['score'] ?> PX
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="bg-slate-900/40 border border-slate-700/80 backdrop-blur-md p-8 relative">
                    <div class="absolute -bottom-[1px] -left-[1px] w-3 h-3 border-b-2 border-l-2 border-cyan-500/40"></div>
                    <div class="absolute -bottom-[1px] -right-[1px] w-3 h-3 border-b-2 border-r-2 border-cyan-500/40"></div>

                    <h2 class="text-xl font-black uppercase tracking-wider text-slate-200 mb-8 flex items-center gap-3 font-mono">
                        <i class="fas fa-crosshairs text-cyan-500"></i>
                        INDEXATION_JOUEURS
                    </h2> 

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 font-mono">
                        <div class="p-6 bg-slate-950/80 border border-blue-500/20 relative shadow-[inner_0_0_15px_rgba(59,130,246,0.05)]">
                            <div class="absolute top-0 right-0 w-1.5 h-1.5 bg-blue-500"></div>
                            <p class="text-xs font-bold text-blue-400 mb-2 uppercase tracking-widest">UNITÉ_COMPTE : ÉQUIPE_A</p>
                            <p class="text-3xl font-black text-white" id="countA">0 / 10</p>
                        </div>
                        <div class="p-6 bg-slate-950/80 border border-rose-500/20 relative shadow-[inner_0_0_15px_rgba(244,63,94,0.05)]">
                            <div class="absolute top-0 right-0 w-1.5 h-1.5 bg-rose-500"></div>
                            <p class="text-xs font-bold text-rose-400 mb-2 uppercase tracking-widest">UNITÉ_COMPTE : ÉQUIPE_B</p>
                            <p class="text-3xl font-black text-white" id="countB">0 / 10</p>
                        </div>
                    </div>

                    <form action="/convocation-convocsave" method="POST" id="convocationForm">
                        <input type="hidden" name="match_id" value="<?= $id ?>">

                        <?php if (empty($joueurs)): ?>
                            <div class="text-center py-16 text-slate-600 font-mono">
                                <i class="fas fa-user-slash text-5xl mb-6 text-slate-800"></i>
                                <p class="text-sm uppercase tracking-widest">Aucun_sujet_valide_dans_la_base_de_données.</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-3 mb-8" id="playersList">
                                <?php foreach ($joueurs as $joueur): 
                                    $convoqueData = null;
                                    foreach ($deja_convoques as $dc) {
                                        if ($dc['joueur_id'] == $joueur['id']) {
                                            $convoqueData = $dc;
                                            break;
                                        }
                                    }
                                    $playerInfo = $playersWithOverlap[$joueur['id']];
                                    $hasOverlap = $playerInfo['has_overlap'];
                                    $isSummoned = in_array($joueur['id'], $ids_convoques);
                                ?>
                                    <div class="flex flex-col md:flex-row items-start md:items-center gap-4 p-5 bg-slate-950/40 border border-slate-900 transition-all duration-200 group
                                        <?= $hasOverlap && !$isSummoned ? 'overlap-row border-amber-500/30' : '' ?>" 
                                        id="player-row-<?= $joueur['id'] ?>">
                                        
                                        <div class="flex items-center h-full">
                                            <input type="checkbox"
                                                name="joueurs[<?= $joueur['id'] ?>][selectionne]"
                                                value="1"
                                                class="w-5 h-5 bg-slate-900 border-slate-800 text-cyan-500 focus:ring-0 focus:ring-offset-0 accent-cyan-500 player-checkbox cursor-pointer"
                                                data-player-id="<?= $joueur['id'] ?>"
                                                <?= in_array($joueur['id'], $ids_convoques) ? 'checked' : '' ?>>
                                        </div>

                                        <div class="flex-1 font-mono">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <p class="text-base font-bold text-slate-200 uppercase tracking-wide group-hover:text-cyan-400 transition-colors">
                                                    <?= htmlspecialchars($joueur['nom']) ?> <?= htmlspecialchars($joueur['prenom']) ?>
                                                </p>
                                                <?php if ($hasOverlap && !$isSummoned): ?>
                                                    <span class="warning-badge flex items-center gap-1">
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                        Chevauchement
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <p class="text-xs text-slate-500 uppercase tracking-wider mt-0.5"><?= htmlspecialchars($joueur['poste']) ?></p>
                                            
                                            <?php if (!empty($playerInfo['all_summoned_matches'])): ?>
                                                <div class="mt-2 flex flex-wrap gap-1">
                                                    <?php foreach ($playerInfo['all_summoned_matches'] as $smId): 
                                                        if (isset($matchesById[$smId]) && $smId != $id): 
                                                            $sm = $matchesById[$smId];
                                                            $isOverlapping = false;
                                                            foreach ($playerInfo['matches'] as $om) {
                                                                if ($om['id'] == $smId) {
                                                                    $isOverlapping = true;
                                                                    break;
                                                                }
                                                            }
                                                        ?>
                                                        <span class="text-[10px] px-2 py-0.5 rounded-full border
                                                            <?= $isOverlapping ? 'bg-amber-950/50 text-amber-400 border-amber-500/30' : 'bg-emerald-950/50 text-emerald-400 border-emerald-500/30' ?>">
                                                            <i class="fas fa-calendar mr-1"></i>
                                                            <?= date('d/m H:i', strtotime($sm['date'])) ?> - <?= htmlspecialchars(substr($sm['lieu'], 0, 15)) ?>
                                                            <?= $isOverlapping ? ' ⚠️' : '' ?>
                                                        </span>
                                                    <?php endif; endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="flex flex-wrap items-center gap-4 font-mono w-full md:w-auto justify-between md:justify-end">
                                            <select name="joueurs[<?= $joueur['id'] ?>][equipe]"
                                                class="text-xs bg-slate-900 border border-slate-800 text-slate-300 px-4 py-2.5 outline-none focus:border-cyan-500 font-bold uppercase tracking-wider team-select"
                                                data-player-id="<?= $joueur['id'] ?>">
                                                <option value="A" <?= $convoqueData && $convoqueData['equipe_match'] === 'A' ? 'selected' : '' ?>>ÉCO_UNITÉ_A</option>
                                                <option value="B" <?= $convoqueData && $convoqueData['equipe_match'] === 'B' ? 'selected' : '' ?>>ÉCO_UNITÉ_B</option>
                                            </select>

                                            <label class="flex items-center gap-2 text-xs font-bold text-slate-500 cursor-pointer select-none border border-slate-900 px-3 py-2 bg-slate-950/80 hover:border-slate-800 transition-colors">
                                                <input type="checkbox"
                                                    name="joueurs[<?= $joueur['id'] ?>][capitaine]"
                                                    value="1"
                                                    class="w-4 h-4 bg-slate-900 border-slate-800 accent-amber-500 captain-checkbox cursor-pointer"
                                                    data-player-id="<?= $joueur['id'] ?>"
                                                    data-team="<?= $convoqueData ? $convoqueData['equipe_match'] : 'A' ?>"
                                                    <?= $convoqueData && $convoqueData['est_capitaine'] ? 'checked' : '' ?>>
                                                <i class="fas fa-crown text-slate-600 peer-checked:text-amber-500 transition-colors"></i>
                                                <span class="uppercase tracking-widest text-[11px]">CAPITAINE</span>
                                            </label>

                                            <div class="relative flex items-center">
                                                <span class="absolute left-3 text-[10px] text-slate-600 font-bold">N°</span>
                                                <input type="number"
                                                    name="joueurs[<?= $joueur['id'] ?>][maillot]"
                                                    value="<?= $convoqueData ? $convoqueData['numero_maillot'] : ($joueur['numero_maillot'] ?? '') ?>"
                                                    placeholder="--"
                                                    min="1" max="99"
                                                    class="w-16 bg-slate-900 border border-slate-800 text-cyan-400 text-xs py-2.5 pl-7 pr-2 text-center font-black outline-none focus:border-cyan-500">
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="flex flex-col md:flex-row gap-4 font-mono text-xs uppercase tracking-widest font-black">
                                <button type="submit" name="convocsave" value="Enregistrer"
                                    class="flex-1 group relative px-8 py-4 bg-cyan-950/60 border border-cyan-500 text-cyan-400 hover:bg-cyan-500 hover:text-black btn-glow-cyan text-center transition-all duration-300">
                                    <span class="absolute top-0 left-0 w-2 h-2 border-t-2 border-l-2 border-cyan-400 group-hover:border-black"></span>
                                    <span class="absolute bottom-0 right-0 w-2 h-2 border-b-2 border-r-2 border-cyan-400 group-hover:border-black"></span>
                                    <i class="fas fa-check mr-2"></i> Enregistrer_&_Publier
                                </button>
                                <a href="/page-matchdetail?id=<?= $id ?>"
                                    class="flex-1 px-8 py-4 bg-slate-950 border border-slate-800 text-slate-400 hover:text-white hover:border-slate-600 text-center transition-all">
                                    Avorter_la_sélection
                                </a>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>

            </div>
        </main>
    </div>

  <script>
    const MAX_PLAYERS = 10;

    function updateCounts() {
        let countA = 0;
        let countB = 0;

        // Étape 1 : Calculer les totaux actuels des joueurs sélectionnés
        document.querySelectorAll('.player-checkbox').forEach(checkbox => {
            if (checkbox.checked) {
                const playerId = checkbox.dataset.playerId;
                const teamSelect = document.querySelector(`.team-select[data-player-id="${playerId}"]`);
                if (teamSelect.value === 'A') {
                    countA++;
                } else {
                    countB++;
                }
            }
        });

        const countAElement = document.getElementById('countA');
        const countBElement = document.getElementById('countB');

        countAElement.textContent = `${countA} / ${MAX_PLAYERS} SÉLECTIONNÉS`;
        countBElement.textContent = `${countB} / ${MAX_PLAYERS} SÉLECTIONNÉS`;

        // Style dynamique des compteurs selon les seuils critiques
        countAElement.className = countA >= MAX_PLAYERS ? 'text-3xl font-black text-rose-500 filter drop-shadow-[0_0_8px_rgba(244,63,94,0.4)]' : 'text-3xl font-black text-blue-400';
        countBElement.className = countB >= MAX_PLAYERS ? 'text-3xl font-black text-rose-500 filter drop-shadow-[0_0_8px_rgba(244,63,94,0.4)]' : 'text-3xl font-black text-rose-400';

        // Étape 2 : Gérer l'état d'activation des éléments sans bloquer l'interface
        document.querySelectorAll('.player-checkbox').forEach(checkbox => {
            const playerId = checkbox.dataset.playerId;
            const teamSelect = document.querySelector(`.team-select[data-player-id="${playerId}"]`);
            const row = document.getElementById(`player-row-${playerId}`);

            if (!checkbox.checked) {
                // Si l'équipe sélectionnée dans le dropdown est pleine, on désactive la checkbox
                if ((teamSelect.value === 'A' && countA >= MAX_PLAYERS) || 
                    (teamSelect.value === 'B' && countB >= MAX_PLAYERS)) {
                    checkbox.disabled = true;
                    row.classList.add('opacity-40'); // Réduction visuelle sans bloquer les pointer-events
                } else {
                    checkbox.disabled = false;
                    row.classList.remove('opacity-40');
                }
            } else {
                // Toujours actif si déjà coché
                checkbox.disabled = false;
                row.classList.remove('opacity-40');
            }
        });
    }

    // Écouteur sur le changement d'équipe (permet de libérer/bloquer la checkbox liée)
    document.querySelectorAll('.team-select').forEach(select => {
        select.addEventListener('change', function() {
            const playerId = this.dataset.playerId;
            const captainCheckbox = document.querySelector(`.captain-checkbox[data-player-id="${playerId}"]`);
            if (captainCheckbox) {
                captainCheckbox.dataset.team = this.value;
            }
            updateCounts();
        });
    });

    // Gestion exclusive du capitaine par équipe
    document.querySelectorAll('.captain-checkbox').forEach(captainCheckbox => {
        captainCheckbox.addEventListener('change', function() {
            if (this.checked) {
                const team = this.dataset.team;
                document.querySelectorAll(`.captain-checkbox[data-team="${team}"]`).forEach(otherCheckbox => {
                    if (otherCheckbox !== this) {
                        otherCheckbox.checked = false;
                    }
                });
            }
        });
    });

    // Écouteur sur le changement de sélection des joueurs
    document.querySelectorAll('.player-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateCounts);
    });

    // Initialisation au chargement de la page
    updateCounts();
</script>
</body>
</html>
