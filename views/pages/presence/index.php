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
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <?php 
                $cards = [
                    ['Taux de présence', $tauxPresence . '%', 'emerald-400', 'fa-users'],
                    ['Présents', $totalPresents, 'blue-400', 'fa-check'],
                    ['Absents', $totalAbsents, 'red-400', 'fa-times'],
                    ['Retards', $totalRetards, 'orange-400', 'fa-clock']
                ];
                foreach ($cards as $c): ?>
                <div class="glass-panel rounded-3xl p-6 border border-white/10 hover:bg-white/10 transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-slate-300 text-xs font-bold uppercase tracking-wider"><?= $c[0] ?></p>
                            <p class="text-3xl font-extrabold text-white mt-1"><?= $c[1] ?></p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center text-<?= $c[2] ?>">
                            <i class="fas <?= $c[3] ?>"></i>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="glass-panel rounded-3xl p-6 mb-10 border border-white/10">
                <form method="GET" action="/presence" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label class="text-xs font-bold text-slate-300 uppercase">Sélectionner un Mois</label>
                        <select name="mois" class="w-full mt-2 bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-white outline-none focus:border-blue-400">
                            <?php foreach ([1=>'Janvier',2=>'Février',3=>'Mars',4=>'Avril',5=>'Mai',6=>'Juin',7=>'Juillet',8=>'Août',9=>'Septembre',10=>'Octobre',11=>'Novembre',12=>'Décembre'] as $m => $n): ?>
                                <option value="<?= $m ?>" <?= $mois == $m ? 'selected' : '' ?> class="text-slate-900"><?= $n ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2.5 rounded-xl font-bold transition shadow-lg">
                        <i class="fas fa-filter mr-2"></i>Filtrer
                    </button>
                </form>
            </div>

            <div class="glass-panel rounded-3xl overflow-hidden border border-white/10">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-black/20 text-slate-300">
                            <tr>
                                <th class="px-8 py-5 text-xs font-bold uppercase tracking-wider">Joueur</th>
                                <th class="px-8 py-5 text-center text-xs font-bold uppercase tracking-wider">Présent</th>
                                <th class="px-8 py-5 text-center text-xs font-bold uppercase tracking-wider">Taux</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-white">
                            <?php if (!empty($joueursStats)): foreach ($joueursStats as $j): ?>
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-8 py-4 font-bold"><?= htmlspecialchars($j['nom'] . ' ' . $j['prenom']) ?></td>
                                <td class="px-8 py-4 text-center"><?= (int)$j['nb_presents'] ?></td>
                                <td class="px-8 py-4 text-center font-mono"><?= $j['total_seances'] > 0 ? round(($j['nb_presents']/$j['total_seances'])*100) : 0 ?>%</td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr><td colspan="3" class="px-8 py-10 text-center text-slate-400">Aucune donnée disponible.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>
</body>
</html>