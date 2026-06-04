<?php
// Initialisation et sécurisation des variables de filtrage/pagination
$mois = isset($_GET['mois']) ? (int)$_GET['mois'] : (int)date('m');
$annee = isset($_GET['annee']) ? (int)$_GET['annee'] : (int)date('Y');
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$totalPages = $totalPages ?? 1; // Assurer une valeur par défaut si non transmise par le contrôleur

$pageTitle = "Gestion des Présences";

$roleUser = $_SESSION['user']['role'] ?? 'joueur';
$currentUserId = $_SESSION['user']['id'] ?? null;

// Calculer les stats globales
$totalPresents = 0;
$totalAbsents = 0;
$totalRetards = 0;
$totalSeancesJoueurs = 0;

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
    <link rel="icon" type="image/png" href="/assets/images/blue_lock_logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gradient-to-br from-slate-50 to-slate-100">
    <div class="flex min-h-screen">
        <?php include_once __DIR__ . '/../../partials/sidebar.php'; ?>

        <main class="flex-1">
            <?php include_once __DIR__ . '/../../partials/header.php'; ?>
            <div class="p-6 md:p-8 lg:p-10">
                <?php if (isset($_GET['msg']) && $_GET['msg'] === 'presences_enregistrees'): ?>
                    <div class="mb-8 p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl flex items-center gap-3 shadow-sm">
                        <i class="fas fa-check-circle text-2xl"></i>
                        Présences enregistrées avec succès !
                    </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                    <div class="group relative overflow-hidden bg-white rounded-3xl shadow-xl border border-slate-100 p-6 hover:shadow-2xl transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-green-100 to-emerald-100 rounded-bl-full opacity-70 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-slate-500 text-sm font-semibold uppercase tracking-wider mb-1">Taux de présence</p>
                                    <p class="text-4xl font-extrabold text-green-600"><?= $tauxPresence ?>%</p>
                                </div>
                                <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-700 rounded-2xl flex items-center justify-center shadow-lg shadow-green-200">
                                    <i class="fas fa-users text-white text-2xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="group relative overflow-hidden bg-white rounded-3xl shadow-xl border border-slate-100 p-6 hover:shadow-2xl transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-bl-full opacity-70 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-slate-500 text-sm font-semibold uppercase tracking-wider mb-1">Présents</p>
                                    <p class="text-4xl font-extrabold text-blue-600"><?= $totalPresents ?></p>
                                </div>
                                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-700 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-200">
                                    <i class="fas fa-check text-white text-2xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="group relative overflow-hidden bg-white rounded-3xl shadow-xl border border-slate-100 p-6 hover:shadow-2xl transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-red-100 to-pink-100 rounded-bl-full opacity-70 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-slate-500 text-sm font-semibold uppercase tracking-wider mb-1">Absents</p>
                                    <p class="text-4xl font-extrabold text-red-600"><?= $totalAbsents ?></p>
                                </div>
                                <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-pink-700 rounded-2xl flex items-center justify-center shadow-lg shadow-red-200">
                                    <i class="fas fa-times text-white text-2xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="group relative overflow-hidden bg-white rounded-3xl shadow-xl border border-slate-100 p-6 hover:shadow-2xl transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-orange-100 to-yellow-100 rounded-bl-full opacity-70 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-slate-500 text-sm font-semibold uppercase tracking-wider mb-1">Retards</p>
                                    <p class="text-4xl font-extrabold text-orange-600"><?= $totalRetards ?></p>
                                </div>
                                <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-yellow-600 rounded-2xl flex items-center justify-center shadow-lg shadow-orange-200">
                                    <i class="fas fa-clock text-white text-2xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-xl border border-slate-100 p-6 mb-10">
                    <form method="GET" action="/presence" class="flex flex-wrap gap-4 items-end">
                        <input type="hidden" name="page" value="1">
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Mois</label>
                            <select name="mois" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all">
                                <?php
                                $mois_fr = [
                                    1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                                    5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                                    9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
                                ];
                                foreach ($mois_fr as $m => $nomMois): ?>
                                    <option value="<?= $m ?>" <?= $mois == $m ? 'selected' : '' ?>>
                                        <?= $nomMois ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="w-32">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Année</label>
                            <select name="annee" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all">
                                <?php for ($a = (int)date('Y'); $a >= (int)date('Y') - 2; $a--): ?>
                                    <option value="<?= $a ?>" <?= $annee == $a ? 'selected' : '' ?>>
                                        <?= $a ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-xl transition-all shadow-lg shadow-blue-200 font-bold">
                            <i class="fas fa-filter mr-2"></i>Filtrer
                        </button>
                    </form>
                </div>

                <div class="bg-white rounded-3xl shadow-xl border border-slate-100 mb-10 overflow-hidden">
                    <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-white to-slate-50">
                        <h2 class="text-2xl font-extrabold text-slate-800 flex items-center gap-3">
                            <i class="fas fa-users text-green-600"></i>
                            Statistiques par joueur
                        </h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Joueur</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Poste</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Présent</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Absent</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Retard</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Taux</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php if (!empty($joueursStats)): ?>
                                    <?php foreach ($joueursStats as $joueur): 
                                        $tauxJoueur = ($joueur['total_seances'] ?? 0) > 0
                                            ? round(($joueur['nb_presents'] / $joueur['total_seances']) * 100, 1)
                                            : 0;
                                    ?>
                                        <tr class="hover:bg-slate-50 transition-all duration-200">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-700 rounded-2xl flex items-center justify-center text-white font-extrabold text-lg mr-4 shadow-sm">
                                                        <?= htmlspecialchars($joueur['numero_maillot'] ?? '?') ?>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-slate-800">
                                                            <?= htmlspecialchars(($joueur['nom'] ?? '') . ' ' . ($joueur['prenom'] ?? '')) ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-slate-600 font-medium">
                                                <?= htmlspecialchars($joueur['poste'] ?? '-') ?>
                                            </td>
                                            <td class="px-6 py-4 text-center font-semibold text-slate-700">
                                                <?= (int)($joueur['total_seances'] ?? 0) ?>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="inline-flex items-center px-4 py-2 rounded-2xl text-sm font-bold bg-green-100 text-green-700">
                                                    <i class="fas fa-check mr-2"></i><?= (int)($joueur['nb_presents'] ?? 0) ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="inline-flex items-center px-4 py-2 rounded-2xl text-sm font-bold bg-red-100 text-red-700">
                                                    <i class="fas fa-times mr-2"></i><?= (int)($joueur['nb_absents'] ?? 0) ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="inline-flex items-center px-4 py-2 rounded-2xl text-sm font-bold bg-orange-100 text-orange-700">
                                                    <i class="fas fa-clock mr-2"></i><?= (int)($joueur['nb_retards'] ?? 0) ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="inline-flex items-center px-4 py-2 rounded-2xl text-sm font-bold 
                                                    <?= $tauxJoueur >= 80 ? 'bg-gradient-to-r from-green-100 to-emerald-100 text-green-700' : ($tauxJoueur >= 60 ? 'bg-gradient-to-r from-yellow-100 to-amber-100 text-yellow-700' : 'bg-gradient-to-r from-red-100 to-pink-100 text-red-700') ?>">
                                                    <?= $tauxJoueur ?>%
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="px-6 py-16 text-center text-slate-500">
                                            <i class="fas fa-inbox text-6xl text-slate-300 mb-4"></i>
                                            <p class="text-lg font-medium">Aucune statistique disponible</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                    <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-white to-slate-50 flex flex-wrap justify-between items-center gap-4">
                        <h2 class="text-2xl font-extrabold text-slate-800 flex items-center gap-3">
                            <i class="fas fa-calendar text-green-600"></i>
                            Historique des séances
                        </h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Lieu</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Présents</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Absents</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php if (!empty($seances)): ?>
                                    <?php foreach ($seances as $seance): ?>
                                        <tr class="hover:bg-slate-50 transition-all duration-200">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-700 rounded-2xl flex items-center justify-center text-white font-extrabold text-lg mr-4 shadow-sm">
                                                        <?= date('d', strtotime($seance['date'])) ?>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-slate-800">
                                                            <?php
                                                            $dateObj = new DateTime($seance['date']);
                                                            $jours = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
                                                            $mois_list = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                                                            echo $jours[$dateObj->format('w')] . " " . $dateObj->format('d') . " " . $mois_list[(int)$dateObj->format('m')] . " " . $dateObj->format('Y');
                                                            ?>
                                                        </p>
                                                        <p class="text-sm text-slate-500 font-medium">
                                                            <i class="far fa-clock mr-1"></i><?= date('H:i', strtotime($seance['date'])) ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex items-center px-4 py-2 rounded-2xl text-sm font-bold 
                                                    <?= ($seance['type'] ?? '') === 'match' ? 'bg-gradient-to-r from-purple-100 to-violet-100 text-purple-700' : 'bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-700' ?>">
                                                    <?= ucfirst(htmlspecialchars($seance['type'] ?? 'entraînement')) ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-slate-600 font-medium">
                                                <i class="fas fa-map-marker-alt mr-2 text-slate-400"></i><?= htmlspecialchars($seance['lieu'] ?? '-') ?>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="inline-flex items-center px-4 py-2 rounded-2xl text-sm font-bold bg-green-100 text-green-700">
                                                    <i class="fas fa-check mr-2"></i><?= (int)($seance['nb_presents'] ?? 0) ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="inline-flex items-center px-4 py-2 rounded-2xl text-sm font-bold bg-red-100 text-red-700">
                                                    <i class="fas fa-times mr-2"></i><?= (int)($seance['nb_absents'] ?? 0) ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <?php if ($roleUser !== 'joueur'): ?>
                                                    <a href="/presence-marquer?seance_id=<?= $seance['id'] ?>"
                                                        class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-xl transition-all shadow-lg shadow-green-200 font-bold text-sm">
                                                        <i class="fas fa-pencil-alt mr-2"></i>
                                                        <?= ($seance['total_presences'] ?? 0) > 0 ? 'Modifier' : 'Marquer' ?>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="px-6 py-16 text-center text-slate-500">
                                            <i class="fas fa-calendar-times text-6xl text-slate-300 mb-4"></i>
                                            <p class="text-lg font-medium">Aucune séance disponible</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php include __DIR__ . '/../../partials/pagination.php'; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>