<?php
// =====================================================================
// DONNÉES ET CONFIGURATION
// =====================================================================
$roleUser = $_SESSION['user']['role'] ?? 'joueur';
$currentUserId = $_SESSION['user']['id'] ?? null;
$currentPage = (int)($page ?? 1);
$offset = ($currentPage - 1) * (int)($perPage ?? 10);

function buildPaginationLink($pageNum) {
    $params = $_GET;
    $params['page'] = $pageNum;
    return '/Convocation-convocation?' . http_build_query($params);
}

// Préparer les données pour le frontend : quels joueurs sont convoqués pour quels matchs
$playerMatchMap = [];
foreach ($summonedMap as $playerId => $matchIds) {
    $playerMatchMap[$playerId] = $matchIds;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Convocations Matrix - FC Blue Lock</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            /* Linear gradient sombre + image Rivals.jpg */
            background: linear-gradient(rgba(2, 6, 23, 0.7), rgba(2, 6, 23, 0.8)), 
                        url('/assets/images/Rivals.jpg') no-repeat center center fixed !important;
            background-size: cover !important;
        }
        .glass-panel {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.4);
        }
        .cyber-grid {
            background-image: linear-gradient(to right, rgba(0, 240, 255, 0.05) 1px, transparent 1px),
                              linear-gradient(to bottom, rgba(0, 240, 255, 0.05) 1px, transparent 1px);
            background-size: 30px 30px;
        }
        .already-summoned {
            opacity: 0.5;
        }
        .summon-badge {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: bold;
        }
    </style>
</head>

<body class="text-slate-100 cyber-grid min-h-screen font-sans">
    <?php include_once __DIR__ . '/../../partials/floating-nav.php'; ?>
    <div class="pt-20 min-h-screen">
        <main class="min-w-0 overflow-x-hidden">
            <div class="p-6 md:p-10 max-w-7xl mx-auto space-y-8">
                
                <?php if (isset($_GET['msg'])): ?>
                    <?php 
                    $msgClass = 'border-cyan-500/30 text-cyan-400';
                    if ($_GET['msg'] === 'already_summoned') {
                        $msgClass = 'border-red-500/30 text-red-400';
                        $displayMsg = '⚠️ Joueur déjà convoqué !';
                    } elseif ($_GET['msg'] === 'overlap') {
                        $msgClass = 'border-amber-500/30 text-amber-400';
                        $displayMsg = '⚠️ Ce joueur est déjà convoqué pour un match qui se chevauche !';
                    } elseif ($_GET['msg'] === 'success') {
                        $msgClass = 'border-green-500/30 text-green-400';
                        $displayMsg = '✅ Convocation enregistrée avec succès !';
                    } else {
                        $displayMsg = htmlspecialchars($_GET['msg']);
                    }
                    ?>
                    <div class="p-4 rounded-xl glass-panel border flex items-center gap-4 text-sm <?= $msgClass ?>">
                        <i class="fas fa-terminal"></i>
                        <span><?= $displayMsg ?></span>
                    </div>
                <?php endif; ?>

                <div class="glass-panel p-8 rounded-2xl border border-slate-700/50 flex flex-col md:flex-row justify-between items-center gap-6">
                    <div>
                        <h2 class="text-3xl md:text-4xl font-black uppercase tracking-tighter text-white">Sélection Égoïste</h2>
                        <p class="text-slate-300 text-sm mt-1">Gérez les déploiements tactiques sur le terrain.</p>
                        <p class="text-slate-400 text-xs mt-2 flex items-center gap-2">
                            <i class="fas fa-info-circle text-cyan-400"></i>
                            Les matchs pour lesquels un joueur est déjà convoqué sont automatiquement désactivés.
                        </p>
                    </div>
                    <div class="bg-black/40 px-6 py-3 rounded-xl border border-slate-800 font-mono text-cyan-400">
                        <?= (int)$totalPlayers ?> PLAYERS
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($qualifiedPlayers as $player): 
                        $isMe = ($player['id'] == $currentUserId);
                        $hasSummon = !empty($summonedMap[$player['id']]);
                        $playerSummonedMatchIds = $playerMatchMap[$player['id']] ?? [];
                    ?>
                        <div class="glass-panel border border-slate-700 rounded-2xl p-6 transition-all hover:border-cyan-500/50 <?= $hasSummon ? 'border-emerald-500/30' : '' ?>">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-xl font-black text-white"><?= htmlspecialchars($player['nom']) ?> <?= htmlspecialchars($player['prenom']) ?></h3>
                                <span class="text-xs font-mono text-slate-300">RATING: <strong class="text-cyan-400"><?= number_format($player['score'], 1) ?></strong></span>
                            </div>
                            
                            <?php if ($hasSummon): ?>
                                <div class="mb-4">
                                    <p class="text-xs text-slate-400 mb-2"><i class="fas fa-calendar-check text-emerald-400 mr-1"></i> Déjà convoqué pour :</p>
                                    <div class="flex flex-wrap gap-1">
                                        <?php foreach ($playerSummonedMatchIds as $mId): 
                                            if (isset($matchesById[$mId])): 
                                                $m = $matchesById[$mId]; ?>
                                                <span class="summon-badge">
                                                    <?= date('d/m', strtotime($m['date'])) ?> - <?= htmlspecialchars($m['lieu']) ?>
                                                </span>
                                            <?php endif; 
                                        endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($roleUser !== 'joueur'): ?>
                                <form action="/Convocation-invoke" method="POST" class="space-y-3">
                                    <input type="hidden" name="joueur_id" value="<?= $player['id'] ?>">
                                    <select name="match_id" class="w-full bg-slate-950/50 border border-slate-700 text-white text-sm rounded-lg p-2.5 outline-none focus:border-cyan-500" required>
                                        <option value="">Sélectionner un match...</option>
                                        <?php foreach ($matches as $match): 
                                            $isAlreadySummoned = in_array($match['id'], $playerSummonedMatchIds);
                                        ?>
                                            <option value="<?= $match['id'] ?>" 
                                                    <?= $isAlreadySummoned ? 'disabled' : '' ?>
                                                    class="<?= $isAlreadySummoned ? 'bg-slate-800 text-slate-500' : 'bg-slate-900 text-white' ?>">
                                                <?= date('d/m/Y H:i', strtotime($match['date'])) ?> - <?= htmlspecialchars($match['lieu']) ?>
                                                <?= $isAlreadySummoned ? ' (déjà convoqué)' : '' ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <select name="equipe" class="w-full bg-slate-950/50 border border-slate-700 text-white text-sm rounded-lg p-2.5 outline-none focus:border-cyan-500" required>
                                        <option value="">Sélectionner une équipe...</option>
                                        <option value="A">Équipe A</option>
                                        <option value="B">Équipe B</option>
                                    </select>
                                    <button type="submit" 
                                            class="w-full bg-cyan-600 hover:bg-cyan-500 text-white font-bold py-2 rounded-lg text-sm transition disabled:opacity-50 disabled:cursor-not-allowed">
                                        <i class="fas fa-user-plus mr-2"></i>Convocation
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                    <nav class="flex justify-center gap-2">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="<?= buildPaginationLink($i) ?>" 
                               class="w-10 h-10 flex items-center justify-center rounded-lg glass-panel <?= $i == $currentPage ? 'border-cyan-500 text-cyan-400' : 'border-slate-700' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                    </nav>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
