<?php
// =====================================================================
// DONNÉES FOURNIES PAR LE CONTRÔLEUR
// =====================================================================
// $qualifiedPlayers : joueurs déjà paginés
// $matches : liste des matchs disponibles
// $summonedMap : convocations par joueur
// $matchesById : index des matchs par id
// $page : page actuelle
// $perPage : nombre d'éléments par page
// $totalPlayers : nombre total de joueurs correspondant aux filtres
// $totalPages : nombre total de pages
// =====================================================================

$roleUser = $_SESSION['user']['role'] ?? 'joueur';
$currentUserId = $_SESSION['user']['id'] ?? null;

// =====================================================================
// CONFIGURATION DE LA PAGINATION
// =====================================================================
$currentPage = $page;
$playersToShow = $qualifiedPlayers;
$offset = ($currentPage - 1) * $perPage;

if ($currentPage > $totalPages && $totalPages > 0) {
    $currentPage = $totalPages;
}

// =====================================================================
// GÉNÉRATION DES LIENS DE PAGINATION
// =====================================================================
function buildPaginationLink($pageNum)
{
    $params = $_GET;
    $params['page'] = $pageNum;
    return '/Convocation-convocation?' . http_build_query($params);
}

// =====================================================================
// GÉNÉRATION DES NUMÉROS DE PAGE
// =====================================================================
function getPaginationRange($currentPage, $totalPages, $delta = 2)
{
    $range = [];
    if ($totalPages <= 7) {
        for ($i = 1; $i <= $totalPages; $i++) {
            $range[] = $i;
        }
        return $range;
    }

    $range[] = 1;
    $start = max(2, $currentPage - $delta);
    $end = min($totalPages - 1, $currentPage + $delta);

    if ($start > 2) {
        $range[] = '...';
    }

    for ($i = $start; $i <= $end; $i++) {
        $range[] = $i;
    }

    if ($end < $totalPages - 1) {
        $range[] = '...';
    }

    $range[] = $totalPages;
    return $range;
}

