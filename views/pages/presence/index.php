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
        .bg-presence {
            background-image: url('/assets/images/Presence.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
    </style>
</head>
<body class="bg-presence min-h-screen">
    <?php include_once __DIR__ . '/../../partials/floating-nav.php'; ?>
    <div class="pt-20 min-h-screen bg-overlay">
        <main>
            <div class="p-6 md:p-10">
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
                        <?php 
                        $cards = [
                            ['Taux de présence', $tauxPresence . '%', 'green', 'fa-users'],
                            ['Présents', $totalPresents, 'blue', 'fa-check'],
                            ['Absents', $totalAbsents, 'red', 'fa-times'],
                            ['Retards', $totalRetards, 'orange', 'fa-clock']
                        ];
                        foreach ($cards as $c): ?>
                        <div class="bg-white rounded-3xl shadow-xl p-6 border border-slate-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-slate-500 text-xs font-bold uppercase"><?= $c[0] ?></p>
                                    <p class="text-3xl font-extrabold text-<?= $c[2] ?>-600"><?= $c[1] ?></p>
                                </div>
                                <div class="w-12 h-12 rounded-2xl bg-<?= $c[2] ?>-100 flex items-center justify-center text-<?= $c[2] ?>-600">
                                    <i class="fas <?= $c[3] ?>"></i>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="bg-white rounded-3xl shadow-xl border border-slate-100 p-6 mb-10">
                        <form method="GET" action="/presence" class="flex flex-wrap gap-4 items-end">
                            <div class="flex-1">
                                <label class="text-xs font-bold text-slate-500 uppercase">Mois</label>
                                <select name="mois" class="w-full mt-2 bg-slate-50 border rounded-xl px-4 py-2">
                                    <?php foreach ([1=>'Janvier',2=>'Février',3=>'Mars',4=>'Avril',5=>'Mai',6=>'Juin',7=>'Juillet',8=>'Août',9=>'Septembre',10=>'Octobre',11=>'Novembre',12=>'Décembre'] as $m => $n): ?>
                                        <option value="<?= $m ?>" <?= $mois == $m ? 'selected' : '' ?>><?= $n ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-xl font-bold"><i class="fas fa-filter mr-2"></i>Filtrer</button>
                        </form>
                    </div>

                    <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Joueur</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Présent</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Taux</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php if (!empty($joueursStats)): foreach ($joueursStats as $j): ?>
                                <tr>
                                    <td class="px-6 py-4 font-bold"><?= htmlspecialchars($j['nom'] . ' ' . $j['prenom']) ?></td>
                                    <td class="px-6 py-4 text-center"><?= (int)$j['nb_presents'] ?></td>
                                    <td class="px-6 py-4 text-center"><?= $j['total_seances'] > 0 ? round(($j['nb_presents']/$j['total_seances'])*100) : 0 ?>%</td>
                                </tr>
                                <?php endforeach; else: ?>
                                <tr><td colspan="3" class="px-6 py-10 text-center">Aucune donnée</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>