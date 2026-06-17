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
    <link rel="icon" type="image/png" href="/assets/images/blue_lock_logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                        url('/assets/images/impact.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .glass-panel {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
        }
    </style>
</head>

<body class="text-white min-h-screen font-sans antialiased">
    <?php include_once __DIR__ . '/../../partials/floating-nav.php'; ?>
    <div class="pt-20 min-h-screen">
        <main class="p-6 md:p-8 lg:p-10 max-w-7xl mx-auto">
            
            <div class="mb-6">
                <a href="/page-match" class="text-blue-400 hover:text-blue-300 text-sm inline-flex items-center gap-2 transition-colors">
                    <i class="fas fa-arrow-left"></i> Retour aux matchs
                </a>
            </div>

            <div class="glass-panel p-6 md:p-8 rounded-2xl mb-8">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
                    <div>
                        <h1 class="text-2xl font-bold flex items-center gap-3">
                            <i class="fas fa-futbol text-blue-400"></i>
                            Équipe A <span class="text-white/50 font-normal">VS</span> Équipe B
                        </h1>

                        <?php
                        $badge = match ($match['statut']) {
                            'planifie', 'publie' => ['label' => 'En attente', 'class' => 'bg-blue-900/50 text-blue-300 border-blue-500/50'],
                            'termine' => ['label' => 'Terminé', 'class' => 'bg-green-900/50 text-green-300 border-green-500/50'],
                            default => ['label' => ucfirst($match['statut']), 'class' => 'bg-gray-900/50 text-gray-300 border-gray-500/50']
                        };
                        ?>
                        <span class="inline-flex items-center text-xs font-semibold px-3 py-1 mt-2 rounded-lg border <?= $badge['class'] ?>">
                            <i class="fas fa-clock mr-1.5 text-[11px]"></i>
                            <?= $badge['label'] ?>
                        </span>
                    </div>

                    <?php if ($match['statut'] === 'termine' && $resultat): ?>
                        <div class="glass-panel p-4 rounded-xl text-center min-w-[160px]">
                            <p class="text-xs text-white/70 uppercase tracking-wider mb-1">Score Final</p>
                            <p class="text-3xl font-bold">
                                <span class="text-blue-400"><?= $resultat['buts_equipe_a'] ?></span>
                                <span class="text-white/50 text-xl mx-1">:</span>
                                <span class="text-red-400"><?= $resultat['buts_equipe_b'] ?></span>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-t border-white/10 pt-6">
                    <div class="flex items-center gap-4 p-4 bg-white/5 rounded-xl">
                        <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center text-blue-400">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div>
                            <p class="text-xs text-white/60">Date</p>
                            <p class="text-sm font-semibold">
                                <?php
                                $days = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
                                $months = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                                $timestamp = strtotime($match['date']);
                                echo $days[date('w', $timestamp)] . ' ' . date('d', $timestamp) . ' ' . $months[date('n', $timestamp)];
                                ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4 p-4 bg-white/5 rounded-xl">
                        <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center text-blue-400">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <p class="text-xs text-white/60">Heure</p>
                            <p class="text-sm font-semibold">
                                <?= date('H:i', strtotime($match['date'])) ?>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 p-4 bg-white/5 rounded-xl">
                        <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center text-blue-400">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <p class="text-xs text-white/60">Lieu</p>
                            <p class="text-sm font-semibold truncate max-w-[180px]">
                                <?= htmlspecialchars($match['lieu']) ?>
                            </p>
                        </div>
                    </div>
                </div>

                <?php if (in_array($_SESSION['user']['role'], ['president', 'organisateur'])): ?>
                    <div class="flex flex-wrap gap-3 mt-6 pt-6 border-t border-white/10 text-sm">
                        <?php if ($match['statut'] === 'planifie'): ?>
                            <a href="/page-matchconvocations?id=<?= $match['id'] ?>"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all shadow-lg">
                                <i class="fas fa-users"></i> Gérer les convocations
                            </a>
                            <a href="/matchSeance-edit?id=<?= $match['id'] ?>"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-xl transition-all border border-white/20">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                        <?php endif; ?>

                        <?php if ($match['statut'] !== 'termine'): ?>
                            <button onclick="confirm('Confirmer la suppression ?') && (window.location.href = '/matchSeance-delete?id=<?= $match['id'] ?>')" 
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-900/30 hover:bg-red-900/50 text-red-300 font-semibold rounded-xl transition-all border border-red-500/30 ml-auto">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <div class="glass-panel p-6 rounded-2xl">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-white/10">
                        <h2 class="text-lg font-bold flex items-center gap-2">
                            <i class="fas fa-shield-alt text-blue-400"></i> Équipe A
                        </h2>
                        <span class="text-xs font-semibold text-white/70 bg-white/10 px-3 py-1 rounded-full"><?= count($joueursParEquipe['A']) ?> Joueurs</span>
                    </div>

                    <?php if (empty($joueursParEquipe['A'])): ?>
                        <div class="text-center py-8 text-white/50 text-sm border border-dashed border-white/20 rounded-xl">
                            <i class="fas fa-user-slash text-2xl mb-2 block"></i>
                            Aucun joueur convoqué dans cette équipe.
                        </div>
                    <?php else: ?>
                        <div class="space-y-2">
                            <?php foreach ($joueursParEquipe['A'] as $convoque): ?>
                                <div class="flex items-center justify-between p-3 bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs font-bold text-blue-300 bg-blue-900/30 border border-blue-500/30 w-8 h-8 flex items-center justify-center rounded-full">
                                            <?= $convoque['numero_maillot'] ? str_pad($convoque['numero_maillot'], 2, '0', STR_PAD_LEFT) : '--' ?>
                                        </span>
                                        <p class="text-sm font-medium">
                                            <?= htmlspecialchars($convoque['nom']) ?> <?= htmlspecialchars($convoque['prenom']) ?>
                                        </p>
                                    </div>
                                    <?php if ($convoque['est_capitaine']): ?>
                                        <span class="text-[11px] font-semibold px-2 py-0.5 bg-amber-900/30 text-amber-300 rounded-lg border border-amber-500/30 inline-flex items-center gap-1">
                                            <i class="fas fa-crown text-[10px]"></i> Cap.
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="glass-panel p-6 rounded-2xl">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-white/10">
                        <h2 class="text-lg font-bold flex items-center gap-2">
                            <i class="fas fa-shield-alt text-red-400"></i> Équipe B
                        </h2>
                        <span class="text-xs font-semibold text-white/70 bg-white/10 px-3 py-1 rounded-full"><?= count($joueursParEquipe['B']) ?> Joueurs</span>
                    </div>

                    <?php if (empty($joueursParEquipe['B'])): ?>
                        <div class="text-center py-8 text-white/50 text-sm border border-dashed border-white/20 rounded-xl">
                            <i class="fas fa-user-slash text-2xl mb-2 block"></i>
                            Aucun joueur convoqué dans cette équipe.
                        </div>
                    <?php else: ?>
                        <div class="space-y-2">
                            <?php foreach ($joueursParEquipe['B'] as $convoque): ?>
                                <div class="flex items-center justify-between p-3 bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs font-bold text-red-300 bg-red-900/30 border border-red-500/30 w-8 h-8 flex items-center justify-center rounded-full">
                                            <?= $convoque['numero_maillot'] ? str_pad($convoque['numero_maillot'], 2, '0', STR_PAD_LEFT) : '--' ?>
                                        </span>
                                        <p class="text-sm font-medium">
                                            <?= htmlspecialchars($convoque['nom']) ?> <?= htmlspecialchars($convoque['prenom']) ?>
                                        </p>
                                    </div>
                                    <?php if ($convoque['est_capitaine']): ?>
                                        <span class="text-[11px] font-semibold px-2 py-0.5 bg-amber-900/30 text-amber-300 rounded-lg border border-amber-500/30 inline-flex items-center gap-1">
                                            <i class="fas fa-crown text-[10px]"></i> Cap.
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
                <div class="glass-panel p-6 rounded-2xl mt-8">
                    <h2 class="text-lg font-bold mb-4 flex items-center gap-2">
                        <i class="fas fa-clipboard-list text-blue-400"></i> Enregistrer les résultats du match
                    </h2>

                    <form action="/resultatMatch-resultatsave" method="POST">
                        <input type="hidden" name="match_id" value="<?= $match['id'] ?>">

                        <div class="flex flex-col sm:flex-row items-center gap-6 bg-white/5 p-4 rounded-xl border border-white/10">
                            
                            <div class="flex items-center gap-4">
                                <div class="text-center">
                                    <label class="text-xs text-white/70 font-medium block mb-1">Buts Équipe A</label>
                                    <input type="number" name="buts_equipe_a" min="0" value="0"
                                        class="w-20 bg-white/10 border border-white/20 text-white rounded-xl text-center py-2 text-xl font-bold outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <span class="text-2xl font-bold text-white/50 mt-5">:</span>

                                <div class="text-center">
                                    <label class="text-xs text-white/70 font-medium block mb-1">Buts Équipe B</label>
                                    <input type="number" name="buts_equipe_b" min="0" value="0"
                                        class="w-20 bg-white/10 border border-white/20 text-white rounded-xl text-center py-2 text-xl font-bold outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            <div class="sm:ml-auto w-full sm:w-auto">
                                <button type="submit" name="save_resultat" value="Enregistrer"
                                    class="w-full sm:w-auto px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-xl transition-all shadow-lg inline-flex items-center justify-center gap-2">
                                    <i class="fas fa-check"></i> Valider les scores
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>

</html>