$paginationRange = getPaginationRange($currentPage, $totalPages);
$pageTitle = "Convocations Matrix";
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
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        cyber: {
                            blue: '#00f0ff',
                            purple: '#7000ff',
                            neon: '#0ff',
                            dark: '#020617',
                            card: '#0b1329'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .cyber-grid {
            background-image: linear-gradient(to right, rgba(0, 240, 255, 0.04) 1px, transparent 1px),
                              linear-gradient(to bottom, rgba(0, 240, 255, 0.04) 1px, transparent 1px);
            background-size: 30px 30px;
        }
        .glow-blue {
            box-shadow: 0 0 15px rgba(0, 240, 255, 0.2);
        }
        .glow-text {
            text-shadow: 0 0 8px rgba(0, 240, 255, 0.6);
        }
        .player-card {
            animation: cyberScan 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.8) 0%, rgba(11, 19, 41, 0.9) 100%);
        }
        @keyframes cyberScan {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(10px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
        .clip-polygon {
            clip-path: polygon(0 0, 100% 0, 100% 85%, 88% 100%, 0 100%);
        }
    </style>
</head>

<body class="bg-slate-950 text-slate-100 cyber-grid min-h-screen font-sans selection:bg-cyber-blue selection:text-slate-950">
    <div class="flex min-h-screen backdrop-blur-[2px]">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1 min-w-0 overflow-x-hidden">
            <?php include_once __DIR__ . '/../../partials/header.php'; ?>
            
            <div class="p-4 md:p-8 lg:p-10 max-w-7xl mx-auto space-y-8">
                
                <?php if (isset($_GET['msg'])): ?>
                    <div class="p-4 rounded-xl border flex items-center gap-4 animate-pulse backdrop-blur-md <?= $_GET['msg'] === 'success' ? 'bg-emerald-950/40 border-emerald-500/50 text-emerald-400' : 'bg-rose-950/40 border-rose-500/50 text-rose-400' ?>">
                        <div class="text-2xl">
                            <?= $_GET['msg'] === 'success' ? '<i class="fas fa-shield-virus"></i>' : '<i class="fas fa-terminal"></i>' ?>
                        </div>
                        <div>
                            <span class="text-xs font-mono block tracking-widest opacity-60">// SYSTEM_LOG</span>
                            <p class="font-bold text-sm md:text-base">
                                <?php
                                if ($_GET['msg'] === 'success') echo "ALGORITHME : Égoïste assigné au match avec succès.";
                                if ($_GET['msg'] === 'already_summoned') echo "CONFLIT : Ce joueur est déjà verrouillé sur ce match.";
                                if ($_GET['msg'] === 'overlap') echo "ERREUR TEMPORELLE : Collision de créneaux horaires détectée pour cet égoïste.";
                                ?>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 border-b border-slate-800 pb-6">
                    <div>
                        <div class="flex items-center gap-3 text-xs tracking-widest font-mono text-cyber-blue mb-2">
                            <span class="w-2 h-2 rounded-full bg-cyber-blue animate-ping"></span>
                            <span>BLUE LOCK PROJECT // INVOCATION MONITOR</span>
                        </div>
                        <h2 class="text-3xl md:text-4xl font-black tracking-tighter uppercase text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-200 to-cyber-blue glow-text">
                            Sélection Éligible
                        </h2>
                        <p class="text-slate-400 text-sm font-medium mt-1">
                            Facteurs d'éligibilité : <span class="text-cyber-blue">Performance (60%)</span> × <span class="text-purple-400">Présence (40%)</span>.
                        </p>
                    </div>
                    <div class="bg-slate-900/80 border border-slate-800 px-4 py-2 rounded-xl text-xs font-mono text-slate-400 flex items-center gap-3">
                        <span class="text-cyber-blue font-bold text-base"><?= $totalPlayers ?></span> ÉGOÏSTES ENREGISTRÉS
                    </div>
                </div>

                <form method="GET" action="/Convocation-convocation" class="bg-slate-900/50 border border-slate-800/80 p-5 rounded-2xl backdrop-blur-md flex flex-wrap gap-4 items-end shadow-xl">
                    <div class="flex-1 min-w-[240px]">
                        <label class="block text-xs font-mono uppercase tracking-wider text-slate-400 mb-2">// RECHERCHER UN ÉGOÏSTE</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-4 top-3.5 text-slate-500 text-sm"></i>
                            <input type="text" name="search_nom" value="<?= htmlspecialchars($_GET['search_nom'] ?? '') ?>" placeholder="Saisir un nom..." class="w-full bg-slate-950/80 border border-slate-800 text-slate-200 rounded-xl pl-11 pr-4 py-3 text-sm focus:border-cyber-blue focus:ring-1 focus:ring-cyber-blue outline-none transition-all placeholder:text-slate-600">
                        </div>
                    </div>
                    
                    <div class="w-full sm:w-40">
                        <label class="block text-xs font-mono uppercase tracking-wider text-slate-400 mb-2">// SECTION EQUIPE</label>
                        <select name="filter_equipe" class="w-full bg-slate-950/80 border border-slate-800 text-slate-200 rounded-xl p-3 text-sm focus:border-cyber-blue focus:ring-1 focus:ring-cyber-blue outline-none transition-all">
                            <option value="">Toutes</option>
                            <option value="1" <?= ($_GET['filter_equipe'] ?? '') === '1' ? 'selected' : '' ?>>Équipe 1</option>
                            <option value="2" <?= ($_GET['filter_equipe'] ?? '') === '2' ? 'selected' : '' ?>>Équipe 2</option>
                        </select>
                    </div>

                    <div class="w-full sm:w-36">
                        <label class="block text-xs font-mono uppercase tracking-wider text-slate-400 mb-2">// RATING MIN</label>
                        <input type="number" step="0.1" name="filter_score" value="<?= htmlspecialchars($_GET['filter_score'] ?? '') ?>" placeholder="0.00" class="w-full bg-slate-950/80 border border-slate-800 text-slate-200 rounded-xl p-3 text-sm focus:border-cyber-blue focus:ring-1 focus:ring-cyber-blue outline-none transition-all placeholder:text-slate-700">
                    </div>

                    <input type="hidden" name="page" value="1">
                    
                    <div class="w-full lg:w-auto flex gap-2">
                        <button type="submit" class="flex-1 lg:flex-none bg-gradient-to-r from-blue-600 to-cyber-blue hover:from-blue-500 hover:to-cyan-400 text-slate-950 font-black px-6 py-3 rounded-xl transition-all uppercase tracking-wider text-sm glow-blue">
                            <i class="fas fa-bolt mr-2"></i> Filtrer
                        </button>
                        <a href="/Convocation-convocation" class="bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold px-4 py-3 rounded-xl transition-all text-sm flex items-center justify-center">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </form>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php if (!empty($playersToShow)): ?>
                        <?php foreach ($playersToShow as $player): 
                            $isMe = ($player['id'] == $currentUserId); 
                            $hasSummon = !empty($summonedMap[$player['id']]);
                        ?>
                            <div class="player-card border <?= ($isMe && $hasSummon) ? 'border-yellow-500/40 shadow-lg shadow-yellow-500/5' : 'border-slate-800 hover:border-cyber-blue/40' ?> rounded-2xl overflow-hidden flex flex-col justify-between transition-all duration-300 group">
                                <div class="p-6 space-y-4">
                                    
                                    <div class="flex justify-between items-start gap-2">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h3 class="text-xl font-black tracking-tight text-white group-hover:text-cyber-blue transition-colors">
                                                    <?= htmlspecialchars($player['nom']) ?>
                                                </h3>
                                                <?php if ($isMe && $hasSummon): ?>
                                                    <span class="inline-flex items-center justify-center bg-yellow-500 text-slate-950 text-[10px] font-black px-1.5 py-0.5 rounded uppercase tracking-widest animate-pulse">
                                                        <i class="fas fa-star mr-1"></i>Moi
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="mt-2 flex items-center gap-2">
                                                <span class="text-xs font-mono text-slate-400">RATING:</span>
                                                <span class="text-sm font-bold font-mono text-cyber-blue"><?= number_format($player['score'], 2) ?></span>
                                            </div>
                                        </div>
                                        <div class="bg-slate-950 border border-slate-800 text-slate-400 px-3 py-1.5 rounded-xl text-xs font-mono text-right shrink-0">
                                            <span class="text-white font-bold block text-sm"><?= $player['nb_convocations'] ?></span>
                                            <?= $player['nb_convocations'] > 1 ? 'MATCHS' : 'MATCH' ?>
                                        </div>
                                    </div>

                                    <?php if ($isMe && $hasSummon): ?>
                                        <div class="p-4 bg-yellow-500/[0.03] border border-yellow-500/20 rounded-xl space-y-3">
                                            <p class="text-[10px] font-black font-mono text-yellow-500 uppercase tracking-widest flex items-center gap-2">
                                                <i class="fas fa-radiation animate-spin text-xs"></i> // CRÉNEAU SÉCURISÉ LOG
                                            </p>
                                            <ul class="space-y-2.5">
                                                <?php
                                                foreach ($summonedMap[$player['id']] as $mId):
                                                    $matchInfo = $matchesById[$mId] ?? null;
                                                    if ($matchInfo):
                                                ?>
                                                        <li class="text-xs text-slate-300 border-l-2 border-yellow-500/50 pl-3">
                                                            <div class="font-bold text-yellow-400 font-mono">
                                                                <?= date('d/m', strtotime($matchInfo['date'])) ?> @ <?= date('H:i', strtotime($matchInfo['date'])) ?>
                                                            </div>
                                                            <div class="text-slate-400 mt-0.5 text-[11px] uppercase tracking-wide truncate">
                                                                <?= htmlspecialchars($matchInfo['description']) ?>
                                                            </div>
                                                        </li>
                                                <?php endif;
                                                endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="p-4 bg-slate-950/60 border-t border-slate-900">
                                    <?php if ($roleUser !== 'joueur'): ?>
                                        <form action="/Convocation-invoke" method="POST" class="space-y-3">
                                            <?php
                                            $playerConvocations = $summonedMap[$player['id']] ?? [];
                                            $availableMatches = array_filter($matches, function ($m) use ($playerConvocations) {
                                                return !in_array($m['id'], $playerConvocations);
                                            });
                                            $isFullySummoned = empty($availableMatches);
                                            ?>

                                            <input type="hidden" name="joueur_id" value="<?= $player['id'] ?>">
                                            
                                            <div class="grid grid-cols-1 gap-2">
                                                <div>
                                                    <label class="block text-[10px] font-mono text-slate-500 uppercase tracking-wider mb-1">CIBLE DU MATCH</label>
                                                    <select name="match_id" required class="w-full bg-slate-900 border border-slate-800 text-slate-300 text-xs rounded-lg p-2 focus:border-cyber-blue outline-none transition-all disabled:opacity-50 disabled:cursor-not-allowed" <?= $isFullySummoned ? 'disabled' : '' ?>>
                                                        <?php if (!$isFullySummoned): ?>
                                                            <?php foreach ($availableMatches as $match): ?>
                                                                <option value="<?= $match['id'] ?>">
                                                                    [<?= strtoupper($match['type']) ?>] <?= date('d/m', strtotime($match['date'])) ?> - <?= htmlspecialchars($match['lieu']) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <option disabled selected>ROSTER COMPLET</option>
                                                        <?php endif; ?>
                                                    </select>
                                                </div>

                                                <div>
                                                    <label class="block text-[10px] font-mono text-slate-500 uppercase tracking-wider mb-1">ALLOCATION BLOC</label>
                                                    <select name="equipe" required class="w-full bg-slate-900 border border-slate-800 text-slate-300 text-xs rounded-lg p-2 focus:border-cyber-blue outline-none transition-all disabled:opacity-50 disabled:cursor-not-allowed" <?= $isFullySummoned ? 'disabled' : '' ?>>
                                                        <option value="A" <?= ($player['equipe'] == 1 || $player['equipe'] === 'A') ? 'selected' : '' ?>>Équipe A</option>
                                                        <option value="B" <?= ($player['equipe'] == 2 || $player['equipe'] === 'B') ? 'selected' : '' ?>>Équipe B</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <button type="submit"
                                                <?= $isFullySummoned ? 'disabled' : '' ?>
                                                class="w-full py-2.5 px-4 rounded-xl text-xs font-black uppercase tracking-wider transition-all duration-300 <?= $isFullySummoned ? 'bg-slate-900 border border-slate-800 text-slate-600 cursor-not-allowed' : 'bg-slate-900 hover:bg-cyber-blue border border-cyber-blue/40 hover:border-cyber-blue text-cyber-blue hover:text-slate-950 shadow-md hover:shadow-cyber-blue/20' ?>">
                                                <i class="<?= $isFullySummoned ? 'fas fa-lock-open' : 'fas fa-crosshairs' ?> mr-1.5"></i>
                                                <?= $isFullySummoned ? 'Saturé' : 'Déployer' ?>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <div class="text-center py-1">
                                            <span class="inline-flex items-center gap-1.5 text-[10px] font-mono text-slate-500 uppercase tracking-widest">
                                                <i class="fas fa-eye text-slate-600"></i> Lecture seule // Chiffrement actif
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-full py-16 text-center bg-slate-900/20 border border-dashed border-slate-800 rounded-3xl">
                            <i class="fas fa-users-slash text-slate-700 text-5xl mb-3 block"></i>
                            <p class="text-slate-500 font-mono text-sm tracking-wide">AUCUNE STRATÉGIE DE JOUEUR CORRESPONDANTE.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                    <div class="mt-12 pt-6 border-t border-slate-900 flex flex-col items-center gap-4">
                        <nav class="flex justify-center items-center gap-2" aria-label="Pagination Matrix">

                            <?php if ($currentPage > 1): ?>
                                <a href="<?= buildPaginationLink($currentPage - 1) ?>"
                                   class="flex items-center justify-center w-10 h-10 rounded-xl bg-slate-900 border border-slate-800 text-slate-400 hover:text-cyber-blue hover:border-cyber-blue transition-all group">
                                    <i class="fas fa-angle-left group-hover:-translate-x-0.5 transition-transform"></i>
                                </a>
                            <?php else: ?>
                                <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-slate-950 border border-slate-900 text-slate-700 cursor-not-allowed">
                                    <i class="fas fa-angle-left"></i>
                                </span>
                            <?php endif; ?>

                            <?php foreach ($paginationRange as $page): ?>
                                <?php if ($page === '...'): ?>
                                    <span class="w-10 h-10 flex items-center justify-center text-slate-600 font-mono font-bold">...</span>
                                <?php elseif ($page == $currentPage): ?>
                                    <span class="w-10 h-10 flex items-center justify-center rounded-xl bg-cyber-blue text-slate-950 font-mono font-black border border-cyber-blue glow-blue">
                                        <?= $page ?>
                                    </span>
                                <?php else: ?>
                                    <a href="<?= buildPaginationLink($page) ?>"
                                       class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-900 border border-slate-800 text-slate-400 hover:text-white hover:border-slate-600 transition-all font-mono font-bold text-sm">
                                        <?= $page ?>
                                    </a>
                                <?php endif; ?>
                            <?php endforeach; ?>

                            <?php if ($currentPage < $totalPages): ?>
                                <a href="<?= buildPaginationLink($currentPage + 1) ?>"
                                   class="flex items-center justify-center w-10 h-10 rounded-xl bg-slate-900 border border-slate-800 text-slate-400 hover:text-cyber-blue hover:border-cyber-blue transition-all group">
                                    <i class="fas fa-angle-right group-hover:translate-x-0.5 transition-transform"></i>
                                </a>
                            <?php else: ?>
                                <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-slate-950 border border-slate-900 text-slate-700 cursor-not-allowed">
                                    <i class="fas fa-angle-right"></i>
                                </span>
                            <?php endif; ?>
                        </nav>

                        <p class="text-center font-mono text-[11px] text-slate-500 tracking-wider">
                            INDEXATION : ENREGISTREMENTS CORRÉLÉS DES ÉGOÏSTES DE <span class="text-slate-300"><?= $offset + 1 ?></span> À <span class="text-slate-300"><?= min($offset + $perPage, $totalPlayers) ?></span> // GLOBAL : <span class="text-cyber-blue"><?= $totalPlayers ?></span>
                        </p>
                    </div>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <script>
        function checkConvocation(selectElement) {
            const form = selectElement.closest('form');
            if(!form) return;
            
            const submitBtn = form.querySelector('button[type="submit"]');
            const selectedOption = selectElement.options[selectElement.selectedIndex];

            if (selectedOption && selectedOption.disabled) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-lock mr-1.5"></i>Saturé';
                submitBtn.className = "w-full py-2.5 px-4 rounded-xl text-xs font-black uppercase tracking-wider transition-all duration-300 bg-slate-900 border border-slate-800 text-slate-600 cursor-not-allowed";
            } else {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-crosshairs mr-1.5"></i>Déployer';
                submitBtn.className = "w-full py-2.5 px-4 rounded-xl text-xs font-black uppercase tracking-wider transition-all duration-300 bg-slate-900 hover:bg-cyber-blue border border-cyber-blue/40 hover:border-cyber-blue text-cyber-blue hover:text-slate-950 shadow-md hover:shadow-cyber-blue/20";
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('select[name="match_id"]').forEach(select => {
                select.addEventListener('change', (e) => checkConvocation(e.target));
                checkConvocation(select);
            });
        });
    </script>
</body>

</html>