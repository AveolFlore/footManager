<?php
// Initialisation des variables
$mois = isset($_GET['mois']) ? (int)$_GET['mois'] : (int)date('m');
$annee = isset($_GET['annee']) ? (int)$_GET['annee'] : (int)date('Y');
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$totalPages = $totalPages ?? 1;
$pageTitle = "Gestion des Présences";
$roleUser = $_SESSION['user']['role'] ?? 'joueur';

// Calcul des stats
$totalPresents = 0; $totalAbsents = 0; $totalRetards = 0; $totalSeancesJoueurs = 0;
if (!empty($joueursStats) && is_array($joueursStats)) {
    foreach ($joueursStats as $joueur) {
        $totalPresents += $joueur['nb_presents'] ?? 0;
        $totalAbsents += $joueur['nb_absents'] ?? 0;
        $totalRetards += $joueur['nb_retards'] ?? 0;
        $totalSeancesJoueurs += $joueur['total_seances'] ?? 0;
    }
}
$tauxPresence = $totalSeancesJoueurs > 0 ? round(($totalPresents / $totalSeancesJoueurs) * 100, 1) : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - FC Blue Lock</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
                        url('/assets/images/Presence.jpg') no-repeat center center fixed;
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
<body class="min-h-screen text-white">
    <?php include_once __DIR__ . '/../../partials/floating-nav.php'; ?>
    
    <div class="pt-20 min-h-screen">
        <main class="p-6 md:p-10 max-w-7xl mx-auto">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <div class="glass-panel rounded-3xl p-6 border border-white/10 hover:bg-white/10 transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-slate-300 text-xs font-bold uppercase tracking-wider">Taux de Présence</p>
                            <p class="text-3xl font-extrabold text-emerald-400 mt-1"><?= $tauxPresence ?>%</p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-emerald-600/30 flex items-center justify-center text-emerald-400">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>

                <div class="glass-panel rounded-3xl p-6 border border-white/10 hover:bg-white/10 transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-slate-300 text-xs font-bold uppercase tracking-wider">Présents</p>
                            <p class="text-3xl font-extrabold text-blue-400 mt-1"><?= $totalPresents ?></p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-blue-600/30 flex items-center justify-center text-blue-400">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                </div>

                <div class="glass-panel rounded-3xl p-6 border border-white/10 hover:bg-white/10 transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-slate-300 text-xs font-bold uppercase tracking-wider">Absents</p>
                            <p class="text-3xl font-extrabold text-red-400 mt-1"><?= $totalAbsents ?></p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-red-600/30 flex items-center justify-center text-red-400">
                            <i class="fas fa-times"></i>
                        </div>
                    </div>
                </div>

                <div class="glass-panel rounded-3xl p-6 border border-white/10 hover:bg-white/10 transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-slate-300 text-xs font-bold uppercase tracking-wider">Retards</p>
                            <p class="text-3xl font-extrabold text-orange-400 mt-1"><?= $totalRetards ?></p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-orange-600/30 flex items-center justify-center text-orange-400">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liste des Séances -->
            <div class="glass-panel rounded-3xl overflow-hidden border border-white/10 mb-10">
                <div class="p-6 border-b border-white/10 flex items-center justify-between">
                    <h2 class="text-xl font-extrabold">Séances à venir</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-black/20 text-slate-300">
                            <tr>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Date</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Lieu</th>
                                <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider">Présents</th>
                                <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-white">
                            <?php if (!empty($seances)): foreach ($seances as $seance): ?>
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold"><?= date('d/m/Y', strtotime($seance['date'])) ?></div>
                                    <div class="text-sm text-slate-400"><?= date('H:i', strtotime($seance['date'])) ?></div>
                                </td>
                                <td class="px-6 py-4"><?= htmlspecialchars($seance['lieu']) ?></td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 bg-blue-600/30 text-blue-300 rounded-full text-sm font-bold">
                                        <?= $seance['nb_presents'] ?> / <?= $seance['total_presences'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php 
                                        $badgeClass = $seance['statut'] === 'termine' ? 'bg-green-600/30 text-green-300 border-green-500/30' :
                                                      ($seance['statut'] === 'publie' ? 'bg-cyan-600/30 text-cyan-300 border-cyan-500/30' :
                                                      'bg-slate-600/30 text-slate-300 border-slate-500/30');
                                    ?>
                                    <span class="px-3 py-1 rounded-full text-sm font-bold border <?= $badgeClass ?>">
                                        <?= ucfirst($seance['statut']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="/presence-marquer?seance_id=<?= $seance['id'] ?>" 
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-xl font-bold text-sm transition shadow-lg">
                                        <i class="fas fa-edit"></i>
                                        Marquer Présences
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr><td colspan="5" class="px-8 py-10 text-center text-slate-400">Aucune séance disponible.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="p-6 border-t border-white/10 flex justify-center gap-2">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>" 
                       class="w-10 h-10 flex items-center justify-center rounded-xl glass-panel border <?= $i == $page ? 'border-blue-500 text-blue-400' : 'border-slate-700' ?>">
                        <?= $i ?>
                    </a>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Statistiques des Joueurs -->
            <div class="glass-panel rounded-3xl overflow-hidden border border-white/10">
                <div class="p-6 border-b border-white/10">
                    <h2 class="text-xl font-extrabold">Statistiques des Joueurs</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-black/20 text-slate-300">
                            <tr>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Joueur</th>
                                <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider">Présent</th>
                                <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider">Absent</th>
                                <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider">Retard</th>
                                <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider">Taux</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-white">
                            <?php if (!empty($joueursStats)): foreach ($joueursStats as $joueur): 
                                $taux = $joueur['total_seances'] > 0 ? round(($joueur['nb_presents']/$joueur['total_seances'])*100) : 0;
                            ?>
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-6 py-4 font-bold">
                                    <?= htmlspecialchars($joueur['nom'] . ' ' . $joueur['prenom']) ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-emerald-400 font-bold"><?= (int)$joueur['nb_presents'] ?></span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-red-400 font-bold"><?= (int)$joueur['nb_absents'] ?></span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-orange-400 font-bold"><?= (int)$joueur['nb_retards'] ?></span>
                                </td>
                                <td class="px-6 py-4 text-center font-mono">
                                    <span class="<?= $taux >= 80 ? 'text-emerald-400' : ($taux >= 50 ? 'text-yellow-400' : 'text-red-400') ?> font-bold">
                                        <?= $taux ?>%
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr><td colspan="5" class="px-8 py-10 text-center text-slate-400">Aucune donnée disponible.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
